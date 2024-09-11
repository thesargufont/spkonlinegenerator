<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use Carbon\Carbon;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class EmployeeController extends Controller
{
    public function index()
    {
        $departments = Department::where('active', 1)->get();

        return view('masters.employee.employee_index', [
            'departments' => $departments,
        ]);
    }

    public function getData($request, $isExcel = '')
    {
        if ($isExcel == "") {
            session([
                'employee' . '.employee_name' => $request->has('employee_name') ?  $request->input('employee_name') : '',
                'employee' . '.employee_nik' => $request->has('employee_nik') ?  $request->input('employee_nik') : '',
                'employee' . '.department' => $request->has('department') ?  $request->input('department') : '',
                'employee' . '.gender' => $request->has('gender') ?  $request->input('gender') : '',
                'employee' . '.status' => $request->has('status') ?  $request->input('status') : '',
            ]);
        }

        $employee_name  = session('employee' . '.employee_name') != '' ? session('employee' . '.employee_name') : '';
        $employee_nik   = session('employee' . '.employee_nik') != '' ? session('employee' . '.employee_nik') : '';
        $department     = session('employee' . '.department') != '' ? session('employee' . '.department') : '';
        $gender         = session('employee' . '.gender') != '' ? session('employee' . '.gender') : '';
        $status         = session('employee' . '.status') != '' ? session('employee' . '.status') : '';

        $employee_name = strtoupper($employee_name);
        $employee_nik = strtoupper($employee_nik);
        $department = strtoupper($department);
        $gender = strtoupper($gender);
        $status = strtoupper($status);

        $employeeDatas = User::where('active', $status);
        if ($employee_name != '') {
            $employeeDatas = $employeeDatas->where('name', 'LIKE',  "%{$employee_name}%");
        }

        if ($employee_nik != '') {
            $employeeDatas = $employeeDatas->where('nik', 'LIKE',  "%{$employee_nik}%");
        }

        if ($department != '') {
            $employeeDatas = $employeeDatas->where('department_id', $department);
        }

        if ($gender != '') {
            $employeeDatas = $employeeDatas->where('gender', $gender);
        }

        return $employeeDatas;
    }

    public function data(Request $request)
    {
        $datas = $this->getData($request);

        $datatables = DataTables::of($datas)
            ->filter(function ($instance) use ($request) {
                return true;
            });

        $datatables = $datatables->addColumn('action', function ($item) use ($request) {
            $txt = '';
            $txt .= "<a href=\"#\" onclick=\"showItem('$item[id]');\" title=\"" . ucfirst(__('view')) . "\" class=\"btn btn-xs btn-secondary\"><i class=\"fa fa-eye fa-fw fa-xs\"></i></a>";
            // $txt .= "<a href=\"#\" onclick=\"editItem($item[id]);\" title=\"" . ucfirst(__('edit')) . "\" class=\"btn btn-xs btn-secondary\"><i class=\"fa fa-edit fa-fw fa-xs\"></i></a>";
            if ($item->active == 1) {
                $txt .= "<a href=\"#\" onclick=\"deleteItem($item[id]);\" title=\"" . ucfirst(__('delete')) . "\" class=\"btn btn-xs btn-secondary\"><i class=\"fa fa-trash fa-fw fa-xs\"></i></a>";
            }

            return $txt;
        })
            ->addColumn('active', function ($item) {
                if ($item->active == 1) {
                    return 'AKTIF';
                } else {
                    return 'TIDAK AKTIF';
                }
            })
            ->addColumn('department', function ($item) {
                $department = Department::where('id', $item->department_id)->first()->department;
                return $department;
            })
            ->editColumn('start_effective', function ($item) {
                return Carbon::createFromFormat("Y-m-d H:i:s", $item->updated_at)->format('d/m/Y');
            })
            ->editColumn('end_effective', function ($item) {
                if ($item->end_effective == null) {
                    return '-';
                } else {
                    return Carbon::createFromFormat("Y-m-d H:i:s", $item->end_effective)->format('d/m/Y');
                }
            })
            ->addColumn('created_by', function ($item) {
                return optional($item->createdBy)->name;
            })
            ->editColumn('created_at', function ($item) {
                return Carbon::createFromFormat("Y-m-d H:i:s", $item->created_at)->format('d/m/Y H:i:s');
            })
            ->addColumn('updated_by', function ($item) {
                return optional($item->updatedBy)->name;
            })
            ->editColumn('updated_at', function ($item) {
                return Carbon::createFromFormat("Y-m-d H:i:s", $item->updated_at)->format('d/m/Y H:i:s');
            });
        return $datatables->make(TRUE);
    }

    public function createNew()
    {
        $departments = Department::where('active', 1)->get();

        return view('masters.employee.form_input', [
            'departments' => $departments,
        ]);
    }

    public function submitData(Request $request)
    {
        $name          = strtoupper($request->name);
        $nik           = $request->nik;
        $department    = intval($request->department);
        $gender        = strtoupper($request->gender);
        $email         = $request->email;
        $phone_number  = strtoupper($request->phone_number);

        if ($name == '') {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Nama wajib terisi, harap periksa kembali formulir pengisian data</div>'
            ]);
        }

        if ($nik == '') {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">NIK wajib terisi, harap periksa kembali formulir pengisian data</div>'
            ]);
        }

        if ($email == '') {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Email wajib terisi, harap periksa kembali formulir pengisian data</div>'
            ]);
        }

        $checkDuplicateData = User::where('email', $email)
            ->where('active', 1)
            ->first();

        if ($checkDuplicateData) {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Email sudah terdaftar dan masih aktif</div>'
            ]);
        }

        try {
            // CREATE DATA 
            DB::beginTransaction();
            $insertUser = new User([
                'name'            => $name,
                'department_id'   => $department,
                'email'           => $email,
                'password'        => Hash::make($nik),
                'nik'             => $nik,
                'gender'          => $gender,
                '$phone_number'   => $phone_number,
                'active'          => 1,
                'start_effective' => Carbon::now()->timezone('Asia/Jakarta'),
                'end_effective'   => null,
                'created_by'      => Auth::user()->id,
                'created_at'      => Carbon::now()->timezone('Asia/Jakarta'),
                'updated_by'      => Auth::user()->id,
                'updated_at'      => Carbon::now()->timezone('Asia/Jakarta'),
            ]);


            $insertUser->save();

            DB::commit();
            return response()->json([
                'success' => true,
                "message" => '<div class="alert alert-success">Data User berhasil disimpan</div>'
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Data gagal di proses, terjadi kesalah system</div>'
            ]);
        }
    }

    public function deleteData(Request $request)
    {
        $user = User::where('id', $request->id)->first();

        try {
            DB::beginTransaction();
            if ($user) {
                $user->active         = 0;
                $user->end_effective  = Carbon::now()->timezone('Asia/Jakarta');
                $user->updated_by     = Auth::user()->id;
                $user->updated_at     = Carbon::now()->timezone('Asia/Jakarta');
                $user->save();

                DB::commit();
                return response()->json([
                    'success' => true,
                    "message" => '<div class="alert alert-success">Data User berhasil dihapus, status : TIDAK AKTIF</div>'
                ]);
            } else {
                return response()->json([
                    'errors' => true,
                    "message" => '<div class="alert alert-danger">Data gagal di proses, data karyawan tidak ditemukan</div>'
                ]);
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Data gagal di proses, terjadi kesalah system</div>'
            ]);
        }
    }

    public function detailData($id)
    {
        $employee = User::where('id', $id)->first();
        if ($employee) {
            if ($employee->active == 1) {
                $active = 'AkTIF';
            } else {
                $active = 'TIDAK AkTIF';
            }
            return view('masters.employee.form_detail', [
                'nama'             => $employee->name,
                'nik' => $employee->nik,
                'department'        => Department::find($employee->department_id) ? Department::find($employee->department_id)->department : '-',
                'gender'             => $employee->gender,
                'email'             => $employee->email,
                'phone_number'              => $employee->phone_number,
                'signature'               => $employee->signature_path,
            ]);
        } else {
            return view('masters.employee.form_detail', [
                'nama'             => '',
                'nik' => '',
                'department'        => '',
                'gender'             => '',
                'email'             => '',
                'phone_number'              => '',
                'signature'               => '',
            ]);
        }
    }

    public function importExcel()
    {
        return view('masters.employee.upload');
    }

    public function makeTempTable()
    {
        Schema::create('temp', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 150)->nullable();
            $table->string('department', 50)->nullable();
            $table->string('nik', 50)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('gender', 50)->nullable();
            $table->string('phone_number', 50)->nullable();
            $table->text('remark')->default('');
            $table->temporary();
        });
    }

    public function dropTempTable()
    {
        Schema::dropIfExists('temp');
    }

    public function uploadDepartment(Request $request)
    {
        $countError = 0;
        $success   = false;
        if ($request->hasfile('validatedCustomFile')) {
            $name = $request->file('validatedCustomFile')->getClientOriginalName();
            $filename = $name;
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            if (strtolower($ext) != 'xlsx') {
                $filename = "";
                $message = '<div class="alert alert-danger">format file tidak sesuai</div>';
                // $error    = ucfirst(__('format file tidak sesuai'));
                $success    = false;
                return response()->json([
                    'filename'    => $filename,
                    'message'    => $message,
                    'success'    => $success,
                ]);
            }

            $extension = $request->file('validatedCustomFile')->getClientOriginalExtension();

            $name = "Department" . "_" . Auth::user()->id . "." . $extension;
            $request->file('validatedCustomFile')->move(storage_path() . '/app/uploads/', $name);
            $attachments = storage_path() . '/app/uploads/' . $name;
            
            $data = (new FastExcel)->import($attachments);

            foreach ($data as $row) {
                $error = 0;

                $name           = trim(strtoupper($row['Nama']), ' ');
                $department     = trim(strtoupper($row['Departemen']), ' ');
                $nik            = trim(strtoupper($row['NIK']), ' ');
                $email          = trim(strtoupper($row['email']), ' ');
                $gender         = trim(strtoupper($row['Jenis Kelamin']), ' ');
                $phone_number   = trim(strtoupper($row['Nomor Telp']), ' ');

                if($name == '' && $department == '' && $nik == '' && $email == '' && $gender == '' && $phone_number == '' ){
                    continue;
                }

                if ($name == '') {
                    $error++;
                }

                if ($department == '') {
                    $error++;
                } else {
                    $checkDepartment = Department::where('department', $department)
                                                  ->where('active', 1)
                                                  ->first();

                    if(!$checkDepartment){
                        $error++;
                    }
                }

                if ($nik == '') {
                    $error++;
                }

                if ($email == '') {
                    $error++;
                }

                if ($gender == '') {
                    $error++;
                } else {
                    $arrGender = ['PRIA', 'WANITA'];
                    if(!in_array($gender, $arrGender)){
                        $error++;
                    }
                }

                if ($error > 0) {
                    $countError++;
                }
            }

            if ($countError > 0) {
                $success   = false;
                $message   = '<div class="alert alert-danger">Terdapat data error, harap periksa kembali file ' . $filename . '</div>';
            } else {
                $success   = true;
                $message   = '<div class="alert alert-success">Validasi data berhasil, data dapat disimpan</div>';
            }

            return response()->json([
                'filename'  => $attachments,
                'success'  => $success,
                'message'  => $message,
            ]);
        } else {
            $message   = '<div class="alert alert-danger">Pilih file...</div>';
            return response()->json([
                'filename'  => '',
                'success'  => $success,
                'message'  => $message,
            ]);
        }
    }

    public function displayUpload(Request $request)
    {
        $this->dropTempTable();
        $this->makeTempTable();
        
        if ($request->fileName != "" || $request->fileName != null) {
            $attachments = $request->fileName;
            $data = (new FastExcel)->import($attachments);

            $countError = 0;
            $tempData = [];
            foreach ($data as $row) {
                $remark = [];

                $name           = trim(strtoupper($row['Nama']), ' ');
                $department     = trim(strtoupper($row['Departemen']), ' ');
                $nik            = trim(strtoupper($row['NIK']), ' ');
                $email          = trim(strtoupper($row['email']), ' ');
                $gender         = trim(strtoupper($row['Jenis Kelamin']), ' ');
                $phone_number   = trim(strtoupper($row['Nomor Telp']), ' ');

                if($name == '' && $department == '' && $nik == '' && $email == '' && $gender == '' && $phone_number == '' ){
                    continue;
                }

                if ($name == '') {
                    $remark [] = 'Nama tidak boleh kosong';
                }

                if ($department == '') {
                    $remark [] = 'Departemen tidak boleh kosong';
                } else {
                    $checkDepartment = Department::where('department', $department)
                                                  ->where('active', 1)
                                                  ->first();

                    if(!$checkDepartment){
                        $remark [] = 'Departemen '.$department.' tidak ditemukan';
                    }
                }

                if ($nik == '') {
                    $remark [] = 'NIK tidak boleh kosong';
                }

                if ($email == '') {
                    $remark [] = 'Email tidak boleh kosong';
                }

                if ($gender == '') {
                    $remark [] = 'Jenis Kelamin tidak boleh kosong';
                } else {
                    $arrGender = ['PRIA', 'WANITA'];
                    if(!in_array($gender, $arrGender)){
                        $remark [] = 'Jenis Kelamin harus PRIA / WANITA';
                    }
                }

                if (count($remark) > 0) {
                    $countError++;
                }

                $tempOutput = [
                    'name' => $name,
                    'department' => $department,
                    'nik' => $nik,
                    'email' => $email,
                    'gender' => $gender,
                    'phone_number' => $phone_number,
                    'remark'         => implode(', ', $remark)
                ];
                DB::table('temp')->insert($tempOutput);
                $tempData = DB::table('temp')->get();
            }
            if(count($tempData) == 0){
                $tempOutput = [
                    'name' => '',
                    'department' => '',
                    'nik' => '',
                    'email' => '',
                    'gender' => '',
                    'phone_number' => '',
                    'remark'         => ''
                ];
                DB::table('temp')->insert($tempOutput);
                $tempData = DB::table('temp')->get();
            }
        } else {
            $tempOutput = [
                'name' => '',
                'department' => '',
                'nik' => '',
                'email' => '',
                'gender' => '',
                'phone_number' => '',
                'remark'         => ''
            ];
            DB::table('temp')->insert($tempOutput);
            $tempData = DB::table('temp')->get();
        }

        $datatables = Datatables::of($tempData)
            ->filter(function ($instance) use ($request) {
                return true;
            });

        return $datatables->make(TRUE);
    }

    public function saveUpload(Request $request)
    {
        if($request->fileData != "")
        {
            $attachments = $request->fileData;  
            $data = (new FastExcel)->import($attachments);
            
            try {
                DB::beginTransaction();
                foreach ($data as $row) {
                    $error = false;

                    $name           = trim(strtoupper($row['Nama']), ' ');
                    $department     = trim(strtoupper($row['Departemen']), ' ');
                    $nik            = trim(strtoupper($row['NIK']), ' ');
                    $email          = trim(strtoupper($row['email']), ' ');
                    $gender         = trim(strtoupper($row['Jenis Kelamin']), ' ');
                    $phone_number   = trim(strtoupper($row['Nomor Telp']), ' ');

                    if($name == '' && $department == '' && $nik == '' && $email == '' && $gender == '' && $phone_number == '' ){
                        continue;
                    }

                    if ($name == '') {
                        $error = true;
                    }
    
                    if ($department == '') {
                        $error = true;
                    } else {
                        $checkDepartment = Department::where('department', $department)
                                                      ->where('active', 1)
                                                      ->first();
    
                        if(!$checkDepartment){
                            $error = true;
                        }
                    }
    
                    if ($nik == '') {
                        $error = true;
                    }
    
                    if ($email == '') {
                        $error = true;
                    }
    
                    if ($gender == '') {
                        $error = true;
                    } else {
                        $arrGender = ['PRIA', 'WANITA'];
                        if(!in_array($gender, $arrGender)){
                            $error = true;
                        }
                    }

                    if (!$error){
                        $insertUser = new User([
                            'name'            => $name,
                            'department_id'   => $checkDepartment->id,
                            'email'           => $email,
                            'password'        => Hash::make($nik),
                            'nik'             => $nik,
                            'gender'          => $gender,
                            '$phone_number'   => $phone_number,
                            'active'          => 1,
                            'start_effective' => Carbon::now()->timezone('Asia/Jakarta'),
                            'end_effective'   => null,
                            'created_by'      => Auth::user()->id,
                            'created_at'      => Carbon::now()->timezone('Asia/Jakarta'),
                            'updated_by'      => Auth::user()->id,
                            'updated_at'      => Carbon::now()->timezone('Asia/Jakarta'),
                        ]);
                        $insertUser->save();
                    } else {
                        DB::rollback();

                        $success   = false;
                        $message   = '<div class="alert alert-danger">Terdapat data error, harap periksa kembali file</div>';

                        return response()->json([
                            'success'  => $success,
                            'message'  => $message,
                        ]);
                    }
                }
                DB::commit();

                $success   = true;
                $message   = '<div class="alert alert-success">File berhasil diproses</div>';
                return response()->json([
                    'success'  => $success,
                    'message'  => $message,
                ]);
            } catch(\Exception $e){
                DB::rollback();
                $success   = false;
                $message   = '<div class="alert alert-danger">Terdapat kesalahn, harap proses kembali</div>';

                return response()->json([
                    'success'  => $success,
                    'message'  => $message,
                ]);
            }
        } else {
            $success   = false;
            $message   = '<div class="alert alert-danger">File tidak ditemukan, harap periksa kembali</div>';

            return response()->json([
                'success'  => $success,
                'message'  => $message,
            ]);
        }
    }

    public function downloadDepartmentTemplate()
    {
        $filename = 'Template_Master_Pengguna.xlsx';
        return response()->download(storage_path('app/files/' . $filename));
    }
}

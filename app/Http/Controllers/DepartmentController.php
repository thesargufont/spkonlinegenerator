<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use Carbon\Carbon;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\DepartmentHist;
use App\Exports\DepartmentExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Schema;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Schema\Blueprint;

class DepartmentController extends Controller
{
    public function index()
    {
        return view('masters.department.department_index');
    }

    public function createNew()
    {
        return view('masters.department.form_input');
    }

    public function submitData(Request $request)
    {
        $departmentName = strtoupper($request->department_name);
        $departmentCode = strtoupper($request->department_code);
        $description = strtoupper($request->description);

        if ($departmentName == '') {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Nama departemen wajib terisi, harap periksa kembali formulir pengisian data</div>'
            ]);
        }

        if ($departmentCode == '') {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Kode bagian wajib terisi, harap periksa kembali formulir pengisian data</div>'
            ]);
        }

        if ($description == '') {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Deskripsi wajib terisi, harap periksa kembali formulir pengisian data</div>'
            ]);
        }

        $checkDuplicateData = Department::where('department', $departmentName)
            ->where('active', 1)
            ->first();

        if ($checkDuplicateData) {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Telah ditemukan data departemen ' . $departmentName . ' yang masih aktif</div>'
            ]);
        }

        try {
            // CREATE DATA 
            DB::beginTransaction();

            $insertDepartment = new Department([
                'department'              => $departmentName,
                'department_code'         => $departmentCode,
                'department_description'  => $description,
                'active'                  => 1,
                'start_effective'         => Carbon::now()->timezone('Asia/Jakarta'),
                'end_effective'           => null,
                'created_by'              => Auth::user()->id,
                'created_at'              => Carbon::now()->timezone('Asia/Jakarta'),
                'updated_by'              => Auth::user()->id,
                'updated_at'              => Carbon::now()->timezone('Asia/Jakarta'),
            ]);
            $insertDepartment->save();

            $insertDepartmentHist = new DepartmentHist([
                'department_id'           => $insertDepartment->id,
                'department'              => $insertDepartment->department,
                'department_code'         => $insertDepartment->department_code,
                'department_description'  => $insertDepartment->department_description,
                'active'                  => $insertDepartment->active,
                'start_effective'         => $insertDepartment->start_effective,
                'end_effective'           => $insertDepartment->end_effective,
                'action'                  => 'CREATE',
                'created_by'              => Auth::user()->id,
                'created_at'              => Carbon::now()->timezone('Asia/Jakarta'),
            ]);
            $insertDepartmentHist->save();

            DB::commit();
            return response()->json([
                'success' => true,
                "message" => '<div class="alert alert-success">Data berhasil disimpan</div>'
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Telah terjadi kesalahan sistem, data gagal diproses</div>'
            ]);
        }
    }

    public function getData($request, $isExcel = '')
    {

        if ($isExcel == "") {
            session([
                'department' . '.department_name' => $request->has('department_name') ?  $request->input('department_name') : '',
                'department' . '.status' => $request->has('status') ?  $request->input('status') : '',
            ]);
        }

        $department_name  = session('department' . '.department_name') != '' ? session('department' . '.department_name') : '';
        $status           = session('department' . '.status') != '' ? session('department' . '.status') : '';

        $department_name  = strtoupper($department_name);
        $status           = strtoupper($status);

        $departmentDatas = Department::where('active', $status);

        if ($department_name != '') {
            $departmentDatas = $departmentDatas->where('department', $department_name);
        }

        return $departmentDatas;
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
            $txt .= "<a href=\"#\" onclick=\"deleteItem($item[id]);\" title=\"" . ucfirst(__('delete')) . "\" class=\"btn btn-xs btn-secondary\"><i class=\"fa fa-trash fa-fw fa-xs\"></i></a>";

            return $txt;
        })
            ->addColumn('active', function ($item) {
                if ($item->active == 1) {
                    return 'AKTIF';
                } else {
                    return 'TIDAK AKTIF';
                }
            })
            ->editColumn('start_effective', function ($item) {
                return Carbon::createFromFormat("Y-m-d H:i:s", $item->start_effective)->format('d/m/Y');
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

    public function exportExcel()
    {
        $datas = $this->getData(null, 'excel');
        return Excel::download(new DepartmentExport($datas), 'DepartmentMaster.xlsx');
    }

    public function importExcel()
    {
        return view('masters.department.upload');
    }

    public function makeTempTable()
    {
        Schema::create('temp', function (Blueprint $table) {
            $table->increments('id');
            $table->string('department', 50)->nullable();
            $table->string('code', 5)->nullable();
            $table->string('description', 255)->nullable();
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

                $department              = trim(strtoupper($row['Nama Bidang']), ' ');
                $department_code         = trim(strtoupper($row['Kode Bidang']), ' ');
                $department_description  = trim(strtoupper($row['Deskripsi']), ' ');

                if($department == '' && $department_code == '' && $department_description == ''){
                    continue;
                }

                $checkDuplicateData = Department::where('department', $department)
                                                ->where('active', 1)
                                                ->first();

                if ($checkDuplicateData) {
                    $error++;
                }

                if ($department_code == '') {
                    $error++;
                } else {
                    $checkDuplicateCode = Department::where('department_code', $department_code)
                                                    ->where('active', 1)
                                                    ->first();

                    if ($checkDuplicateCode) {
                        $error++;
                    }
                }

                if ($department_description == '') {
                    $error++;
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

                $department              = trim(strtoupper($row['Nama Bidang']), ' ');
                $department_code         = trim(strtoupper($row['Kode Bidang']), ' ');
                $department_description  = trim(strtoupper($row['Deskripsi']), ' ');

                if($department == '' && $department_code == '' && $department_description == ''){
                    continue;
                }

                $checkDuplicateData = Department::where('department', $department)
                                                ->where('active', 1)
                                                ->first();

                if ($checkDuplicateData) {
                    $remark [] = 'Terdapat Departemen '.$department.' yang masih aktif';
                }

                if ($department_code == '') {
                    $remark [] = 'Kode Departemen tidak boleh kosong';
                } else {
                    $checkDuplicateCode = Department::where('department_code', $department_code)
                                                    ->where('active', 1)
                                                    ->first();

                    if ($checkDuplicateCode) {
                        $remark [] = 'Terdapat Kode Departemen '.$department_code.' yang masih aktif';
                    }
                }

                if ($department_description == '') {
                    $remark [] = 'Deskripsi tidak boleh kosong';
                }

                if (count($remark) > 0) {
                    $countError++;
                }

                $tempOutput = [
                    'department'     => $department,
                    'code'           => $department_code,
                    'description'    => $department_description,
                    'remark'         => implode(', ', $remark)
                ];
                DB::table('temp')->insert($tempOutput);
                $tempData = DB::table('temp')->get();
            }
            if(count($tempData) == 0){
                $tempOutput = [
                    'department'     => '',
                    'code'           => '',
                    'description'    => '',
                    'remark'         => ''
                ];
                DB::table('temp')->insert($tempOutput);
                $tempData = DB::table('temp')->get();
            }
        } else {
            $tempOutput = [
                'department'     => '',
                'code'           => '',
                'description'    => '',
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

                    $department              = trim(strtoupper($row['Nama Bidang']), ' ');
                    $department_code         = trim(strtoupper($row['Kode Bidang']), ' ');
                    $department_description  = trim(strtoupper($row['Deskripsi']), ' ');

                    if($department == '' && $department_code == '' && $department_description == ''){
                        continue;
                    }

                    $checkDuplicateData = Department::where('department', $department)
                                                ->where('active', 1)
                                                ->first();

                    if ($checkDuplicateData) {
                        $error = true;
                    }

                    if ($department_code == '') {
                        $error = true;
                    } else {
                        $checkDuplicateCode = Department::where('department_code', $department_code)
                                                        ->where('active', 1)
                                                        ->first();

                        if ($checkDuplicateCode) {
                            $error = true;
                        }
                    }

                    if ($department_description == '') {
                        $error = true;
                    }

                    if (!$error){
                        $insertDepartment = new Department([
                            'department'              => $department,
                            'department_code'         => $department_code,
                            'department_description'  => $department_description,
                            'active'                  => 1,
                            'start_effective'         => Carbon::now()->timezone('Asia/Jakarta'),
                            'end_effective'           => null,
                            'created_by'              => Auth::user()->id,
                            'created_at'              => Carbon::now()->timezone('Asia/Jakarta'),
                            'updated_by'              => Auth::user()->id,
                            'updated_at'              => Carbon::now()->timezone('Asia/Jakarta'),
                        ]);
                        $insertDepartment->save();
            
                        $insertDepartmentHist = new DepartmentHist([
                            'department_id'           => $insertDepartment->id,
                            'department'              => $insertDepartment->department,
                            'department_code'         => $insertDepartment->department_code,
                            'department_description'  => $insertDepartment->department_description,
                            'active'                  => $insertDepartment->active,
                            'start_effective'         => $insertDepartment->start_effective,
                            'end_effective'           => $insertDepartment->end_effective,
                            'action'                  => 'CREATE',
                            'created_by'              => Auth::user()->id,
                            'created_at'              => Carbon::now()->timezone('Asia/Jakarta'),
                        ]);
                        $insertDepartmentHist->save();
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
        $filename = 'Template_Master_Bidang.xlsx';
        return response()->download(storage_path('app/files/' . $filename));
    }

    public function deleteData(Request $request)
    {
        $department = Department::where('id', $request->id)->first();

        try {
            DB::beginTransaction();
            if ($department) {
                $department->active         = 0;
                $department->end_effective  = Carbon::now()->timezone('Asia/Jakarta');
                $department->updated_by     = Auth::user()->id;
                $department->updated_at     = Carbon::now()->timezone('Asia/Jakarta');
                $department->save();

                $insertDepartmentHist = new DepartmentHist([
                    'department_id'           => $department->id,
                    'department'              => $department->department,
                    'department_code'         => $department->department_code,
                    'department_description'  => $department->department_description,
                    'active'                  => $department->active,
                    'start_effective'         => $department->start_effective,
                    'end_effective'           => $department->end_effective,
                    'action'                  => 'UPDATE',
                    'created_by'              => Auth::user()->id,
                    'created_at'              => Carbon::now()->timezone('Asia/Jakarta'),
                ]);
                $insertDepartmentHist->save();

                DB::commit();
                return response()->json([
                    'success' => true,
                    "message" => '<div class="alert alert-success">Data departemen berhasil dihapus, status : TIDAK AKTIF</div>'
                ]);
            } else {
                return response()->json([
                    'errors' => true,
                    "message" => '<div class="alert alert-danger">Data gagal di proses, data departemen tidak ditemukan</div>'
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
        $department = Department::where('id', $id)->first();

        if ($department) {
            if ($department->active == 1) {
                $active = 'AkTIF';
            } else {
                $active = 'TIDAK AkTIF';
            }
            return view('masters.department.form_detail', [
                'department'             => $department->department,
                'department_code'        => $department->department_code,
                'department_description' => $department->department_description != '' ? $department->department_description : '-',
                'active'                 => $active,
                'start_effective'        => $department->start_effective != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $department->start_effective)->format('d/m/Y H:i:s') : '-',
                'end_effective'          => $department->end_effective != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $department->end_effective)->format('d/m/Y H:i:s') : '-',
                'created_by'             => optional($department->createdBy)->name,
                'created_at'             => $department->created_at != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $department->created_at)->format('d/m/Y H:i:s') : '-',
                'updated_by'             => optional($department->updatedBy)->name,
                'updated_at'             => $department->updated_at != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $department->updated_at)->format('d/m/Y H:i:s') : '-',
            ]);
        } else {
            return view('masters.department.form_detail', [
                'department'             => '',
                'department_code'        => '',
                'department_description' => '',
                'active'                 => '',
                'start_effective'        => '',
                'end_effective'          => '',
                'created_by'             => '',
                'created_at'             => '',
                'updated_by'             => '',
                'updated_at'             => '',
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use Carbon\Carbon;
use App\Models\Job;
use App\Models\JobHist;
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

class JobController extends Controller
{
    public function index()
    {
        $departments = Department::where('active', 1)->get();

        return view('masters.job.job_index', [
            'departments' => $departments
        ]);
    }

    public function getData($request, $isExcel = '')
    {

        if ($isExcel == "") {
            session([
                'job' . '.wo_category' => $request->has('wo_category') ?  $request->input('wo_category') : '',
                'job' . '.department_code' => $request->has('department_code') ?  $request->input('department_code') : '',
                'job' . '.status' => $request->has('status') ?  $request->input('status') : '',
            ]);
        }

        $wo_category      = session('job' . '.wo_category') != '' ? session('job' . '.wo_category') : '';
        $department_code  = session('job' . '.department_code') != '' ? session('job' . '.department_code') : '';
        $status           = session('job' . '.status') != '' ? session('job' . '.status') : '';

        $wo_category      = strtoupper($wo_category);
        $department_code  = strtoupper($department_code);
        $status           = strtoupper($status);

        $jobDatas = Job::where('active', $status);

        if ($wo_category != '') {
            $jobDatas = $jobDatas->where('wo_category', $wo_category);
        }

        if ($department_code != '') {
            $jobDatas = $jobDatas->where('department_id', $department_code);
        }

        return $jobDatas;
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
            ->addColumn('department', function ($item) {
                return optional($item->department)->department;
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

    public function createNew()
    {
        $departments = Department::where('active', 1)->get();

        return view('masters.job.form_input', [
            'departments' => $departments
        ]);
    }

    public function submitData(Request $request)
    {
        $wo_category  = strtoupper($request->wo_category);
        $job_category = strtoupper($request->job_category);
        $description  = strtoupper($request->description);
        $department   = strtoupper($request->department);

        if ($wo_category == '') {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Kategori WO wajib terisi, harap periksa kembali formulir pengisian data</div>'
            ]);
        }

        if ($job_category == '') {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Kategori pekerjaan wajib terisi, harap periksa kembali formulir pengisian data</div>'
            ]);
        }

        if ($department == '') {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Departemen wajib terisi, harap periksa kembali formulir pengisian data</div>'
            ]);
        }

        $checkDuplicateData = Job::where('department_id', $department)
            ->where('wo_category', $wo_category)
            ->where('job_category', $job_category)
            ->where('active', 1)
            ->first();

        if ($checkDuplicateData) {
            $departmentAvail = Department::where('id', $department)->first();
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Telah ditemukan data pekerjaan milik departement ' . $departmentAvail->department . ' , dengan kategori WO ' . $wo_category . ' , dan kategori pekerjaan ' . $job_category . ' yang masih aktif</div>'
            ]);
        }

        try {
            // CREATE DATA 
            DB::beginTransaction();

            $insertjiob = new Job([
                'department_id'       => $department,
                'wo_category'         => $wo_category,
                'job_category'        => $job_category,
                'job'                 => '',
                'job_description'     => $description,
                'active'              => 1,
                'start_effective'     => Carbon::now()->timezone('Asia/Jakarta'),
                'end_effective'       => null,
                'created_by'          => Auth::user()->id,
                'created_at'          => Carbon::now()->timezone('Asia/Jakarta'),
                'updated_by'          => Auth::user()->id,
                'updated_at'          => Carbon::now()->timezone('Asia/Jakarta'),
            ]);
            $insertjiob->save();

            $insertJobHist = new JobHist([
                'job_category_id'     => $insertjiob->id,
                'department_id'       => $insertjiob->department_id,
                'wo_category'         => $insertjiob->wo_category,
                'job_category'        => $insertjiob->job_category,
                'job_description'     => $insertjiob->job_description,
                'active'              => $insertjiob->active,
                'start_effective'     => $insertjiob->start_effective,
                'end_effective'       => $insertjiob->end_effective,
                'action'              => 'CREATE',
                'created_by'          => Auth::user()->id,
                'created_at'          => Carbon::now()->timezone('Asia/Jakarta'),
            ]);
            $insertJobHist->save();

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

    public function deleteData(Request $request)
    {
        $job = Job::where('id', $request->id)->first();

        try {
            DB::beginTransaction();
            if ($job) {
                $job->active         = 0;
                $job->end_effective  = Carbon::now()->timezone('Asia/Jakarta');
                $job->updated_by     = Auth::user()->id;
                $job->updated_at     = Carbon::now()->timezone('Asia/Jakarta');
                $job->save();

                $insertJobHist = new JobHist([
                    'job_category_id'     => $job->id,
                    'department_id'       => $job->department_id,
                    'wo_category'         => $job->wo_category,
                    'job_category'        => $job->job_category,
                    'job_description'     => $job->job_description,
                    'active'              => $job->active,
                    'start_effective'     => $job->start_effective,
                    'end_effective'       => $job->end_effective,
                    'action'              => 'UPDATE',
                    'created_by'          => Auth::user()->id,
                    'created_at'          => Carbon::now()->timezone('Asia/Jakarta'),
                ]);
                $insertJobHist->save();

                DB::commit();
                return response()->json([
                    'success' => true,
                    "message" => '<div class="alert alert-success">Data pekerjaan berhasil dihapus, status : TIDAK AKTIF</div>'
                ]);
            } else {
                return response()->json([
                    'errors' => true,
                    "message" => '<div class="alert alert-danger">Data gagal di proses, data pekerjaan tidak ditemukan</div>'
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
        $job = Job::where('id', $id)->first();

        if ($job) {
            if ($job->active == 1) {
                $active = 'AkTIF';
            } else {
                $active = 'TIDAK AkTIF';
            }

            return view('masters.job.form_detail', [
                'wo_category'          => $job->wo_category,
                'job_category'         => $job->job_category,
                'job_description'      => $job->job_description != '' ? $job->job_description : '-',
                'department'           => optional($job->department)->department,
                'active'               => $active,
                'start_effective'      => $job->start_effective != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $job->start_effective)->format('d/m/Y H:i:s') : '-',
                'end_effective'        => $job->end_effective != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $job->end_effective)->format('d/m/Y H:i:s') : '-',
                'created_by'           => optional($job->createdBy)->name,
                'created_at'           => $job->created_at != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $job->created_at)->format('d/m/Y H:i:s') : '-',
                'updated_by'           => optional($job->updatedBy)->name,
                'updated_at'           => $job->updated_at != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $job->updated_at)->format('d/m/Y H:i:s') : '-',
            ]);
        } else {
            return view('masters.job.form_detail', [
                'wo_category'         => '',
                'job_category'        => '',
                'job_description'     => '',
                'department'          => '',
                'active'              => '',
                'start_effective'     => '',
                'end_effective'       => '',
                'created_by'          => '',
                'created_at'          => '',
                'updated_by'          => '',
                'updated_at'          => '',
            ]);
        }
    }

    public function importExcel()
    {
        return view('masters.job.upload');
    }

    public function makeTempTable()
    {
        Schema::create('temp', function (Blueprint $table) {
            $table->increments('id');
            $table->string('department', 50)->nullable();
            $table->string('wo_category', 50)->nullable();
            $table->string('job_category', 50)->nullable();
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

                $department      = trim(strtoupper($row['Departemen']), ' ');
                $wo_category     = trim(strtoupper($row['Kategori WO']), ' ');
                $job_category    = trim(strtoupper($row['Kategori Pekerjaan']), ' ');
                $description     = trim(strtoupper($row['Deskripsi Pekerjaan']), ' ');

                if($department == '' && $wo_category == '' && $job_category == '' && $description == '') {
                    continue;
                }

                if ($wo_category == '') {
                    $error++;
                } else {
                    $arrWO = ['PEKERJAAN', 'LAPORAN GANGGHUAN'];
                    if(!in_array($wo_category, $arrWO)){
                        $error++;
                    }
                }

                if ($job_category == '') {
                    $error++;
                }

                $checkDepartment = Department::where('department', $department)
                                                ->where('active', 1)
                                                ->first();

                if (!$checkDepartment) {
                    $error++;
                } else {
                    $checkDuplicate = Job::where('department_id', $checkDepartment->id)
                                         ->where('wo_category', $wo_category)
                                         ->where('job_category', $job_category)
                                         ->where('active', 1)
                                         ->first();
                    if($checkDuplicate){
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

                $department              = trim(strtoupper($row['Departemen']), ' ');
                $wo_category             = trim(strtoupper($row['Kategori WO']), ' ');
                $job_category            = trim(strtoupper($row['Kategori Pekerjaan']), ' ');
                $description             = trim(strtoupper($row['Deskripsi Pekerjaan']), ' ');

                if($department == '' && $wo_category == '' && $job_category == '' && $description == '') {
                    continue;
                }

                if ($wo_category == '') {
                    $remark [] = 'Kategori WO tidak boleh kosong';
                } else {
                    $arrWO = ['PEKERJAAN', 'LAPORAN GANGGHUAN'];
                    if(!in_array($wo_category, $arrWO)){
                        $remark [] = 'Kategori WO harus terisi PEKERJAAN / LAPORAN GANGGHUAN';
                    }
                }

                if ($job_category == '') {
                    $remark [] = 'Kategori Pekerjaan tidak boleh kosong';
                }

                $checkDepartment = Department::where('department', $department)
                                                ->where('active', 1)
                                                ->first();

                if (!$checkDepartment) {
                    $remark [] = 'Departemen '.$department.' tidak ditemukan';
                } else {
                    $checkDuplicate = Job::where('department_id', $checkDepartment->id)
                                         ->where('wo_category', $wo_category)
                                         ->where('job_category', $job_category)
                                         ->where('active', 1)
                                         ->first();
                    if($checkDuplicate){
                        $remark [] = 'Terdapat duplikat data untuk deparyemen '.$department.', kategori WO '.$wo_category.', kategori pekerjaan '.$job_category;
                    }
                }

                if (count($remark) > 0) {
                    $countError++;
                }

                $tempOutput = [
                    'department'     => $department,
                    'wo_category'    => $wo_category,
                    'job_category'   => $job_category,
                    'description'    => $description,
                    'remark'         => implode(', ', $remark)
                ];
                DB::table('temp')->insert($tempOutput);
                $tempData = DB::table('temp')->get();
            }
            if(count($tempData) == 0){
                $tempOutput = [
                    'department'     => '',
                    'wo_category'    => '',
                    'job_category'   => '',
                    'description'    => '',
                    'remark'         => ''
                ];
                DB::table('temp')->insert($tempOutput);
                $tempData = DB::table('temp')->get();
            }
        } else {
            $tempOutput = [
                'department'     => '',
                'wo_category'    => '',
                'job_category'   => '',
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

                    $department              = trim(strtoupper($row['Departemen']), ' ');
                    $wo_category             = trim(strtoupper($row['Kategori WO']), ' ');
                    $job_category            = trim(strtoupper($row['Kategori Pekerjaan']), ' ');
                    $description             = trim(strtoupper($row['Deskripsi Pekerjaan']), ' ');

                    if($department == '' && $wo_category == '' && $job_category == '' && $description == '') {
                        continue;
                    }

                    if ($wo_category == '') {
                        $error = true;
                    } else {
                        $arrWO = ['PEKERJAAN', 'LAPORAN GANGGHUAN'];
                        if(!in_array($wo_category, $arrWO)){
                            $error = true;
                        }
                    }
    
                    if ($job_category == '') {
                        $error = true;
                    }
    
                    $checkDepartment = Department::where('department', $department)
                                                    ->where('active', 1)
                                                    ->first();
    
                    if (!$checkDepartment) {
                        $error = true;
                    } else {
                        $checkDuplicate = Job::where('department_id', $checkDepartment->id)
                                             ->where('wo_category', $wo_category)
                                             ->where('job_category', $job_category)
                                             ->where('active', 1)
                                             ->first();
                        if($checkDuplicate){
                            $error = true;
                        }
                    }

                    if (!$error){
                        $insertjiob = new Job([
                            'department_id'       => $checkDepartment->id,
                            'wo_category'         => $wo_category,
                            'job_category'        => $job_category,
                            'job'                 => '',
                            'job_description'     => $description,
                            'active'              => 1,
                            'start_effective'     => Carbon::now()->timezone('Asia/Jakarta'),
                            'end_effective'       => null,
                            'created_by'          => Auth::user()->id,
                            'created_at'          => Carbon::now()->timezone('Asia/Jakarta'),
                            'updated_by'          => Auth::user()->id,
                            'updated_at'          => Carbon::now()->timezone('Asia/Jakarta'),
                        ]);
                        $insertjiob->save();
            
                        $insertJobHist = new JobHist([
                            'job_category_id'     => $insertjiob->id,
                            'department_id'       => $insertjiob->department_id,
                            'wo_category'         => $insertjiob->wo_category,
                            'job_category'        => $insertjiob->job_category,
                            'job_description'     => $insertjiob->job_description,
                            'active'              => $insertjiob->active,
                            'start_effective'     => $insertjiob->start_effective,
                            'end_effective'       => $insertjiob->end_effective,
                            'action'              => 'CREATE',
                            'created_by'          => Auth::user()->id,
                            'created_at'          => Carbon::now()->timezone('Asia/Jakarta'),
                        ]);
                        $insertJobHist->save();
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
        $filename = 'Template_Master_Pekerjaan.xlsx';
        return response()->download(storage_path('app/files/' . $filename));
    }
}

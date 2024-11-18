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
}

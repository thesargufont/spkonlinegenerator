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
                'start_effective' => Carbon::now(),
                'end_effective'   => null,
                'created_by'      => Auth::user()->id,
                'created_at'      => Carbon::now(),
                'updated_by'      => Auth::user()->id,
                'updated_at'      => Carbon::now(),
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
                $user->active      = 0;
                $user->updated_by  = Auth::user()->id;
                $user->updated_at  = Carbon::now();
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
}

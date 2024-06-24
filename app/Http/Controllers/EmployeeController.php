<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Exception;

class EmployeeController extends Controller
{
    public function index()
    {
        return view('masters.employee.employee_index');
    }

    public function getData($request, $isExcel = '')
    {
        $employee_name = strtoupper($request->employee_name);
        $employee_nik = strtoupper($request->nik);

        $employeeDatas = User::where('remember_token', null);
        if ($employee_name != '') {
            $employeeDatas = $employeeDatas->where('name', $employee_name);
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
            $txt .= "<a href=\"#\" onclick=\"editItem($item[id]);\" title=\"" . ucfirst(__('edit')) . "\" class=\"btn btn-xs btn-secondary\"><i class=\"fa fa-edit fa-fw fa-xs\"></i></a>";
            $txt .= "<a href=\"#\" onclick=\"deleteItem($item[id]);\" title=\"" . ucfirst(__('delete')) . "\" class=\"btn btn-xs btn-secondary\"><i class=\"fa fa-trash fa-fw fa-xs\"></i></a>";

            return $txt;
        })
            ->addColumn('department', function ($item) {
                $department = Department::where('id', $item->department_id)->first()->department;
                return $department;
            })
            ->editColumn('start_effective', function ($item) {
                return Carbon::createFromFormat("Y-m-d H:i:s", $item->updated_at)->format('d/m/Y');
            });
        return $datatables->make(TRUE);
    }

    public function createNew()
    {
        return view('masters.employee.form_input');
    }

    public function submitData(Request $request)
    {
        $name  = strtoupper($request->name);
        $nik    = $request->nik;
        $email    = $request->email;
        $password    = $request->password;
        $confirm_password    = $request->confirm_password;
        $department    = intval($request->department);
        $gender    = strtoupper($request->gender);

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

        if ($password == '') {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Password wajib terisi, harap periksa kembali formulir pengisian data</div>'
            ]);
        }

        if ($password != $confirm_password) {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Konfirmasi password tidak sesuai, silahkan dipastikan kembali password sudah sesuai.</div>'
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
                'name' => $name,
                'department_id' => $department,
                'email' => $email,
                'password' => bcrypt($password),
                'nik' => $nik,
                'gender' => $gender,
                'active' => 1,
                'start_effective'         => Carbon::now(),
                'end_effective'           => null,
                'created_by'              => Auth::user()->id,
                'created_at'              => Carbon::now(),
                'updated_by'              => Auth::user()->id,
                'updated_at'              => Carbon::now(),
            ]);


            $insertUser->save();

            DB::commit();
            return response()->json([
                'success' => true,
                "message" => '<div class="alert alert-success">User berhasil disimpan</div>'
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">' . $e . '</div>'
            ]);
        }
    }
}

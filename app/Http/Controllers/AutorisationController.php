<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use Carbon\Carbon;
use App\Models\Department;
use App\Models\Device;
use App\Models\DeviceHist;
use App\Models\GeneralCode;
use App\Models\Role;
use App\Models\Job;
use App\Models\JobHist;
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
use Maatwebsite\Excel\Row;

class AutorisationController extends Controller
{
    public function index()
    {
        $user_id = Auth::user()->id;
        $roles = Role::where('user_id', $user_id)->where('active', 1)->distinct()->pluck('role')->toArray();
        $access_right = array('SUPERADMIN');
        if (count(array_intersect($roles, $access_right)) == 0) {
            return redirect()->route('home');
        }

        $roles = GeneralCode::where('section','SPONGE')->where('label','ROLE')->where('end_effective',null)->get();
        $authorities = GeneralCode::where('section','SPONGE')->where('label','AUTHORITY')->where('end_effective',null)->get();

        return view('masters.autorisation.autorisation_index',[
            'roles' => $roles,
            'authorities' => $authorities,
        ]);
    }

    public function getData($request, $isExcel = '')
    {
        //dd($request->role);
        if ($isExcel == "") {
            session([
                'autoritation' . '.nik' => $request->has('nik') ?  $request->input('nik') : '',
                'autoritation' . '.user_name' => $request->has('user_name') ?  $request->input('user_name') : '',
                'autoritation' . '.role' => $request->has('role') ?  $request->input('role') : '',
                'autoritation' . '.authority' => $request->has('authority') ?  $request->input('authority') : '',
                'autoritation' . '.effective_date' => $request->has('effective_date') ?  $request->input('effective_date') : '',
                'autoritation' . '.end_effective' => $request->has('end_effective') ?  $request->input('end_effective') : '',
                'autoritation' . '.active' => $request->has('active') ?  $request->input('active') : '',
            ]);
        }

        $nik              = session('autoritation' . '.nik') != '' ? session('autoritation' . '.nik') : '';
        $user_name            = session('autoritation' . '.user_name') != '' ? session('autoritation' . '.user_name') : '';
        $role         = session('autoritation' . '.role') != '' ? session('autoritation' . '.role') : '';
        $authority       = session('autoritation' . '.authority') != '' ? session('autoritation' . '.authority') : '';
        $effective_date = session('autoritation'.'.effective_date')!=''?Carbon::createFromFormat('d/m/Y',session('autoritation'.'.effective_date'))->format('Y-m-d'):'';
        $end_effective = session('autoritation'.'.end_effective')!=''?Carbon::createFromFormat('d/m/Y',session('autoritation'.'.end_effective'))->format('Y-m-d'):'';
        //$effective_date  = session('autoritation' . '.effective_date') != '' ? session('autoritation' . '.effective_date') : '';
        //$end_effective    = session('autoritation' . '.end_effective') != '' ? session('autoritation' . '.end_effective') : '';
        $active           = session('autoritation' . '.active') != '' ? session('autoritation' . '.active') : '';

        $nik     = strtoupper($nik);
        $user_name           = strtoupper($user_name);
        $role        = strtoupper($role);
        $authority      = strtoupper($authority);
        $active          = strtoupper($active);

        // $getdataOther = Role::select('roles.*')
        // // ->distinct('sponge_headers.wo_number')
        // ->leftJoin('users', 'users.id', '=', 'roles.user_id')
        // ->where('users.nik', 'LIKE',  "%{$nik}%")
        // ->where('users.name', 'LIKE',  "%{$user_name}%")
        // ->where('roles.role','LIKE',"%{$role}%")
        // ->where('roles.authority','LIKE',"%{$authority}%")
        // ->where('roles.active',$active)
        // ->where('roles.role','!=','SUPERADMIN')
        // ->orderBy('roles.role','desc')
        // ->orderBy('users.name')
        // ;

        $getdata = Role::select('roles.*')
        // ->distinct('sponge_headers.wo_number')
        ->leftJoin('users', 'users.id', '=', 'roles.user_id')
        ->where('users.nik', 'LIKE',  "%{$nik}%")
        ->where('users.name', 'LIKE',  "%{$user_name}%")
        ->where('roles.role','LIKE',"%{$role}%")
        ->where('roles.authority','LIKE',"%{$authority}%")
        // ->where('roles.start_effective',$effective_date)
        // ->where('roles.end_effective',$end_effective)
        // ->where('roles.role','SUPERADMIN')
        ->orderBy('users.name')
        ;

        //dd($effective_date,$end_effective);
        if($effective_date != ''){
            $getdata->whereDate('roles.start_effective',$effective_date);
        }
        if($end_effective != ''){
            $getdata->whereDate('roles.end_effective',$end_effective);
        }
        if($active != ''){
            $getdata->where('roles.active',$active);
        }

        // $getdata = Role::union($getdataSU)
        // ->union($getdataOther)
        // ;

        return $getdata;
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
            ->editColumn('active', function ($item) {
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
            ->addColumn('nik', function ($item) {
                $user = User::find($item->user_id);
                if($user){
                    return $user->nik;
                }else{
                    return 'nik not found';
                }
            })
            ->addColumn('user', function ($item) {
                $user = User::find($item->user_id);
                if($user){
                    return $user->name;
                }else{
                    return 'user not found';
                }
            });

        return $datatables->make(TRUE);
    }

    public function createNew()
    {
        $users = User::where('active',1)->get();
        $roles = GeneralCode::where('section','SPONGE')->where('label','ROLE')->where('end_effective',null)->get();
        $authorities = GeneralCode::where('section','SPONGE')->where('label','AUTHORITY')->where('end_effective',null)->get();
        $today = Carbon::now();
        $effective_date = Carbon::createFromFormat("Y-m-d H:i:s", $today)->format('d/m/Y');

        return view('masters.autorisation.form_input', [
            'users' => $users,
            'roles' => $roles,
            'authorities' => $authorities,
            'effective_date' => $effective_date,
        ]);
    }

    public function submitData(Request $request)
    {
        $user  = $request->user;
        $role = $request->role;
        $authority  = $request->authority;

        //dd($user,$role,$authority);

        if ($user == '' || $user == null || $user == 'null') {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Pengguna wajib terisi, harap periksa kembali formulir pengisian data</div>'
            ]);
        }

        if ($role == '' || $role == 'null' || $role == null) {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Role wajib terisi, harap periksa kembali formulir pengisian data</div>'
            ]);
        }

        if ($authority == '' || $authority == 'null' || $authority == null) {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Otoritas wajib terisi, harap periksa kembali formulir pengisian data</div>'
            ]);
        }

        $checkDuplicateData = Role::where('user_id', $user)
            ->where('role', $role)
            ->where('authority', $authority)
            ->where('active', 1)
            ->first();

        if ($checkDuplicateData) {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Role serupa sudah pernah dibuat dan masih aktif</div>'
            ]);
        }

        try {
            // CREATE DATA 
            DB::beginTransaction();

            $insertrole = new Role([
                'user_id'       => $user,
                'role'         => $role,
                'authority'        => $authority,
                'active'              => 1,
                'start_effective'     => Carbon::now(),
                'end_effective'       => null,
                'created_by'          => Auth::user()->id,
                'created_at'          => Carbon::now(),
                'updated_by'          => Auth::user()->id,
                'updated_at'          => Carbon::now(),
            ]);
            $insertrole->save();

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
        $role = Role::where('id', $request->id)->first();

        try {
            DB::beginTransaction();
            if ($role) {
                $role->active         = 0;
                $role->end_effective  = Carbon::now();
                $role->updated_by     = Auth::user()->id;
                $role->updated_at     = Carbon::now();
                $role->save();

                DB::commit();
                return response()->json([
                    'success' => true,
                    "message" => '<div class="alert alert-success">Role berhasil dihapus, status : TIDAK AKTIF</div>'
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
                "message" => '<div class="alert alert-danger">Data gagal di proses, terjadi kesalahan sistem</div>'
            ]);
        }
    }

    public function detailData($id)
    {
        $role = Role::where('id', $id)->first();

        if ($role) {
            if ($role->active == 1) {
                $active = 'AkTIF';
            } else {
                $active = 'TIDAK AkTIF';
            }

            $user = User::where('id',$role->user_id)->first();
            if($user){
                $name = $user->name;
                $nik = $user->nik;
            }else{
                $name = '';
                $nik = '';
            }

            return view('masters.autorisation.form_detail', [
                'name'          => $name,
                'nik'          => $nik,
                'role'         => $role->role,
                'authority'      => $role->authority,
                'active'               => $active,
                'start_effective'      => $role->start_effective != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $role->start_effective)->format('d/m/Y') : '-',
                'end_effective'        => $role->end_effective != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $role->end_effective)->format('d/m/Y') : '-',
            ]);
        } else {
            return view('masters.autorisation.form_detail', [
                'name'         => '',
                'nik'         => '',
                'role'        => '',
                'authority'     => '',
                'active'              => '',
                'start_effective'     => '',
                'end_effective'       => '',
            ]);
        }
    }

    public function editData($id)
    {
        $role = Role::where('id', $id)->first();
        if ($role) {
            if ($role->active == 1) {
                $active = 'AkTIF';
            } else {
                $active = 'TIDAK AkTIF';
            }
            return view('masters.device.form_edit', [
                'id'               => $id,
                'device'               => $device->device_name,
                'device_description'   => $device->device_description != '' ? $device->device_description : '-',
                'brand'                => $device->brand,
                'location'             => optional($device->location)->location,
                'department'           => optional($device->department)->department,
                'device_category'      => optional($device->deviceCategory)->device_category,
                'serial_number'        => $device->serial_number,
                'activa_number'        => $device->activa_number,
                'active'               => $active,
                'start_effective'      => $device->start_effective != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $device->start_effective)->format('d/m/Y H:i:s') : '-',
                'end_effective'        => $device->end_effective != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $device->end_effective)->format('d/m/Y H:i:s') : '-',
                'created_by'           => optional($device->createdBy)->name,
                'created_at'           => $device->created_at != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $device->created_at)->format('d/m/Y H:i:s') : '-',
                'updated_by'           => optional($device->updatedBy)->name,
                'updated_at'           => $device->updated_at != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $device->updated_at)->format('d/m/Y H:i:s') : '-',
            ]);
        } else {
            return view('masters.device.form_edit', [
                'id'               => $id,
                'device'               => '',
                'device_description'   => '',
                'brand'                => '',
                'location'             => '',
                'department'           => '',
                'device_category'      => '',
                'serial_number'        => '',
                'activa_number'        => '',
                'active'               => '',
                'start_effective'      => '',
                'end_effective'        => '',
                'created_by'           => '',
                'created_at'           => '',
                'updated_by'           => '',
                'updated_at'           => '',
            ]);
        }
    }

    public function updateData(Request $request)
    {
        $device_name     = strtoupper($request->device_name);
        $description     = strtoupper($request->description);
        $brand           = strtoupper($request->brand);
        $location        = strtoupper($request->location);
        $department      = strtoupper($request->department);
        $device_category = strtoupper($request->device_category);
        $serial_number   = strtoupper($request->serial_number);
        $activa_number   = strtoupper($request->activa_number);
        $id   = $request->id;
        // dd($request);

        if ($device_name == '') {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Nama peralatan wajib terisi, harap periksa kembali formulir pengisian data</div>'
            ]);
        }

        if ($brand == '') {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Brand wajib terisi, harap periksa kembali formulir pengisian data</div>'
            ]);
        }

        if ($location == '') {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Lokasi wajib terisi, harap periksa kembali formulir pengisian data</div>'
            ]);
        }

        if ($department == '') {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Departemen wajib terisi, harap periksa kembali formulir pengisian data</div>'
            ]);
        }

        if ($device_category == '') {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Kategori peralatan wajib terisi, harap periksa kembali formulir pengisian data</div>'
            ]);
        }

        if ($serial_number == '') {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Nomor seri peralatan wajib terisi, harap periksa kembali formulir pengisian data</div>'
            ]);
        }

        if ($activa_number == '') {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">EQ ID peralatan wajib terisi, harap periksa kembali formulir pengisian data</div>'
            ]);
        }

        $checkDuplicateData = Device::where('device_name', $device_name)
            ->where('brand', $brand)
            ->where('location_id', $location)
            ->where('department_id', $department)
            ->where('device_category_id', $device_category)
            ->where('serial_number', $serial_number)
            ->where('activa_number', $activa_number)
            ->where('active', 1)
            ->first();

        if ($checkDuplicateData) {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Telah ditemukan data peralatan ' . $device_name . ' yang masih aktif</div>'
            ]);
        }

        $insertDevice = Device::find($id);
        // dd($insertDevice, $id);
        if(!$insertDevice){
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">ID tidak ditemukan.</div>'
            ]);
        }

        try {
            // CREATE DATA 
            DB::beginTransaction();

            $insertDevice->device_name =  $device_name;
            $insertDevice->device_description = $description;
            $insertDevice->brand = $brand;
            $insertDevice->serial_number = $serial_number; 
            $insertDevice->activa_number = $activa_number;
            $insertDevice->save();

            $insertDeviceHist = new DeviceHist([
                'device_id'            => $insertDevice->id,
                'device_name'          => $insertDevice->device_name,
                'device_description'   => $insertDevice->device_description,
                'brand'                => $insertDevice->brand,
                'location_id'          => $insertDevice->location_id,
                'department_id'        => $insertDevice->department_id,
                'device_category_id'   => $insertDevice->device_category_id,
                'serial_number'        => $insertDevice->serial_number,
                'activa_number'        => $insertDevice->activa_number,
                'active'               => $insertDevice->active,
                'start_effective'      => $insertDevice->start_effective,
                'end_effective'        => $insertDevice->end_effective,
                'action'               => 'UPDATE',
                'created_by'           => Auth::user()->id,
                'created_at'           => Carbon::now(),
            ]);
            $insertDeviceHist->save();

            DB::commit();
            return response()->json([
                'success' => true,
                "message" => '<div class="alert alert-success">Data peralatan berhasil disimpan</div>'
            ]);
        } catch (Exception $e) {
            // dd($e);
            DB::rollback();
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Telah terjadi kesalahan sistem, data gagal diproses</div>'
                // "message" => '<div class="alert alert-danger">'.$e.'</div>'
            ]);
        }
    }
}
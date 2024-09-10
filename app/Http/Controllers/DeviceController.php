<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use Carbon\Carbon;
use App\Models\Device;
use App\Models\Location;
use App\Models\Department;
use App\Models\DeviceHist;
use Illuminate\Http\Request;
use App\Models\DepartmentHist;
use App\Models\DeviceCategory;
use App\Exports\DepartmentExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Schema;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Schema\Blueprint;

class DeviceController extends Controller
{
    public function index()
    {
        $locations        = Location::where('active', 1)->get();
        $departments      = Department::where('active', 1)->get();
        $deviceCategories = DeviceCategory::where('active', 1)->get()->unique('device_category');

        return view('masters.device.device_index', [
            'locations' => $locations,
            'departments' => $departments,
            'deviceCategories' => $deviceCategories,
        ]);
    }

    public function getData($request, $isExcel = '')
    {

        if ($isExcel == "") {
            session([
                'device' . '.device_name' => $request->has('device_name') ?  $request->input('device_name') : '',
                'device' . '.brand' => $request->has('brand') ?  $request->input('brand') : '',
                'device' . '.location' => $request->has('location') ?  $request->input('location') : '',
                'device' . '.department' => $request->has('department') ?  $request->input('department') : '',
                'device' . '.device_category' => $request->has('device_category') ?  $request->input('device_category') : '',
                'device' . '.serial_number' => $request->has('serial_number') ?  $request->input('serial_number') : '',
                'device' . '.activa_number' => $request->has('activa_number') ?  $request->input('activa_number') : '',
                'device' . '.status' => $request->has('status') ?  $request->input('status') : '',
            ]);
        }

        $device_name      = session('device' . '.device_name') != '' ? session('device' . '.device_name') : '';
        $brand            = session('device' . '.brand') != '' ? session('device' . '.brand') : '';
        $location         = session('device' . '.location') != '' ? session('device' . '.location') : '';
        $department       = session('device' . '.department') != '' ? session('device' . '.department') : '';
        $device_category  = session('device' . '.device_category') != '' ? session('device' . '.device_category') : '';
        $serial_number    = session('device' . '.serial_number') != '' ? session('device' . '.serial_number') : '';
        $activa_number    = session('device' . '.activa_number') != '' ? session('device' . '.activa_number') : '';
        $status           = session('device' . '.status') != '' ? session('device' . '.status') : '';

        $device_name     = strtoupper($device_name);
        $brand           = strtoupper($brand);
        $location        = strtoupper($location);
        $department      = strtoupper($department);
        $device_category = strtoupper($device_category);
        $serial_number   = strtoupper($serial_number);
        $activa_number   = strtoupper($activa_number);
        $status          = strtoupper($status);

        $deviceDatas = Device::where('active', $status);

        if ($device_name != '') {
            $deviceDatas = $deviceDatas->where('device_name', 'LIKE',  "%{$device_name}%");
        }

        if ($brand != '') {
            $deviceDatas = $deviceDatas->where('brand', 'LIKE',  "%{$brand}%");
        }

        if ($location != '') {
            $deviceDatas = $deviceDatas->where('location_id', $location);
        }

        if ($department != '') {
            $deviceDatas = $deviceDatas->where('department_id', $department);
        }

        if ($device_category != '') {
            $deviceDatas = $deviceDatas->where('device_category_id', $device_category);
        }

        if ($serial_number != '') {
            $deviceDatas = $deviceDatas->where('serial_number', 'LIKE',  "%{$serial_number}%");
        }

        if ($activa_number != '') {
            $deviceDatas = $deviceDatas->where('activa_number', 'LIKE',  "%{$activa_number}%");
        }

        return $deviceDatas;
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
            ->addColumn('active', function ($item) {
                if ($item->active == 1) {
                    return 'AKTIF';
                } else {
                    return 'TIDAK AKTIF';
                }
            })
            ->addColumn('location', function ($item) {
                return optional($item->location)->location;
            })
            ->addColumn('department', function ($item) {
                return optional($item->department)->department;
            })
            ->addColumn('device_category', function ($item) {
                return optional($item->deviceCategory)->device_category;
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
        $locations        = Location::where('active', 1)->get();
        $departments      = Department::where('active', 1)->get();
        $deviceCategories = DeviceCategory::where('active', 1)->get()->unique('device_category');

        return view('masters.device.form_input', [
            'locations' => $locations,
            'departments' => $departments,
            'deviceCategories' => $deviceCategories
        ]);
    }

    public function submitData(Request $request)
    {
        $device_name     = strtoupper($request->device_name);
        $description     = strtoupper($request->description);
        $brand           = strtoupper($request->brand);
        $location        = strtoupper($request->location);
        $department      = strtoupper($request->department);
        $device_category = strtoupper($request->device_category);
        $serial_number   = strtoupper($request->serial_number);
        $activa_number   = strtoupper($request->activa_number);

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

        try {
            // CREATE DATA 
            DB::beginTransaction();

            $insertDevice = new Device([
                'device_name'          => $device_name,
                'device_description'   => $description,
                'brand'                => $brand,
                'location_id'          => $location,
                'department_id'        => $department,
                'device_category_id'   => $device_category,
                'serial_number'        => $serial_number,
                'activa_number'        => $activa_number,
                'active'               => 1,
                'start_effective'      => Carbon::now()->timezone('Asia/Jakarta'),
                'end_effective'        => null,
                'created_by'           => Auth::user()->id,
                'created_at'           => Carbon::now()->timezone('Asia/Jakarta'),
                'updated_by'           => Auth::user()->id,
                'updated_at'           => Carbon::now()->timezone('Asia/Jakarta'),
            ]);
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
                'action'               => 'CREATE',
                'created_by'           => Auth::user()->id,
                'created_at'           => Carbon::now()->timezone('Asia/Jakarta'),
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
            ]);
        }
    }

    public function deleteData(Request $request)
    {
        $device = Device::where('id', $request->id)->first();

        try {
            DB::beginTransaction();
            if ($device) {
                $device->active         = 0;
                $device->end_effective  = Carbon::now()->timezone('Asia/Jakarta');
                $device->updated_by     = Auth::user()->id;
                $device->updated_at     = Carbon::now()->timezone('Asia/Jakarta');
                $device->save();

                $insertDeviceHist = new DeviceHist([
                    'device_id'            => $device->id,
                    'device_name'          => $device->device_name,
                    'device_description'   => $device->device_description,
                    'brand'                => $device->brand,
                    'location_id'          => $device->location_id,
                    'department_id'        => $device->department_id,
                    'device_category_id'   => $device->device_category_id,
                    'serial_number'        => $device->serial_number,
                    'activa_number'        => $device->activa_number,
                    'active'               => $device->active,
                    'start_effective'      => $device->start_effective,
                    'end_effective'        => $device->end_effective,
                    'action'               => 'UPDATE',
                    'created_by'           => Auth::user()->id,
                    'created_at'           => Carbon::now()->timezone('Asia/Jakarta'),
                ]);
                $insertDeviceHist->save();

                DB::commit();
                return response()->json([
                    'success' => true,
                    "message" => '<div class="alert alert-success">Data perlatan berhasil dihapus, status : TIDAK AKTIF</div>'
                ]);
            } else {
                return response()->json([
                    'errors' => true,
                    "message" => '<div class="alert alert-danger">Data gagal di proses, data peralatan tidak ditemukan</div>'
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
        $device = Device::where('id', $id)->first();
        if ($device) {
            if ($device->active == 1) {
                $active = 'AkTIF';
            } else {
                $active = 'TIDAK AkTIF';
            }
            return view('masters.device.form_detail', [
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
            return view('masters.device.form_detail', [
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

    public function editData($id)
    {
        $device = Device::where('id', $id)->first();
        if ($device) {
            if ($device->active == 1) {
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

    public function importExcel()
    {
        return view('masters.device.upload');
    }

    public function makeTempTable()
    {
        Schema::create('temp', function (Blueprint $table) {
            $table->increments('id');
            $table->string('device', 50)->nullable();
            $table->string('description', 50)->nullable();
            $table->string('brand', 50)->nullable();
            $table->string('location', 50)->nullable();
            $table->string('department', 50)->nullable();
            $table->string('device_category', 50)->nullable();
            $table->string('serial_number', 50)->nullable();
            $table->string('activa_number', 50)->nullable();
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

                $device            = trim(strtoupper($row['Nama Peralatan']), ' ');
                $description       = trim(strtoupper($row['Deskripsi']), ' ');
                $brand             = trim(strtoupper($row['Brand']), ' ');
                $location          = trim(strtoupper($row['Lokasi']), ' ');
                $department        = trim(strtoupper($row['Departemen']), ' ');
                $device_category   = trim(strtoupper($row['Kategori Alat']), ' ');
                $serial_number     = trim(strtoupper($row['Serial Number']), ' ');
                $activa_number     = trim(strtoupper($row['Nomor Aktiva']), ' ');

                if($description == '' && $device == '' && $brand == '' && $location == '' && $department == '' && $device_category == '' && $serial_number == '' && $activa_number == ''){
                    continue;
                }

                if ($device == '') {
                    $error++;
                } 

                if ($brand == '') {
                    $error++;
                } 

                if ($location == '') {
                    $error++;
                } else {
                    $locationCheck = Location::where('location', $location)
                                             ->where('active', 1)
                                             ->first();

                    if(!$locationCheck){
                        $error++;
                    }
                }

                if ($department == '') {
                    $error++;
                } else {
                    $departmentCheck = Department::where('department', $department)
                                                 ->where('active', 1)
                                                 ->first();

                    if(!$departmentCheck){
                        $error++;
                    }
                }

                if ($device_category == '') {
                    $error++;
                } else {
                    $deviceCategoryCheck = DeviceCategory::where('device_category', $device_category)
                                                         ->where('active', 1)
                                                         ->first();

                    if(!$deviceCategoryCheck){
                        $error++;
                    }
                }

                if ($serial_number == '') {
                    $error++;
                } 

                if ($activa_number == '') {
                    $error++;
                }

                if ($error > 0) {
                    $countError++;
                } else {
                    $checkDuplicate = Device::where('device_name', $device)
                                            ->where('brand', $brand)
                                            ->where('location_id', $locationCheck->id)
                                            ->where('department_id', $departmentCheck->id)
                                            ->where('device_category_id', $deviceCategoryCheck->id)
                                            ->where('serial_number', $serial_number)
                                            ->where('activa_number', $activa_number)
                                            ->where('active', 1)
                                            ->first();
                    if($checkDuplicate){
                        $countError++;
                    }
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

                $device            = trim(strtoupper($row['Nama Peralatan']), ' ');
                $description       = trim(strtoupper($row['Deskripsi']), ' ');
                $brand             = trim(strtoupper($row['Brand']), ' ');
                $location          = trim(strtoupper($row['Lokasi']), ' ');
                $department        = trim(strtoupper($row['Departemen']), ' ');
                $device_category   = trim(strtoupper($row['Kategori Alat']), ' ');
                $serial_number     = trim(strtoupper($row['Serial Number']), ' ');
                $activa_number     = trim(strtoupper($row['Nomor Aktiva']), ' ');

                if($description == '' && $device == '' && $brand == '' && $location == '' && $department == '' && $device_category == '' && $serial_number == '' && $activa_number == ''){
                    continue;
                }

                if ($device == '') {
                    $remark [] = 'Nama PEralatan tidak boleh kosong';
                } 

                if ($brand == '') {
                    $remark [] = 'Brand tidak boleh kosong';
                } 

                if ($location == '') {
                    $remark [] = 'Lokasi tidak boleh kosong';
                } else {
                    $locationCheck = Location::where('location', $location)
                                             ->where('active', 1)
                                             ->first();

                    if(!$locationCheck){
                        $remark [] = 'Lokasi '.$location.' tidak ditemukan';
                    }
                }

                if ($department == '') {
                    $remark [] = 'Departemen tidak boleh kosong';
                } else {
                    $departmentCheck = Department::where('department', $department)
                                                 ->where('active', 1)
                                                 ->first();

                    if(!$departmentCheck){
                        $remark [] = 'Departemen '.$department.' tidak ditemukan';
                    }
                }

                if ($device_category == '') {
                    $remark [] = 'Kategori Alat tidak boleh kosong';
                } else {
                    $deviceCategoryCheck = DeviceCategory::where('device_category', $device_category)
                                                         ->where('active', 1)
                                                         ->first();

                    if(!$deviceCategoryCheck){
                        $remark [] = 'Kategori Alat '.$device_category.' tidak ditemukan';
                    }
                }

                if ($serial_number == '') {
                    $remark [] = 'Serial Number tidak boleh kosong';
                } 

                if ($activa_number == '') {
                    $remark [] = 'Nomor Altiva tidak boleh kosong';
                } 

                if (count($remark) > 0) {
                    $countError++;
                } else {
                    $checkDuplicate = Device::where('device_name', $device)
                                            ->where('brand', $brand)
                                            ->where('location_id', $locationCheck->id)
                                            ->where('department_id', $departmentCheck->id)
                                            ->where('device_category_id', $deviceCategoryCheck->id)
                                            ->where('serial_number', $serial_number)
                                            ->where('activa_number', $activa_number)
                                            ->where('active', 1)
                                            ->first();
                    if($checkDuplicate){
                        $remark [] = 'Terdapat duplikat data untuk peralatan '.$device;
                        $countError++;
                    }
                }

                $tempOutput = [
                    'device'          => $device,
                    'description'     => $description,
                    'brand'           => $brand,
                    'location'        => $location,
                    'department'      => $department,
                    'device_category' => $device_category,
                    'serial_number'   => $serial_number,
                    'activa_number'   => $activa_number,
                    'remark'          => implode(', ', $remark)
                ];
                DB::table('temp')->insert($tempOutput);
                $tempData = DB::table('temp')->get();
            }
            if(count($tempData) == 0){
                $tempOutput = [
                    'device'          => '',
                    'description'     => '',
                    'brand'           => '',
                    'location'        => '',
                    'department'      => '',
                    'device_category' => '',
                    'serial_number'   => '',
                    'activa_number'   => '',
                    'remark'          => ''
                ];
    
                DB::table('temp')->insert($tempOutput);
                $tempData = DB::table('temp')->get();
            }
        } else {
            $tempOutput = [
                'device'          => '',
                'description'     => '',
                'brand'           => '',
                'location'        => '',
                'department'      => '',
                'device_category' => '',
                'serial_number'   => '',
                'activa_number'   => '',
                'remark'          => ''
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

                    $device            = trim(strtoupper($row['Nama Peralatan']), ' ');
                    $description       = trim(strtoupper($row['Deskripsi']), ' ');
                    $brand             = trim(strtoupper($row['Brand']), ' ');
                    $location          = trim(strtoupper($row['Lokasi']), ' ');
                    $department        = trim(strtoupper($row['Departemen']), ' ');
                    $device_category   = trim(strtoupper($row['Kategori Alat']), ' ');
                    $serial_number     = trim(strtoupper($row['Serial Number']), ' ');
                    $activa_number     = trim(strtoupper($row['Nomor Aktiva']), ' ');

                    if($description == '' && $device == '' && $brand == '' && $location == '' && $department == '' && $device_category == '' && $serial_number == '' && $activa_number == ''){
                        continue;
                    }

                    if ($device == '') {
                        $error = true;
                    } 
    
                    if ($brand == '') {
                        $error = true;
                    } 
    
                    if ($location == '') {
                        $error = true;
                    } else {
                        $locationCheck = Location::where('location', $location)
                                                 ->where('active', 1)
                                                 ->first();
    
                        if(!$locationCheck){
                            $error = true;
                        }
                    }
    
                    if ($department == '') {
                        $error = true;
                    } else {
                        $departmentCheck = Department::where('department', $department)
                                                     ->where('active', 1)
                                                     ->first();
    
                        if(!$departmentCheck){
                            $error = true;
                        }
                    }
    
                    if ($device_category == '') {
                        $error = true;
                    } else {
                        $deviceCategoryCheck = DeviceCategory::where('device_category', $device_category)
                                                             ->where('active', 1)
                                                             ->first();
    
                        if(!$deviceCategoryCheck){
                            $error = true;
                        }
                    }
    
                    if ($serial_number == '') {
                        $error = true;
                    } 
    
                    if ($activa_number == '') {
                        $error = true;
                    }
    
                    if ($error == 0) {
                        $checkDuplicate = Device::where('device_name', $device)
                                                ->where('brand', $brand)
                                                ->where('location_id', $locationCheck->id)
                                                ->where('department_id', $departmentCheck->id)
                                                ->where('device_category_id', $deviceCategoryCheck->id)
                                                ->where('serial_number', $serial_number)
                                                ->where('activa_number', $activa_number)
                                                ->where('active', 1)
                                                ->first();
                        if($checkDuplicate){
                            $error = true;
                        }
                    }

                    if (!$error){
                        $insertDevice = new Device([
                            'device_name'          => $device,
                            'device_description'   => $description,
                            'brand'                => $brand,
                            'location_id'          => $locationCheck->id,
                            'department_id'        => $departmentCheck->id,
                            'device_category_id'   => $deviceCategoryCheck->id,
                            'serial_number'        => $serial_number,
                            'activa_number'        => $activa_number,
                            'active'               => 1,
                            'start_effective'      => Carbon::now()->timezone('Asia/Jakarta'),
                            'end_effective'        => null,
                            'created_by'           => Auth::user()->id,
                            'created_at'           => Carbon::now()->timezone('Asia/Jakarta'),
                            'updated_by'           => Auth::user()->id,
                            'updated_at'           => Carbon::now()->timezone('Asia/Jakarta'),
                        ]);
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
                            'action'               => 'CREATE',
                            'created_by'           => Auth::user()->id,
                            'created_at'           => Carbon::now()->timezone('Asia/Jakarta'),
                        ]);
                        $insertDeviceHist->save();
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
        $filename = 'Template_Master_Peralatan.xlsx';
        return response()->download(storage_path('app/files/' . $filename));
    }
}

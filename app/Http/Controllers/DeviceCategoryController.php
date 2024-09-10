<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use Carbon\Carbon;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\DepartmentHist;
use App\Models\DeviceCategory;
use App\Exports\DepartmentExport;
use App\Models\DeviceCategoryHist;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Schema;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Schema\Blueprint;

class DeviceCategoryController extends Controller
{
    public function index()
    {
        $deviceCategories = DeviceCategory::where('active', 1)->get()->unique('device_category');

        return view('masters.device_category.device_category_index', [
            'deviceCategories' => $deviceCategories,
        ]);
    }

    public function getData($request, $isExcel = '')
    {

        if ($isExcel == "") {
            session([
                'device_category' . '.device_category' => $request->has('device_category') ?  $request->input('device_category') : '',
                'device_category' . '.disturbance_category' => $request->has('disturbance_category') ?  $request->input('disturbance_category') : '',
                'device_category' . '.status' => $request->has('status') ?  $request->input('status') : '',
            ]);
        }

        $device_category       = session('device_category' . '.device_category') != '' ? session('device_category' . '.device_category') : '';
        $disturbance_category  = session('device_category' . '.disturbance_category') != '' ? session('device_category' . '.disturbance_category') : '';
        $status                = session('device_category' . '.status') != '' ? session('device_category' . '.status') : '';

        $device_category       = strtoupper($device_category);
        $disturbance_category  = strtoupper($disturbance_category);
        $status                = strtoupper($status);

        $deviceCategoryDatas = DeviceCategory::where('active', $status);

        if ($device_category != '') {
            $deviceCategoryDatas = $deviceCategoryDatas->where('device_category', $device_category);
        }

        if ($disturbance_category != '') {
            $deviceCategoryDatas = $deviceCategoryDatas->where('disturbance_category', 'LIKE',  "%{$disturbance_category}%");
        }

        return $deviceCategoryDatas;
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

    public function createNew()
    {

        return view('masters.device_category.form_input');
    }

    public function submitData(Request $request)
    {
        $device_category  = strtoupper($request->device_category);
        $disturbance_category = strtoupper($request->disturbance_category);

        if ($device_category == '') {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Kategori alat wajib terisi, harap periksa kembali formulir pengisian data</div>'
            ]);
        }

        if ($disturbance_category == '') {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Kategori gangguan wajib terisi, harap periksa kembali formulir pengisian data</div>'
            ]);
        }

        $checkDuplicateData = DeviceCategory::where('device_category', $device_category)
            ->where('disturbance_category', $disturbance_category)
            ->where('active', 1)
            ->first();

        if ($checkDuplicateData) {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Telah ditemukan data kategori alat ' . $device_category . ' , kategori gangguan ' . $disturbance_category . ' yang masih aktif</div>'
            ]);
        }

        try {
            // CREATE DATA 
            DB::beginTransaction();

            $insertDeviceCategory = new DeviceCategory([
                'device_category'      => $device_category,
                'disturbance_category' => $disturbance_category,
                'active'               => 1,
                'start_effective'      => Carbon::now()->timezone('Asia/Jakarta'),
                'end_effective'        => null,
                'created_by'           => Auth::user()->id,
                'created_at'           => Carbon::now()->timezone('Asia/Jakarta'),
                'updated_by'           => Auth::user()->id,
                'updated_at'           => Carbon::now()->timezone('Asia/Jakarta'),
            ]);
            $insertDeviceCategory->save();

            $insertDeviceCategoryHist = new DeviceCategoryHist([
                'device_category_id'   => $insertDeviceCategory->id,
                'device_category'      => $insertDeviceCategory->device_category,
                'disturbance_category' => $insertDeviceCategory->disturbance_category,
                'active'               => $insertDeviceCategory->active,
                'start_effective'      => $insertDeviceCategory->start_effective,
                'end_effective'        => $insertDeviceCategory->end_effective,
                'action'               => 'CREATE',
                'created_by'           => Auth::user()->id,
                'created_at'           => Carbon::now()->timezone('Asia/Jakarta'),
            ]);
            $insertDeviceCategoryHist->save();

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
        $deviceCategory = DeviceCategory::where('id', $request->id)->first();

        try {
            DB::beginTransaction();
            if ($deviceCategory) {
                $deviceCategory->active         = 0;
                $deviceCategory->end_effective  = Carbon::now()->timezone('Asia/Jakarta');
                $deviceCategory->updated_by     = Auth::user()->id;
                $deviceCategory->updated_at     = Carbon::now()->timezone('Asia/Jakarta');
                $deviceCategory->save();

                $insertDeviceCategoryHist = new DeviceCategoryHist([
                    'device_category_id'   => $deviceCategory->id,
                    'device_category'      => $deviceCategory->device_category,
                    'disturbance_category' => $deviceCategory->disturbance_category,
                    'active'               => $deviceCategory->active,
                    'start_effective'      => $deviceCategory->start_effective,
                    'end_effective'        => $deviceCategory->end_effective,
                    'action'               => 'UPDATE',
                    'created_by'           => Auth::user()->id,
                    'created_at'           => Carbon::now()->timezone('Asia/Jakarta'),
                ]);
                $insertDeviceCategoryHist->save();

                DB::commit();
                return response()->json([
                    'success' => true,
                    "message" => '<div class="alert alert-success">Data kategori pekerjaan berhasil dihapus, status : TIDAK AKTIF</div>'
                ]);
            } else {
                return response()->json([
                    'errors' => true,
                    "message" => '<div class="alert alert-danger">Data gagal di proses, data kategori pekerjaan tidak ditemukan</div>'
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
        $deviceCategory = DeviceCategory::where('id', $id)->first();

        if ($deviceCategory) {
            if ($deviceCategory->active == 1) {
                $active = 'AkTIF';
            } else {
                $active = 'TIDAK AkTIF';
            }

            return view('masters.device_category.form_detail', [
                'device_category'      => $deviceCategory->device_category,
                'disturbance_category' => $deviceCategory->disturbance_category,
                'active'               => $active,
                'start_effective'      => $deviceCategory->start_effective != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $deviceCategory->start_effective)->format('d/m/Y H:i:s') : '-',
                'end_effective'        => $deviceCategory->end_effective != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $deviceCategory->end_effective)->format('d/m/Y H:i:s') : '-',
                'created_by'           => optional($deviceCategory->createdBy)->name,
                'created_at'           => $deviceCategory->created_at != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $deviceCategory->created_at)->format('d/m/Y H:i:s') : '-',
                'updated_by'           => optional($deviceCategory->updatedBy)->name,
                'updated_at'           => $deviceCategory->updated_at != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $deviceCategory->updated_at)->format('d/m/Y H:i:s') : '-',
            ]);
        } else {
            return view('masters.device_category.form_detail', [
                'device_category'      => '',
                'disturbance_category' => '',
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

    public function importExcel()
    {
        return view('masters.device_category.upload');
    }

    public function makeTempTable()
    {
        Schema::create('temp', function (Blueprint $table) {
            $table->increments('id');
            $table->string('category', 50)->nullable();
            $table->string('disturbance_category', 50)->nullable();
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

                $device_category         = trim(strtoupper($row['Kategori Alat']), ' ');
                $disturbance_category    = trim(strtoupper($row['Kategori Gangguan']), ' ');

                if($device_category == '' && $disturbance_category == ''){
                    continue;
                }

                if ($device_category == '') {
                    $error++;
                }

                if ($disturbance_category == '') {
                    $error++;
                }

                if ($error > 0) {
                    $countError++;
                } else {
                    $checkDuplicate = DeviceCategory::where('device_category', $device_category)
                                                    ->where('disturbance_category', $disturbance_category)
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

                $device_category         = trim(strtoupper($row['Kategori Alat']), ' ');
                $disturbance_category    = trim(strtoupper($row['Kategori Gangguan']), ' ');

                if($device_category == '' && $disturbance_category == ''){
                    continue;
                }

                if ($device_category == '') {
                    $remark [] = 'Kategori Alat tidak boleh kosong';
                }

                if ($disturbance_category == '') {
                    $remark [] = 'Kategori Gangguan tidak boleh kosong';
                }

                if (count($remark) == 0) {
                    $checkDuplicate = DeviceCategory::where('device_category', $device_category)
                                                    ->where('disturbance_category', $disturbance_category)
                                                    ->where('active', 1)
                                                    ->first();
                    if($checkDuplicate){
                        $remark [] = 'Terdapat duplikat data untuk Kategori Alat '.$device_category;
                    }
                }

                if (count($remark) > 0) {
                    $countError++;
                }

                $tempOutput = [
                    'category'         => $device_category,
                    'disturbance_category'    => $disturbance_category,
                    'remark'                  => implode(', ', $remark)
                ];
                DB::table('temp')->insert($tempOutput);
                $tempData = DB::table('temp')->get();
            }
            if(count($tempData) == 0){
                $tempOutput = [
                    'category'       => '',
                    'disturbance_category'  => '',
                    'remark'                => ''
                ];
                DB::table('temp')->insert($tempOutput);
                $tempData = DB::table('temp')->get();
            }
        } else {
            $tempOutput = [
                'category'       => '',
                'disturbance_category'  => '',
                'remark'                => ''
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

                    $device_category         = trim(strtoupper($row['Kategori Alat']), ' ');
                    $disturbance_category    = trim(strtoupper($row['Kategori Gangguan']), ' ');

                    if($device_category == '' && $disturbance_category == ''){
                        continue;
                    }
                    
                    if ($device_category == '') {
                        $error = true;
                    }
    
                    if ($disturbance_category == '') {
                        $error = true;
                    }
    
                    if (!$error) {
                        $checkDuplicate = DeviceCategory::where('device_category', $device_category)
                                                        ->where('disturbance_category', $disturbance_category)
                                                        ->where('active', 1)
                                                        ->first();
                        if($checkDuplicate){
                            $error = true;
                        }
                    }

                    if (!$error){
                        $insertDeviceCategory = new DeviceCategory([
                            'device_category'      => $device_category,
                            'disturbance_category' => $disturbance_category,
                            'active'               => 1,
                            'start_effective'      => Carbon::now()->timezone('Asia/Jakarta'),
                            'end_effective'        => null,
                            'created_by'           => Auth::user()->id,
                            'created_at'           => Carbon::now()->timezone('Asia/Jakarta'),
                            'updated_by'           => Auth::user()->id,
                            'updated_at'           => Carbon::now()->timezone('Asia/Jakarta'),
                        ]);
                        $insertDeviceCategory->save();
            
                        $insertDeviceCategoryHist = new DeviceCategoryHist([
                            'device_category_id'   => $insertDeviceCategory->id,
                            'device_category'      => $insertDeviceCategory->device_category,
                            'disturbance_category' => $insertDeviceCategory->disturbance_category,
                            'active'               => $insertDeviceCategory->active,
                            'start_effective'      => $insertDeviceCategory->start_effective,
                            'end_effective'        => $insertDeviceCategory->end_effective,
                            'action'               => 'CREATE',
                            'created_by'           => Auth::user()->id,
                            'created_at'           => Carbon::now()->timezone('Asia/Jakarta'),
                        ]);
                        $insertDeviceCategoryHist->save();
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
        $filename = 'Template_Master_Kategori_Peralatan.xlsx';
        return response()->download(storage_path('app/files/' . $filename));
    }
}

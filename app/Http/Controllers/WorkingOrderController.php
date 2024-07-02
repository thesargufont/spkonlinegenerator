<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\User;
use Exception;
use Carbon\Carbon;
use App\Models\Job;
use App\Models\Device;
use App\Models\DeviceCategory;
use App\Models\Location;
use App\Models\GeneralCode;
use App\Models\SpongeHeader;
use App\Models\SpongeDetail;
use App\Models\SpongeDetailHist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

class WorkingOrderController extends Controller
{
    public function index()
    {
        $department = Department::where('active', 1)->get()->toArray();

        return view('forms.working_order.working_order_index');
    }

    public function getData($request, $isExcel = '')
    {
        $user = Auth::user()->id;
        $spongeheader = SpongeHeader::where('created_by', $user)->where('wo_number', 'like', '%' . $request->wo_number . '%');

        return $spongeheader;
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
            ->editColumn('job_category', function ($item) {
                $job_category = Job::where('id', $item->job_category)->first();
                if ($job_category) {
                    return $job_category->job_category;
                } else {
                    return '-';
                }
            })
            ->editColumn('status', function ($item) {
                return 'Not Approve';
            })
            ->editColumn('start_effective', function ($item) {
                return Carbon::createFromFormat("Y-m-d H:i:s", $item->updated_at)->format('d/m/Y');
            });
        return $datatables->make(TRUE);
    }

    public function createNew()
    {
        $department_arr = Department::select('id', 'department', 'department_code')->where('active', 1)->get();
        $wo_category_arr = Job::select('wo_category')->groupBy('wo_category')->get();
        $location_arr = Location::select('id', 'location', 'location_type')->where('active', 1)->where('end_effective', null)->get();
        $device_arr = Device::select('device_name')->where('active', 1)->where('end_effective', null)->groupBy('device_name')->get();

        $data = [
            'department' => $department_arr,
            'wo_category' => $wo_category_arr,
            'location' => $location_arr,
            'device' => $device_arr,
        ];

        return view('forms.working_order.form_input', $data);
    }

    public function getWONumber(Request $request)
    {
        try {
            /*WO NUMBER Preparation*/
            //get month year
            $now = Carbon::now();
            $year = $now->year;
            $month =  $now->month;

            //get user department
            $dept_code = Department::where('id', $request->department_id)->first()->department_code;

            //get number
            $cek_number = SpongeHeader::where('wo_number', 'like', '%WO' . '/' . $dept_code . '/' . str_pad($month, 2, 0, STR_PAD_LEFT) . '/' . $year)->orderBy('created_at', 'desc')->first();
            $number = 0;
            if ($cek_number) {
                $number = intval(substr($cek_number->wo_number, 0, 5));
            }
            $number++;

            //generate wo number
            $wo_number = str_pad($number, 5, '0', STR_PAD_LEFT) . '/' . 'WO' . '/' . $dept_code . '/' . str_pad($month, 2, 0, STR_PAD_LEFT) . '/' . $year;
            /*WO NUMBER complete*/

            return response()->json(['success' => true, 'message' => '', 'wo_number' => $wo_number]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getJobCategory(Request $request)
    {
        try {
            $job_categories = Job::select('id', 'job_category')->where('wo_category', $request->wo_category)->where('department_id', $request->department)->get();

            return response()->json(['success' => true, 'message' => '', 'job_categories' => $job_categories]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getDeviceModel(Request $request)
    {
        try {
            $devices = Device::select('id', 'brand')->where('device_name', $request->device)->where('department_id', $request->department)->where('location_id', $request->location)->get();

            return response()->json(['success' => true, 'message' => '', 'devices' => $devices]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getDeviceCode(Request $request)
    {
        try {
            //dd($request->device_model);
            $devices = Device::find($request->device_model);

            return response()->json(['success' => true, 'message' => '', 'devices' => $devices]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getDisturbanceCategory(Request $request)
    {
        try {
            $device = Device::find($request->device);
            if ($device) {
                $disturbance_cek = DeviceCategory::find($device->device_category_id);
                if ($disturbance_cek) {
                    $disturbances = DeviceCategory::select('id', 'disturbance_category')->where('device_category', $disturbance_cek->device_category)->get();
                } else {
                    return response()->json([
                        'errors' => true,
                        "message" => '<div class="alert alert-danger">Kategori gangguan tidak ditemukan. Mohon cek kembali atau hubungi admin.</div>'
                    ]);
                }
            } else {
                return response()->json([
                    'errors' => true,
                    "message" => '<div class="alert alert-danger">Ada kesalahan di pencarian model alat. Mohon cek kembali atau hubungi admin.</div>'
                ]);
            }

            return response()->json(['success' => true, 'message' => '', 'disturbances' => $disturbances]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function submitData(Request $request)
    {
        //HEADER VALIDATION
        if ($request->wo_number == '' || $request->wo_number == null) {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Nomor WO belum diisi. Mohon cek kembali.</div>'
            ]);
        }
        if ($request->wo_category == '' || $request->wo_category == null) {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Kategori pekerjaan belum diisi. Mohon cek kembali.</div>'
            ]);
        }
        if ($request->job_category == '' || $request->job_category == null) {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Tipe pekerjaan belum diisi. Mohon cek kembali.</div>'
            ]);
        }
        if ($request->department == '' || $request->department == null) {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Departemen belum diisi. Mohon cek kembali.</div>'
            ]);
        }
        if ($request->effective_date == null) {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Tanggal efektif belum diisi. Mohon cek kembali.</div>'
            ]);
        }
        //HEADER VALIDATION - CHECK WO NUMBER DUPLICATION
        $wo_number_cek = SpongeHeader::where('wo_number', $request->wo_number)->first();
        if ($wo_number_cek) {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Nomor WO sudah terpakai. Mohon muat ulang halaman.</div>'
            ]);
        }
        //HEADER VALIDATION - VERIFY DEPARTMENT ID
        $department_cek = Department::find($request->department);
        if (!$department_cek) {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Departemen tidak ditemukan. Mohon cek kembali</div>'
            ]);
        } else {
            if ($department_cek->active != 1) {
                return response()->json([
                    'errors' => true,
                    "message" => '<div class="alert alert-danger">Departemen yang dipilih sudah tidak aktif. Mohon cek kembali</div>'
                ]);
            }
        }

        //DETAIL VALIDATION
        if (!isset($request->details)) {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Detail belum diinputkan. Mohon cek kembali</div>'
            ]);
        }
        foreach ($request->details as $detail) {
            if ($detail['location'] == '' || $detail['location'] == null) {
                return response()->json([
                    'errors' => true,
                    "message" => '<div class="alert alert-danger">Ada tipe pekerjaan yang belum diisi. Mohon cek kembali.</div>'
                ]);
            } else {
                $location_cek = Location::find($detail['location']);
                //dd($location_cek);
                if (!$location_cek) {
                    return response()->json([
                        'errors' => true,
                        "message" => '<div class="alert alert-danger">Ada lokasi yang tidak ditemukan. Mohon cek kembali</div>'
                    ]);
                } else {
                    if ($location_cek->active != 1) {
                        return response()->json([
                            'errors' => true,
                            "message" => '<div class="alert alert-danger">Lokasi ' . $location_cek->location . ' sudah tidak aktif. Mohon cek kembali</div>'
                        ]);
                    }
                }
            }
            if ($detail['device'] == '' || $detail['device'] == null) {
                return response()->json([
                    'errors' => true,
                    "message" => '<div class="alert alert-danger">Ada alat yang belum diisi. Mohon cek kembali.</div>'
                ]);
            } else {
                $device_cek = Device::find($detail['device']);
                if (!$device_cek) {
                    return response()->json([
                        'errors' => true,
                        "message" => '<div class="alert alert-danger">Ada device yang tidak ditemukan. Mohon cek kembali</div>'
                    ]);
                } else {
                    if ($device_cek->active != 1) {
                        return response()->json([
                            'errors' => true,
                            "message" => '<div class="alert alert-danger">Alat ' . $device_cek->eq_id . ' sudah tidak aktif. Mohon cek kembali</div>'
                        ]);
                    }
                }
            }
            if ($request->wo_category == 'PEKERJAAN' && ($detail['disturbance_category'] == '' || $detail['disturbance_category'] == null)) {
                return response()->json([
                    'errors' => true,
                    "message" => '<div class="alert alert-danger">Ada kategori gangguan yang belum diisi. Mohon cek kembali.</div>'
                ]);
            }
            if ($detail['description'] == '' || $detail['description'] == null) {
                return response()->json([
                    'errors' => true,
                    "message" => '<div class="alert alert-danger">Ada deskripsi yang belum diisi. Mohon cek kembali.</div>'
                ]);
            }

            if (array_key_exists('photo1', $detail)) {
                // dd($detail['photo1']->getClientOriginalExtension());
                // if ($detail['photo1']->getClientOriginalExtension() != 'jpg' || $detail['photo1']->getClientOriginalExtension() != 'jpeg') {
                //     return response()->json([
                //         'errors' => true,
                //         "message" => '<div class="alert alert-danger">Format tipe Gambar 1 tidak sesuai. Mohon cek kembali.</div>'
                //     ]);
                // }
            }
            if (array_key_exists('photo2', $detail)) {
                $path2 = Storage::putFile('uploads', $detail['photo2']);
            }
            if (array_key_exists('photo3', $detail)) {
                $path3 = Storage::putFile('uploads', $detail['photo3']);
            }
        }


        //TRANSACTION
        try {
            DB::beginTransaction();
            $spongeHeader = new SpongeHeader([
                'wo_number' => $request->wo_number,
                'wo_type' => $request->wo_category,
                'job_category' => $request->job_category,
                'department' => Department::find($request->department)->department,
                'effective_date' => Carbon::createFromFormat('d/m/Y', $request->effective_date),
                'created_by'              => Auth::user()->id,
                'created_at'              => Carbon::now(),
                'updated_by'              => Auth::user()->id,
                'updated_at'              => Carbon::now(),
            ]);
            $spongeHeader->save();

            $spongeDetails = [];
            $spongeDetailHists = [];
            foreach ($request->details as $detail) {
                if (array_key_exists('photo1', $detail)) {
                    $path1 = Storage::putFile('uploads', $detail['photo1']);
                } else {
                    $path1 = '';
                }
                if (array_key_exists('photo2', $detail)) {
                    $path2 = Storage::putFile('uploads', $detail['photo2']);
                } else {
                    $path2 = '';
                }
                if (array_key_exists('photo3', $detail)) {
                    $path3 = Storage::putFile('uploads', $detail['photo3']);
                } else {
                    $path3 = '';
                }
                $spongeDetail = new SpongeDetail([
                    'wo_number_id' => $spongeHeader->id,
                    'reporter_location' => Location::find($detail['location'])->location,
                    'device_id' => $detail['device'],
                    'disturbance_category' => $detail['disturbance_category'],
                    'wo_decription' => $detail['description'],
                    'wo_attachment1' => $path1,
                    'wo_attachment2' => $path2,
                    'wo_attachment3' => $path3,
                    'start_at' => $spongeHeader->effective_date,
                    'estimated_end' => $spongeHeader->effective_date,
                    'created_by'              => Auth::user()->id,
                    'created_at'              => Carbon::now(),
                    'updated_by'              => Auth::user()->id,
                    'updated_at'              => Carbon::now(),
                ]);
                $spongeDetails[] = $spongeDetail;
                $spongeDetailHist = new SpongeDetailHist([
                    'wo_number_id' => $spongeHeader->id,
                    'reporter_location' => Location::find($detail['location'])->location,
                    'device_id' => $detail['device'],
                    'disturbance_category' => $detail['disturbance_category'],
                    'wo_decription' => $detail['description'],
                    'wo_attachment1' => $path1,
                    'wo_attachment2' => $path2,
                    'wo_attachment3' => $path3,
                    'start_at' => $spongeHeader->effective_date,
                    'estimated_end' => $spongeHeader->effective_date,
                    'action' => 'CREATE',
                    'created_by'              => Auth::user()->id,
                    'created_at'              => Carbon::now(),
                    'updated_by'              => Auth::user()->id,
                    'updated_at'              => Carbon::now(),
                ]);
                $spongeDetailHists[] = $spongeDetailHist;
            }

            foreach ($spongeDetails as $insert) {
                $insert->save();
            }
            foreach ($spongeDetailHists as $insert) {
                $insert->save();
            }

            DB::commit();
            return response()->json([
                'success' => true,
                "message" => '<div class="alert alert-success">' . $spongeHeader->wo_number . ' berhasil disimpan</div>'
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger"> Server Error : ' . $e->getMessage() . '. Silahkan kontak developer atau admin.</div>'
            ]);
        }
    }
}

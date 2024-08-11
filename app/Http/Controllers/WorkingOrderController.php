<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use Carbon\Carbon;
use App\Models\Job;
use App\Models\Role;
use App\Models\Device;
use App\Models\Location;
use App\Models\Department;
use App\Models\Notification;
use App\Models\SpongeDetail;
use App\Models\SpongeHeader;
use Illuminate\Http\Request;
use App\Models\DeviceCategory;
use App\Models\GeneralCode;
use App\Models\SpongeDetailHist;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToArray;
use Yajra\DataTables\Facades\DataTables;

class WorkingOrderController extends Controller
{
    public function index()
    {
        $user_id = Auth::user()->id;
        $roles = Role::where('user_id', $user_id)->where('active', 1)->distinct()->pluck('role')->toArray();
        $access_right = array('SUPERADMIN', 'USER');
        if (count(array_intersect($roles, $access_right)) == 0) {
            $access = false;
            return redirect()->route('home');
        } else {
            $access = true;
        }

        $locations        = Location::where('active', 1)->get();
        $departments      = Department::where('active', 1)->get();
        $wo_status        = GeneralCode::where('section','SPONGE')->where('label','STATUS_HEADER')->where('end_effective',null)->get();

        return view('forms.working_order.working_order_index', [
            'hidden_status' => 'hidden',
            'return_msg' => '',
            'access' => $access,
            'locations' => $locations,
            'departments' => $departments,
            'wo_status' => $wo_status,
        ]);
    }

    public function getData($request, $isExcel = '')
    {
        if ($isExcel == "") {
            session([
                'working_order' . '.wo_number' => $request->has('wo_number') ?  $request->input('wo_number') : '',
                'working_order' . '.spk_number' => $request->has('spk_number') ?  $request->input('spk_number') : '',
                'working_order' . '.wo_category' => $request->has('wo_category') ?  $request->input('wo_category') : '',
                'working_order' . '.department' => $request->has('department') ?  $request->input('department') : '',
                'working_order' . '.wo_status' => $request->has('wo_status') ?  $request->input('wo_status') : '',
                // 'working_order' . '.location' => $request->has('location') ?  $request->input('location') : '',
            ]);
        }

        $wo_number    = session('working_order' . '.wo_number') != '' ? session('working_order' . '.wo_number') : '';
        $spk_number   = session('working_order' . '.spk_number') != '' ? session('working_order' . '.spk_number') : '';
        $wo_category  = session('working_order' . '.wo_category') != '' ? session('working_order' . '.wo_category') : '';
        $department   = session('working_order' . '.department') != '' ? session('working_order' . '.department') : '';
        $wo_status   = session('working_order' . '.wo_status') != '' ? session('working_order' . '.wo_status') : '';
        // $location     = session('working_order' . '.location') != '' ? session('working_order' . '.location') : '';

        // dd($wo_status);

        $user = Auth::user()->id;
        $spongeheader_ongoing = SpongeHeader::where('created_by', $user)->where('status','=','ONGOING')->orderBy('created_at','desc')
                                ->where('wo_number', 'LIKE',  "%{$wo_number}%")
                                ->where('spk_number', 'LIKE',  "%{$spk_number}%")
                                ->where('wo_category', 'LIKE',  "%{$wo_category}%")
                                ->where('department_id', 'LIKE',  "%{$department}%")
                                ->where('status', 'LIKE',  "%{$wo_status}%")
                                ;
        $spongeheader_done = SpongeHeader::where('created_by', $user)->where('status','=','DONE')->orderBy('created_at','desc')
                            ->where('wo_number', 'LIKE',  "%{$wo_number}%")
                            ->where('spk_number', 'LIKE',  "%{$spk_number}%")
                            ->where('wo_category', 'LIKE',  "%{$wo_category}%")
                            ->where('department_id', 'LIKE',  "%{$department}%")
                            ->where('status', 'LIKE',  "%{$wo_status}%")
                            ;
        $spongeheader_closed = SpongeHeader::where('created_by', $user)->where('status','=','CLOSED')->orderBy('created_at','desc')
                                ->where('wo_number', 'LIKE',  "%{$wo_number}%")
                                ->where('spk_number', 'LIKE',  "%{$spk_number}%")
                                ->where('wo_category', 'LIKE',  "%{$wo_category}%")
                                ->where('department_id', 'LIKE',  "%{$department}%")
                                ->where('status', 'LIKE',  "%{$wo_status}%")
                                ;
        $spongeheader_cancel = SpongeHeader::where('created_by', $user)->where('status','=','CANCEL')->orderBy('created_at','desc')
                                ->where('wo_number', 'LIKE',  "%{$wo_number}%")
                                ->where('spk_number', 'LIKE',  "%{$spk_number}%")
                                ->where('wo_category', 'LIKE',  "%{$wo_category}%")
                                ->where('department_id', 'LIKE',  "%{$department}%")
                                ->where('status', 'LIKE',  "%{$wo_status}%")
                                ;
        $spongeheader = SpongeHeader::where('created_by', $user)->where('status','NOT APPROVE')
                        ->where('wo_number', 'LIKE',  "%{$wo_number}%")
                        ->where('spk_number', 'LIKE',  "%{$spk_number}%")
                        ->where('wo_category', 'LIKE',  "%{$wo_category}%")
                        ->where('department_id', 'LIKE',  "%{$department}%")
                        ->where('status', 'LIKE',  "%{$wo_status}%")
                        ->orderBy('created_at','desc')
                        ->union($spongeheader_ongoing)
                        ->union($spongeheader_done)
                        ->union($spongeheader_closed)
                        ->union($spongeheader_cancel)
                        ;

        // if ($wo_number != '') {
        //     $spongeheader = $spongeheader->where('wo_number', 'LIKE',  "%{$wo_number}%");
        // }

        // if ($spk_number != '') {
        //     $spongeheader = $spongeheader->where('spk_number', 'LIKE',  "%{$spk_number}%");
        // }

        // if ($wo_category != '') {
        //     $spongeheader = $spongeheader->where('wo_category', 'LIKE',  "%{$wo_category}%");
        // }

        // if ($department != '') {
        //     $spongeheader = $spongeheader->where('department_id', 'LIKE',  "%{$department}%");
        // }

        // if ($wo_status != '') {
        //     $spongeheader = $spongeheader->where('status', 'LIKE',  "%{$wo_status}%");
        // }

        // if ($location != '') {
        //     $spongeheader = $spongeheader->where('location_id', 'LIKE',  "%{$location}%");
        // }

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
            $show_url = route('form-input.working-order.detail', ['id' => $item->id]);

            $txt = '';
            $txt .= "<a href=\"#\" onclick=\"showItem($item[id]);\" title=\"" . ucfirst(__('view')) . "\" class=\"btn btn-xs btn-secondary\"><i class=\"fa fa-eye fa-fw fa-xs\"></i></a>";
            // $txt .= "<a href=\"#\" title=\"" . ucfirst(__('edit')) . "\" class=\"btn btn-xs btn-secondary\"><i class=\"fa fa-edit fa-fw fa-xs\"></i></a>";

            return $txt;
        })
            ->addColumn('department', function ($item) {
                $cek = Department::find($item->department_id);
                if ($cek) {
                    return $cek->department;
                } else {
                    return 'ID department tidak ditemukan';
                }
            })
            ->editColumn('job_category', function ($item) {
                $job_category = Job::where('id', $item->job_category)->first();
                if ($job_category) {
                    if ($job_category->$job_category != '' || $job_category->$job_category != null) {
                        return $job_category->job_category;
                    } else {
                        return '-';
                    }
                } else {
                    return '-';
                }
            })
            ->editColumn('created_by', function ($item) {
                $cek = User::find($item->created_by);
                if ($cek) {
                    return $cek->name;
                } else {
                    return 'User ID : ' . $item->created_by;
                }
            })
            ->editColumn('approve_by', function ($item) {
                if ($item->approve_by != '' || $item->approve_by != null) {
                    $cek = User::find($item->created_by);
                    if ($cek) {
                        return $cek->name;
                    } else {
                        return 'User ID : ' . $item->created_by;
                    }
                } else {
                    return '-';
                }
            })
            ->editColumn('spk_number', function ($item) {
                if ($item->spk_number != '' || $item->spk_number != null) {
                    return $item->spk_number;
                } else {
                    return '-';
                }
            })
            ->editColumn('status', function ($item) {
                if ($item->status != '' || $item->status != null) {
                    return $item->status;
                } else {
                    return 'NOT APPROVE';
                }
            })
            ->editColumn('approve_at', function ($item) {
                if ($item->approve_at != '' || $item->approve_at != null) {
                    return Carbon::createFromFormat("Y-m-d H:i:s", $item->updated_at)->format('d/m/Y');
                } else {
                    return '-';
                }
            })
            ->editColumn('effective_date', function ($item) {
                return Carbon::createFromFormat("Y-m-d H:i:s", $item->updated_at)->format('d/m/Y');
            });
        return $datatables->make(TRUE);
    }

    public function createNew()
    {
        try {
            $user_id = Auth::user()->id;
            $roles = Role::where('user_id', $user_id)->where('active', 1)->distinct()->pluck('role')->toArray();
            $access_right = array('SUPERADMIN', 'USER');
            $locations   = Location::where('active', 1)->get();
            $departments = Department::where('active', 1)->get();

            if (count(array_intersect($roles, $access_right)) == 0) {
                $access = false;
                $data = [
                    'hidden_status' => '',
                    'return_msg' => 'Anda tidak punya hak akses untuk membuat laporan work order.',
                    'access' => $access,
                    'locations' => $locations,
                    'departments' => $departments,
                ];
                return view('forms.working_order.working_order_index', $data);
            } else {
                $access = true;
            }
            $department_arr = Department::select('id', 'department', 'department_code')->where('active', 1)->get()->toArray();
            if (empty($department_arr)) {
                $data = [
                    'hidden_status' => '',
                    'return_msg' => 'Data departemen tidak ditemukan. Pastikan Master Departemen sudah disetting.',
                    'access' => $access,
                    'locations' => $locations,
                    'departments' => $departments,
                ];

                return view('forms.working_order.working_order_index', $data);
            }
            $wo_category_arr = Job::select('wo_category')->where('wo_category', '!=', 'LAPORAN GANGGUAN')->distinct()->get()->toArray();
            if (empty($wo_category_arr)) {
                $data = [
                    'hidden_status' => '',
                    'return_msg' => 'Kategori WO tidak ditemukan. Pastikan Master Pekerjaan sudah disetting.',
                    'access' => $access,
                    'locations' => $locations,
                    'departments' => $departments,
                ];

                return view('forms.working_order.working_order_index', $data);
            }
            $location_arr = Location::select('id', 'location', 'location_type')->where('active', 1)->where('end_effective', null)->get()->toArray();
            if (empty($location_arr)) {
                $data = [
                    'hidden_status' => '',
                    'return_msg' => 'Data lokasi tidak ditemukan. Pastikan Master Lokasi sudah disetting.',
                    'access' => $access,
                    'locations' => $locations,
                    'departments' => $departments,
                ];

                return view('forms.working_order.working_order_index', $data);
            }
            $device_arr = Device::select('device_name')->where('active', 1)->where('end_effective', null)->groupBy('device_name')->get()->toArray();
            if (empty($device_arr)) {
                $data = [
                    'hidden_status' => '',
                    'return_msg' => 'Data alat tidak ditemukan. Pastikan Master Alat sudah disetting.',
                    'access' => $access,
                    'locations' => $locations,
                    'departments' => $departments,
                ];

                return view('forms.working_order.working_order_index', $data);
            }
            $effective_date = Carbon::now()->format('d/m/Y');

            $data = [
                'department' => $department_arr,
                'wo_category' => $wo_category_arr,
                'location' => $location_arr,
                'device' => $device_arr,
                'effective_date' => $effective_date,
            ];

            return view('forms.working_order.form_input', $data);
        } catch (\Exception $e) {
            $data = [
                'hidden_status' => '',
                'return_msg' => 'Service Error :' . $e->getMessage(),
                'access' => true,
                'locations' => $locations,
                'departments' => $departments,
            ];

            return view('forms.working_order.working_order_index', $data);
        }
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
            $job_categories = Job::select('id', 'job_category')->where('wo_category', $request->wo_category)->where('department_id', $request->department)->orderBy('id')->get();

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
        // if ($request->job_category == '' || $request->job_category == null) {
        //     return response()->json([
        //         'errors' => true,
        //         "message" => '<div class="alert alert-danger">Tipe pekerjaan belum diisi. Mohon cek kembali.</div>'
        //     ]);
        // }
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
                            "message" => '<div class="alert alert-danger">Alat ' . $device_cek->activa_number . ' sudah tidak aktif. Mohon cek kembali</div>'
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

            // $newFilename1 = '';
            // $newFilename2 = '';
            // $newFilename3 = '';

            if (array_key_exists('photo1', $detail)) {
                // dd(strval($detail['photo1']->getClientOriginalExtension()));
                if (strtolower(strval($detail['photo1']->getClientOriginalExtension())) != 'jpg' && strtolower(strval($detail['photo1']->getClientOriginalExtension())) != 'jpeg') {
                    return response()->json([
                        'errors' => true,
                        "message" => '<div class="alert alert-danger">Format tipe Gambar 1 tidak sesuai. Mohon cek kembali.</div>'
                    ]);
                }
                if (filesize($detail['photo1']) > 5120000) {
                    return response()->json([
                        'errors' => true,
                        "message" => '<div class="alert alert-danger">Ukuran gambar tidak boleh lebih dari 5MB. Mohon cek kembali.</div>'
                    ]);
                }
                // $newFilename1 = str_replace('/', '-', $request->wo_number) . '-photo1_det' . $number . '.' . $detail['photo1']->getClientOriginalExtension();
                // Storage::putFileAs('local', $detail['photo1'], $newFilename1);
            }
            if (array_key_exists('photo2', $detail)) {
                if (strtolower(strval($detail['photo2']->getClientOriginalExtension())) != 'jpg' && strtolower(strval($detail['photo2']->getClientOriginalExtension())) != 'jpeg') {
                    return response()->json([
                        'errors' => true,
                        "message" => '<div class="alert alert-danger">Format tipe Gambar 1 tidak sesuai. Mohon cek kembali.</div>'
                    ]);
                }
                if (filesize($detail['photo2']) > 5120000) {
                    return response()->json([
                        'errors' => true,
                        "message" => '<div class="alert alert-danger">Ukuran gambar tidak boleh lebih dari 5MB. Mohon cek kembali.</div>'
                    ]);
                }
                // $newFilename2 = str_replace('/', '-', $request->wo_number) . '-photo2_det' . $number . '.' . $detail['photo2']->getClientOriginalExtension();
            }
            if (array_key_exists('photo3', $detail)) {
                if (strtolower(strval($detail['photo3']->getClientOriginalExtension())) != 'jpg' && strtolower(strval($detail['photo3']->getClientOriginalExtension())) != 'jpeg') {
                    return response()->json([
                        'errors' => true,
                        "message" => '<div class="alert alert-danger">Format tipe Gambar 1 tidak sesuai. Mohon cek kembali.</div>'
                    ]);
                }
                if (filesize($detail['photo3']) > 5120000) {
                    return response()->json([
                        'errors' => true,
                        "message" => '<div class="alert alert-danger">Ukuran gambar tidak boleh lebih dari 5MB. Mohon cek kembali.</div>'
                    ]);
                }
                // $newFilename3 = str_replace('/', '-', $request->wo_number) . '-photo3_det' . $number . '.' . $detail['photo3']->getClientOriginalExtension();
            }
            // $number++;
        }


        //TRANSACTION
        try {
            DB::beginTransaction();
            $spongeHeader = new SpongeHeader([
                'wo_number'       => $request->wo_number,
                'wo_category'     => $request->wo_category,
                'job_category'    => $request->job_category ? $request->job_category : '',
                'department_id'   => $request->department,
                'effective_date'  => Carbon::createFromFormat('d/m/Y', $request->effective_date)->timezone('Asia/Jakarta'),
                'status'          => $request->wo_category == 'LAPORAN GANGGUAN' ? 'DONE' : 'NOT APPROVE',
                'created_by'      => Auth::user()->id,
                'created_at'      => Carbon::now()->timezone('Asia/Jakarta'),
                'updated_by'      => Auth::user()->id,
                'updated_at'      => Carbon::now()->timezone('Asia/Jakarta'),
            ]);
            $spongeHeader->save();

            // $spongeDetails = [];
            // $spongeDetailHists = [];
            $number = 1;
            foreach ($request->details as $detail) {
                if ($request->wo_category == 'LAPORAN GANGGUAN') {
                    $closeAt = Carbon::createFromFormat('d/m/Y', $request->effective_date)->timezone('Asia/Jakarta');
                } else {
                    $closeAt = null;
                }

                $newFilename1 = '';
                $newFilename2 = '';
                $newFilename3 = '';

                if (array_key_exists('photo1', $detail)) {
                    $newFilename1 = str_replace('/', '-', $request->wo_number) . '-photo1_det' . $number . '.' . $detail['photo1']->getClientOriginalExtension();
                    // Storage::putFileAs('public', $detail['photo1'], $newFilename1);
                }
                if (array_key_exists('photo2', $detail)) {
                    $newFilename2 = str_replace('/', '-', $request->wo_number) . '-photo2_det' . $number . '.' . $detail['photo2']->getClientOriginalExtension();
                    // Storage::putFileAs('public', $detail['photo2'], $newFilename2);
                }
                if (array_key_exists('photo3', $detail)) {
                    $newFilename3 = str_replace('/', '-', $request->wo_number) . '-photo3_det' . $number . '.' . $detail['photo3']->getClientOriginalExtension();
                    // Storage::putFileAs('public', $detail['photo3'], $newFilename3);
                }

                $spongeDetail = new SpongeDetail([
                    'wo_number_id'          => $spongeHeader->id,
                    'location_id'           => $detail['location'],
                    'device_id'             => $detail['device'],
                    'disturbance_category'  => $detail['disturbance_category'],
                    'wo_description'        => $detail['description'],
                    'wo_attachment1'        => 'public/' . $newFilename1,
                    'wo_attachment2'        => 'public/' . $newFilename2,
                    'wo_attachment3'        => 'public/' . $newFilename3,
                    'start_at'              => null,
                    'estimated_end'         => null,
                    'close_at'              => $closeAt,
                    'created_by'            => Auth::user()->id,
                    'created_at'            => Carbon::now()->timezone('Asia/Jakarta'),
                    'updated_by'            => Auth::user()->id,
                    'updated_at'            => Carbon::now()->timezone('Asia/Jakarta'),
                ]);
                $spongeDetail->save();

                $spongeDetailHist = new SpongeDetailHist([
                    'sponge_detail_id'      => $spongeDetail->id,
                    'wo_number_id'          => $spongeHeader->id,
                    'location_id'           => $detail['location'],
                    'device_id'             => $detail['device'],
                    'disturbance_category'  => $detail['disturbance_category'],
                    'wo_description'        => $detail['description'],
                    'wo_attachment1'        => 'public/' . $newFilename1,
                    'wo_attachment2'        => 'public/' . $newFilename2,
                    'wo_attachment3'        => 'public/' . $newFilename3,
                    'start_at'              => null,
                    'estimated_end'         => null,
                    'close_at'              => $closeAt,
                    'action'                => 'CREATE',
                    'created_by'            => Auth::user()->id,
                    'created_at'            => Carbon::now()->timezone('Asia/Jakarta'),
                    'updated_by'            => Auth::user()->id,
                    'updated_at'            => Carbon::now()->timezone('Asia/Jakarta'),
                ]);
                $spongeDetailHist->save();

                if (array_key_exists('photo1', $detail)) {
                    // $newFilename1 = str_replace('/', '-', $request->wo_number) . '-photo1_det' . $number . '.' . $detail['photo1']->getClientOriginalExtension();
                    Storage::putFileAs('public', $detail['photo1'], $newFilename1);
                }
                if (array_key_exists('photo2', $detail)) {
                    // $newFilename2 = str_replace('/', '-', $request->wo_number) . '-photo2_det' . $number . '.' . $detail['photo2']->getClientOriginalExtension();
                    Storage::putFileAs('public', $detail['photo2'], $newFilename2);
                }
                if (array_key_exists('photo3', $detail)) {
                    // $newFilename3 = str_replace('/', '-', $request->wo_number) . '-photo3_det' . $number . '.' . $detail['photo3']->getClientOriginalExtension();
                    Storage::putFileAs('public', $detail['photo3'], $newFilename3);
                }
                $number++;
            }

            $getRoleApprove = Role::whereIn('role', ['SPV', 'SUPERADMIN'])
                ->where('authority', 'APPROVE')
                ->pluck('user_id')
                ->toArray();

            $recipientIds = User::whereIn('id', $getRoleApprove)
                ->where('department_id', $request->department)
                ->pluck('id')
                ->toArray();

            $description = 'Permintaan persetujuan untuk working order ' . $request->wo_number . '. Dibuat oleh ' . Auth::user()->name . ', pada  ' . Carbon::now()->timezone('Asia/Jakarta');
            $url = route('form-input.approval.detail', ['id' => $spongeHeader->id]);
            $createNotif = Notification::createNotification($recipientIds, $description, $url);

            if (!$createNotif['success']) {
                DB::rollback();
                return response()->json([
                    'errors' => true,
                    "message" => '<div class="alert alert-danger"> Terjadi kesalahan, Notification error</div>'
                ]);
            }
            DB::commit();

            // if (array_key_exists('photo1', $detail)) {

            // }
            // if (array_key_exists('photo2', $detail)) {

            // }
            // if (array_key_exists('photo3', $detail)) {

            // }
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

    public function checkDetail($id)
    {
        $spongeheader = SpongeHeader::find($id);
        if (!$spongeheader) {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger"> Server Error : Data tidak ditemukan. Silahkan muat ulang halaman atau hubungi admin.</div>'
            ]);
        }
        $spongedetails = SpongeDetail::where('wo_number_id', $spongeheader->id)->get();
        if (empty($spongedetails->toArray())) {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger"> Server Error : Detail data tidak ditemukan. Silahkan muat ulang halaman atau hubungi admin.</div>'
            ]);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function detail($id)
    {
        $spongeheader = SpongeHeader::find($id);
        if (!$spongeheader) {
            return back()->with('status', '<div class="alert alert-success"> Data tidak ditemukan. Silahkan coba lagi atau hubungi admin.</div>');
        }
        $spongedetails = SpongeDetail::where('wo_number_id', $spongeheader->id)->get();
        if (empty($spongedetails->toArray())) {
            return back()->with('status', '<div class="alert alert-success"> Detail data tidak ditemukan. Silahkan coba lagi atau hubungi admin.</div>');
        }
        $job_category = $spongeheader->job_category;

        $details = [];
        $index = 1;
        foreach ($spongedetails as $detail) {
            //$device = Device::find($detail->device_id);
            $engineer = '';
            if ($detail->job_executor) {
                $user = User::find($detail->job_executor);
                if ($user) {
                    $engineer = $user->name;
                }
            }
            $supervisor = '';
            if ($detail->job_supervisor) {
                $user = User::find($detail->job_supervisor);
                if ($user) {
                    $supervisor = $user->name;
                }
            }
            $aid = '';
            if ($detail->job_aid) {
                $user = User::find($detail->job_aid);
                if ($user) {
                    $aid = $user->name;
                }
            }
            $details[$index] = [
                'location' => Location::find($detail->location_id) ? Location::find($detail->location_id)->location : '',
                'disturbance_category' => DeviceCategory::find($detail->disturbance_category) ? DeviceCategory::find($detail->disturbance_category)->disturbance_category : '-',
                'description' => $detail->wo_description,
                'image_path1' => $detail->wo_attachment1,
                'image_path2' => $detail->wo_attachment2,
                'image_path3' => $detail->wo_attachment3,
                'device' =>  Device::find($detail->device_id) ?  Device::find($detail->device_id)->device_name : '-',
                'device_model' =>  Device::find($detail->device_id) ?  Device::find($detail->device_id)->brand : '-',
                'device_code' => Device::find($detail->device_id) ?  Device::find($detail->device_id)->activa_number : '-',
                'start_effective' => $detail->start_at ? Carbon::createFromFormat("Y-m-d H:i:s", $detail->start_at)->format('d/m/Y') : null,
                'estimated_end' => $detail->estimated_end ? Carbon::createFromFormat("Y-m-d H:i:s", $detail->estimated_end)->format('d/m/Y') : null,
                'engineer' => $engineer,
                'supervisor' => $supervisor,
                'aid' => $aid,
                'job_description' => $detail->job_description,
                'job_attachment1' => $detail->job_attachment1,
                'job_attachment2' => $detail->job_attachment2,
                'job_attachment3' => $detail->job_attachment3,
                'wp_number' => $detail->wp_number,
                'engineer_status' => $detail->executor_progress,
                'executor_desc' => $detail->executor_desc,
            ];
            $index++;
        }

        $data = [
            'spk_number' => $spongeheader->spk_number,
            'wo_number' => $spongeheader->wo_number,
            'wo_category' => $spongeheader->wo_category,
            'department' => Department::find($spongeheader->department_id) ? Department::find($spongeheader->department_id)->department : 'ID department tidak ditemukan : ' . $spongeheader->department_id,
            'job_category' => $job_category,
            'effective_date' => Carbon::createFromFormat("Y-m-d H:i:s", $spongeheader->effective_date)->format('d/m/Y'),
            'details' => $details,
        ];

        return view('forms.working_order.detail', $data);
    }
}

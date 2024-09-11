<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Device;
use App\Models\DeviceCategory;
use App\Models\GeneralCode;
use App\Models\Job;
use App\Models\Location;
use App\Models\Role;
use App\Models\SpongeDetail;
use App\Models\SpongeHeader;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;

class ReportController extends Controller {
    public function index() {
        $user_id = Auth::user()->id;
        $roles = Role::where('user_id', $user_id)->where('active', 1)->distinct()->pluck('role')->toArray();
        $access_right = array('SUPERADMIN', 'SPV', 'ENGINEER', 'USER');

        $getDataFilter = $this->getDataFilter();

        $woNumber           = $getDataFilter['woNumber'];
        $spkNumber          = $getDataFilter['spkNumber'];
        $woCategory         = $getDataFilter['woCategory'];
        $department         = $getDataFilter['department'];
        $location           = $getDataFilter['location'];
        $workOrderStatus    = $getDataFilter['workOrderStatus'];
        $engineerStatus     = $getDataFilter['engineerStatus'];

        if (count(array_intersect($roles, $access_right)) == 0) {
            return redirect()->route('home');
        }

        $array = [
            'woNumber'          => $woNumber,
            'spkNumber'         => $spkNumber,
            'woCategory'        => $woCategory,
            'department'        => $department,
            'location'          => $location,
            'workOrderStatus'   => $workOrderStatus,
            'engineerStatus'    => $engineerStatus,
        ];

        // Mengirim data ke view
        return view('reports.report_index', $array);
    }

    public function getDataSpongeHeader($request, $isExcel = '') {
        try {
            if ($isExcel === "") {
                session([
                    'report' . '.wo_number' => $request->has('wo_number') ?  $request->input('wo_number') : '',
                    'report' . '.spk_number' => $request->has('spk_number') ?  $request->input('spk_number') : '',
                    'report' . '.effective_date_start' => $request->has('effective_date_start') ?  $request->input('effective_date_start') : '',
                    'report' . '.effective_date_end' => $request->has('effective_date_end') ?  $request->input('effective_date_end') : '',
                    'report' . '.wo_category' => $request->has('wo_category') ?  $request->input('wo_category') : '',
                    'report' . '.job_category' => $request->has('job_category') ?  $request->input('job_category') : '',
                    'report' . '.department' => $request->has('department') ?  $request->input('department') : '',
                    'report' . '.location' => $request->has('location') ?  $request->input('location') : '',
                    'report' . '.wo_status' => $request->has('wo_status') ?  $request->input('wo_status') : '',
                    'report' . '.engineer_status' => $request->has('engineer_status') ?  $request->input('engineer_status') : '',
                ]);
            }

            $wo_number              = session('report' . '.wo_number') != '' ? session('report' . '.wo_number') : '';
            $spk_number             = session('report' . '.spk_number') != '' ? session('report' . '.spk_number') : '';
            $effective_date_start   = session('report' . '.effective_date_start') != '' ? session('report' . '.effective_date_start') : '';
            $effective_date_end     = session('report' . '.effective_date_end') != '' ? session('report' . '.effective_date_end') : '';
            $wo_category            = session('report' . '.wo_category') != '' ? session('report' . '.wo_category') : '';
            $job_category           = session('report' . '.job_category') != '' ? session('report' . '.job_category') : '';
            $department             = session('report' . '.department') != '' ? session('report' . '.department') : '';
            $location               = session('report' . '.location') != '' ? session('report' . '.location') : '';
            $wo_status              = session('report' . '.wo_status') != '' ? session('report' . '.wo_status') : '';
            $engineer_status        = session('report' . '.engineer_status') != '' ? session('report' . '.engineer_status') : '';

            if($effective_date_start) {
                $effective_date_start2 = Carbon::parse($effective_date_start)->format('Y-m-d h:i:s');
            } else {
                $effective_date_start2 = '';
            }

            if($effective_date_end) {
                $effective_date_end2 = Carbon::parse($effective_date_end)->format('Y-m-d h:i:s');
            } else {
                $effective_date_end2 = '';
            }

            $user = Auth::user()->id;

            $userRole = Role::where('user_id', $user)
                        ->first();

            $findDepartmentId = Department::where('department', 'LIKE', "%{$department}%")
                                    ->first()->id ?? '';

            $findUserDepartmentId = User::where('id', $user)
                                    ->first()->department_id ?? '';

            $findLocationId = Location::where('location', $location)
                                ->first()->id ?? '';

//            $spongeheader = SpongeHeader::join('sponge_details', 'sponge_headers.id', 'sponge_details.wo_number_id')
//                ->select(
//                    'sponge_headers.*',
//                    'sponge_details.wo_number_id',
//                    'sponge_details.cr_number',
////                    'sponge_details.wp_number',
//                    'sponge_details.location_id',
//                    'sponge_details.device_id',
//                    'sponge_details.disturbance_category',
//                    'sponge_details.wo_description',
//                    'sponge_details.job_description',
//                    'sponge_details.job_executor',
//                    'sponge_details.job_supervisor',
//                    'sponge_details.job_aid',
//                    'sponge_details.executor_progress',
//                    'sponge_details.executor_desc',
//                    'sponge_details.wo_attachment1',
//                    'sponge_details.wo_attachment2',
//                    'sponge_details.job_attachment1',
//                    'sponge_details.job_attachment2',
//                    'sponge_details.job_attachment3',
//                    'sponge_details.start_at',
//                    'sponge_details.estimated_end',
//                    'sponge_details.close_at',
//                    'sponge_details.canceled_at'
//                )
//                ->orderBy('sponge_headers.created_at','desc');

            $spongeheader = SpongeHeader::query();

//            if (in_array($user_login, $users)){
//            if($userRole->role === 'SUPERADMIN') {
                if($wo_number) {
                    $spongeheader = $spongeheader->where('wo_number', 'LIKE',  "%{$wo_number}%");
                }

                if($wo_category) {
                    $spongeheader = $spongeheader->where('wo_category', 'LIKE',  "%{$wo_category}%");
                }

                if($spk_number) {
                    $spongeheader = $spongeheader->where('spk_number', 'LIKE',  "%{$spk_number}%");
                }

                if($job_category) {
                    $spongeheader = $spongeheader->where('job_category', 'LIKE',  "%{$job_category}%");
                }

                if($findDepartmentId === '') {
                    $spongeheader = $spongeheader->where('department_id', 'LIKE',  "%{$findDepartmentId}%");
                }

                if($wo_status) {
                    $spongeheader = $spongeheader->where('status', 'LIKE',  "%{$wo_status}%");
                }

//                if($location) {
//                    $spongeheader = $spongeheader->where('sponge_details.location_id', 'LIKE',  "%{$findLocationId}%");
//                }
//
//                if($engineer_status) {
//                    $spongeheader = $spongeheader->where('sponge_details.executor_progress', 'LIKE',  "%{$engineer_status}%");
//                }

                if (!empty($effective_date_start2) && !empty($effective_date_end2)) {
                    $spongeheader = $spongeheader->whereBetween('effective_date', [$effective_date_start2, $effective_date_end2]);
                } else if (!empty($effective_date_start2)) {
                    $spongeheader = $spongeheader->whereDate('effective_date', '>=', $effective_date_start2);
                } else if (!empty($effective_date_end2)) {
                    $spongeheader = $spongeheader->whereDate('effective_date', '<=', $effective_date_end2);
                }

//            }
//            else if ($userRole->role === 'SPV') {
////                dd('SPV');
////                $spongeheader = SpongeHeader::orderBy('created_at','desc');
//
//                if($wo_number) {
//                    $spongeheader = $spongeheader->where('sponge_headers.wo_number', 'LIKE',  "%{$wo_number}%");
//                }
//
//                if($wo_category) {
//                    $spongeheader = $spongeheader->where('sponge_headers.wo_category', 'LIKE',  "%{$wo_category}%");
//                }
//
//                if($spk_number) {
//                    $spongeheader = $spongeheader->where('sponge_headers.spk_number', 'LIKE',  "%{$spk_number}%");
//                }
//
//                if($job_category) {
//                    $spongeheader = $spongeheader->where('sponge_headers.job_category', 'LIKE',  "%{$job_category}%");
//                }
//
//                if($findDepartmentId === '') {
//                    $spongeheader = $spongeheader->where('sponge_headers.department_id', 'LIKE',  "%{$findDepartmentId}%");
//                } else {
//                    $spongeheader = $spongeheader->where('sponge_headers.department_id', 'LIKE',  "%{$findUserDepartmentId}%");
//                }
//
//                if($wo_status) {
//                    $spongeheader = $spongeheader->where('sponge_headers.status', 'LIKE',  "%{$wo_status}%");
//                }
//
////                if($location) {
////                    $spongeheader = $spongeheader->where('sponge_details.location_id', 'LIKE',  "%{$findLocationId}%");
////                }
////
////                if($engineer_status) {
////                    $spongeheader = $spongeheader->where('sponge_details.executor_progress', 'LIKE',  "%{$engineer_status}%");
////                }
//
//                if (!empty($effective_date_start2) && !empty($effective_date_end2)) {
//                    $spongeheader = $spongeheader->whereBetween('sponge_headers.effective_date', [$effective_date_start2, $effective_date_end2]);
//                } else if (!empty($effective_date_start2)) {
//                    $spongeheader = $spongeheader->whereDate('sponge_headers.effective_date', '>=', $effective_date_start2);
//                } else if (!empty($effective_date_end2)) {
//                    $spongeheader = $spongeheader->whereDate('sponge_headers.effective_date', '<=', $effective_date_end2);
//                }
//            } else if($userRole->role === 'ENGINEER') {
//                $spongeHeaderEngineer = SpongeHeader::join('sponge_details', 'sponge_headers.id', 'sponge_details.wo_number_id')
//                    ->select(
//                        'sponge_headers.*',
//                        'sponge_details.wo_number_id',
//                        'sponge_details.cr_number',
//    //                    'sponge_details.wp_number',
//                        'sponge_details.location_id',
//                        'sponge_details.device_id',
//                        'sponge_details.disturbance_category',
//                        'sponge_details.wo_description',
//                        'sponge_details.job_description',
//                        'sponge_details.job_executor',
//                        'sponge_details.job_supervisor',
//                        'sponge_details.job_aid',
//                        'sponge_details.executor_progress',
//                        'sponge_details.executor_desc',
//                        'sponge_details.wo_attachment1',
//                        'sponge_details.wo_attachment2',
//                        'sponge_details.job_attachment1',
//                        'sponge_details.job_attachment2',
//                        'sponge_details.job_attachment3',
//                        'sponge_details.start_at',
//                        'sponge_details.estimated_end',
//                        'sponge_details.close_at',
//                        'sponge_details.canceled_at'
//                    )
//                    ->orderBy('sponge_headers.created_at','desc');
//
//                if($user) {
//                    $spongeHeaderEngineer = $spongeHeaderEngineer->where('sponge_details.job_executor', $user);
//                }
//
//                if($wo_number) {
//                    $spongeHeaderEngineer = $spongeHeaderEngineer->where('sponge_headers.wo_number', 'LIKE',  "%{$wo_number}%");
//                }
//
//                if($wo_category) {
//                    $spongeHeaderEngineer = $spongeHeaderEngineer->where('sponge_headers.wo_category', 'LIKE',  "%{$wo_category}%");
//                }
//
//                if($spk_number) {
//                    $spongeHeaderEngineer = $spongeHeaderEngineer->where('sponge_headers.spk_number', 'LIKE',  "%{$spk_number}%");
//                }
//
//                if($job_category) {
//                    $spongeHeaderEngineer = $spongeHeaderEngineer->where('sponge_headers.job_category', 'LIKE',  "%{$job_category}%");
//                }
//
//                if($findDepartmentId === '') {
//                    $spongeHeaderEngineer = $spongeHeaderEngineer->where('sponge_headers.department_id', 'LIKE',  "%{$findDepartmentId}%");
//                }
//
//                if($wo_status) {
//                    $spongeHeaderEngineer = $spongeHeaderEngineer->where('sponge_headers.status', 'LIKE',  "%{$wo_status}%");
//                }
//
////                if($location) {
////                    $spongeheader = $spongeheader->where('sponge_details.location_id', 'LIKE',  "%{$findLocationId}%");
////                }
////
////                if($engineer_status) {
////                    $spongeheader = $spongeheader->where('sponge_details.executor_progress', 'LIKE',  "%{$engineer_status}%");
////                }
//
//                if (!empty($effective_date_start2) && !empty($effective_date_end2)) {
//                    $spongeHeaderEngineer = $spongeHeaderEngineer->whereBetween('sponge_headers.effective_date', [$effective_date_start2, $effective_date_end2]);
//                } else if (!empty($effective_date_start2)) {
//                    $spongeHeaderEngineer = $spongeHeaderEngineer->whereDate('sponge_headers.effective_date', '>=', $effective_date_start2);
//                } else if (!empty($effective_date_end2)) {
//                    $spongeHeaderEngineer = $spongeHeaderEngineer->whereDate('sponge_headers.effective_date', '<=', $effective_date_end2);
//                }
//            } else if($userRole->role === 'USER') {
////                dd('USER');
//
////                $spongeheader = SpongeHeader::orderBy('created_at','desc')
////                                ->where('created_by', $user);
//                if($user) {
//                    $spongeheader = $spongeheader->where('sponge_headers.created_by', $user);
//                }
//
//                if($wo_number) {
//                    $spongeheader = $spongeheader->where('sponge_headers.wo_number', 'LIKE',  "%{$wo_number}%");
//                }
//
//                if($wo_category) {
//                    $spongeheader = $spongeheader->where('sponge_headers.wo_category', 'LIKE',  "%{$wo_category}%");
//                }
//
//                if($spk_number) {
//                    $spongeheader = $spongeheader->where('sponge_headers.spk_number', 'LIKE',  "%{$spk_number}%");
//                }
//
//                if($job_category) {
//                    $spongeheader = $spongeheader->where('sponge_headers.job_category', 'LIKE',  "%{$job_category}%");
//                }
//
//                if($findDepartmentId === '') {
//                    $spongeheader = $spongeheader->where('sponge_headers.department_id', 'LIKE',  "%{$findDepartmentId}%");
//                }
//
//                if($wo_status) {
//                    $spongeheader = $spongeheader->where('sponge_headers.status', 'LIKE',  "%{$wo_status}%");
//                }
//
////                if($location) {
////                    $spongeheader = $spongeheader->where('sponge_details.location_id', 'LIKE',  "%{$findLocationId}%");
////                }
////
////                if($engineer_status) {
////                    $spongeheader = $spongeheader->where('sponge_details.executor_progress', 'LIKE',  "%{$engineer_status}%");
////                }
//
//                if (!empty($effective_date_start2) && !empty($effective_date_end2)) {
//                    $spongeheader = $spongeheader->whereBetween('sponge_headers.effective_date', [$effective_date_start2, $effective_date_end2]);
//                } else if (!empty($effective_date_start2)) {
//                    $spongeheader = $spongeheader->whereDate('sponge_headers.effective_date', '>=', $effective_date_start2);
//                } else if (!empty($effective_date_end2)) {
//                    $spongeheader = $spongeheader->whereDate('sponge_headers.effective_date', '<=', $effective_date_end2);
//                }
//            } else {
//                dd('Role not found');
//            }

//            } else {
//                $spongeheader_ongoing = SpongeHeader::whereIn('created_by', $user_login)->where('status','=','ONGOING')->orderBy('created_at','desc')
//                    ->where('wo_number', 'LIKE',  "%{$wo_number}%")
//                ;
//                $spongeheader_done = SpongeHeader::whereIn('created_by', $user_login)->where('status','=','DONE')->orderBy('created_at','desc')
//                    ->where('wo_number', 'LIKE',  "%{$wo_number}%")
//                ;
//                $spongeheader_closed = SpongeHeader::whereIn('created_by', $user_login)->where('status','=','CLOSED')->orderBy('created_at','desc')
//                    ->where('wo_number', 'LIKE',  "%{$wo_number}%")
//                ;
//                $spongeheader_cancel = SpongeHeader::whereIn('created_by', $user_login)->where('status','=','CANCEL')->orderBy('created_at','desc')
//                    ->where('wo_number', 'LIKE',  "%{$wo_number}%")
//                ;
//                $spongeheader = SpongeHeader::whereIn('created_by', $user_login)->where('status','NOT APPROVE')
//                    ->where('wo_number', 'LIKE',  "%{$wo_number}%")
//                    ->orderBy('created_at','desc')
//                    ->union($spongeheader_ongoing)
//                    ->union($spongeheader_done)
//                    ->union($spongeheader_closed)
//                    ->union($spongeheader_cancel);
//            }

//            dd(json_encode($spongeheader->get()->toArray()));
//            if($userRole->role === 'ENGINEER') {
//                return $spongeHeaderEngineer;
//            } else {
                return $spongeheader;
//            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function dataTable(Request $request) {
        $dataHeader = $this->getDataSpongeHeader($request);

        if(!$dataHeader) {
            throw new \Exception('Error');
        }

        $dataHeaderTable = DataTables::of($dataHeader)
            ->filter(function ($instance) use ($request) {
                return true;
            });

        $dataHeaderTable = $dataHeaderTable->addColumn('action', function ($item) use ($request) {
            $show_url = route('form-input.working-order.detail', ['id' => $item->id]);

            $txt = '';
            $txt .= "<a href=\"#\" onclick=\"showItem($item[id]);\" title=\"" . ucfirst(__('view')) . "\" class=\"btn btn-xs btn-secondary\"><i class=\"fa fa-eye fa-fw fa-xs\"></i></a>";

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
        return $dataHeaderTable->make(TRUE);
    }

    public function getDataFilter() {
        try {
            $woNumber = SpongeHeader::orderBy('id', 'ASC')
                ->pluck('wo_number')
                ->unique()
                ->toArray();

            $spkNumber = SpongeHeader::orderBy('id', 'ASC')
                ->pluck('spk_number')
                ->unique()
                ->toArray();

            $woCategory = Job::orderBy('id', 'ASC')
                ->pluck('wo_category')
                ->unique()
                ->toArray();

            $department = Department::where('active', 1)
                ->whereNull('end_effective')
                ->orderBy('id', 'ASC')
                ->select('department_code', 'department')
                ->get()
                ->toArray() ?: [];

            $location = Location::where('active', 1)
                ->orderBy('id', 'ASC')
                ->whereNull('end_effective')
                ->select('location','location_type')
                ->get()
                ->toArray() ?: [];

            $workOrderStatus = GeneralCode::where('section', 'SPONGE')
                                ->whereNull('end_effective')
                                ->where('label', 'STATUS_HEADER')
                                ->orderBy('id', 'ASC')
                                ->pluck('reff1')
                                ->toArray();

            $engineerStatus = GeneralCode::where('section', 'SPONGE')
                                ->whereNull('end_effective')
                                ->where('label', 'STATUS_DETAIL')
                                ->orderBy('id', 'ASC')
                                ->pluck('reff1')
                                ->toArray();

            $array = [
                'success'           => true,
                'message'           => 'Data Fetch Successfully',
                'woNumber'          => $woNumber ?? [],
                'spkNumber'         => $spkNumber ?? [],
                'woCategory'        => $woCategory ?? [],
                'department'        => $department ?? [],
                'location'          => $location ?? [],
                'workOrderStatus'   => $workOrderStatus ?? [],
                'engineerStatus'    => $engineerStatus ?? [],
            ];

            return $array;

        } catch (\Exception $e) {
            return [
                'success'   => true,
                'message'   => $e->getMessage(),
            ];
        }
    }

    public function downloadXLSX(Request $request) {
        try {
            $dataExcel = $this->getDataSpongeHeader(null, 'excel');

            if(!$dataExcel) {
                throw new \Exception('Error');
            }

//            dd(json_encode($dataExcel->get()->toArray()));

            return Excel::download(new ReportExport($dataExcel), 'TransactionReport.xlsx');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()->with('error', 'Failed to export data: ' . $e->getMessage());
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
        $spongedetails = SpongeDetail::where('wo_number_id', $spongeheader->id)->orderBy('id')->get();
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

        return view('reports.report_detail', $data);
    }
}

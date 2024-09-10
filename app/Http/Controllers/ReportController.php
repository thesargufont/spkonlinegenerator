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
        $access_right = array('SUPERADMIN', 'SPV');

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
//            if($isExcel === "") {
//                session([
//                    'working_order' . '.wo_number' => $request->has('wo_number') ?  $request->input('wo_number') : '',
//                ]);
//            }

//            $user_login = Auth::user()->id;
//            $users = Role::whereIn('role',['SPV','SUPERADMIN'])->pluck('id')->toArray();

            $wo_number    = session('working_order' . '.wo_number') !== '' ? session('working_order' . '.wo_number') : '';

//            if (in_array($user_login, $users)){
                $spongeheader_ongoing = SpongeHeader::where('status','=','ONGOING')->orderBy('created_at','desc')
                    ->where('wo_number', 'LIKE',  "%{$wo_number}%")
                ;
                $spongeheader_done = SpongeHeader::where('status','=','DONE')->orderBy('created_at','desc')
                    ->where('wo_number', 'LIKE',  "%{$wo_number}%")
                ;
                $spongeheader_closed = SpongeHeader::where('status','=','CLOSED')->orderBy('created_at','desc')
                    ->where('wo_number', 'LIKE',  "%{$wo_number}%")
                ;
                $spongeheader_cancel = SpongeHeader::where('status','=','CANCEL')->orderBy('created_at','desc')
                    ->where('wo_number', 'LIKE',  "%{$wo_number}%")
                ;
                $spongeheader = SpongeHeader::where('status','NOT APPROVE')
                    ->where('wo_number', 'LIKE',  "%{$wo_number}%")
                    ->orderBy('created_at','desc')
                    ->union($spongeheader_ongoing)
                    ->union($spongeheader_done)
                    ->union($spongeheader_closed)
                    ->union($spongeheader_cancel)
                ;
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

            return $spongeheader;
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
            $woNumber = SpongeHeader::pluck('wo_number')
                ->unique()
                ->toArray();

            $spkNumber = SpongeHeader::pluck('spk_number')
                ->unique()
                ->toArray();

            $woCategory = Job::pluck('wo_category')
                ->unique()
                ->toArray();

            $department = Department::where('active', 1)
                ->whereNull('end_effective')
                ->select('department_code', 'department')
                ->get()
                ->toArray() ?: [];

            $location = Location::where('active', 1)
                ->whereNull('end_effective')
                ->select('location','location_type')
                ->get()
                ->toArray() ?: [];

            $workOrderStatus = GeneralCode::where('section', 'SPONGE')
                                ->whereNull('end_effective')
                                ->where('label', 'STATUS_HEADER')
                                ->pluck('reff1')
                                ->toArray();

            $engineerStatus = GeneralCode::where('section', 'SPONGE')
                                ->whereNull('end_effective')
                                ->where('label', 'STATUS_DETAIL')
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
            $dataExcel = $this->getDataSpongeHeader(null);

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

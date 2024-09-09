<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\GeneralCode;
use App\Models\Job;
use App\Models\Location;
use App\Models\Role;
use App\Models\SpongeHeader;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller {
    public function index() {
        $user_id = Auth::user()->id;
        $roles = Role::where('user_id', $user_id)->where('active', 1)->distinct()->pluck('role')->toArray();
        $access_right = array('SUPERADMIN', 'SPV');

        $getDataFilter = $this->getDataFilter();

        $woNumber           = $getDataFilter['woNumber'];
        $spkNumber          = $getDataFilter['spkNumber'];
        $department         = $getDataFilter['department'];
        $location           = $getDataFilter['location'];
        $workOrderStatus    = $getDataFilter['workOrderStatus'];
        $engineerStatus     = $getDataFilter['engineerStatus'];

        if (count(array_intersect($roles, $access_right)) == 0) {
            return redirect()->route('home');
        }

        // Mengirim data ke view
        return view('reports.report_index', [
            'woNumberDropdown'  => $woNumber,
            'spkNumberDropdown' => $spkNumber,
            'department'        => $department,
            'location'          => $location,
            'workOrderStatus'   => $workOrderStatus,
            'engineerStatus'    => $engineerStatus,
        ]);
    }

    public function getDataSpongeHeader($request, $isExcel = '') {
        try {
            if($isExcel === "") {
                session([
                    'working_order' . '.wo_number' => $request->has('wo_number') ?  $request->input('wo_number') : '',
                ]);
            }

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
                ->toArray();

            $spkNumber = SpongeHeader::pluck('spk_number')
                ->toArray();

            $woCategory = Job::pluck('wo_category')
                ->toArray();

            $department = Department::where('active', 1)
                ->whereNull('end_effective')
                ->select('department_code', 'department')
                ->get()
                ->toArray();

            $location = Location::where('active', 1)
                ->whereNull('end_effective')
                ->select('location','location_type')
                ->get()
                ->toArray();

            $status = GeneralCode::where('section', 'SPONGE')
                ->whereNull('end_effective')
                ->whereNull('end_effective');

            $workOrderStatus = $status->where('label', 'STATUS_HEADER')
                ->pluck('reff1')
                ->toArray();

            $engineerStatus = $status->where('label', 'STATUS_DETAIL')
                ->pluck('reff1')
                ->toArray();

            return [
                'success'   => true,
                'message'   => 'Data Fetch Successfully',
                'woNumber'  => $woNumber ?? [],
                'spkNumber' => $spkNumber ?? [],
                'woCategory' => $woCategory ?? [],
                'department' => $department ?? [],
                'location' => $location ?? [],
                'workOrderStatus' => $workOrderStatus ?? [],
                'engineerStatus' => $engineerStatus ?? [],
            ];

        } catch (\Exception $e) {
            return [
                'success'   => true,
                'message'   => $e->getMessage(),
            ];
        }
    }

    public function downloadXLSX(Request $request) {
        try {

        } catch (\Exception $e) {

        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\DeviceCategory;
use App\Models\GeneralCode;
use App\Models\Location;
use App\Models\Role;
use App\Models\SpongeDetail;
use App\Models\SpongeHeader;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class DetailChartController extends Controller {
    public function index($label, $label_type) {
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

        return view('detail_chart.index', [
            'hidden_status'     => 'hidden',
            'return_msg'        => '',
            'access'            => $access,
            'locations'         => $locations,
            'departments'       => $departments,
            'wo_status'         => $wo_status,
            'label_type'        => $label_type,
            'label'             => $label,
        ]);
    }

    public function getData($request, $isExcel = '')
    {
//        $user = Auth::user()->id;
        $spongeheader = SpongeHeader::query();
            if($request->label_type === 'status') {
                $spongeheader = $spongeheader->where('status','LIKE', $request->label)
                                ->orderBy('created_at','desc');
            }

            if($request->label_type === 'input') {
                $spongeheader = $spongeheader->where('wo_category','LIKE', $request->label)
                    ->orderBy('created_at','desc');
            }

            if($request->label_type === 'gangguan') {
                $deviceCategory = DeviceCategory::where('disturbance_category', $request->label)
                                    ->first();

                $spongeDetail = SpongeDetail::where('disturbance_category', $deviceCategory->id)
                                    ->pluck('wo_number_id')
                                    ->toArray();

                $spongeheader = $spongeheader->whereIn('id', $spongeDetail)
                                ->orderBy('created_at','desc');
            }

            if($request->label_type === 'pekerjaan') {
                $spongeheader = $spongeheader->where('job_category','LIKE', $request->label)
                    ->orderBy('created_at','desc');
            }
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
                return $item->job_category;
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
}

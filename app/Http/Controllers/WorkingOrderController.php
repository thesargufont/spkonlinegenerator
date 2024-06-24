<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use Carbon\Carbon;
use App\Models\Location;
use App\Models\LocationHist;
use App\Models\SpongeHeader;
use App\Models\SpongeDetail;
use App\Models\SpongeDetailHist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class WorkingOrderController extends Controller
{
    public function index()
    {
        return view('forms.working_order.working_order_index');
    }

    public function getData($request, $isExcel = '')
    {

        if ($isExcel == "") {
            session([
                'work_order' . '.wo_number' => $request->has('wo_number') ?  $request->input('wo_number') : '',
                'work_order' . '.wo_type' => $request->has('wo_type') ?  $request->input('wo_type') : '',
                'work_order' . '.wo_category' => $request->has('wo_category') ?  $request->input('wo_category') : '',
                'work_order' . '.disturbance_category' => $request->has('disturbance_category') ?  $request->input('disturbance_category') : '',
                'work_order' . '.department' => $request->has('department') ?  $request->input('department') : '',
                'work_order' . '.location' => $request->has('location') ?  $request->input('location') : '',
                'work_order' . '.wo_status' => $request->has('wo_status') ?  $request->input('wo_status') : '',
                'work_order' . '.engineer_status' => $request->has('engineer_status') ?  $request->input('engineer_status') : '',
            ]);
        }

        $wo_number  = session('work_order' . '.wo_number') != '' ? session('work_order' . '.wo_number') : '';
        $wo_type         = session('work_order' . '.wo_type') != '' ? session('work_order' . '.wo_type') : '';
        $wo_category         = session('work_order' . '.wo_category') != '' ? session('work_order' . '.wo_category') : '';
        $disturbance_category         = session('work_order' . '.disturbance_category') != '' ? session('work_order' . '.disturbance_category') : '';
        $department         = session('work_order' . '.department') != '' ? session('work_order' . '.department') : '';
        $location         = session('work_order' . '.location') != '' ? session('work_order' . '.location') : '';
        $wo_status         = session('work_order' . '.wo_status') != '' ? session('work_order' . '.wo_status') : '';
        $engineer_status         = session('work_order' . '.engineer_status') != '' ? session('work_order' . '.engineer_status') : '';

        $user = Auth()->id;
        $spongeheader = SpongeHeader::where('user', $user);

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
        return view('forms.working_order.form_input');
    }

    public function submitData(Request $request)
    {
        //dd($request->all());
        foreach ($request->details as $detail) {
            //dd($detail);
            dd($detail['photo1']->getClientOriginalName());
            $name = $detail['photo1']->getClientOriginalName() . '.' . $detail['photo1']->getClientOriginalExtension();
            // if ($detail->hasFile('photo1')) {
            //     $file = $detail->file('photo1');

            //     //you also need to keep file extension as well
            //     $name = $file->getClientOriginalName() . '.' . $file->getClientOriginalExtension();


            //     //using array instead of object
            //     // $image['filePath'] = $name;
            //     // $file->move(public_path() . '/uploads/', $name);
            // }
        }
    }
}

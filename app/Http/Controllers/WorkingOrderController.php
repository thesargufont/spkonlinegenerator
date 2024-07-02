<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\User;
use Exception;
use Carbon\Carbon;
use App\Models\Job;
use App\Models\Device;
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

        $user = Auth::user()->id;
        $spongeheader = SpongeHeader::where('created_by', $user);

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
        $department_arr = Department::select('id', 'department', 'department_code')->where('active', 1)->get();
        $wo_category_arr = Job::select('wo_category')->groupBy('wo_category')->get();
        $location_arr = Location::select('id', 'location', 'location_type')->where('active', 1)->where('end_effective', null)->get();
        $device_arr = Device::select('id', 'device_name', 'barand', 'device_description', 'serial_number', 'eq_id')->where('active', 1)->where('end_effective', null)->get();

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
            $get_number = GeneralCode::where('section', 'WO_NUMBER_COUNTER')
                ->where('label', $dept_code)
                ->where('reff1', strval($month))
                ->where('reff2', strval($year))
                ->where('end_effective', null)
                ->first();
            $number = 0;
            if (!$get_number) {
                $number = 1;
            } else {
                $number = intval($get_number->reff1);
            }

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
            $job_categories = Job::select('id', 'job_category')->where('wo_category', $request->wo_category)->get();

            return response()->json(['success' => true, 'message' => '', 'job_categories' => $job_categories]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function submitData(Request $request)
    {
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
                "message" => '<div class="alert alert-success">Data berhasil disimpan</div>'
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">' . $e . '</div>'
            ]);
        }
    }
}

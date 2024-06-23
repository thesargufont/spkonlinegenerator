<?php
  
namespace App\Http\Controllers;
  
use App\User;
use Exception;
use Carbon\Carbon;
use App\Models\Location;
use App\Models\LocationHist;
use Illuminate\Http\Request;
use App\Exports\LocationExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
  
class LocationController extends Controller
{
    public function index()
    {
        return view('masters.location.location_index');
    }

    public function getData($request,$isExcel='')
    {
        
        if($isExcel == "")
        {
            session([
                    'department'.'.location_name' => $request->has('location_name')?  $request->input('location_name') : '',
                    'department'.'.status' => $request->has('status')?  $request->input('status'): '', 
            ]);
        } 

        $location_name  = session('department'.'.location_name')!=''?session('department'.'.location_name'):'';
        $status         = session('department'.'.status')!=''?session('department'.'.status'):'';

        $location_name  = strtoupper($location_name);
        $status         = strtoupper($status);

        $locationDatas = Location::where('active', $status);
        
        if($location_name != ''){
            $locationDatas = $locationDatas->where('location', $location_name);
        }
        
        return $locationDatas;
    }

    public function data(Request $request)
    {
        $datas = $this->getData($request);

        $datatables = DataTables::of($datas)
            ->filter(function($instance) use ($request) {
                return true;
            });
        
        $datatables = $datatables->addColumn('action', function($item) use ($request){
            $txt = '';
            $txt .= "<a href=\"#\" onclick=\"showItem('$item[id]');\" title=\"" . ucfirst(__('view')) . "\" class=\"btn btn-xs btn-secondary\"><i class=\"fa fa-eye fa-fw fa-xs\"></i></a>";
            $txt .= "<a href=\"#\" onclick=\"editItem($item[id]);\" title=\"" . ucfirst(__('edit')) . "\" class=\"btn btn-xs btn-secondary\"><i class=\"fa fa-edit fa-fw fa-xs\"></i></a>";
            $txt .= "<a href=\"#\" onclick=\"deleteItem($item[id]);\" title=\"" . ucfirst(__('delete')) . "\" class=\"btn btn-xs btn-secondary\"><i class=\"fa fa-trash fa-fw fa-xs\"></i></a>";

            return $txt;
        })
        ->addColumn('active', function ($item) {
            if($item->active == 1){
                return 'AKTIF';
            } else {
                return 'TIDAK AKTIF';
            }
        })
        ->editColumn('start_effective', function ($item) {
            return Carbon::createFromFormat("Y-m-d H:i:s", $item->start_effective)->format('d/m/Y');
        })
        ->editColumn('end_effective', function ($item) {
            if($item->end_effective == null){
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
        return view('masters.location.form_input');
    }

    public function submitData(Request $request)
    {
        $locationtName  = strtoupper($request->location_name);
        $description    = strtoupper($request->description);
        $locationType   = strtoupper($request->location_type);
        $addresss       = strtoupper($request->addresss);

        if($locationtName == ''){
            return response()->json(['errors' => true, 
                            "message"=> '<div class="alert alert-danger">Nama lokasi wajib terisi, harap periksa kembali formulir pengisian data</div>'
                    ]);    
        }
        
        if($description == ''){
            return response()->json(['errors' => true, 
                            "message"=> '<div class="alert alert-danger">Deskripsi wajib terisi, harap periksa kembali formulir pengisian data</div>'
                    ]);    
        }

        $checkDuplicateData = Location::where('location', $locationtName)
                                        ->where('active', 1)
                                        ->first();
                                             
        if($checkDuplicateData){
            return response()->json(['errors' => true, 
                            "message"=> '<div class="alert alert-danger">Telah ditemukan data lokasi '.$locationtName.' yang masih aktif</div>'
                    ]);    
        }

        try {
            // CREATE DATA 
            DB::beginTransaction();
            
            $insertLocation = new Location([
                'location'                => $locationtName, 
                'location_description'    => $description,
                'location_type'           => $locationType,
                'address'                 => $addresss,
                'code'                    => '',
                'sub_district'            => '',
                'district'                => '',
                'city'                    => '',
                'province'                => '',
                'country'                 => '',
                'active'                  => 1,
                'start_effective'         => Carbon::now(),
                'end_effective'           => null,
                'created_by'              => Auth::user()->id,
                'created_at'              => Carbon::now(),
                'updated_by'              => Auth::user()->id,
                'updated_at'              => Carbon::now(),
            ]);
            $insertLocation->save();

            $insertLocationHist = new LocationHist([
                'location_id'             => $insertLocation->id,
                'location'                => $insertLocation->location,
                'location_description'    => $insertLocation->location_description,
                'location_type'           => $insertLocation->location_type,
                'address'                 => $insertLocation->address,
                'code'                    => $insertLocation->code,
                'sub_district'            => $insertLocation->sub_district,
                'district'                => $insertLocation->district,
                'city'                    => $insertLocation->city,
                'province'                => $insertLocation->province,
                'country'                 => $insertLocation->country,
                'active'                  => $insertLocation->active,
                'start_effective'         => $insertLocation->start_effective,
                'end_effective'           => $insertLocation->end_effective,
                'action'                  => 'CREATE',
                'created_by'              => Auth::user()->id,
                'created_at'              => Carbon::now(),
            ]);
            $insertLocationHist->save();

            DB::commit();
            return response()->json(['success' => true, 
                            "message"=> '<div class="alert alert-success">Data lokasi disimpan</div>'
                    ]);   
        } catch (Exception $e){
            DB::rollback();
            return response()->json(['errors' => true, 
                            "message"=> '<div class="alert alert-danger">Telah terjadi kesalahan sistem, data gagal diproses</div>'
                    ]);       
        }
    }

    public function exportExcel()
    {
        $datas = $this->getData(null, 'excel');
        return Excel::download(new LocationExport($datas), 'LocationMaster.xlsx');
    }
}
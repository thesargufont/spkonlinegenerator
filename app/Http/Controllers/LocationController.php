<?php
  
namespace App\Http\Controllers;
  
use App\User;
use Exception;
use Carbon\Carbon;
use App\Models\Basecamp;
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
        $basecamps = Basecamp::where('active', 1)->get();

        return view('masters.location.location_index', [
            'basecamps' => $basecamps,
        ]);
    }

    public function getData($request,$isExcel='')
    {
        
        if($isExcel == "")
        {
            session([
                    'department'.'.location_name' => $request->has('location_name')?  $request->input('location_name') : '',
                    'department'.'.basecamp' => $request->has('basecamp')?  $request->input('basecamp'): '', 
                    'department'.'.status' => $request->has('status')?  $request->input('status'): '', 
            ]);
        } 

        $location_name  = session('department'.'.location_name')!=''?session('department'.'.location_name'):'';
        $basecamp       = session('department'.'.basecamp')!=''?session('department'.'.basecamp'):'';
        $status         = session('department'.'.status')!=''?session('department'.'.status'):'';

        $location_name  = strtoupper($location_name);
        $basecamp       = strtoupper($basecamp);
        $status         = strtoupper($status);

        $locationDatas = Location::where('active', $status);
        
        if($basecamp != ''){
            $locationDatas = $locationDatas->where('basecamp_id', $basecamp);
        }

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
        ->addColumn('basecamp', function ($item) {
            return optional($item->basecamp)->basecamp;    
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
        $basecamps = Basecamp::where('active', 1)->get();

        return view('masters.location.form_input', [
            'basecamps' => $basecamps,
        ]);
    }

    public function submitData(Request $request)
    {
        $locationtName  = strtoupper($request->location_name);
        $description    = strtoupper($request->description);
        $locationType   = strtoupper($request->location_type);
        $basecamp       = strtoupper($request->basecamp);
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

        if($basecamp == ''){
            return response()->json(['errors' => true, 
                            "message"=> '<div class="alert alert-danger">Basecamp wajib terisi, harap periksa kembali formulir pengisian data</div>'
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
                'basecamp_id'             => $basecamp,
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
                'basecamp_id'             => $insertLocation->basecamp_id,
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

    public function deleteData(Request $request)
    {
        $location = Location::where('id', $request->id)->first();
        
        try {
            DB::beginTransaction();
            if($location){
                $location->active         = 0;
                $location->end_effective  = Carbon::now();
                $location->updated_by     = Auth::user()->id;
                $location->updated_at     = Carbon::now();
                $location->save();

                $insertLocationHist = new LocationHist([
                    'location_id'             => $location->id,
                    'location'                => $location->location,
                    'location_description'    => $location->location_description,
                    'location_type'           => $location->location_type,
                    'basecamp_id'             => $location->basecamp_id,
                    'address'                 => $location->address,
                    'code'                    => $location->code,
                    'sub_district'            => $location->sub_district,
                    'district'                => $location->district,
                    'city'                    => $location->city,
                    'province'                => $location->province,
                    'country'                 => $location->country,
                    'active'                  => $location->active,
                    'start_effective'         => $location->start_effective,
                    'end_effective'           => $location->end_effective,
                    'action'                  => 'UPDATE',
                    'created_by'              => Auth::user()->id,
                    'created_at'              => Carbon::now(),
                ]);
                $insertLocationHist->save();

                DB::commit();
                return response()->json([
                    'success' => true,
                    "message" => '<div class="alert alert-success">Data lokasi berhasil dihapus, status : TIDAK AKTIF</div>'
                ]);
            } else {
                return response()->json([
                    'errors' => true,
                    "message" => '<div class="alert alert-danger">Data gagal di proses, data lokasi tidak ditemukan</div>'
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
        $location = Location::where('id', $id)->first();
        if($location){
            if($location->active == 1){
                $active = 'AkTIF';
            } else {
                $active = 'TIDAK AkTIF';
            }
            return view('masters.location.form_detail', [
                'location'             => $location->location,
                'location_description' => $location->location_description != '' ? $location->location_description : '-',
                'location_type'        => $location->location_type != '' ? $location->location_type : '-',
                'basecamp'             => optional($location->basecamp)->basecamp,
                'address'              => $location->address != '' ? $location->address : '-',
                'active'               => $active,
                'start_effective'      => $location->start_effective != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $location->start_effective)->format('d/m/Y H:i:s') : '-',
                'end_effective'        => $location->end_effective != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $location->end_effective)->format('d/m/Y H:i:s') : '-',
                'created_by'           => optional($location->createdBy)->name,
                'created_at'           => $location->created_at != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $location->created_at)->format('d/m/Y H:i:s') : '-',
                'updated_by'           => optional($location->updatedBy)->name,
                'updated_at'           => $location->updated_at != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $location->updated_at)->format('d/m/Y H:i:s') : '-',
            ]);
        } else {
            return view('masters.location.form_detail', [
                'location'             => '',
                'location_description' => '',
                'location_type'        => '',
                'basecamp'             => '',
                'address'              => '',
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
}
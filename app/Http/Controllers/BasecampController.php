<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use Carbon\Carbon;
use App\Models\Basecamp;
use App\Models\Department;
use App\Models\BasecampHist;
use Illuminate\Http\Request;
use App\Models\DepartmentHist;
use App\Exports\DepartmentExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Schema;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Schema\Blueprint;

class BasecampController extends Controller
{
    public function index()
    {
        return view('masters.basecamp.basecamp_index');
    }

    public function getData($request, $isExcel = '')
    {

        if ($isExcel == "") {
            session([
                'basecamp' . '.basecamp_name' => $request->has('basecamp_name') ?  $request->input('basecamp_name') : '',
                'basecamp' . '.status' => $request->has('status') ?  $request->input('status') : '',
            ]);
        }

        $basecamp_name  = session('basecamp' . '.basecamp_name') != '' ? session('basecamp' . '.basecamp_name') : '';
        $status           = session('basecamp' . '.status') != '' ? session('basecamp' . '.status') : '';

        $basecamp_name  = strtoupper($basecamp_name);
        $status           = strtoupper($status);

        $basecampDatas = Basecamp::where('active', $status);

        if ($basecamp_name != '') {
            $basecampDatas = $basecampDatas->where('basecamp', $basecamp_name);
        }

        return $basecampDatas;
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
            // $txt .= "<a href=\"#\" onclick=\"editItem($item[id]);\" title=\"" . ucfirst(__('edit')) . "\" class=\"btn btn-xs btn-secondary\"><i class=\"fa fa-edit fa-fw fa-xs\"></i></a>";
            $txt .= "<a href=\"#\" onclick=\"deleteItem($item[id]);\" title=\"" . ucfirst(__('delete')) . "\" class=\"btn btn-xs btn-secondary\"><i class=\"fa fa-trash fa-fw fa-xs\"></i></a>";

            return $txt;
        })
            ->addColumn('active', function ($item) {
                if ($item->active == 1) {
                    return 'YA';
                } else {
                    return 'TIDAK';
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
        return view('masters.basecamp.form_input');
    }

    public function submitData(Request $request)
    {
        $basecampName = strtoupper($request->basecamp_name);
        $description = strtoupper($request->description);

        if ($basecampName == '') {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Nama basecamp wajib terisi, harap periksa kembali formulir pengisian data</div>'
            ]);
        }

        if ($description == '') {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Deskripsi wajib terisi, harap periksa kembali formulir pengisian data</div>'
            ]);
        }

        $checkDuplicateData = Basecamp::where('basecamp', $basecampName)
            ->where('active', 1)
            ->first();

        if ($checkDuplicateData) {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Telah ditemukan data basecamp ' . $basecampName . ' yang masih aktif</div>'
            ]);
        }

        try {
            // CREATE DATA 
            DB::beginTransaction();

            $insertBasecamp = new Basecamp([
                'basecamp'              => $basecampName,
                'basecamp_description'  => $description,
                'active'                => 1,
                'start_effective'       => Carbon::now()->timezone('Asia/Jakarta'),
                'end_effective'         => null,
                'created_by'            => Auth::user()->id,
                'created_at'            => Carbon::now()->timezone('Asia/Jakarta'),
                'updated_by'            => Auth::user()->id,
                'updated_at'            => Carbon::now()->timezone('Asia/Jakarta'),
            ]);
            $insertBasecamp->save();

            $insertbasecampHist = new BasecampHist([
                'basecamp_id'           => $insertBasecamp->id,
                'basecamp'              => $insertBasecamp->basecamp,
                'basecamp_description'  => $insertBasecamp->basecamp_description,
                'active'                => $insertBasecamp->active,
                'start_effective'       => $insertBasecamp->start_effective,
                'end_effective'         => $insertBasecamp->end_effective,
                'action'                => 'CREATE',
                'created_by'            => Auth::user()->id,
                'created_at'            => Carbon::now()->timezone('Asia/Jakarta'),
            ]);
            $insertbasecampHist->save();

            DB::commit();
            return response()->json([
                'success' => true,
                "message" => '<div class="alert alert-success">Data berhasil disimpan</div>'
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Telah terjadi kesalahan sistem, data gagal diproses</div>'
            ]);
        }
    }

    public function deleteData(Request $request)
    {
        $basecamp = Basecamp::where('id', $request->id)->first();

        try {
            DB::beginTransaction();
            if ($basecamp) {
                $basecamp->active         = 0;
                $basecamp->end_effective  = Carbon::now()->timezone('Asia/Jakarta');
                $basecamp->updated_by     = Auth::user()->id;
                $basecamp->updated_at     = Carbon::now()->timezone('Asia/Jakarta');
                $basecamp->save();

                $insertbasecampHist = new BasecampHist([
                    'basecamp_id'           => $basecamp->id,
                    'basecamp'              => $basecamp->basecamp,
                    'basecamp_description'  => $basecamp->basecamp_description,
                    'active'                => $basecamp->active,
                    'start_effective'       => $basecamp->start_effective,
                    'end_effective'         => $basecamp->end_effective,
                    'action'                => 'UPDATE',
                    'created_by'            => Auth::user()->id,
                    'created_at'            => Carbon::now()->timezone('Asia/Jakarta'),
                ]);
                $insertbasecampHist->save();

                DB::commit();
                return response()->json([
                    'success' => true,
                    "message" => '<div class="alert alert-success">Data basecamp berhasil dihapus, status : TIDAK AKTIF</div>'
                ]);
            } else {
                return response()->json([
                    'errors' => true,
                    "message" => '<div class="alert alert-danger">Data gagal di proses, data basecamp tidak ditemukan</div>'
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
        $basecamp = Basecamp::where('id', $id)->first();
        if ($basecamp) {
            if ($basecamp->active == 1) {
                $active = 'AkTIF';
            } else {
                $active = 'TIDAK AkTIF';
            }
            return view('masters.basecamp.form_detail', [
                'basecamp'             => $basecamp->basecamp,
                'basecamp_description' => $basecamp->basecamp_description != '' ? $basecamp->basecamp_description : '-',
                'active'               => $active,
                'start_effective'      => $basecamp->start_effective != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $basecamp->start_effective)->format('d/m/Y H:i:s') : '-',
                'end_effective'        => $basecamp->end_effective != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $basecamp->end_effective)->format('d/m/Y H:i:s') : '-',
                'created_by'           => optional($basecamp->createdBy)->name,
                'created_at'           => $basecamp->created_at != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $basecamp->created_at)->format('d/m/Y H:i:s') : '-',
                'updated_by'           => optional($basecamp->updatedBy)->name,
                'updated_at'           => $basecamp->updated_at != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $basecamp->updated_at)->format('d/m/Y H:i:s') : '-',
            ]);
        } else {
            return view('masters.basecamp.form_detail', [
                'basecamp'             => '',
                'basecamp_description' => '',
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

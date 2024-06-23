<?php
  
namespace App\Http\Controllers;
  
use App\User;
use Exception;
use Carbon\Carbon;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\DepartmentHist;
use App\Exports\DepartmentExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use Yajra\DataTables\Facades\DataTables;
  
class DepartmentController extends Controller
{
    public function index()
    {
        return view('masters.department.department_index');
    }

    public function createNew()
    {
        return view('masters.department.form_input');
    }

    public function submitData(Request $request)
    {
        $departmentName = strtoupper($request->department_name);
        $description = strtoupper($request->description);

        if($departmentName == ''){
            return response()->json(['errors' => true, 
                            "message"=> '<div class="alert alert-danger">Nama bagian wajib terisi, harap periksa kembali formulir pengisian data</div>'
                    ]);    
        }
        
        if($description == ''){
            return response()->json(['errors' => true, 
                            "message"=> '<div class="alert alert-danger">Deskripsi wajib terisi, harap periksa kembali formulir pengisian data</div>'
                    ]);    
        }

        $checkDuplicateData = Department::where('department', $departmentName)
                                        ->where('active', 1)
                                        ->first();
                                             
        if($checkDuplicateData){
            return response()->json(['errors' => true, 
                            "message"=> '<div class="alert alert-danger">Telah ditemukan data bagian '.$departmentName.' yang masih aktif</div>'
                    ]);    
        }

        try {
            // CREATE DATA 
            DB::beginTransaction();
            
            $insertDepartment = new Department([
                'department'              => $departmentName,
                'department_description'  => $description,
                'active'                  => 1,
                'start_effective'         => Carbon::now(),
                'end_effective'           => null,
                'created_by'              => Auth::user()->id,
                'created_at'              => Carbon::now(),
                'updated_by'              => Auth::user()->id,
                'updated_at'              => Carbon::now(),
            ]);
            $insertDepartment->save();

            $insertDepartmentHist = new DepartmentHist([
                'department_id'           => $insertDepartment->id,
                'department'              => $insertDepartment->department,
                'department_description'  => $insertDepartment->department_description,
                'active'                  => $insertDepartment->active,
                'start_effective'         => $insertDepartment->start_effective,
                'end_effective'           => $insertDepartment->end_effective,
                'action'                  => 'CREATE',
                'created_by'              => Auth::user()->id,
                'created_at'              => Carbon::now(),
            ]);
            $insertDepartmentHist->save();

            DB::commit();
            return response()->json(['success' => true, 
                            "message"=> '<div class="alert alert-success">Data berhasil disimpan</div>'
                    ]);   
        } catch (Exception $e){
            DB::rollback();
            return response()->json(['errors' => true, 
                            "message"=> '<div class="alert alert-danger">Telah terjadi kesalahan sistem, data gagal diproses</div>'
                    ]);       
        }
    }

    public function getData($request,$isExcel='')
    {
        
        if($isExcel == "")
        {
            session([
                    'department'.'.department_name' => $request->has('department_name')?  $request->input('department_name') : '',
                    'department'.'.status' => $request->has('status')?  $request->input('status'): '', 
            ]);
        } 

        $department_name  = session('department'.'.department_name')!=''?session('department'.'.department_name'):'';
        $status           = session('department'.'.status')!=''?session('department'.'.status'):'';

        $department_name  = strtoupper($department_name);
        $status           = strtoupper($status);

        $departmentDatas = Department::where('active', $status);
        
        if($department_name != ''){
            $departmentDatas = $departmentDatas->where('department', $department_name);
        }
        
        return $departmentDatas;
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

    public function exportExcel()
    {
        $datas = $this->getData(null, 'excel');
        return Excel::download(new DepartmentExport($datas), 'DepartmentMaster.xlsx');
    }

    public function importExcel()
    {
        return view('masters.department.upload');
    }

    public function uploadDepartment(Request $request)
    {
        $countError = 0;
        $success   = false;
        if($request->hasfile('validatedCustomFile'))
        {
            $name = $request->file('validatedCustomFile')->getClientOriginalName();
            $filename = $name;
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            if (strtolower($ext) != 'xlsx') {
                $filename = "";
                $message = '<div class="alert alert-danger">format file tidak sesuai</div>';
                // $error    = ucfirst(__('format file tidak sesuai'));
                $success    = false;
                return response()->json(['filename'    => $filename,
                                         'message'    => $message,
                                         'success'    => $success,
                                        ]);
            }

            $extension = $request->file('validatedCustomFile')->getClientOriginalExtension();
            
            $name = "Department" . "_" . Auth::user()->id . "." . $extension;
            $request->file('validatedCustomFile')->move(storage_path().'/app/uploads/', $name);  
            $attachments = storage_path().'/app/uploads/'. $name; 

            $data = (new FastExcel)->import($attachments);

            foreach ($data as $row) {
                $error = 0;

                $department              = trim(strtoupper($row['Nama Bidang']), ' ');
                $department_description  = trim(strtoupper($row['Deskripsi']), ' ');
                
                $checkDuplicateData = Department::where('department', $department)
                                        ->where('active', 1)
                                        ->first();

                if($checkDuplicateData){
                    $error++;
                }
                
                if($department_description == ''){
                    $error++;
                }

                if($error > 0){
                    $countError++;
                }
            }

            if($countError > 0){
                $success   = false;
                $message   = '<div class="alert alert-danger">Terdapat data error, harap periksa kembali file '.$filename.'</div>';
            } else {
                $success   = true;
                $message   = '<div class="alert alert-success">Validasi data berhasil, data dapat disimpan</div>';
            }

            return response()->json(['filename'  => $filename,
                                     'success'  => $success,
                                     'message'  => $message,
                                    ]);
        } else {
            $message   = '<div class="alert alert-danger">Pilih file...</div>';
            return response()->json(['filename'  => '',
                                     'success'  => $success,
                                     'message'  => $message,
                                    ]);
        }
    }

    public function downloadDepartmentTemplate()
    {
        $filename = 'Template_Master_Bidang.xlsx';
        return response()->download(storage_path('app/files/'.$filename));
    }
}
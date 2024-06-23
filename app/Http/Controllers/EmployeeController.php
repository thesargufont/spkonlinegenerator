<?php
  
namespace App\Http\Controllers;
  
use App\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
  
class EmployeeController extends Controller
{
    public function index()
    {
        return view('masters.employee.employee_index');
    }

    public function getData($request,$isExcel='')
    {
        $employee_name = strtoupper($request->employee_name);
        $employee_name_id = strtoupper($request->employee_name_id);
        $status = strtoupper($request->status);

        $employeeDatas = User::where('remember_token',null);
        if($employee_name != ''){
            $employeeDatas = $employeeDatas->where('name',$employee_name);
        } 

        return $employeeDatas;
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
        ->addColumn('gender', function ($item) {
            return 'DUMMY DATA';
        })
        ->addColumn('department', function ($item) {
            return 'DUMMY DATA';
        })
        ->addColumn('location', function ($item) {
            return 'DUMMY DATA';
        })
        ->addColumn('status', function ($item) {
            return 'DUMMY DATA';
        })
        ->addColumn('telephone', function ($item) {
            return 'DUMMY DATA';
        });
        return $datatables->make(TRUE);
    }
}
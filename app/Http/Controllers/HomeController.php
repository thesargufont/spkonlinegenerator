<?php
  
namespace App\Http\Controllers;
  
use Carbon\Carbon;
use App\Models\Department;
use App\Models\Notification;
use App\Models\SpongeHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
  
class HomeController extends Controller
{
    public function index()
    {
        $spongeDatas = SpongeHeader::where('status', '!=', 'NOT APPROVE');
        
        $totalReport         = clone $spongeDatas;
        $totalReportJob      = clone $spongeDatas;
        $totalReportProblem  = clone $spongeDatas;

        $totalReport        = $totalReport->count();
        $totalReportJob     = $totalReportJob->where('wo_category', 'PEKERJAAN')->count();
        $totalReportProblem = $totalReportProblem->where('wo_category', 'LAPORAN GANGGUAN')->count();

        if($totalReportJob != 0){
            $jobPercentage    = ($totalReportJob * 100) / $totalReport;
        } else {
            $jobPercentage = 0;
        }
        if($totalReportJob != 0){
            $problemPercentage = ($totalReportProblem * 100) / $totalReport;
        } else {
            $problemPercentage = 0;
        }

        // REPORT PROBLEM
        $ReportDatas = SpongeHeader::where('status', '!=', 'NOT APPROVE');
        $ReportDatasJob     = Clone $ReportDatas;
        $ReportDatasProblem = Clone $ReportDatas;

        $ReportJob     = $ReportDatasJob->where('wo_category', 'PEKERJAAN');
        $ReportProblem = $ReportDatasProblem->where('wo_category', 'LAPORAN GANGGUAN');

        $jobTlkm   = clone $ReportJob;
        $jobScd    = clone $ReportJob;
        $jobPsis   = clone $ReportJob;
        $jobUpt    = clone $ReportJob;
        $jobDspc   = clone $ReportJob;

        $deptTel = Department::where('department', 'TELEKOMUNIKASI')->first();
        $deptSca = Department::where('department', 'SCADA')->first();
        $deptPro = Department::where('department', 'PROSIS')->first();
        $deptUpt = Department::where('department', 'UPT')->first();
        $deptDis = Department::where('department', 'DISPATCHER')->first();

        $jobTlkm   = $jobTlkm->where('department_id', $deptTel->id)->count();
        $jobScd    = $jobScd->where('department_id', $deptSca->id)->count();
        $jobPsis   = $jobPsis->where('department_id', $deptPro->id)->count();
        $jobUpt    = $jobUpt->where('department_id', $deptUpt->id)->count();
        $jobDspc   = $jobDspc->where('department_id', $deptDis->id)->count();

        $jobCount = [$jobTlkm, $jobScd, $jobPsis, $jobUpt, $jobDspc];
        
        $problemTlkm  = clone $ReportProblem;
        $problemScd   = clone $ReportProblem;
        $problemPsis  = clone $ReportProblem;
        $problemUpt   = clone $ReportProblem;
        $problemDspc  = clone $ReportProblem;

        $problemTlkm  = $problemTlkm->where('department_id', $deptTel->id)->count();
        $problemScd   = $problemScd->where('department_id', $deptSca->id)->count();
        $problemPsis  = $problemPsis->where('department_id', $deptPro->id)->count();
        $problemUpt   = $problemUpt->where('department_id', $deptUpt->id)->count();
        $problemDspc  = $problemDspc->where('department_id', $deptDis->id)->count();

        $problemCount = [$problemTlkm, $problemScd, $problemPsis, $problemUpt, $problemDspc];

        return view('home', [
            'totalReport' => $totalReport,
            'totalReportJob' => $totalReportJob,
            'totalReportProblem' => $totalReportProblem,
            'jobPercentage' => $jobPercentage,
            'problemPercentage' => $problemPercentage,
            'jobCount' => $jobCount,
            'jobTlkm' => $jobTlkm,
            'jobScd' => $jobScd,
            'jobPsis' => $jobPsis,
            'jobUpt' => $jobUpt,
            'jobDspc' => $jobDspc,
            'problemCount' => $problemCount,
            'problemTlkm' => $problemTlkm,
            'problemScd' => $problemScd,
            'problemPsis' => $problemPsis,
            'problemUpt' => $problemUpt,
            'problemDspc' => $problemDspc,
        ]);
    }

    public function getData($request,$isExcel='')
    {
        $spongeDatas = SpongeHeader::where('status', '!=', '');
        return $spongeDatas;
    }

    public function data(Request $request)
    {
        $datas = $this->getData($request);
        $datatables = DataTables::of($datas)
            ->filter(function($instance) use ($request) {
                return true;
            });
        
        $datatables = $datatables
        ->addColumn('department', function ($item) {
            return optional($item->department)->department;
        })
        ->editColumn('job_category', function ($item) {
            if($item->job_category == 'null'){
                return '-';
            } else {
                return $item->job_category;
            }
        })
        ->editColumn('effective_date', function ($item) {
            return $item->effective_date != '' ?  Carbon::createFromFormat("Y-m-d H:i:s", $item->effective_date)->format('d/m/Y') : '-';
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

    public function getNotif(Request $request){
        $notificationAvailable = Notification::where('user_id', Auth::user()->id)
                                           ->where('read', 0)
                                           ->count();
        
        if(Auth::user()->name == 'SUPERADMIN'){
            $master = true;
        } else {
            $master = false;
        }
        dd($notificationAvailable, $master);
        if($notificationAvailable != 0){
            return response()->json([
                        'success' => true,
                        'master' => $master,
                        "message"=> '<div class="alert alert-danger">You have new notifications</div>'
                    ]); 
        } else {
            return response()->json([
                        'errors' => true, 
                        'master' => $master,
                        "message"=> '<div class="alert alert-danger">Notifications not found</div>'
                    ]); 
        }
    }
}
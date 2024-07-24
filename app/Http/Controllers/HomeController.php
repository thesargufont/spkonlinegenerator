<?php
  
namespace App\Http\Controllers;
  
use Carbon\Carbon;
use App\Models\SpongeHeader;
use Illuminate\Http\Request;
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
        $totalReportJob     = $totalReportJob->where('wo_type', 'PEKERJAAN')->count();
        $totalReportProblem = $totalReportProblem->where('wo_type', 'LAPORAN GANGGUAN')->count();

        $jobPercentage    = ($totalReportJob * 100) / $totalReport;
        $problemPercentage = ($totalReportProblem * 100) / $totalReport;

        // REPORT PROBLEM
        $ReportDatas = SpongeHeader::where('status', '!=', 'NOT APPROVE');
        $ReportDatasJob     = Clone $ReportDatas;
        $ReportDatasProblem = Clone $ReportDatas;

        $ReportJob     = $ReportDatasJob->where('wo_type', 'PEKERJAAN');
        $ReportProblem = $ReportDatasProblem->where('wo_type', 'LAPORAN GANGGUAN');

        $jobTlkm   = clone $ReportJob;
        $jobScd    = clone $ReportJob;
        $jobPsis   = clone $ReportJob;
        $jobUpt    = clone $ReportJob;
        $jobDspc   = clone $ReportJob;

        $jobTlkm   = $jobTlkm->where('department', 'TELEKOMUNIKASI')->count();
        $jobScd    = $jobScd->where('department', 'SCADA')->count();
        $jobPsis   = $jobPsis->where('department', 'PROSIS')->count();
        $jobUpt    = $jobUpt->where('department', 'UPT')->count();
        $jobDspc   = $jobDspc->where('department', 'DISPATCHER')->count();

        $jobCount = [$jobTlkm, $jobScd, $jobPsis, $jobUpt, $jobDspc];
        
        $problemTlkm  = clone $ReportProblem;
        $problemScd   = clone $ReportProblem;
        $problemPsis  = clone $ReportProblem;
        $problemUpt   = clone $ReportProblem;
        $problemDspc  = clone $ReportProblem;

        $problemTlkm  = $problemTlkm->where('department', 'TELEKOMUNIKASI')->count();
        $problemScd   = $problemScd->where('department', 'SCADA')->count();
        $problemPsis  = $problemPsis->where('department', 'PROSIS')->count();
        $problemUpt   = $problemUpt->where('department', 'UPT')->count();
        $problemDspc  = $problemDspc->where('department', 'DISPATCHER')->count();

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
}
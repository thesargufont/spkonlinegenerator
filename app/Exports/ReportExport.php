<?php

namespace App\Exports;

use App\Models\Department;
use App\Models\Device;
use App\Models\Location;
use App\Models\SpongeDetail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use Carbon\Carbon;

class ReportExport extends DefaultValueBinder implements WithCustomValueBinder,FromView, ShouldAutoSize, WithTitle, WithEvents
{
    use Exportable;
    /**
     * @return \Illuminate\Support\Collection
     */

    public function __construct($request = '')
    {
        $this->request = $request;
        $this->countdata = 1;
        return $this;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function (BeforeExport $event) {
                $event->writer->getProperties()->setCreator(Auth::user()->name);
                $event->writer->getProperties()->setSubject(config('app.name') . ' - ' . 'Data Laporan');
            },

            AfterSheet::class   => function (AfterSheet $event) {
                $countdata = $this->countdata + 4;
                $event->sheet->mergeCells('A1:B1');
                $event->sheet->mergeCells('C1:F1');
                $event->sheet->mergeCells('A2:B2');
                $event->sheet->mergeCells('C2:F2');
                $event->sheet->mergeCells('A3:B3');
                $event->sheet->mergeCells('C3:F3');
                $event->sheet->getStyle('A4:Y' . $countdata)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
            },
        ];
    }

    public function view(): View
    {
        $items = [];
        $counter = 0;

        // Iterate through each request data
        foreach ($this->request->cursor() as $data) {
            // Retrieve user and department data with checks
            $userCreated = User::find($data->created_by);
            $department = Department::find($data->department_id);

            // Initialize userCreated and department if not found
            $userCreatedName = $userCreated ? $userCreated->name : '';
            $departmentName = $department ? $department->department : '';

            // Retrieve sponge details and iterate through them
            $findSpongeDetail = SpongeDetail::where('wo_number_id', $data->id)->cursor();

            foreach ($findSpongeDetail as $detail) {
                // Retrieve related location, device, and users with checks
                $findLocation = Location::find($detail->location_id);
                $findDevice = Device::find($detail->device_id);
                $findUserJobExecutor = User::find($detail->job_executor);
                $findUserJobSupervisor = User::find($detail->job_supervisor);

                // Initialize values if not found
                $location = $findLocation ? $findLocation->location : '';
                $deviceName = $findDevice ? $findDevice->device_name : '';
                $engineer = $findUserJobExecutor ? $findUserJobExecutor->name : '';
                $supervisor = $findUserJobSupervisor ? $findUserJobSupervisor->name : '';

                // Prepare the item for the list
                $item = [
                    'no'                        => ++$counter,
                    'wo_number'                 => $data->wo_number ?? '',
                    'spk_number'                => $data->spk_number ?? '',
                    'ba_number'                 => $detail->cr_number ?? '',
                    'department'                => $departmentName,
                    'wo_type'                   => $data->wo_category ?? '',
                    'job_category'              => $data->job_category ?? '',
                    'location'                  => $location,
                    'device_name'               => $deviceName,
                    'disturbance_category'      => $detail->disturbance_category ?? '',
                    'wo_description'            => $detail->wo_description ?? '',
                    'job_description'           => $detail->job_description ?? '',
                    'engineer'                  => $engineer,
                    'supervisor'                => $supervisor,
                    'engineer_progress'         => $detail->executor_progress ?? '',
                    'engineer_description'      => $detail->executor_desc ?? '',
                    'status'                    => $data->status ?? '',
                    'created_by'                => $userCreatedName,
                    'effective_date'            => $data->effective_date ? Carbon::createFromFormat('Y-m-d H:i:s', $data->effective_date)->format('d/m/Y H:i:s') : '',
                    'approve_by'                => $data->approve_by ?? '',
                    'approve_at'                => $data->approve_at ? Carbon::createFromFormat('Y-m-d H:i:s', $data->approve_at)->format('d/m/Y H:i:s') : '',
                    'start_at'                  => $detail->start_at ? Carbon::createFromFormat('Y-m-d H:i:s', $detail->start_at)->format('d/m/Y H:i:s') : '',
                    'estimated_end'             => $detail->estimated_end ? Carbon::createFromFormat('Y-m-d H:i:s', $detail->estimated_end)->format('d/m/Y H:i:s') : '',
                    'close_at'                  => $detail->close_at ? Carbon::createFromFormat('Y-m-d H:i:s', $detail->close_at)->format('d/m/Y H:i:s') : '',
                    'cancelled_at'              => $detail->canceled_at ? Carbon::createFromFormat('Y-m-d H:i:s', $detail->canceled_at)->format('d/m/Y H:i:s') : '',
                ];

                $items[] = $item;
            }
        }

        // Prepare view data
        $data = collect($items);
        $auth = Auth::user();
        $datetimenow = date('d/m/Y H:i:s');
        $this->countdata = count($items);

        return view('reports.export.export', [
            'items' => $data,
            'auth' => $auth,
            'date' => $datetimenow
        ]);
    }

    /**
     * @return array
     */
    public function title(): string
    {
        return "Laporan";
    }
}

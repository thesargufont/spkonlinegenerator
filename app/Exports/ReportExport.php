<?php

namespace App\Exports;

use App\Models\Department;
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
                $event->sheet->getStyle('A4:J' . $countdata)->applyFromArray([
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
//        dd($this->request->get());
        foreach ($this->request->cursor() as $data) {
            $userCreated = User::where('id', $data->created_by)
                            ->first();
//            $userUpdated = User::find($data->updated_by);

//            dd($data->department_id);

            $department = Department::where('id', $data->department_id)
                            ->first();

//            dd(++$counter, $data->wo_number, $data->spk_number, $department->department, $data->wo_category, $data->job_category, $data->status, $userCreated->name, $data->effective_date, $data->approve_by, $data->approve_at);

            $item = [
                'no'                        => ++$counter,
                'wo_number'                 => $data->wo_number ?? '',
                'spk_number'                => $data->spk_number ?? '',
                'ba_number'                 => '',
                'department'                => $department->department ?? '',
                'wo_type'                   => $data->wo_category ?? '',
                'job_category'              => $data->job_category ?? '',
                'location'                  => '',
                'device_name'               => '',
                'disturbance_category'      => '',
                'wo_description'            => '',
                'job_description'           => '',
                'engineer'                  => '',
                'supervisor'                => '',
                'engineer_progress'         => '',
                'engineer_description'      => '',
                'status'                    => $data->status ?? '',
                'created_by'                => $userCreated->name ?? '',
                'effective_date'            => $data->effective_date ? Carbon::createFromFormat('Y-m-d H:i:s', $data->effective_date)->format('d/m/Y H:i:s') : '',
                'approve_by'                => $data->approve_by ?? '',
                'approve_at'                => $data->approve_at ? Carbon::createFromFormat('Y-m-d H:i:s', $data->approve_at)->format('d/m/Y H:i:s') : '',
                'start_at'                  => '',
                'estimated_end'             => '',
                'close_at'                  => '',
                'cancelled_at'              => '',
            ];

            $items[] = $item;
        }

        $data = collect($items);
        $auth = Auth::user();
        $datetimenow = Date('d/m/Y H:i:s');
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

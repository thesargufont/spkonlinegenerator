<?php

namespace App\Exports;

use App\User;
use Carbon\Carbon;

use App\Models\Department;
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

class LocationExport extends DefaultValueBinder implements  WithCustomValueBinder,FromView, ShouldAutoSize, WithTitle, WithEvents
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

    public function registerEvents(): array 
    {
        return [
            BeforeExport::class => function (BeforeExport $event) {
                $event->writer->getProperties()->setCreator(Auth::user()->name);
                $event->writer->getProperties()->setSubject(config('app.name') . ' - ' . 'Data Bagian');
            },

            AfterSheet::class   => function (AfterSheet $event) {
                $countdata = $this->countdata + 4;
                $event->sheet->mergeCells('A1:B1');
                $event->sheet->mergeCells('C1:F1');
                $event->sheet->mergeCells('A2:B2');
                $event->sheet->mergeCells('C2:F2');
                $event->sheet->mergeCells('A3:B3');
                $event->sheet->mergeCells('C3:F3');
                $event->sheet->getStyle('A4:L' . $countdata)->applyFromArray([
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
        foreach ($this->request->cursor() as $data) {
            $userCreated = User::find($data->created_by);
            $userUpdated = User::find($data->updated_by);
            if($data->active == 1){
                $active = 'AKTIF';
            } else {
                $active = 'TIDAK AKTIF';
            }

            if($data->end_effective != null){
                $endEffective = Carbon::createFromFormat('Y-m-d H:i:s', $data->end_effective)->format('d/m/Y H:i:s');
            } else {
                $endEffective = '-';
            }
            $item = [
                'no'                      => ++$counter,
                'location'                => $data->location,
                'location_description'    => $data->location_description,
                'location_type'           => $data->location_type,
                'address'                 => $data->address,
                'active'                  => $active,
                'start_effective'         => Carbon::createFromFormat('Y-m-d H:i:s', $data->start_effective)->format('d/m/Y H:i:s'),
                'end_effective'           => $endEffective,
                'created_by'              => $userCreated->name,
                'created_at'              => Carbon::createFromFormat('Y-m-d H:i:s', $data->created_at)->format('d/m/Y H:i:s'),
                'updated_by'              => $userUpdated->name,
                'updated_at'              => Carbon::createFromFormat('Y-m-d H:i:s', $data->updated_at)->format('d/m/Y H:i:s'),
            ];
            $items[] = $item;
        }
        
        $data = collect($items);
        $auth = Auth::user();
        $datetimenow = Date('d/m/Y H:i:s');
        $this->countdata = count($items);
        return view('masters.location.export.export',['items' => $data, 'auth' => $auth, 'date' => $datetimenow]);
    }

    /**
     * @return array
     */
    public function title(): string
    {
        return "Lokasi";
    }
}

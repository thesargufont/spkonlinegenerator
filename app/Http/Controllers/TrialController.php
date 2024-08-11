<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Device;
use App\Models\DeviceCategory;
use App\Models\Job;
use App\Models\Location;
use App\Models\SpongeDetail;
use App\Models\SpongeHeader;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class TrialController extends \App\Http\Controllers\Controller {
    public function generatePDF($id)
    {
        $dataHeader = SpongeHeader::where('id', $id)->first();
        $dataDetail = SpongeDetail::where('wo_number_id', $dataHeader->id)->get();

        $getData = [];
        $index = 0;
        foreach ($dataDetail as $detail) {
            $getData[$index] = [
                'spk_number'     => $dataHeader->spk_number,
                'wo_number'     => $dataHeader->wo_number,
                'department'     => Department::find($dataHeader->department_id) ? Department::find($dataHeader->department_id)->department : '-',
                'job_category'     => Job::find($dataHeader->job_category) ? Department::find($dataHeader->job_category)->job_category : '-',
                'effective_date' => Carbon::createFromFormat("Y-m-d H:i:s", $dataHeader->effective_date)->format('d-m-Y'),
                'approve_at' => Carbon::createFromFormat("Y-m-d H:i:s", $dataHeader->approve_at)->format('d-m-Y'),
                'start_at' => Carbon::createFromFormat("Y-m-d H:i:s", $detail->start_at)->format('d-m-Y'),
                'estimated_end' => Carbon::createFromFormat("Y-m-d H:i:s", $detail->estimated_end)->format('d-m-Y'),
                'location'       => Location::find($detail->location_id) ? Location::find($detail->location_id)->location : '-',
                'device'       => Device::find($detail->device_id) ? Device::find($detail->device_id)->device_name : '-',
                'brand'       => Device::find($detail->device_id) ? Device::find($detail->device_id)->brand : '-',
                'serial_number'       => Device::find($detail->device_id) ? Device::find($detail->device_id)->serial_number : '-',
                'activa_number'       => Device::find($detail->device_id) ? Device::find($detail->device_id)->activa_number : '-',
                'engineer'   => $detail->executorBy != '' ? optional($detail->executorBy)->name : '',
                'supervisor' => $detail->supervisorBy != '' ? optional($detail->supervisorBy)->name : '',
                'wo_description' => $detail->wo_description,
                'job_description'    => $detail->job_description,
            ];
            $index++;
        }


        $pdf = PDF::loadView('forms.approval.pdf.print-trial', [
            'data' => $getData
        ])->setOptions(['dpi' => 150]);

        $pdf = $pdf->setPaper('a4', 'potrait');
        $documentNumber = str_replace('/', '', $getData[0]['spk_number']);

        $today = Carbon::now()->format('Y/m');
        Storage::put('dms/stok/' . $today . '/' . $documentNumber . '.pdf', $pdf->output());

        return $pdf->download($documentNumber . '.pdf');
    }

    public function generatePDFEngineer($id)
    {
        $dataHeader = SpongeHeader::where('id', $id)->first();
        $dataDetail = SpongeDetail::where('wo_number_id', $dataHeader->id)->get();

        // dd($dataHeader, $dataDetail);

        $getData = [];
        $index = 0;
        foreach ($dataDetail as $detail) {
            $device = Device::find($detail->device_id);
            $path1 = $detail->wo_attachment1;

            // dd(storage_path($path1));
            $src1 = "data:image/jpg;base64,{{ base64_encode(file_get_contents(storage_path('" . $path1 . "'))) }}";
            // dd($src1);

            $getData[$index] = [
                'spk_number'     => $dataHeader->spk_number,
                'wo_number'     => $dataHeader->wo_number,
                'wp_number'     => $detail->cr_number,
                'department'     => Department::find($dataHeader->department_id) ? Department::find($dataHeader->department_id)->department : '-',
                'job_category'     => Job::find($dataHeader->job_category) ? Department::find($dataHeader->job_category)->job_category : '-',
                'effective_date' => Carbon::createFromFormat("Y-m-d H:i:s", $dataHeader->effective_date)->format('d-m-Y'),
                'day' => strtoupper(Carbon::parse($dataHeader->effective_date)->day),
                'approve_at' => Carbon::createFromFormat("Y-m-d H:i:s", $dataHeader->approve_at)->format('d-m-Y'),
                'start_at' => Carbon::createFromFormat("Y-m-d H:i:s", $detail->start_at)->format('d-m-Y'),
                'estimated_end' => Carbon::createFromFormat("Y-m-d H:i:s", $detail->estimated_end)->format('d-m-Y'),
                'location'       => Location::find($detail->location_id) ? Location::find($detail->location_id)->location : '-',
                'device'       => $device ? $device->device_name : '-',
                'brand'       => $device ? $device->brand : '-',
                'serial_number'       => $device ? $device->serial_number : '-',
                'activa_number'       => $device ? $device->activa_number : '-',
                'device_category'       => DeviceCategory::find($device->device_category_id) ? DeviceCategory::find($device->device_category_id)->device_category : '-',
                'engineer'   => $detail->executorBy != '' ? optional($detail->executorBy)->name : '',
                'supervisor' => $detail->supervisorBy != '' ? optional($detail->supervisorBy)->name : '',
                'wo_description' => $detail->wo_description,
                'job_description'    => $detail->job_description,
                'image_path1' => $src1,
                'image_path2' => $detail->wo_attachment2,
                'image_path3' => $detail->wo_attachment3,
            ];
            $index++;
        }


        $pdf = PDF::loadView('forms.engineer.pdf.print-trial', [
            'data' => $getData
        ])->setOptions(['dpi' => 150]);

        $pdf = $pdf->setPaper('a4', 'potrait');
        $documentNumber = str_replace('/', '', $getData[0]['spk_number']);

        $today = Carbon::now()->format('Y/m');
        Storage::put('dms/stok/' . $today . '/' . $documentNumber . '.pdf', $pdf->output());

        return $pdf->download($documentNumber . '.pdf');
    }
}


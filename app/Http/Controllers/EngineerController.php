<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\User;
use Exception;
use Carbon\Carbon;
use App\Models\Job;
use App\Models\Device;
use App\Models\DeviceCategory;
use App\Models\Location;
use App\Models\GeneralCode;
use App\Models\Role;
use App\Models\SpongeHeader;
use App\Models\SpongeDetail;
use App\Models\SpongeDetailHist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

use Barryvdh\DomPDF\Facade as PDF;

class EngineerController extends Controller
{
    public function index()
    {
        $user_id = Auth::user()->id;
        $roles = Role::where('user_id', $user_id)->where('active', 1)->distinct()->pluck('role')->toArray();
        $access_right = array('SUPERADMIN', 'ENGINEER');
        if (count(array_intersect($roles, $access_right)) == 0) {
            return redirect()->route('home');
        }

        return view('forms.engineer.engineer_index');
    }

    public function getData($request, $isExcel = '')
    {
        $user = Auth::user()->id;
        // $spongeheader = SpongeHeader::leftJoin('sponge_details', 'sponge_headers.id', '=', 'sponge_details.wo_number_id')
        //     ->where('sponge_headers.status', 'ONGOING')
        //     ->where('sponge_details.job_executor', $user);

        // return $spongeheader;
        $spongeheader = SpongeHeader::select('sponge_headers.*')
            ->distinct('sponge_headers.wo_number')
            ->leftJoin('sponge_details', 'sponge_headers.id', '=', 'sponge_details.wo_number_id')
            ->where('sponge_headers.status', '!=', 'CANCEL')
            ->where('sponge_headers.status', '!=', 'CLOSED')
            ->where('sponge_details.job_executor', $user);

        return $spongeheader;
        // $spongeDetail = SpongeDetail::where('job_executor', $user)->pluck('sponge_header_id')->groupBy('sponge_header_id')->toArray();
    }

    public function data(Request $request)
    {
        $datas = $this->getData($request);

        $datatables = DataTables::of($datas)
            ->filter(function ($instance) use ($request) {
                return true;
            });

        $datatables = $datatables->addColumn('action', function ($item) use ($request) {
            // dd($item);
            $txt = '';
            $txt .= "<a href=\"#\" onclick=\"showItem($item->id);\"title=\"" . ucfirst(__('edit')) . "\" class=\"btn btn-xs btn-secondary\"><i class=\"fa fa-edit fa-fw fa-xs\"></i></a>";
            $spongeDetail = SpongeDetail::where('wo_number_id', $item->id)->where('executor_progress', 'DONE')->first();
            if ($spongeDetail) {
                $txt .= "<a href=\"#\" onclick=\"downloadItem($item[id]);\"title=\"" . ucfirst(__('download')) . "\" class=\"btn btn-xs btn-secondary\"><i class=\"fa fa-download fa-fw fa-xs\"></i></a>";
            }
            return $txt;
        })
            // ->editColumn('job_category', function ($item) {
            //     $job_category = Job::where('id', $item->job_category)->first();
            //     if ($job_category) {
            //         if ($job_category->$job_category != '' || $job_category->$job_category != null) {
            //             return $job_category->job_category;
            //         } else {
            //             return '-';
            //         }
            //     } else {
            //         return '-';
            //     }
            // })
            ->editColumn('created_by', function ($item) {
                $cek = User::find($item->created_by);
                if ($cek) {
                    return $cek->name;
                } else {
                    return 'User ID : ' . $item->created_by;
                }
            })
            ->editColumn('approve_by', function ($item) {
                if ($item->approve_by != '' || $item->approve_by != null) {
                    $cek = User::find($item->created_by);
                    if ($cek) {
                        return $cek->name;
                    } else {
                        return 'User ID : ' . $item->created_by;
                    }
                } else {
                    return '-';
                }
            })
            ->addColumn('department', function ($item) {
                $cek = Department::find($item->department_id);
                if ($cek) {
                    return $cek->department;
                } else {
                    return 'ID department tidak ditemukan';
                }
            })
            ->editColumn('spk_number', function ($item) {
                if ($item->spk_number != '' || $item->spk_number != null) {
                    return $item->spk_number;
                } else {
                    return '-';
                }
            })
            ->editColumn('status', function ($item) {
                if ($item->status != '' || $item->status != null) {
                    return $item->status;
                } else {
                    return 'NOT APPROVE';
                }
            })
            ->editColumn('approve_at', function ($item) {
                if ($item->approve_at != '' || $item->approve_at != null) {
                    return Carbon::createFromFormat("Y-m-d H:i:s", $item->updated_at)->format('d/m/Y');
                } else {
                    return '-';
                }
            })
            ->editColumn('effective_date', function ($item) {
                return Carbon::createFromFormat("Y-m-d H:i:s", $item->updated_at)->format('d/m/Y');
            });
        return $datatables->make(TRUE);
    }

    public function submit(Request $request)
    {
        //dd($request);

        //HEADER VALIDATION
        if ($request->header_id == '' || $request->header_id == null) {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Terjadi Kesalahan. ID Work Order tidak ditemukan. Coba muat ulang halaman.</div>'
            ]);
        }

        $spongeHeader = SpongeHeader::find($request->header_id);
        if (!$spongeHeader) {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Data Work Order tidak ditemukan. Pastikan kembali nomor WO tidak dibatalkan atau hubungi admin.</div>'
            ]);
        }

        //DETAIL VALIDATION
        if (!isset($request->detail)) {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Detail belum diisi. Mohon cek kembali</div>'
            ]);
        }
        foreach ($request->detail as $detail) {
            if ($detail['status_engineer'] == '' || $detail['status_engineer'] == null) {
                return response()->json([
                    'errors' => true,
                    "message" => '<div class="alert alert-danger">Status Engineer kosong. Mohon cek kembali.</div>'
                ]);
            }
            if ($detail['desc_engineer'] == '' || $detail['desc_engineer'] == null) {
                return response()->json([
                    'errors' => true,
                    "message" => '<div class="alert alert-danger">Deskripsi Engineer kosong. Mohon cek kembali.</div>'
                ]);
            }
            $spongeDetail = SpongeDetail::find($detail['id']);
            if (!$spongeDetail) {
                return response()->json([
                    'errors' => true,
                    "message" => '<div class="alert alert-danger">Detail Work Order tidak ditemukan. Pastikan kembali nomor WO tidak dibatalkan atau hubungi admin.</div>'
                ]);
            }
            $newFilename1 = '';
            if (array_key_exists('photo1', $detail)) {
                //dd($detail);
                //dd(strval($detail['photo1']->getClientOriginalExtension()));
                if (strtolower(strval($detail['photo1']->getClientOriginalExtension())) != 'jpg' && strtolower(strval($detail['photo1']->getClientOriginalExtension())) != 'jpeg') {
                    return response()->json([
                        'errors' => true,
                        "message" => '<div class="alert alert-danger">Format tipe Gambar 1 tidak sesuai. Mohon cek kembali.</div>'
                    ]);
                }
                if (filesize($detail['photo1']) > 5120000) {
                    return response()->json([
                        'errors' => true,
                        "message" => '<div class="alert alert-danger">Ukuran gambar tidak boleh lebih dari 500KB. Mohon cek kembali.</div>'
                    ]);
                }
                $newFilename1 = str_replace('/', '-', $spongeHeader->wo_number) . '-photo1_job' . '.' . $detail['photo1']->getClientOriginalExtension();
                //Storage::putFileAs('local', $detail['photo1'], $newFilename1);
            }
            $newFilename2 = '';
            if (array_key_exists('photo2', $detail)) {
                //dd($detail);
                //dd(strval($detail['photo2']->getClientOriginalExtension()));
                if (strtolower(strval($detail['photo2']->getClientOriginalExtension())) != 'jpg' && strtolower(strval($detail['photo2']->getClientOriginalExtension())) != 'jpeg') {
                    return response()->json([
                        'errors' => true,
                        "message" => '<div class="alert alert-danger">Format tipe Gambar 1 tidak sesuai. Mohon cek kembali.</div>'
                    ]);
                }
                if (filesize($detail['photo2']) > 5120000) {
                    return response()->json([
                        'errors' => true,
                        "message" => '<div class="alert alert-danger">Ukuran gambar tidak boleh lebih dari 500KB. Mohon cek kembali.</div>'
                    ]);
                }
                $newFilename2 = str_replace('/', '-', $spongeHeader->wo_number) . '-photo2_job' . '.' . $detail['photo2']->getClientOriginalExtension();
                //Storage::putFileAs('local', $detail['photo2'], $newFilename2);
            }
            $newFilename3 = '';
            if (array_key_exists('photo3', $detail)) {
                //dd($detail);
                //dd(strval($detail['photo3']->getClientOriginalExtension()));
                if (strtolower(strval($detail['photo3']->getClientOriginalExtension())) != 'jpg' && strtolower(strval($detail['photo3']->getClientOriginalExtension())) != 'jpeg') {
                    return response()->json([
                        'errors' => true,
                        "message" => '<div class="alert alert-danger">Format tipe Gambar 1 tidak sesuai. Mohon cek kembali.</div>'
                    ]);
                }
                if (filesize($detail['photo3']) > 5120000) {
                    return response()->json([
                        'errors' => true,
                        "message" => '<div class="alert alert-danger">Ukuran gambar tidak boleh lebih dari 500KB. Mohon cek kembali.</div>'
                    ]);
                }
                $newFilename3 = str_replace('/', '-', $spongeHeader->wo_number) . '-photo3_job' . '.' . $detail['photo3']->getClientOriginalExtension();
                //Storage::putFileAs('local', $detail['photo3'], $newFilename3);
            }
        }

        //TRANSACTION
        try {
            DB::beginTransaction();

            $cek_status = 'DONE';
            $status_done = true;
            foreach ($request->detail as $detail) {
                if ($spongeDetail->job_attachment1 != '' && $newFilename1 == '') {
                    $newFilename1 = str_replace('public/', '', $spongeDetail->job_attachment1);
                }
                if ($spongeDetail->job_attachment2 != '' && $newFilename2 == '') {
                    $newFilename2 = str_replace('public/', '', $spongeDetail->job_attachment2);
                }
                if ($spongeDetail->job_attachment3 != '' && $newFilename3 == '') {
                    $newFilename3 = str_replace('public/', '', $spongeDetail->job_attachment3);
                }
                $spongeDetail = SpongeDetail::find($detail['id']);
                $spongeDetail->executor_progress = $detail['status_engineer'];
                $spongeDetail->executor_desc     = $detail['desc_engineer'];
                $spongeDetail->job_attachment1   = $newFilename1 != '' ? 'public/' . $newFilename1 : $newFilename1;
                $spongeDetail->job_attachment2   = $newFilename2 != '' ? 'public/' . $newFilename2 : $newFilename2;
                $spongeDetail->job_attachment3   = $newFilename3 != '' ? 'public/' . $newFilename3 : $newFilename3;
                $spongeDetail->updated_at   = Carbon::now()->timezone('Asia/Jakarta');
                $spongeDetail->save();

                $spongeDetailHist = new SpongeDetailHist([
                    'sponge_detail_id'     => $spongeDetail->id,
                    'wo_number_id'         => $spongeDetail->wo_number_id,
                    'location_id'    => $spongeDetail->location_id,
                    'device_id'            => $spongeDetail->device_id,
                    'disturbance_category' => $spongeDetail->disturbance_category,
                    'wo_description'       => $spongeDetail->wo_description,
                    'wo_attachment1'       => $spongeDetail->wo_attachment1,
                    'wo_attachment2'       => $spongeDetail->wo_attachment2,
                    'wo_attachment3'       => $spongeDetail->wo_attachment3,
                    'job_attachment1'      => $spongeDetail->job_attachment1,
                    'job_attachment1'      => $spongeDetail->job_attachment2,
                    'job_attachment1'      => $spongeDetail->job_attachment3,
                    'executor_progress'    => $spongeDetail->executor_progress,
                    'executor_desc'        => $spongeDetail->executor_desc,
                    'start_at'             => $spongeDetail->start_at,
                    'estimated_end'        => $spongeDetail->estimated_end,
                    'action'               => 'UPDATE',
                    'created_by'           => Auth::user()->id,
                    'created_at'           => Carbon::now()->timezone('Asia/Jakarta'),
                    'updated_by'           => Auth::user()->id,
                    'updated_at'           => Carbon::now()->timezone('Asia/Jakarta'),
                ]);
                $spongeDetailHist->save();

                if ($cek_status != $spongeDetail->executor_progress) {
                    $status_done = false;
                }

                if ($status_done) {
                    $spongeHeader->status = 'DONE';
                    $spongeHeader->updated_at = Carbon::now()->timezone('Asia/Jakarta');
                    $spongeHeader->save();
                }

                if (array_key_exists('photo1', $detail)) {
                    Storage::putFileAs('public', $detail['photo1'], $newFilename1);
                }
                if (array_key_exists('photo2', $detail)) {
                    Storage::putFileAs('public', $detail['photo2'], $newFilename2);
                }
                if (array_key_exists('photo3', $detail)) {
                    Storage::putFileAs('public', $detail['photo3'], $newFilename3);
                }
            }

            DB::commit();

            // if (array_key_exists('photo1', $detail)) {
            //     Storage::putFileAs('public', $detail['photo1'], $newFilename1);
            // }
            return response()->json([
                'success' => true,
                "message" => '<div class="alert alert-success">' . $spongeHeader->wo_number . ' berhasil diupdate</div>'
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger"> Server Error : ' . $e->getMessage() . '. Silahkan kontak developer atau admin.</div>'
            ]);
        }
    }

    public function detail($id)
    {
        $spongeheader = SpongeHeader::find($id);
        if (!$spongeheader) {
            return back();
        }
        $spongedetails = SpongeDetail::where('wo_number_id', $spongeheader->id)->where('job_executor', Auth::user()->id)->get();
        if (empty($spongedetails->toArray())) {
            return back();
        }
        $status_detail = GeneralCode::where('section', 'SPONGE')->where('label', 'STATUS_DETAIL')->pluck('reff1')->toArray();
        if (empty($status_detail)) {
            return back();
        }

        $index = 1;
        foreach ($spongedetails as $detail) {
            $device = Device::find($detail->device_id);
            $details[] = [
                'index' => $index,
                'id' => $detail->id,
                'location' => Location::find($detail->location_id) ? Location::find($detail->location_id)->location : 'id lokasi tidak ditemukan',
                'disturbance_category' => DeviceCategory::find($detail->disturbance_category) ? DeviceCategory::find($detail->disturbance_category)->disturbance_category : '-',
                'description' => $detail->job_description,
                'image_path1' => $detail->wo_attachment1,
                'image_path2' => $detail->wo_attachment2,
                'image_path3' => $detail->wo_attachment3,
                'device' => $device->device_name,
                'device_model' => $device->brand,
                'device_code' => $device->activa_number,
                'supervisor' => User::find($detail->job_supervisor) ? User::find($detail->job_supervisor)->name : 'ID user tidak ditemukan : ' . $detail->job_supervisor,
                'engineer' => User::find($detail->job_executor) ? User::find($detail->job_executor)->name : 'ID user tidak ditemukan : ' . $detail->job_executor,
                'aid' => User::find($detail->job_aid) ? User::find($detail->job_aid)->name : 'ID user tidak ditemukan : ' . $detail->job_aid,
                'engineer_status' => $detail->executor_progress != '' ? $detail->executor_progress : 'ONGOING',
                'executor_desc' => $detail->executor_desc,
                'start_effective' => Carbon::createFromFormat("Y-m-d H:i:s", $detail->start_at)->format('d/m/Y'),
                'estimated_end' => Carbon::createFromFormat("Y-m-d H:i:s", $detail->estimated_end)->format('d/m/Y'),
                'job_attachment1' => $detail->job_attachment1,
                'job_attachment2' => $detail->job_attachment2,
                'job_attachment3' => $detail->job_attachment3,
            ];
            // dd($details);
            $index++;
        }
        $count = count($details);
        //dd($details);

        /*SPK NUMBER Preparation*/
        //get month year
        $now = Carbon::now();
        $year = $now->year;
        $month =  $now->month;

        //get user department
        $dept_code = substr(Department::find($spongeheader->department_id)->department, 0, 3);

        //dd('%SPKI/UP2BJTD/FASOP/' . '/' . $dept_code . '/' .  $year);
        //get number
        $cek_number = SpongeHeader::where('spk_number', 'like', '%SPKI/UP2BJTD/FASOP' . '/' . $dept_code . '/' .  $year)->orderBy('created_at', 'desc')->first();
        $number = 0;
        if ($cek_number) {
            $number = intval(substr($cek_number->spk_number, 0, 5));
        }
        $number++;

        //generate wo number
        $spk_number = str_pad($number, 5, '0', STR_PAD_LEFT) . '/' . 'SPKI/UP2BJTD/FASOP' . '/' . $dept_code . '/' . $year;
        /*WO NUMBER complete*/

        // if (is_array($details) && count($details) > 0) {
        //     dd($details, $status_detail);
        // }

        $data = [
            'spk_number' => $spk_number,
            'id' => $spongeheader->id,
            'status' => $spongeheader->status,
            'wo_number' => $spongeheader->wo_number,
            'wo_category' => $spongeheader->wo_category,
            'department' => Department::find($spongeheader->department_id) ? Department::find($spongeheader->department_id)->department : 'id department tidak ditemukan',
            'job_category' => $spongeheader->job_category,
            'effective_date' => Carbon::createFromFormat("Y-m-d H:i:s", $spongeheader->effective_date)->format('d/m/Y'),
            'details' => $details,
            'status_detail' => $status_detail,
            'length' => $count,
        ];

        return view('forms.engineer.detail', $data);
    }

    public function generatePDF($id)
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


        $pdf = PDF::loadView('forms.engineer.pdf.print', [
            'data' => $getData
        ])->setOptions(['dpi' => 150]);

        $pdf = $pdf->setPaper('a4', 'potrait');
        $documentNumber = str_replace('/', '', $getData[0]['spk_number']);

        $today = Carbon::now()->format('Y/m');
        Storage::put('dms/stok/' . $today . '/' . $documentNumber . '.pdf', $pdf->output());

        return $pdf->download($documentNumber . '.pdf');
    }
}

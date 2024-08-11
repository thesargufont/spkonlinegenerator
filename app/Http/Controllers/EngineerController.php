<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\User;
use Exception;
use Carbon\Carbon;
use App\Models\Job;
use App\Models\Basecamp;
use App\Models\Device;
use App\Models\DeviceCategory;
use App\Models\Location;
use App\Models\GeneralCode;
use App\Models\Role;
use App\Models\Notification;
use App\Models\SpongeHeader;
use App\Models\SpongeDetail;
use App\Models\SpongeDetailHist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

use Barryvdh\DomPDF\Facade as PDF;
use PhpOffice\PhpWord\TemplateProcessor;

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
            if (($detail['wp_number'] == '' || $detail['wp_number'] == null) && $detail['status_engineer'] == 'DONE') {
                return response()->json([
                    'errors' => true,
                    "message" => '<div class="alert alert-danger">Nomor WP masih kosong. Mohon cek kembali.</div>'
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
                $newFilename1 = str_replace('/', '-', $spongeHeader->wo_number) . '-photo1_job' . Auth::user()->nik . '.'  . $detail['photo1']->getClientOriginalExtension();
                // if(Storage::exists($newFilename1)){

                // }
                Storage::delete('public/' . $newFilename1);
                Storage::putFileAs('public', $detail['photo1'], $newFilename1);
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
                $newFilename2 = str_replace('/', '-', $spongeHeader->wo_number) . '-photo2_job' . Auth::user()->nik . '.' . $detail['photo2']->getClientOriginalExtension();
                Storage::delete('public/' . $newFilename2);
                Storage::putFileAs('public', $detail['photo2'], $newFilename2);
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
                $newFilename3 = str_replace('/', '-', $spongeHeader->wo_number) . '-photo3_job' . Auth::user()->nik . '.' . $detail['photo3']->getClientOriginalExtension();
                Storage::delete('public/' . $newFilename3);
                Storage::putFileAs('public', $detail['photo3'], $newFilename3);
            }
        }



        //TRANSACTION
        try {
            DB::beginTransaction();

            foreach ($request->detail as $detail) {
                /*cr NUMBER Preparation*/
                //get month year
                $now = Carbon::now();
                $year = $now->year;
                $month =  $now->month;

                //get user department
                $dept_code = Department::where('id', $spongeHeader->department_id)->first()->department_code;

                //get number
                $cek_number = SpongeDetail::where('cr_number', 'like', '%BA' . '/' . $dept_code . '/' . str_pad($month, 2, 0, STR_PAD_LEFT) . '/' . $year)->orderBy('created_at', 'desc')->first();
                $number = 0;
                if ($cek_number) {
                    $number = intval(substr($cek_number->cr_number, 0, 5));
                }
                $number++;

                //generate wo number
                if ($detail['status_engineer'] == 'DONE') {
                    $cr_number = str_pad($number, 5, '0', STR_PAD_LEFT) . '/' . 'BA' . '/' . $dept_code . '/' . str_pad($month, 2, 0, STR_PAD_LEFT) . '/' . $year;
                } else {
                    $cr_number = '';
                }
                /*cr NUMBER complete*/

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
                $spongeDetail->wp_number     = $detail['wp_number'];
                $spongeDetail->cr_number     = $cr_number;
                $spongeDetail->job_attachment1   = $newFilename1 != '' ? 'public/' . $newFilename1 : $newFilename1;
                $spongeDetail->job_attachment2   = $newFilename2 != '' ? 'public/' . $newFilename2 : $newFilename2;
                $spongeDetail->job_attachment3   = $newFilename3 != '' ? 'public/' . $newFilename3 : $newFilename3;
                $spongeDetail->close_at  = $detail['status_engineer'] == 'DONE' ? Carbon::now()->timezone('Asia/Jakarta'): null;
                $spongeDetail->updated_at   = Carbon::now()->timezone('Asia/Jakarta');
                $spongeDetail->save();

                $spongeDetailHist = new SpongeDetailHist([
                    'sponge_detail_id'     => $spongeDetail->id,
                    'wo_number_id'         => $spongeDetail->wo_number_id,
                    'cr_number'         => $spongeDetail->cr_number,
                    'wp_number'         => $spongeDetail->wp_number,
                    'location_id'    => $spongeDetail->location_id,
                    'device_id'            => $spongeDetail->device_id,
                    'disturbance_category' => $spongeDetail->disturbance_category,
                    'wo_description'       => $spongeDetail->wo_description,
                    'wo_attachment1'       => $spongeDetail->wo_attachment1,
                    'wo_attachment2'       => $spongeDetail->wo_attachment2,
                    'wo_attachment3'       => $spongeDetail->wo_attachment3,
                    'job_attachment1'      => $spongeDetail->job_attachment1,
                    'job_attachment2'      => $spongeDetail->job_attachment2,
                    'job_attachment3'      => $spongeDetail->job_attachment3,
                    'job_executor'          => $spongeDetail->job_executor,
                    'job_supervisor'          => $spongeDetail->job_supervisor,
                    'job_aid'          => $spongeDetail->job_aid,
                    'job_description'          => $spongeDetail->job_description,
                    'executor_progress'    => $spongeDetail->executor_progress,
                    'executor_desc'        => $spongeDetail->executor_desc,
                    'start_at'             => $spongeDetail->start_at,
                    'estimated_end'        => $spongeDetail->estimated_end,
                    'action'               => 'UPDATE',
                    'close_at'           => $spongeDetail->executor_progress == 'DONE' ? Carbon::now()->timezone('Asia/Jakarta') : null,
                    'created_by'           => Auth::user()->id,
                    'created_at'           => Carbon::now()->timezone('Asia/Jakarta'),
                    'updated_by'           => Auth::user()->id,
                    'updated_at'           => Carbon::now()->timezone('Asia/Jakarta'),
                ]);
                $spongeDetailHist->save();

                // if (array_key_exists('photo1', $detail)) {
                //     Storage::putFileAs('public', $detail['photo1'], $newFilename1);
                // }
                // if (array_key_exists('photo2', $detail)) {
                //     Storage::putFileAs('public', $detail['photo2'], $newFilename2);
                // }
                // if (array_key_exists('photo3', $detail)) {
                //     Storage::putFileAs('public', $detail['photo3'], $newFilename3);
                // }
                // $spongeHeader->wp_number     = $detail['wp_number'];
            }

            $status_done = 1;
            $cek_all_status = SpongeDetail::where('wo_number_id', $spongeHeader->id)->pluck('executor_progress')->toArray();
            if (!empty($cek_all_status)) {
                foreach ($cek_all_status as $status) {
                    if ($status != 'DONE') {
                        $status_done = 0;
                    }
                }
            }

            if ($status_done == 1) {
                $spongeHeader->status = 'DONE';
                $spongeHeader->updated_at = Carbon::now()->timezone('Asia/Jakarta');
                $spongeHeader->save();

                //notif input
                $recipientIds = User::where('id', $spongeHeader->created_by)
                // ->where('department_id', $request->department)
                ->pluck('id')
                ->toArray();

                $description = 'No working order ' . $spongeHeader->wo_number . '. telah selesai dikerjakan pada  ' . Carbon::now()->timezone('Asia/Jakarta');
                $url = route('form-input.working-order.detail', ['id' => $spongeHeader->id]);
                $createNotif = Notification::createNotification($recipientIds, $description, $url);

                if (!$createNotif['success']) {
                    DB::rollback();
                    return response()->json([
                        'errors' => true,
                        "message" => '<div class="alert alert-danger"> Terjadi kesalahan, Notification error</div>'
                    ]);
                }

                //notif spv
                $recipientIds = User::where('id', $spongeHeader->approve_by)
                // ->where('department_id', $request->department)
                ->pluck('id')
                ->toArray();

                $description = 'No working order ' . $spongeHeader->wo_number . '. telah selesai dikerjakan pada  ' . Carbon::now()->timezone('Asia/Jakarta');
                $url = route('form-input.approval.detail', ['id' => $spongeHeader->id]);
                $createNotif = Notification::createNotification($recipientIds, $description, $url);

                if (!$createNotif['success']) {
                    DB::rollback();
                    return response()->json([
                        'errors' => true,
                        "message" => '<div class="alert alert-danger"> Terjadi kesalahan, Notification error</div>'
                    ]);
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
        $status = '';
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
                'wp_number' => $detail->wp_number,
            ];
            // dd($details);
            $index++;
            $status = $detail->executor_progress;
        }

        if($spongeheader->status == 'CANCEL' || $spongeheader->status == 'CLOSED'){
            $status = 'DONE';
        }

        $count = count($details);
        //dd($details);

        /*SPK NUMBER Preparation*/
        //get month year
        // $now = Carbon::now();
        // $year = $now->year;
        // $month =  $now->month;

        //get user department
        // $dept_code = substr(Department::find($spongeheader->department_id)->department, 0, 3);

        //dd('%SPKI/UP2BJTD/FASOP/' . '/' . $dept_code . '/' .  $year);
        //get number
        // $cek_number = SpongeHeader::where('spk_number', 'like', '%SPKI/UP2BJTD/FASOP' . '/' . $dept_code . '/' .  $year)->orderBy('created_at', 'desc')->first();
        // $number = 0;
        // if ($cek_number) {
        //     $number = intval(substr($cek_number->spk_number, 0, 5));
        // }
        // $number++;

        //generate wo number
        // $spk_number = str_pad($number, 5, '0', STR_PAD_LEFT) . '/' . 'SPKI/UP2BJTD/FASOP' . '/' . $dept_code . '/' . $year;
        /*WO NUMBER complete*/

        // if (is_array($details) && count($details) > 0) {
        //     dd($details, $status_detail);
        // }

        $data = [
            'spk_number' => $spongeheader->spk_number,
            'id' => $spongeheader->id,
            'status' => $status,
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
        $dataDetail = SpongeDetail::where('wo_number_id', $dataHeader->id)->where('executor_progress', 'DONE')->get();

        // dd($dataHeader, $dataDetail);

        $getData = [];
        $index = 0;
        foreach ($dataDetail as $detail) {
            $device = Device::find($detail->device_id);
            $path1 = $detail->job_attachment1 !== '' ? 'app/' . $detail->job_attachment1 : '';
            $path2 = $detail->job_attachment2 !== '' ? 'app/' . $detail->job_attachment2 : '';
            $path3 = $detail->job_attachment3 !== '' ? 'app/' . $detail->job_attachment3 : '';

            $location = Location::find($detail->location_id);
            if ($location) {
                $basecamp = Basecamp::find($location->basecamp_id);
            } else {
                $basecamp = false;
            }

            $getData[$index] = [
                'spk_number'     => $dataHeader->spk_number,
                'wo_number'     => $dataHeader->wo_number,
                'cr_number'     => $detail->cr_number,
                'wp_number'     => $detail->wp_number,
                'department'     => Department::find($dataHeader->department_id) ? Department::find($dataHeader->department_id)->department : '-',
                'job_category'     => $dataHeader->job_category,
                'effective_date' => Carbon::createFromFormat("Y-m-d H:i:s", $dataHeader->effective_date)->format('d-m-Y'),
                'day' => strtoupper(Carbon::parse($dataHeader->effective_date)->day),
                'approve_at' => Carbon::createFromFormat("Y-m-d H:i:s", $dataHeader->approve_at)->format('d-m-Y'),
                'start_at' => Carbon::createFromFormat("Y-m-d H:i:s", $detail->start_at)->format('d-m-Y'),
                'estimated_end' => Carbon::createFromFormat("Y-m-d H:i:s", $detail->estimated_end)->format('d-m-Y'),
                'location'       => $location ? $location->location : '-',
                'basecamp'       => $basecamp ? $basecamp->basecamp : '-',
                'device'       => $device ? $device->device_name : '-',
                'brand'       => $device ? $device->brand : '-',
                'serial_number'       => $device ? $device->serial_number : '-',
                'activa_number'       => $device ? $device->activa_number : '-',
                'device_category'       => DeviceCategory::find($device->device_category_id) ? DeviceCategory::find($device->device_category_id)->device_category : '-',
                'engineer'   => $detail->executorBy != '' ? optional($detail->executorBy)->name : '',
                'supervisor' => $detail->supervisorBy != '' ? optional($detail->supervisorBy)->name : '',
                'wo_description' => $detail->wo_description,
                'job_description'    => $detail->job_description,
                'image_path1' => $path1,
                'image_path2' => $path2,
                'image_path3' => $path3,
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

    public function generateWord($id)
    {
        $dataHeader = SpongeHeader::where('id', $id)->first();
        $dataDetail = SpongeDetail::where('wo_number_id', $dataHeader->id)->where('executor_progress', 'DONE')->where('job_executor', Auth::user()->id)->get();

        // dd($dataHeader, $dataDetail);

        $getData = [];
        $index = 1;
        foreach ($dataDetail as $detail) {
            $device = Device::find($detail->device_id);
            $path1 = $detail->job_attachment1 !== '' ? 'app/' . $detail->job_attachment1 : '';
            $path2 = $detail->job_attachment2 !== '' ? 'app/' . $detail->job_attachment2 : '';
            $path3 = $detail->job_attachment3 !== '' ? 'app/' . $detail->job_attachment3 : '';

            $location = Location::find($detail->location_id);
            if ($location) {
                $basecamp = Basecamp::find($location->basecamp_id);
            } else {
                $basecamp = false;
            }

            $getData[$index] = [
                'spk_number'     => $dataHeader->spk_number,
                'wo_number'     => $dataHeader->wo_number,
                'cr_number'     => $detail->cr_number,
                'wp_number'     => $detail->wp_number,
                'department'     => Department::find($dataHeader->department_id) ? Department::find($dataHeader->department_id)->department : '-',
                'job_category'     => $dataHeader->job_category,
                'effective_date' => Carbon::createFromFormat("Y-m-d H:i:s", $dataHeader->effective_date)->format('d/m/Y'),
                'updated_at' => Carbon::createFromFormat("Y-m-d H:i:s", $detail->updated_at)->format('d/m/Y'),
                'day' => strtoupper(Carbon::parse($dataHeader->effective_date)->day),
                'approve_at' => Carbon::createFromFormat("Y-m-d H:i:s", $dataHeader->approve_at)->format('d-m-Y'),
                'start_at' => Carbon::createFromFormat("Y-m-d H:i:s", $detail->start_at)->format('d/m/Y'),
                'estimated_end' => Carbon::createFromFormat("Y-m-d H:i:s", $detail->estimated_end)->format('d/m/Y'),
                'location'       => $location ? $location->location : '-',
                'basecamp'       => $basecamp ? $basecamp->basecamp : '-',
                'device'       => $device ? $device->device_name : '-',
                'brand'       => $device ? $device->brand : '-',
                'serial_number'       => $device ? $device->serial_number : '-',
                'activa_number'       => $device ? $device->activa_number : '-',
                'device_category'       => DeviceCategory::find($device->device_category_id) ? DeviceCategory::find($device->device_category_id)->device_category : '-',
                'engineer'   => $detail->executorBy != '' ? optional($detail->executorBy)->name : '',
                'supervisor' => $detail->supervisorBy != '' ? optional($detail->supervisorBy)->name : '',
                'wo_description' => $detail->wo_description,
                'job_description'    => $detail->job_description,
                'image_path1' => $path1,
                'image_path2' => $path2,
                'image_path3' => $path3,
            ];
            $index++;
        }

        foreach ($getData as $key => $data) {
            //dd($key, $data['wp_number']);
            $templateProcessor = new TemplateProcessor('Format_BA.docx');
            $templateProcessor->setValue('effective_date', $data['effective_date']);
            $templateProcessor->setValue('wp_number', $data['wp_number']);
            $templateProcessor->setValue('spk_number', $data['spk_number']);
            $templateProcessor->setValue('job_description', $data['job_description']);
            $templateProcessor->setValue('location', $data['location']);
            $templateProcessor->setValue('basecamp', $data['basecamp']);
            $templateProcessor->setValue('engineer', $data['engineer']);

            if ($path1 != '') {
                $imagePath1 = storage_path($path1 ?? '');
                $templateProcessor->setImageValue('imagepath1', array('path' => $imagePath1, 'width' => 360, 'height' => 200, 'ratio' => false));
            } else {
                $templateProcessor->setValue('imagepath1', '');
            }
            if ($path2 != '') {
                $imagePath2 = storage_path($path2 ?? '');
                $templateProcessor->setImageValue('imagepath2', array('path' => $imagePath2, 'width' => 360, 'height' => 200, 'ratio' => false));
            } else {
                $templateProcessor->setValue('imagepath2', '');
            }
            if ($path3 != '') {
                $imagePath3 = storage_path($path3 ?? '');
                $templateProcessor->setImageValue('imagepath3', array('path' => $imagePath3, 'width' => 360, 'height' => 200, 'ratio' => false));
            } else {
                $templateProcessor->setValue('imagepath3', '');
            }

            $executorSignaturePath   = optional($detail->executorBy)->signature_path;
            if ($executorSignaturePath != '') {
                $pathExecutor = $executorSignaturePath !== 'public/' ? 'app/' . $executorSignaturePath : '';
                $sigPath = storage_path($pathExecutor ?? '');
                $templateProcessor->setImageValue('executor_sig', array('path' => $sigPath, 'width' => 200, 'ratio' => true));
            } else {
                $templateProcessor->setValue('executor_sig', 'Dokumen ini telah ditandatangani secara komputerisasi oleh ' . $data['engineer'] . ' pada tanggal ' . $data['updated_at']);
            }



            $documentNumber = str_replace('/', '', $data['wp_number']);
            $fileName = $documentNumber . '_' . $data['engineer'] . '_' . $key;
            $templateProcessor->saveAs($fileName . '.docx');
            return response()->download($fileName . '.docx')->deleteFileAfterSend(true);
        }
    }
}

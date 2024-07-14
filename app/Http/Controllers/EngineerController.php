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
        $spongeheader = SpongeHeader::leftJoin('sponge_details', 'sponge_headers.id', '=', 'sponge_details.wo_number_id')
            ->where('sponge_headers.status', 'ONGOING')
            ->where('sponge_headers.wo_number', 'like', '%' . $request->wo_number . '%')
            ->where('sponge_details.job_executor', $user);

        return $spongeheader;
    }

    public function data(Request $request)
    {
        $datas = $this->getData($request);

        $datatables = DataTables::of($datas)
            ->filter(function ($instance) use ($request) {
                return true;
            });

        $datatables = $datatables->addColumn('action', function ($item) use ($request) {

            $txt = '';
            $txt .= "<a href=\"#\" onclick=\"showItem($item[wo_number_id]);\"title=\"" . ucfirst(__('edit')) . "\" class=\"btn btn-xs btn-secondary\"><i class=\"fa fa-edit fa-fw fa-xs\"></i></a>";
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
                if (filesize($detail['photo1']) > 512000) {
                    return response()->json([
                        'errors' => true,
                        "message" => '<div class="alert alert-danger">Ukuran gambar tidak boleh lebih dari 500KB. Mohon cek kembali.</div>'
                    ]);
                }
                $newFilename1 = str_replace('/', '-', $spongeHeader->wo_number) . '-photo1_job' . '.' . $detail['photo1']->getClientOriginalExtension();
                Storage::putFileAs('local', $detail['photo1'], $newFilename1);
            }
        }

        //TRANSACTION
        try {
            DB::beginTransaction();

            foreach ($request->detail as $detail) {
                $spongeDetail = SpongeDetail::find($detail['id']);
                $spongeDetail->executor_progress = $detail['status_engineer'];
                $spongeDetail->executor_desc = $detail['desc_engineer'];
                $spongeDetail->job_attachment1 = 'public/' . $newFilename1;
                $spongeDetail->save();

                $spongeDetailHist = new SpongeDetailHist([
                    'sponge_detail_id' => $spongeDetail->id,
                    'wo_number_id' => $spongeDetail->wo_number_id,
                    'reporter_location' => $spongeDetail->reporter_location,
                    'device_id' => $spongeDetail->device_id,
                    'disturbance_category' => $spongeDetail->disturbance_category,
                    'wo_description' => $spongeDetail->wo_description,
                    'wo_attachment1' => $spongeDetail->wo_attachment1,
                    'wo_attachment2' => $spongeDetail->wo_attachment2,
                    'wo_attachment3' => $spongeDetail->wo_attachment3,
                    'job_attachment1' => $spongeDetail->job_attachment1,
                    'executor_progress' => $spongeDetail->executor_progress,
                    'executor_desc' => $spongeDetail->executor_desc,
                    'start_at' => $spongeDetail->start_at,
                    'estimated_end' => $spongeDetail->estimated_end,
                    'action' => 'UPDATE',
                    'created_by'              => Auth::user()->id,
                    'created_at'              => Carbon::now()->timezone('Asia/Jakarta'),
                    'updated_by'              => Auth::user()->id,
                    'updated_at'              => Carbon::now()->timezone('Asia/Jakarta'),
                ]);
                $spongeDetailHist->save();
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
        $spongedetails = SpongeDetail::where('wo_number_id', $spongeheader->id)->get();
        $status_detail = GeneralCode::where('section', 'SPONGE')->where('label', 'STATUS_DETAIL')->pluck('reff1')->toArray();

        $index = 1;
        foreach ($spongedetails as $detail) {
            $device = Device::find($detail->device_id);
            $details[$index] = [
                'id' => $detail->id,
                'location' => $detail->reporter_location,
                'disturbance_category' => DeviceCategory::find($detail->disturbance_category) ? DeviceCategory::find($detail->disturbance_category)->disturbance_category : '-',
                'description' => $detail->wo_description,
                'image_path1' => $detail->wo_attachment1,
                'image_path2' => $detail->wo_attachment2,
                'image_path3' => $detail->wo_attachment3,
                'device' => $device->device_name,
                'device_model' => $device->brand,
                'device_code' => $device->eq_id,
                'supervisor' => $detail->job_supervisor,
                'engineer' => $detail->job_executor,
                'engineer_status' => $detail->executor_progress != '' ? $detail->executor_progress : 'ONGOING',
                'executor_desc' => $detail->executor_description,
                'start_effective' => Carbon::createFromFormat("Y-m-d H:i:s", $detail->start_at)->format('d/m/Y'),
                'estimated_end' => Carbon::createFromFormat("Y-m-d H:i:s", $detail->estimated_end)->format('d/m/Y'),
            ];
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
        $dept_code = substr($spongeheader->department, 0, 3);

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

        // dd($engineers, $spvs);

        $data = [
            'spk_number' => $spk_number,
            'id' => $spongeheader->id,
            'status' => $spongeheader->status,
            'wo_number' => $spongeheader->wo_number,
            'wo_category' => $spongeheader->wo_type,
            'department' => $spongeheader->department,
            'job_category' => $spongeheader->job_category,
            'effective_date' => Carbon::createFromFormat("Y-m-d H:i:s", $spongeheader->effective_date)->format('d/m/Y'),
            'details' => $details,
            'status_detail' => $status_detail,
            'length' => $count,
        ];

        return view('forms.engineer.detail', $data);
    }
}

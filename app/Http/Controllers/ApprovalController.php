<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use Carbon\Carbon;
use App\Models\Job;
use App\Models\Role;
use App\Models\Device;
use App\Models\Location;
// use Barryvdh\DomPDF\PDF;
use Barryvdh\DomPDF\Facade as PDF;
use App\Models\Department;
use App\Models\SpongeDetail;
use App\Models\SpongeHeader;
use Illuminate\Http\Request;
use App\Models\DeviceCategory;
use App\Models\SpongeDetailHist;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ApprovalController extends Controller
{
    public function index()
    {
        $user_id = Auth::user()->id;
        $roles = Role::where('user_id', $user_id)->where('active', 1)->distinct()->pluck('role')->toArray();
        $access_right = array('SUPERADMIN', 'SPV');
        if (count(array_intersect($roles, $access_right)) == 0) {
            return redirect()->route('home');
        }

        return view('forms.approval.approval_index');
    }

    public function getData($request, $isExcel = '')
    {
        $user = Auth::user()->id;
        $spongeheader = SpongeHeader::where('status', '!=', 'DONE')->where('wo_number', 'like', '%' . $request->wo_number . '%');

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
            $show_url = route('form-input.working-order.detail', ['id' => $item->id]);

            $txt = '';
            $txt .= "<a href=\"#\" onclick=\"showItem($item[id]);\"title=\"" . ucfirst(__('edit')) . "\" class=\"btn btn-xs btn-secondary\"><i class=\"fa fa-edit fa-fw fa-xs\"></i></a>";
            if ($item->spk_number != '') {
                $txt .= "<a href=\"#\" onclick=\"downloadItem($item[id]);\"title=\"" . ucfirst(__('download')) . "\" class=\"btn btn-xs btn-secondary\"><i class=\"fa fa-download fa-fw fa-xs\"></i></a>";
            }
            return $txt;
        })
            ->addColumn('department', function ($item) {
                $cek = Department::find($item->department_id);
                if ($cek) {
                    return $cek->department;
                } else {
                    return 'ID department tidak ditemukan';
                }
            })
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

    public function approve(Request $request)
    {
        //HEADER VALIDATION
        if ($request->spk_number == '' || $request->spk_number == null) {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Nomor SPK belum diisi. Mohon cek kembali.</div>'
            ]);
        }

        $spongeHeader = SpongeHeader::find($request->header_id);
        if (!$spongeHeader) {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Data Work Order tidak ditemukan. Pastikan kembali nomor WO tidak dibatalkan atau hubungi admin.</div>'
            ]);
        }

        //HEADER VALIDATION - CHECK WO NUMBER DUPLICATION
        $spk_number_cek = SpongeHeader::where('spk_number', $request->spk_number)->first();
        if ($spk_number_cek) {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Nomor SPK sudah terpakai. Mohon muat ulang halaman.</div>'
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
            if ($detail['engineer'] == '' || $detail['engineer'] == null) {
                return response()->json([
                    'errors' => true,
                    "message" => '<div class="alert alert-danger">Ada engineer yang belum dipilih. Mohon cek kembali.</div>'
                ]);
            } else {
                $engineer_cek = User::find($detail['engineer']);
                //dd($location_cek);
                if (!$engineer_cek) {
                    return response()->json([
                        'errors' => true,
                        "message" => '<div class="alert alert-danger">Data engineer tidak ditemukan. Mohon cek kembali</div>'
                    ]);
                } else {
                    if ($engineer_cek->active != 1) {
                        return response()->json([
                            'errors' => true,
                            "message" => '<div class="alert alert-danger">Engineer ' . $engineer_cek->name . ' sudah tidak aktif. Mohon cek kembali</div>'
                        ]);
                    }
                }
            }
            if ($detail['supervisor'] == '' || $detail['supervisor'] == null) {
                return response()->json([
                    'errors' => true,
                    "message" => '<div class="alert alert-danger">Ada supervisor yang belum diisi. Mohon cek kembali.</div>'
                ]);
            } else {
                $supervisor_cek = User::find($detail['supervisor']);
                if (!$supervisor_cek) {
                    return response()->json([
                        'errors' => true,
                        "message" => '<div class="alert alert-danger">Data supervisor tidak ditemukan. Mohon cek kembali</div>'
                    ]);
                } else {
                    if ($supervisor_cek->active != 1) {
                        return response()->json([
                            'errors' => true,
                            "message" => '<div class="alert alert-danger">Supervisor ' . $supervisor_cek->name . ' sudah tidak aktif. Mohon cek kembali</div>'
                        ]);
                    }
                }
            }
            if ($detail['start_at'] == '' || $detail['start_at'] == null) {
                return response()->json([
                    'errors' => true,
                    "message" => '<div class="alert alert-danger">Ada tanggal mulai yang belum diisi. Mohon cek kembali.</div>'
                ]);
            }
            if ($detail['estimated_end'] == '' || $detail['estimated_end'] == null) {
                return response()->json([
                    'errors' => true,
                    "message" => '<div class="alert alert-danger">Ada estimasi selesai yang belum diisi. Mohon cek kembali.</div>'
                ]);
            }

            $spongeDetail = SpongeDetail::find($detail['id']);
            if (!$spongeDetail) {
                return response()->json([
                    'errors' => true,
                    "message" => '<div class="alert alert-danger">Detail Work Order tidak ditemukan. Pastikan kembali nomor WO tidak dibatalkan atau hubungi admin.</div>'
                ]);
            }
        }

        //TRANSACTION
        try {
            DB::beginTransaction();
            $spongeHeader->spk_number = $request->spk_number;
            $spongeHeader->approve_by = Auth::user()->id;
            $spongeHeader->approve_at = Carbon::now()->timezone('Asia/Jakarta');
            $spongeHeader->status = $request->action;
            $spongeHeader->save();

            foreach ($request->detail as $detail) {
                $spongeDetail = SpongeDetail::find($detail['id']);
                $spongeDetail->job_executor = $detail['engineer'];
                $spongeDetail->job_supervisor = $detail['supervisor'];
                $spongeDetail->job_aid = $detail['aid'];
                $spongeDetail->job_description = $detail['job_description'];
                $spongeDetail->start_at = Carbon::createFromFormat('d/m/Y', $detail['start_at']);
                $spongeDetail->estimated_end = Carbon::createFromFormat('d/m/Y', $detail['estimated_end']);
                $spongeDetail->save();

                $spongeDetailHist = new SpongeDetailHist([
                    'sponge_detail_id'        => $spongeDetail->id,
                    'wo_number_id'            => $spongeDetail->wo_number_id,
                    'location_id'       => $spongeDetail->location_id,
                    'device_id'               => $spongeDetail->device_id,
                    'disturbance_category'    => $spongeDetail->disturbance_category,
                    'wo_description'          => $spongeDetail->wo_description,
                    'wo_attachment1'          => $spongeDetail->wo_attachment1,
                    'wo_attachment2'          => $spongeDetail->wo_attachment2,
                    'wo_attachment3'          => $spongeDetail->wo_attachment3,
                    'job_executor'          => $spongeDetail->job_executor,
                    'job_supervisor'          => $spongeDetail->job_supervisor,
                    'job_aid'          => $spongeDetail->job_aid,
                    'job_description'          => $spongeDetail->job_description,
                    'start_at'                => $spongeDetail->start_at,
                    'estimated_end'           => $spongeDetail->estimated_end,
                    'action'                  => 'UPDATE',
                    'created_by'              => Auth::user()->id,
                    'created_at'              => Carbon::now()->timezone('Asia/Jakarta'),
                    'updated_by'              => Auth::user()->id,
                    'updated_at'              => Carbon::now()->timezone('Asia/Jakarta'),
                ]);
                $spongeDetailHist->save();
            }

            DB::commit();
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

    public function notApprove(Request $request)
    {

        $spongeHeader = SpongeHeader::find($request->header_id);
        if (!$spongeHeader) {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Data Work Order tidak ditemukan. Pastikan kembali nomor WO tidak dibatalkan atau hubungi admin.</div>'
            ]);
        }

        //TRANSACTION
        try {
            DB::beginTransaction();
            $spongeHeader->status = $request->action;
            $spongeHeader->save();

            DB::commit();
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

    public function cancel(Request $request)
    {
        $spongeHeader = SpongeHeader::find($request->header_id);
        if (!$spongeHeader) {
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Data Work Order tidak ditemukan. Pastikan kembali nomor WO tidak dibatalkan atau hubungi admin.</div>'
            ]);
        }

        foreach ($request->detail as $detail) {
            $spongeDetail = SpongeDetail::find($detail['id']);
            if (!$spongeDetail) {
                return response()->json([
                    'errors' => true,
                    "message" => '<div class="alert alert-danger">Detail Work Order tidak ditemukan. Pastikan kembali nomor WO tidak dibatalkan atau hubungi admin.</div>'
                ]);
            }
        }

        //TRANSACTION
        try {
            DB::beginTransaction();
            $spongeHeader->spk_number = '';
            $spongeHeader->status = 'NOT APPROVE';
            $spongeHeader->save();


            foreach ($request->detail as $detail) {
                $spongeDetail = SpongeDetail::find($detail['id']);
                $spongeDetail->job_executor = null;
                $spongeDetail->job_supervisor = null;
                $spongeDetail->job_aid = null;
                $spongeDetail->job_description = '';
                $spongeDetail->start_at = null;
                $spongeDetail->estimated_end = null;
                $spongeDetail->save();
            }

            DB::commit();
            return response()->json([
                'success' => true,
                "message" => '<div class="alert alert-success">' . $spongeHeader->wo_number . ' berhasil dibatalkan</div>'
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

        $get_engineers = Role::where('role', 'ENGINEER')->where('active', 1)->pluck('user_id')->toArray();
        $engineers = [];
        foreach ($get_engineers as $id) {
            $engineer = User::where('id', $id)->where('active', 1)->first();
            if ($engineer) {
                $engineers[] = [
                    'id' => $engineer->id,
                    'name' => $engineer->name,
                    'nik' => $engineer->nik,
                ];
            }
        }

        $get_spvs = Role::where('role', 'SPV')->where('active', 1)->pluck('user_id')->toArray();
        $spvs = [];
        foreach ($get_spvs as $id) {
            $spv = User::where('id', $id)->where('active', 1)->first();
            if ($spv) {
                $spvs[] = [
                    'id' => $spv->id,
                    'name' => $spv->name,
                    'nik' => $spv->nik,
                ];
            }
        }

        $get_aids = Role::where('active', 1)->pluck('user_id')->toArray();
        $aids = [];
        foreach ($get_aids as $id) {
            $aid = User::where('id', $id)->where('active', 1)->first();
            if ($aid) {
                $aids[] = [
                    'id' => $aid->id,
                    'name' => $aid->name,
                    'nik' => $aid->nik,
                ];
            }
        }

        $index = 1;
        foreach ($spongedetails as $detail) {
            $device = Device::find($detail->device_id);
            $details[$index] = [
                'id' => $detail->id,
                'location' => Location::find($detail->location_id) ? Location::find($detail->location_id)->location : 'ID lokasi tidak ditemukan',
                'disturbance_category' => DeviceCategory::find($detail->disturbance_category) ? DeviceCategory::find($detail->disturbance_category)->disturbance_category : '-',
                'description' => $detail->wo_description,
                'image_path1' => $detail->wo_attachment1,
                'image_path2' => $detail->wo_attachment2,
                'image_path3' => $detail->wo_attachment3,
                'device' => $device->device_name,
                'device_model' => $device->brand,
                'device_code' => $device->activa_number,
                'start_at' => $detail->start_at ? Carbon::createFromFormat("Y-m-d H:i:s", $detail->start_at)->format('d/m/Y') : null,
                'estimated_end' => $detail->estimated_end ? Carbon::createFromFormat("Y-m-d H:i:s", $detail->estimated_end)->format('d/m/Y') : null,
                'engineer' => $detail->job_executor,
                'supervisor' => $detail->job_supervisor,
                'aid' => $detail->job_aid,
                'job_description' => $detail->job_description,
            ];
            $index++;
        }
        $count = count($details);

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
        if ($spongeheader->spk_number == '' || $spongeheader->spk_number == null) {
            $spk_number = str_pad($number, 5, '0', STR_PAD_LEFT) . '/' . 'SPKI/UP2BJTD/FASOP' . '/' . $dept_code . '/' . $year;
        } else {
            $spk_number = $spongeheader->spk_number;
        }
        /*WO NUMBER complete*/

        $data = [
            'spk_number' => $spk_number,
            'id' => $spongeheader->id,
            'status' => $spongeheader->status,
            'wo_number' => $spongeheader->wo_number,
            'wo_category' => $spongeheader->wo_category,
            'department' => Department::find($spongeheader->department_id)->department,
            'job_category' => $spongeheader->job_category,
            'effective_date' => Carbon::createFromFormat("Y-m-d H:i:s", $spongeheader->effective_date)->format('d/m/Y'),
            'engineers' => $engineers,
            'spvs' => $spvs,
            'aids' => $aids,
            'details' => $details,
            'length' => $count,
        ];

        return view('forms.approval.detail', $data);
    }

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


        $pdf = PDF::loadView('forms.approval.pdf.print', [
            'data' => $getData
        ])->setOptions(['dpi' => 150]);

        $pdf = $pdf->setPaper('a4', 'potrait');
        $documentNumber = str_replace('/', '', $getData[0]['spk_number']);

        $today = Carbon::now()->format('Y/m');
        Storage::put('dms/stok/' . $today . '/' . $documentNumber . '.pdf', $pdf->output());

        return $pdf->download($documentNumber . '.pdf');
    }
}

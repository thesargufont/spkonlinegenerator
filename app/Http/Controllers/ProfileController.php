<?php
  
namespace App\Http\Controllers;
  
use File;
use App\User;
use Exception;
use Carbon\Carbon;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\DepartmentHist;
use App\Exports\DepartmentExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Schema\Blueprint;
  
class ProfileController extends Controller
{
    public function index()
    {
        $userId = Auth::user()->id;
        $userData = User::where('id', $userId)->first();
        $departments = Department::where('active', 1)->get();
        $genders = ['PRIA', 'WANIATA'];
        
        return view('masters.user_profile.user_profile_index', [
            'userData' => $userData,
            'departments' => $departments,
            'genders' => $genders,
        ]);
    }

    public function newPassword(Request $request)
    {
        $userData = User::where('id', Auth::user()->id)->first();

        if(!Hash::check($request->password, $userData->password)){
            return response()->json(['errors' => true, 
                            "message"=> '<div class="alert alert-danger">Password lama salah, harap periksa kembali</div>'
                    ]);  
        }

        if(!Hash::check($request->new_password, Hash::make($request->confirm_password))){
            return response()->json(['errors' => true, 
                            "message"=> '<div class="alert alert-danger">Konfirmasi password tidak sesuai, harap periksa kembali</div>'
                    ]);  
        }

        if(strlen(trim($request->new_password)) == 0){
            return response()->json(['errors' => true, 
                            "message"=> '<div class="alert alert-danger">Format password baru salah, harap periksa kembali</div>'
                    ]); 
        } else if (strlen(trim($request->new_password)) < 8){
            return response()->json(['errors' => true, 
                            "message"=> '<div class="alert alert-danger">Format password baru tidak boleh kurang dari 8 karakter, harap periksa kembali</div>'
                    ]); 
        }

        if(strlen(trim($request->confirm_password)) == 0){
            return response()->json(['errors' => true, 
                            "message"=> '<div class="alert alert-danger">Format konfirmasi password baru salah, harap periksa kembali</div>'
                    ]); 
        } else if (strlen(trim($request->confirm_password)) < 8){
            return response()->json(['errors' => true, 
                            "message"=> '<div class="alert alert-danger">Format konfirmasi password baru tidak boleh kurang dari 8 karakter, harap periksa kembali</div>'
                    ]); 
        }

        try {
            DB::beginTransaction();

            $userData->password   = Hash::make($request->confirm_password);
            $userData->updated_by = Auth::user()->id;
            $userData->updated_at = Carbon::now();
            $userData->save();

            DB::commit();
            return response()->json([
                'success' => true,
                "message" => '<div class="alert alert-success">Data password berhasil diperbaharui</div>'
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Data gagal di proses, terjadi kesalah system</div>'
            ]);
        }
    }

    public function newSignature(Request $request)
    {
        try {
            $userData = User::where('id', Auth::user()->id)->first();

            if(!Storage::exists('public'.DIRECTORY_SEPARATOR.'signature'))
            {
                Storage::makeDirectory('public'.DIRECTORY_SEPARATOR.'signature');
            }

            $path = "public/signature/";

            $imageData = str_replace('data:image/png;base64,', '', $request->image);

            $decodedImage = base64_decode($imageData);
            $disk = Storage::disk('local');
            $filenameWithExtension = str_replace(" ","",Auth::user()->name).'.png';

            $filePath = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'signature'.DIRECTORY_SEPARATOR.$filenameWithExtension);
            $exists = Storage::exists('public'.DIRECTORY_SEPARATOR.'signature'.DIRECTORY_SEPARATOR.$filenameWithExtension);


            if($exists) {
                File::delete($filePath);
            }
            
            $disk->put($path . $filenameWithExtension, $decodedImage);
        
            DB::beginTransaction();

            $userData->signature_path = $path.$filenameWithExtension;
            $userData->updated_by     = Auth::user()->id;
            $userData->updated_at     = Carbon::now();
            $userData->save();

            DB::commit();
            return response()->json([
                'success' => true,
                "message" => '<div class="alert alert-success">Data tanda tangan berhasil diperbaharui</div>'
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'errors' => true,
                "message" => '<div class="alert alert-danger">Data gagal di proses, terjadi kesalah system</div>'
            ]);
        }
    }
}
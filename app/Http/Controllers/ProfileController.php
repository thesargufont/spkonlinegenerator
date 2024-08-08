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

    public function newSignature(Request $request)
    {
        if(!Storage::exists('public'.DIRECTORY_SEPARATOR.'signature'))
        {
            Storage::makeDirectory('public'.DIRECTORY_SEPARATOR.'signature');
        }

        $path = "public/signature/";

        $decodedImage = base64_decode($request->image);
        $disk = Storage::disk('local');
        $filenameWithExtension = str_replace(" ","",Auth::user()->name).'.jpeg';

        $filePath = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'signature'.DIRECTORY_SEPARATOR.$filenameWithExtension);
        $exists = Storage::exists('public'.DIRECTORY_SEPARATOR.'signature'.DIRECTORY_SEPARATOR.$filenameWithExtension);
        if($exists) {
            File::delete($filePath);
        }
        
        $disk->put($path . $filenameWithExtension, $decodedImage);

        dd('masuk sini', $decodedImage);
    }
}
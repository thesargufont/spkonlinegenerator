<?php
  
namespace App\Http\Controllers;
  
use App\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
  
class DeviceController extends Controller
{
    public function index()
    {
        return view('masters.device.device_index');
    }
}
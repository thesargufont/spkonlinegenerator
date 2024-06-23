<?php
  
namespace App\Http\Controllers;
  
use App\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
  
class LocationController extends Controller
{
    public function index()
    {
        return view('masters.location.location_index');
    }
}
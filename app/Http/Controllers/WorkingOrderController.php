<?php
  
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
  
class WorkingOrderController extends Controller
{
    public function index()
    {   
        return view('working_order');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Issue;
use App\Section;
use DB;

class SummaryController extends Controller
{
    public function index(){
    	/*$status =  DB::table('issues')
	               ->select('status', DB::raw('count(*) as total'))
	               ->groupBy('status')
	               ->lists('total','status');*/
        //return $status;

	   if(\Auth::user()->role == "admin")
	   {
		    $open = Issue::where('status', '=', 'Open')->count();
		    $closed = Issue::where('status', '=', 'Closed')->count();
		    $total = Issue::all()->count();

		    $management		     = Issue::where('section_id', '=', 1)->where('status', '!=', 'Closed')->count();
		    $papua	             = Issue::where('section_id', '=', 2)->where('status', '!=', 'Closed')->count();
		    $sulawesi_maluku	 = Issue::where('section_id', '=', 3)->where('status', '!=', 'Closed')->count();
		    $ceskal_bali_nusra   = Issue::where('section_id', '=', 4)->where('status', '!=', 'Closed')->count();
		    $sumatera_kalbar_nu	 = Issue::where('section_id', '=', 5)->where('status', '!=', 'Closed')->count();
		    $operation_support   = Issue::where('section_id', '=', 6)->where('status', '!=', 'Closed')->count();
		   

	    	$issuecount = Issue::all()->count();

	    	return view('summaryadmin', compact('issuecount', 'open', 'closed', 'total',
	    		'management', 'papua', 'sulawesi_maluku', 'ceskal_bali_nusra', 'sumatera_kalbar_nu', 'operation_support'));
       }

       if(\Auth::user()->role == "operator" || \Auth::user()->role == "member")
       {
       		$open1 = Issue::where('status', '=', 'Open')->where('section_id', '=', \Auth::user()->section_id)->count();
       		$open2 = Section::find(\Auth::user()->section_id)->issues()->where('status', '=', 'Open')->count();
       		$open = $open1 + $open2;
		    
		 
		    $closed1 = Issue::where('status', '=', 'Closed')->where('section_id', '=', \Auth::user()->section_id)->count();
       		$closed2 = Section::find(\Auth::user()->section_id)->issues()->where('status', '=', 'Closed')->count();
		    $closed = $closed1 + $closed2;
		    
		    $total1 = Issue::where('section_id', '=', \Auth::user()->section_id)->count();
		    $total2 = Section::find(\Auth::user()->section_id)->issues()->count();
		    $total = $total1 + $total2;

		    return view('summary', compact('open', 'closed','total'));
       }

    }
}

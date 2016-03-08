<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Issue;
use App\Section;

class ExportController extends Controller
{
    public function index(){
    	return view('export');
    }

    public function exportExcel(Request $request){

      $start_date = $request->start_date;
      $end_date = $request->end_date;

      $start_parse = \Carbon\Carbon::parse($start_date)->toFormattedDateString();
      $end_parse = \Carbon\Carbon::parse($end_date)->toFormattedDateString();

      $dataexport = Issue::whereBetween('issued_date', array( $start_date, $end_date) )->with('sections')->with('section')->get();
      //return $dataexport;

      if ($dataexport->count() == 0){
         return back()->with('error_message', trans("data not found on $start_parse - $end_parse"));
      } else {

    	\Excel::create("Head Meeting [ $start_parse - $end_parse ]", function($excel) use ($dataexport){

		    // set the properties
            $excel->setTitle('O&M BSS - Head Meeting')
                  ->setCreator('Adimas Lutfi, Web operation developer');

            $excel->sheet('MoM', function($sheet) use ($dataexport){
               $row = 1;
               $sheet->row($row, array(
                  'Weekly Meeting Report',
               ));

               $row = 4;
               $sheet->row($row, array(
                  'Section / Dept',
                  'Issue Description',
                  'Action Taken',
                 // 'PIC',
                  'Issue Date',
                  'Due Date',
                  'Completion Date',
                  'Status',
                  'Action',
                  'Date created'          
                  
               ));

               foreach($dataexport as $dataexport) {
                  $sheet->row(++$row, array(
                     $dataexport->section->name,
                     $dataexport->issue_description,
                     $dataexport->action_taken,
                     //$dataexport->pic,
                     $dataexport->issued_date,
                     $dataexport->due_date,
                     $dataexport->completion_date,
                     $dataexport->status,
                     $dataexport->action_update,
                     $dataexport->created_at,
                  ));
               }

            });

		})->export('xls');

    }

   }

    
}

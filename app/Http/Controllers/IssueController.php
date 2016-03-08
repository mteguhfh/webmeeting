<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Issue;
use App\Section;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Carbon;

class IssueController extends Controller
{
    /**
     * Display a listing of the resource.
     * i didnt really know what im doing.
     * @return Response
     */
    public function index()
    {
        if(\Auth::user()->role == "operator" && \Auth::user()->section_id == 1)
        {
            $manager_open = Issue::where('status', '=', 'Open')->where('section_id', '=', \Auth::user()->section_id)->count();
            $manager_closed = Issue::where('status', '=', 'Closed')->where('section_id', '=', \Auth::user()->section_id)->count();
            $manager_total = $manager_closed + $manager_open;

            $pp_open = Section::find(\Auth::user()->section_id)->issues()->where('issues.section_id', '=', 2)->where('status', '=', "Open")->count();
            $pp_closed =  Section::find(\Auth::user()->section_id)->issues()->where('issues.section_id', '=', 2)->where('status', '=', "Closed")->count();
            $pp_total = $pp_open + $pp_closed;

            $sm_open = Section::find(\Auth::user()->section_id)->issues()->where('issues.section_id', '=', 3)->where('status', '=', "Open")->count();
            $sm_closed =  Section::find(\Auth::user()->section_id)->issues()->where('issues.section_id', '=', 3)->where('status', '=', "Closed")->count();
            $sm_total = $sm_open + $sm_closed;

            $cbn_open = Section::find(\Auth::user()->section_id)->issues()->where('issues.section_id', '=', 4)->where('status', '=', "Open")->count();
            $cbn_closed =  Section::find(\Auth::user()->section_id)->issues()->where('issues.section_id', '=', 4)->where('status', '=', "Closed")->count();
            $cbn_total = $cbn_open + $cbn_closed;

            $skn_open = Section::find(\Auth::user()->section_id)->issues()->where('issues.section_id', '=', 5)->where('status', '=', "Open")->count();
            $skn_closed =  Section::find(\Auth::user()->section_id)->issues()->where('issues.section_id', '=', 5)->where('status', '=', "Closed")->count();
            $skn_total = $skn_open + $skn_closed; 

            $os_open = Section::find(\Auth::user()->section_id)->issues()->where('issues.section_id', '=', 6)->where('status', '=', "Open")->count();
            $os_closed =  Section::find(\Auth::user()->section_id)->issues()->where('issues.section_id', '=', 6)->where('status', '=', "Closed")->count();
            $os_total = $os_open +  $os_closed;

            $close_total = $pp_closed + $sm_closed + $cbn_closed + $skn_closed + $os_closed;
            $open_total = $pp_open + $sm_open + $cbn_open + $skn_open + $os_open;
            $all_total = $pp_total + $sm_total + $cbn_total + $skn_total + $os_total;

            return view('issue.index_manager', compact(
                'manager_open',
                'manager_closed',
                'manager_total',
                'pp_open',
                'pp_closed',
                'pp_total',
                'sm_open',
                'sm_closed',
                'sm_total',
                'cbn_open',
                'cbn_closed',
                'cbn_total',
                'skn_open',
                'skn_closed',
                'skn_total',
                'os_open',
                'os_closed',
                'os_total',
                'close_total',
                'open_total',
                'all_total'
            ));

        }

        if(\Auth::user()->role == "operator")
        {
            
            $issues1 = Issue::with('section', 'sections')->where('section_id', '=', \Auth::user()->section_id)->orderBy('due_date', 'asc')->get();
            $issues02 = Section::find(\Auth::user()->section_id)->issues()->orderBy('due_date', 'asc')->get();
            $issues2 = $issues02->load('section', 'sections');

            $issues = $issues1->merge($issues2);
            
            return view('issue.index', compact('issues'));
        }

        if(\Auth::user()->role == "admin")
        {
            $issues = Issue::with('section', 'sections')->orderBy('due_date', 'asc')->get();
            return view('issue.index', compact('issues'));
        }
    }

    public function managerAll(){
        $issues1 = Issue::with('section', 'sections')->where('section_id', '=', \Auth::user()->section_id)->orderBy('due_date', 'asc')->get();
        $issues02 = Section::find(\Auth::user()->section_id)->issues()->orderBy('due_date', 'asc')->get();
        $issues2 = $issues02->load('section', 'sections');

        $issues = $issues1->merge($issues2);
        
        return view('issue.index', compact('issues'));
    }

    /**
     * search data by query in every field of issue
     * grouped by section.
     * i dont really know what im doing :|
     * @return Mix
     */

    public function search(Request $request){

        if(\Auth::user()->role == "operator")
        {
            $issues1 = Issue::with('section')->where('section_id', '=', \Auth::user()->section_id)
               ->where( function ($query) use ($request)
                {
                    $query->orWhere('issue_description', 'like', "%$request->search%")
                    ->orWhere('action_taken', 'like', "%$request->search%")
                    ->orWhere('status', 'like', "%$request->search%")
                    ->orWhere('action_update', 'like', "%$request->search%");
                })->paginate(10);
            $issues02 = Section::find(\Auth::user()->section_id)->issues()
                ->where( function ($query) use ($request)
                {
                    $query->orWhere('issue_description', 'like', "%$request->search%")
                    ->orWhere('action_taken', 'like', "%$request->search%")
                    ->orWhere('status', 'like', "%$request->search%")
                    ->orWhere('action_update', 'like', "%$request->search%");
                })->paginate(10);
            $issues2 = $issues02->load('section', 'sections');
            $issues = $issues1->merge($issues2);  
        }else{
            $issues = Issue::with('section')
                 ->Where('issue_description', 'like', "%$request->search%")
                 ->orWhere('action_taken', 'like', "%$request->search%")
                 ->orWhere('status', 'like', "%$request->search%")
                 ->orWhere('action_update', 'like', "%$request->search%")
                 ->paginate(10);
        }

        return view ('issue.index', compact('issues'));
    }

    /**
     * sort data which having priority
     * @return response
     */

    public function prioritylist()
    {
        $title = "priority";
        $issues = Issue::where('priority', 1)->orWhere('priority', 2)->orderBy('priority', 'desc')->paginate(10);
        return view('issue.list-status', compact('issues', 'title'));
    }


    /**
     * sort data per section
     * @return Response
     */

    public function section($id)
    {
        $issues = Issue::where('section_id', '=', $id)->orderBy('issued', 'desc')->paginate(10);
        
        return view('issue.list', compact('issues', 'id'));
    }

    /**
     * listing issue by status
     * i know this not DRY, but this is fast solution will
     * refactor later
     * @return Response
     */

    public function open(){

        if(\Auth::user()->role == "operator")
        {
            $title = "Open";
            $issues1 = Issue::with('section', 'sections')->where('status', '=', 'Open')->where('section_id', '=', \Auth::user()->section_id)->orderBy('due_date', 'asc')->get();
            $issues02 = Section::find(\Auth::user()->section_id)->issues()->where('status', '=', 'Open')->orderBy('due_date', 'asc')->get();
            $issues2 = $issues02->load('section', 'sections');

            $issues = $issues1->merge($issues2);
            return view('issue.list-status', compact('issues', 'title'));
        }

        if(\Auth::user()->role =="admin"){
          $title = "Open";
          $issues = Issue::with('section', 'sections')->where('status', '=', 'Open')->orderBy('due_date', 'asc')->get();
          return view('issue.list-status', compact('issues', 'title'));
        }
    }

    public function closed(){

        if(\Auth::user()->role == "operator")
        {
            $title = "Closed";
            $issues1 = Issue::with('section','sections')->where('status', '=', 'Closed')->where('section_id', '=', \Auth::user()->section_id)->orderBy('due_date', 'asc')->get();
            $issues02 = Section::find(\Auth::user()->section_id)->issues()->where('status', '=', 'Closed')->orderBy('due_date', 'asc')->get();
            $issues2 = $issues02->load('section', 'sections');

            $issues = $issues1->merge($issues2);
            return view('issue.list-status', compact('issues', 'title' ));
        }

        if(\Auth::user()->role =="admin"){
          $title = "Closed";
          $issues = Issue::with('section', 'sections')->where('status', '=', 'Closed')->orderBy('due_date', 'asc')->get();
          return view('issue.list-status', compact('issues', 'title'));
        }
    }

    public function opensection($sect_id){

        if(\Auth::user()->role == "operator" && \Auth::user()->section_id == 1 )
        {
            if($sect_id == 1)
            {
                $title = "Open";
                $issues = Issue::with('section', 'sections')->where('status', '=', 'Open')->where('section_id', '=', \Auth::user()->section_id )->orderBy('due_date', 'asc')->get();
                return view('issue.list-status', compact('issues', 'title'));
            }else{
               $title = "Open";
                $issues = Section::find(\Auth::user()->section_id)->issues()->where('issues.section_id', '=', $sect_id)->where('status', '=', "Open")->get();
                $issues = $issues->load('section', 'sections');
                return view('issue.list-status', compact('issues', 'title'));
            } 
        } else{
            return back();
        }
    }

    public function closedsection($sect_id){
        if(\Auth::user()->role == "operator" && \Auth::user()->section_id == 1 )
        {
            if($sect_id == 1)
            {
                $title = "Close";
                $issues = Issue::with('section', 'sections')->where('status', '=', 'Close')->where('section_id', '=', \Auth::user()->section_id )->orderBy('due_date', 'asc')->get();
                return view('issue.list-status', compact('issues', 'title'));
            } else{
                $title = "Closed";
                $issues = Section::find(\Auth::user()->section_id)->issues()->where('issues.section_id', '=', $sect_id)->where('status', '=', "Closed")->get();
                $issues = $issues->load('section', 'sections');
                return view('issue.list-status', compact('issues', 'title'));
            }
        } else{
            return back();
        }
    }

    public function totalsection($sect_id){
        if(\Auth::user()->role == "operator" && \Auth::user()->section_id == 1 )
        {
            $title = "All";
            if($sect_id == 1)
            {
               $issues = Issue::with('section','sections')->where('section_id', '=', 1)->orderBy('due_date', 'asc')->get();
               return view('issue.list-status', compact('issues', 'title'));   
            } else{

                $issues = Section::find(\Auth::user()->section_id)->issues()->where('issues.section_id', '=', $sect_id)->get();
                $issues = $issues->load('section', 'sections');
                return view('issue.list-status', compact('issues', 'title'));
            }
        } else{
            return back();
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $section = Section::lists('name', 'id');
        return view ('issue.create', compact('section', 'readonly'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //validate input
        $validator = $this->validate($request, [
                        'issue_description'   => 'required',
                        'action_taken'        => 'required',
                        'due_date'            => 'required|date',
                       
        ]);

        $issue = new Issue;
        if(\Auth::user()->role == "admin")
        {
          $issue->section_id = $request->input('section_id');  
        }else{
            $issue->section_id = \Auth::user()->section_id;  
        }
        
        $issue->issue_description = $request->input('issue_description');
        $issue->action_taken = $request->input('action_taken');
        $issue->issued_date = $request->input('issued_date');
        $issue->due_date = $request->input('due_date');
        $issue->completion_date = $request->input('completion_date');
        $issue->priority = $request->input('priority');
        $issue->user_update = \Auth::user()->name;
        if(!empty($request->input('completion_date') &&  $request->input('completion_date') != '0000-00-00'))
        {
            $issue->status = "Closed";   
        }else{
            $issue->status = $request->input('status');
        }
        $issue->action_update = $request->input('action_update');
        $issue->save(); 
        if( !empty($request->input('section_list')) )
        {
             $issue->sections()->sync($request->input('section_list'));
        }
    

        //return redirect back to index

        \Session::flash('flash_message', "Issue data has been created");
        return redirect("issue/$issue->id");

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $issue = Issue::findOrFail($id);
        $sections = $issue->sections;

        //parsed carbon date
        $ctarget = \Carbon\Carbon::parse($issue->target);
        $overday = $ctarget->diffInDays(\Carbon\Carbon::now(), false);
        $extended_target = \Carbon\Carbon::parse($issue->extended_target);
        $overday_extended = $ctarget->diffInDays( $extended_target, false);

        return view('issue.show', compact('issue', 'overday', 'overday_extended', 'sections'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        
        $section = Section::lists('name', 'id');
        $issue = Issue::findOrFail($id);
        return view('issue.edit', compact('issue', 'section', 'readonly'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
      
        $validator = $this->validate($request, [
            'issue_description'   => 'required',
            'action_taken'        => 'required',
            'due_date'            => 'required|date',
            'status'              => 'required',
        ]);

       $issue = Issue::findOrFail($id);
       if(\Auth::user()->role == "admin")
       {
          $issue->section_id = $request->input('section_id');  
       }
        
       $issue->issue_description = $request->input('issue_description');
       $issue->action_taken = $request->input('action_taken');
       
       $issue->due_date = $request->input('due_date');

       $issue->user_update = \Auth::user()->name;
       if(!empty($request->input('completion_date') &&  $request->input('completion_date') != '0000-00-00'))
        {
            $issue->status = "Closed";   
        }else{
            $issue->status = $request->input('status');
        }
       $issue->action_update = $request->input('action_update');

       // ok save user
       $issue->save();
       if( !empty($request->input('section_list')) )
       {
          $issue->sections()->sync($request->input('section_list'));
       }else{
          $issue->sections()->sync([]);
       }
     
       //$issue->update($request->all());

       \Session::flash('flash_message', "Issue data has been updated!");
        return redirect("issue/$issue->id");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        // delete
        $issue = Issue::find($id);
        $issue->delete();

        // redirect
        \Session::flash('flash_message', "Issue data has been succesfully deleted");

        return redirect('issue/');
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vtr = $this->isNewVisit();

        return response()->json([
            "visitor" => $vtr
        ]);
    }

    public function isNewVisit(){
        $obj = "";
        $today = date('Y-m-d',time());
        $visit = Visitor::where('ip',getUserIp())
                        ->whereDate('created_at','=',$today)
                        ->get();
        if(count($visit) < 1):
            // not record yet do one now 
            $vData = [
                "ip" => getUserIp(),
                "os" => getUserOs(),
                "browser" => getUserBrowser(),
                "device" => getUserDevice()
            ];

            Visitor::create($vData);

            // make backup
            Visitor::makeBackup();
        endif;

        $v_all = Visitor::all()->count();
        $v_today = Visitor::whereDate('created_at','=',date('Y-m-d',time()))
                            ->get()
                            ->count();
        // object to return data
        $obj = [
            "visited_month" => $this->visitedMonth(),
            "visited_year" => $this->visitedYear(),
            "visited_today" => $v_today,
            "visited_all" => $v_all,
            "ip" => getUserIp(),
            "os" => getUserOs(),
            "browser" => getUserBrowser(),
            "device" => getUserDevice()
        ];

        return $obj;
    }

    public function visitedMonth(){
        $visit = Visitor::whereMonth('created_at','=',date('m',time()))
                        ->whereYear('created_at','=',date('Y',time()))
                        ->get();
        $count = count($visit);
        return $count;
    }

    public function visitedYear(){
        $visit = Visitor::whereYear('created_at','=',date('Y',time()))
                        ->get();
        $count = count($visit);
        return $count;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Visitor  $visitor
     * @return \Illuminate\Http\Response
     */
    public function show(Visitor $visitor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Visitor  $visitor
     * @return \Illuminate\Http\Response
     */
    public function edit(Visitor $visitor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Visitor  $visitor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Visitor $visitor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Visitor  $visitor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Visitor $visitor)
    {
        //
    }
}

<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Issue;
use App\Metric;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MetricController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //
        $data = [];
        if ($request->metric === 'issuesCount') {
            switch ($request->period) {
                case 'month':
                    $period = Carbon::now()->subMonth();
                    break;

                case 'week':
                    $period = Carbon::now()->subWeek();
                    break;

                case 'day':
                    $period = Carbon::now()->subDay();
                    break;

                case 'quarter':
                    $period = Carbon::now()->subQuarter();
                    break;

                case 'year':
                    $period = Carbon::now()->subYear();
                    break;

                default:
                    $period = Carbon::now()->subWeek();
            }
            $datePeriod = CarbonPeriod::create($period, Carbon::now());
            foreach ($datePeriod as $date) {
                $data[$date->format('Y-m-d')] = 0;
            }
            $issues = Issue::where('created_at', '>=', $period)->where('organization_id', '=', Auth::user()->organization->id)->groupBy('date')
                ->get(array(
                    DB::raw('Date(created_at) as date'),
                    DB::raw('COUNT(*) as count')
                ));

            foreach ($issues as $date) {
                $data[$date->date] = $date->count;
            }
        }

        return $data;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Metric $metric
     * @return \Illuminate\Http\Response
     */
    public function edit(Metric $metric)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Metric $metric
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Metric $metric)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Metric $metric
     * @return \Illuminate\Http\Response
     */
    public function destroy(Metric $metric)
    {
        //
    }
}
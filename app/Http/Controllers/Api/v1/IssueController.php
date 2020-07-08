<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Issue;
use App\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class IssueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $organization = Auth::user()->organization;
        if ($organization->isClient()) {
            $type = 'author_organization_id';
        } else {
            $type = 'organization_id';
            if ($request->get('organization')) {
                $type = 'author_organization_id';
                $organization = Organization::findOrFail($request->get('organization'));
            }
        }
        $columns = ['id', 'title', null, null, 'created_at'];
        $search = $request->get('search')['value'];
        $issues = Issue::where($type, $organization->id);
        if ($request->get('status')) {
            $issues = $issues->where('issue_status_id', '=', $request->get('status'));
        }
        if ($request->get('employee')) {
            $employeeId = ($request->get('employee') === 'my' ? Auth::user()->id : $request->get('employee'));
            $issues = $issues->where('employee_id', '=', $employeeId);
        }
        $issues = $issues->where(function ($query) use ($search) {
            $query->where('title', 'LIKE', '%' . $search . '%')
                ->OrWhereHas('author', function (Builder $query) use ($search) {
                    $query->where('title', 'LIKE', '%' . $search . '%');
                })->OrWhereHas('employee', function (Builder $query) use ($search) {
                    $query->where('title', 'LIKE', '%' . $search . '%');
                })->OrWhereHas('status', function (Builder $query) use ($search) {
                    $query->where('title', 'LIKE', '%' . $search . '%');
                })->OrWhereHas('author', function (Builder $query) use ($search) {
                    $query->whereHas('organization', function (Builder $query) use ($search) {
                        $query->where('title', 'LIKE', '%' . $search . '%');
                    });
                });
        });
        foreach ($request->get('order') as $order) {
            $issues = $issues->orderBy($columns[$order['column']], $order['dir']);
        }
        $issues = $issues->get();
        $filteredCount = count($issues);
        $issues = $issues->slice($request->get('start'), $request->get('length'))->values();
        $issuesCount = $organization->issues->count();
        return array('data' => $issues, 'recordsTotal' => $issuesCount, 'recordsFiltered' => $filteredCount, 'draw' => $request->get('draw'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public
    function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public
    function store(Request $request)
    {
        //
        $request->validate([
            'title' => 'required|max:50|min:4',
            'description' => 'max:10000000',
        ]);

        $organization = Auth::user()->organization;
        $observers = $request->observer_ids;
        $request = $request->except('author', 'observer_ids');

        $issueStatus = $organization->issueStatuses()->where('type_id', '=', 2)->first();
        if (!$issueStatus) {
            $issueStatus = $organization->issueStatuses()->first();
        }

        $issue = new Issue($request);
        $issue->author_id = Auth::user()->id;
        $issue->issue_status_id = $issueStatus->id;
        $issue->author_organization_id = $organization->id;
        $issue->organization_id = ($organization->isClient() ? $organization->parent_id : $organization->id);

        $issue->save();

        $issue->observers()->attach($observers);

        return array('status' => 'success', 'created' => true, 'message' => 'Заявка создана', 'issue' => $issue);
    }

    /**
     * Display the specified resource.
     *
     * @param Issue $issue
     * @return Issue
     */
    public
    function show(Issue $issue)
    {
        //
        return $issue->load('type', 'priority', 'observers')->append('favorite');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Issue $issue
     * @return Response
     */
    public
    function edit(Issue $issue)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Issue $issue
     * @return Issue
     */
    public
    function update(Request $request, Issue $issue)
    {
        //
        $issue->update($request->all());
        return array('status' => 'success', 'updated' => true, 'message' => 'Заявка обновлена', 'issue' => $issue->fresh()->load('type', 'priority', 'observers')->append('favorite'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Issue $issue
     * @return Response
     */
    public
    function destroy(Issue $issue)
    {
        //
    }
}

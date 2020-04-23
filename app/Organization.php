<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed clients
 * @property mixed parent_id
 * @property mixed id
 */
class Organization extends Model
{
    //

    protected $appends = ['image'];

    public function issueStatuses()
    {
        return $this->hasMany('App\IssueStatus');
    }

    public function issues()
    {
        $key = ($this->isClient()) ? 'author_organization_id' : 'organization_id';
        return $this->hasMany('App\Issue', $key);
    }

    public function isClient()
    {
        return ($this->parent_id) ? true : false;
    }

    public function clientIssues()
    {
        return $this->hasMany('App\Issue', 'author_organization_id');
    }

    public function clients()
    {
        return $this->hasMany('App\Organization', 'parent_id')->withCount('clientIssues');
    }

    public function parent()
    {
        return $this->belongsTo('App\Organization', 'parent_id');
    }

    public function getImageAttribute()
    {
        return asset('/storage/clients/' . $this->id . '.jpg');
    }
}
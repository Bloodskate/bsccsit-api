<?php

/**
 * Tags Model
 *
 * @author Prasanna Mishra
 */

class Tag extends \Illuminate\Database\Eloquent\Model
{ 
    protected $table = 'tags';
    protected $fillable = ['id', 'name'];
    protected $dates = ['created_at', 'updated_at']; 

    public function getDates()
    {
        return [];
    }

    public function projects()
    {
        return $this->belongsToMany('Project');
    }
}
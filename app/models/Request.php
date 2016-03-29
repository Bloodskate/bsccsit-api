<?php

/**
 * Request Model
 *
 * @author Prasanna Mishra
 */

class Request extends \Illuminate\Database\Eloquent\Model
{ 
    protected $table = 'requests';
    protected $fillable = ['id', 'user_id', 'project_id'];
    protected $dates = ['created_at', 'updated_at']; 

    public function getDates()
    {
        return [];
    }
}
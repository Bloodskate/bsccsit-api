<?php

/**
 * Survey Model
 *
 * @author Prasanna Mishra
 */

class Survey extends \Illuminate\Database\Eloquent\Model
{ 
    protected $table = 'survey';
    protected $fillable = ['id', 'email', 'name'];
    protected $dates = ['created_at', 'updated_at']; 

    public function getDates()
    {
        return [];
    }
}
<?php

/**
 * Community Model
 *
 * @author Prasanna Mishra
 */

class Community extends \Illuminate\Database\Eloquent\Model
{ 
    protected $table = 'communities';
    protected $fillable = ['id', 'title', 'isVerified', 'extra'];
    protected $dates = ['created_at', 'updated_at']; 

    public function getDates()
    {
        return [];
    }
}
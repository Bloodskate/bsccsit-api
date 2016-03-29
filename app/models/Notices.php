<?php

/**
 * T.U. Notices Model
 *
 * @author Prasanna Mishra
 */

class Notice extends \Illuminate\Database\Eloquent\Model
{ 
    protected $table = 'notices';

    protected $fillable = ['id', 'title', 'notice', 'time'];

    protected $dates = ['created_at', 'updated_at']; 
    
    public function getDates()
    {
        return [];
    }
}
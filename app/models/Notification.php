<?php

/**
 * Notifications Model
 *
 * @author Prasanna Mishra
 */

class Notification extends \Illuminate\Database\Eloquent\Model
{ 
    protected $table = 'notifications';

    protected $fillable = ['id', 'title', 'description', 'deeplink', 'show'];

    protected $dates = ['created_at', 'updated_at']; 
    
    public function getDates()
    {
        return [];
    }
}
<?php

/**
 * User Model
 *
 * @author Prasanna Mishra
 */

class User extends \Illuminate\Database\Eloquent\Model
{ 
    protected $table = 'users';
    protected $fillable = ['name', 'id', 'email', 'phone_number', 'semester', 'college', 'gender', 'location', 'communities'];
    protected $dates = ['created_at', 'updated_at']; 

    public function requests(){

    	return $this->hasMany('Request');

    }

    public function ownProjects(){

    	return $this->hasMany('Project');

    }


    public function projects()
    {
        return $this->belongsToMany('Project');
    }

    public function getDates()
    {
        return [];
    }
}
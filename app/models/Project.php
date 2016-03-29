<?php

/**
 * Projects Model
 *
 * @author Prasanna Mishra
 */

class Project extends \Illuminate\Database\Eloquent\Model
{ 
    protected $table = 'projects';
    protected $fillable = ['id', 'user_id', 'title', 'description', 'tags', 'reuired_users', 'num_users', 'status'];
    protected $dates = ['created_at', 'updated_at']; 

    public function requests(){

    	return $this->hasMany('Request');

    }


    public function users()
    {
        return $this->belongsToMany('User');
    }
    
    public function getDates()
    {
        return [];
    }

    public function tags()
    {
    	return $this->belongsToMany('Tag');
    }
}
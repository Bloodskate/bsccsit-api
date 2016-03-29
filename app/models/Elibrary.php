<?php

/**
 * Elibrary Model
 *
 * @author Prasanna Mishra
 */

class Elibrary extends \Illuminate\Database\Eloquent\Model
{ 
    protected $table = 'elibrary';
    protected $fillable = ['id', 'title', 'source', 'semester', 'tag', 'filename', 'link'];
    protected $dates = ['created_at', 'updated_at']; 

    public function getDates()
    {
        return [];
    }
}
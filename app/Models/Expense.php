<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
     use SoftDeletes;
    use Uuids;
  public $timestamps = true;
  public $incrementing = false;
    protected $primaryKey = "id";
	          
	protected $fillable = ['title','amount','expense_no','note', 'date'];
	 public $table = 'expenses';

}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Contributors_payment extends Model
{
     use SoftDeletes;
    use Uuids;
  public $timestamps = true;
  public $incrementing = false;
    protected $primaryKey = "payment_id";
	          
	protected $fillable = ['client_id','payment_amount','payment_date','balance','paymenttype_id', 'note', 'payment_no'];
	 public $table = 'contributors_payments';

}


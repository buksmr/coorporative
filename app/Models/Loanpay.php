<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Loanpay extends Model
{
     use SoftDeletes;
    use Uuids;
  public $timestamps = true;
  public $incrementing = false;
    protected $primaryKey = "payment_id";
	          
	protected $fillable = ['loan_id','payment_amount','payment_date','balance','paymenttype_id', 'note', 'payment_no', 'client_id', 'no_month'];
	 public $table = 'loanpays';

}


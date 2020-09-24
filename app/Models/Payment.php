<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends UuidModel
{
    use SoftDeletes;
  use Uuids;
  public $timestamps = true;
  public $incrementing = false;
    protected $primaryKey = "payment_id";
	
	protected $fillable = ['invoice_id','payment_amount','payment_date','balance','paymenttype_id', 'note', 'payment_no', 'no_month'];
	 public $table = 'payments';
}
 
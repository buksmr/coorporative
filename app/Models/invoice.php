<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes; 

class invoice extends UuidModel

{
	use SoftDeletes;
  use Uuids;
  public $timestamps = true;
  public $incrementing = false;
    protected $primaryKey = "invoice_id";
	
	protected $fillable = ['user_id', 'client_id', 'company_id', 'invoice_number', 'date','expires_on', 'discount', 'currency_id', 'summary', 'terms', 'footer', 'invoice_template','balance', 'invoice_status_id','total', 'total_paid_amount'];
	 public $table = 'invoices';
}

 
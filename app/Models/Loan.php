<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
     use SoftDeletes;
    use Uuids;
    public $timestamps = true;
      public $incrementing = false;
    public $primaryKey = "loan_id";
    protected $fillable = ['client_id', 'user_id', 'amount','loan_no', 'disburse_account', 'disburse_bank', 'amount_disbursed','monthly_payment', 'paid_amount', 'balance', 'payment_plan', 'interest_rate', 'note', 'status', 'date', 'month_year'];
    public $table = 'loans';  
}
     
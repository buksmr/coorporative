<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes; 
use Illuminate\Database\Eloquent\Model;

class Client extends UuidModel

{
	use SoftDeletes;
  use Uuids;

    protected $primaryKey = "id";
	
	protected $fillable = ['client_name', 'surname', 'first_name', 'last_name', 'email', 'address', 'city', 'state','postal_code', 'country', 'amount', 'phone_number', 'fax_number', 'office_unit', 'default_currency', 'tax_code', 'status'];
	 public $table = 'clients';



}

 
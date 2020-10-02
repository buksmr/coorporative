<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Company;
use App\Models\Currencies;
use App\Models\invoice;
use App\Models\invoice_amount;
use App\Models\invoice_attachments;
use App\Models\invoice_item;
use App\Models\invoice_item_amount;
use App\Models\invoice_notes;
use App\Models\Invoice_status_master;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\productmap;
use App\Models\System;
use App\Models\TaxRate;
use App\Models\User;
use Auth;
use Elibyy\TCPDF\TCPDFHelper;
use TCPDF;
use File;
use Illuminate\Http\Request;
use Input;
use Response;

class invoiceController extends Controller {


	public function index() {

		$company = company::select('id', 'company_name')

			->where('deleted_at', '=', NULL)->get();

		$invoice = invoice::select('invoice_amounts.total_amount as total_amount', 'invoices.invoice_id as invoice_id', 'invoice_number', 'date', 'total', 'total_paid_amount', 'expires_on', 'balance', 'currency_id', 'invoice_status_master.invoicestatus', 'users.name as username')
->leftJoin('users', 'invoices.user_id', '=', 'users.id')
			->leftJoin('invoice_amounts', 'invoices.invoice_id', '=', 'invoice_amounts.invoice_id')
			->leftJoin('invoice_item_amounts', 'invoices.invoice_id', '=', 'invoice_item_amounts.invoice_id')
			->leftjoin('invoice_status_master', 'invoices.invoice_status_id', '=', 'invoice_status_master.invoicestatus_id')
			->where('invoices.deleted_at', '=', NULL)
            ->orderBy('invoices.created_at', 'DESC')
            ->get();

		//echo "<pre>";print_r($invoice);exit;
		$invoiceviewArray = array();
		foreach ($invoice as $key => $value) {
			$CurrenciesData = Currencies::select('symbol', 'placement', 'thousands', 'decimal')->where('id', '=', $value->currency_id)->first();
			$today = date('Y-m-d');

			$date = $value->expires_on;
			$is_date = 0;
			if ($date <= $today) {

				$is_date = 1;
			}

			array_push($invoiceviewArray, [

				//"currency_id" => Currencies::select('symbol')->where('id','=',$value->currency_id)->first()['symbol'],
				"currencies_id" => $CurrenciesData['symbol'],
				"currencies_placement" => $CurrenciesData['placement'],
				"currencydecimal" => $CurrenciesData['decimal'],
				"currencythousands" => $CurrenciesData['thousands'],
				"invoice_id" => $value->invoice_id,
				"invoice_number" => $value->invoice_number,
				"date" => $value->date,
				"expires_on" => $value->expires_on,
				"client_name" => $value->client_name,
				"total_amount" => $value->total_amount,
				"balance" => $value->balance,
                "username" => $value->username,
				"total" => number_format($value->total, 2),
				"total_paid_amount" => number_format($value->total_paid_amount, 2),
				"status" => $value->invoicestatus,
				"is_date" => $is_date,

			]);

		}

		// echo "<pre>";print_r($invoiceviewArray);exit;
		//echo "<pre>";print_r($invoiceviewArray);exit;
		return response::json(['error' => false, 'message' => "success", "invoiceview" => $invoiceviewArray, "company" => $company], 200);

	}


	public function store(Request $request) {
		$formData = $request->all();

		//echo "<pre>";print_r($request->all());exit;
		$client = client::where('client_name', $request->client_id)->first();
		//   $NewDate=date('y:m:d', strtotime("+10 days"));
		//echo "<pre>";print_r($NewDate);exit;
		$client_id = $client->id;

		$invoice = invoice::create([
			"user_id" => Auth::User()->id,
			"client_id" => $client_id,
			"date" => $request->date,
			"company_id" => $request->company,
			"invoice_template" => $request->invoice_template,
			"created_at" => date('Y-m-d H:i:s'),

			//$invoice_id = $invoice->id;
		]);
		//echo "<pre>";print_r($invoice);exit;

		$invoice_id = $invoice->invoice_id;
		return response()->json(['status' => 1, 'message' => "stored successfully", 'id' => $invoice_id, "client_id" => $client_id], 200);

		//	  return response()->json(['status' => 1, 'message' => "success", ''id'' => $invoice_id], 200);
	}

	public function show($id) {
		//echo "<pre>";print_r($id);exit;

		$client = invoice::select('client_name', 'email', 'status', 'address', 'city', 'state', 'postal_code', 'country', 'phone_number', 'mobile_number', 'fax_number', 'web_site', 'tax_code', 'default_currency', 'date')
			->leftJoin('clients', 'invoices.client_id', '=', 'clients.id')
			->where('invoice_id', $id)->first();

		//echo "<pre>";print_r($client);exit;

		$ClientArray = [

			'client_name' => $client->client_name,
			'email' => $client->email,
			'address' => $client->address,
			'city' => $client->city,
			'state' => $client->state,
			'postal_code' => $client->postal_code,
			'country' => $client->country,
			'phone_number' => $client->phone_number,
			'mobile_number' => $client->mobile_number,
			'web_site' => $client->web_site,
			'tax_code' => $client->tax_code,
			'default_currency' => $client->default_currency,
			'status' => $client->status,
			'date' => $client->date,
		];

		$company = invoice::where('invoice_id', $id)->first();

		$company_id = $company->company_id;

		$companydetails = Company::select('company_name', 'address', 'city', 'state', 'country', 'zipcode', 'mobile', 'website')

			->where('id', $company_id)->first();

		$date = invoice::where('invoice_id', $id)->first();

		$invoicedate = $date->date;

		//echo "<pre>";print_r($company_id);exit;

		$CompanyArray = [
			'company_name' => $companydetails->company_name,
			'address' => $companydetails->address,
			'city' => $companydetails->city,
			'state' => $companydetails->state,
			'zipcode' => $companydetails->zipcode,
			'country' => $companydetails->country,
			'mobile' => $companydetails->mobile,
			'website' => $companydetails->website,

		];

		$InvoiceDate = date("Y-m-d", strtotime($invoicedate));
		//echo "<pre>";print_r($Invoicedate);exit;
		$expiryDate = date("Y-m-d", strtotime($invoicedate . '+10 days'));
		return response::json(['error' => false, 'message' => "success", "expiryDate" => $expiryDate, "InvoiceDate" => $InvoiceDate, "clientdetails" => $ClientArray, "companydetails" => $CompanyArray], 200);

	}

	public function update(Request $request) {
		//echo "<pre>"; print_r($request->all());exit;
		$n = $request['number_of_products'];
		$formdata = $request->all();

		$product_names = array();
		$descriptions = array();
		$quantities = array();
		$prices = array();
		$tax_rate_ids = array();
		$totals = array();
		for ($i = 0; $i < $n; $i++) {
			//echo "<pre> Hai"; print_r($request['products'.$i.'product_name']); exit;
			array_push($product_names, $request['products' . $i . 'product_name']);
			array_push($descriptions, $request['products' . $i . 'description']);
			array_push($quantities, $request['products' . $i . 'quantity']);
			array_push($prices, $request['products' . $i . 'price']);
			array_push($tax_rate_ids, $request['products' . $i . 'tax_rate_id']);
			array_push($totals, $request['products' . $i . 'total']);

		}

		//echo "<pre>"; print_r($product_names);exit;
		$temp_product_names = $product_names;
		$product_names = array();
		$product_weight = array();

		for ($i = 0; $i < $n; $i++) {

			$temp = explode("(", $temp_product_names[$i]);
			array_push($product_names, $temp[0]);

		}

		//echo "<pre>"; print_r($product_names);exit;

		$notes = $request->input('note');
		$file_names = $request->input('file_name');
		//echo "<pre>";print_r($formdata);

		$date = $request['date'];
		$expires_on = $request['expires_on'];

		$getinvoices = invoice::create([
			"user_id" => Auth::User()->id,
			"date" => date('Y-m-d'),
			'invoice_number' => $formdata['invoice_number'],
			'total_paid_amount' => $formdata['subtotal'],
			'total' => $formdata['wholetotal'],

			//$invoice_id = $invoice->id;
		]);

		//echo "<pre>"; print_r($getinvoices);exit;

		$invoice_id = $getinvoices->invoice_id;

		for ($i = 0; $i < $n; $i++) {

			$getTaxRate = TaxRate::where('percentage', $tax_rate_ids[$i])->first();
			$tax_rate_id = $getTaxRate->id;
			$tax_rate_percentage = $getTaxRate->percentage;

			$getProduct = product::where('product_name', $product_names[$i])
				->first();

			$product_map_id = $getProduct->id;

			//echo "<pre>";print_r($product_map_id);exit;

			$invoice_item = invoice_item::create([

				"product_id" => $product_map_id,
				"invoice_id" => $invoice_id,
				"product_name" => $product_names[$i],
				"description" => $descriptions[$i],
				"tax_rate_id" => $tax_rate_id,
				"total" => $totals[$i],
				"tax_value" => $tax_rate_ids[$i],
				"quantity" => $quantities[$i],
				"price" => $prices[$i],

			]);

			$invoice_items = invoice_item::select('quantity')->where('product_id', $product_map_id)->get();

	

          $selling_price_per = $totals[$i] / $quantities[$i];

			$product_update = [
				'selling_price' => $totals[$i],
                'selling_price_per'=>$selling_price_per

			];

			$invoice = Product::where('id', $product_map_id)->increment('total_quantity', $quantities[$i], $product_update);

		}

		//echo "<pre>";print_r($product_map_id);exit;
		$invoice_id = $getinvoices->invoice_id;

		$invoice_amount = invoice_amount::create([

			"invoice_id" => $invoice_id,
			"item_tax_total" => $request->input('totaltax'),
			"discount_amount" => $request->input('discountval'),
			"item_sub_total" => $request->input('subtotal'),
			"total_amount" => $request->input('wholetotal'),
			"created_at" => date('Y-m-d H:i:s'),

		]);

		$invoice_item_id = $invoice_item->item_id;
		$sub_total_not_dis = 0;
		for ($j = 0; $j < count($totals); $j++) {
			$sub_total_not_dis += $totals[$j];
		}

		$invoice_item_amount = invoice_item_amount::create([

			"invoice_id" => $invoice_id,
			"item_id" => $invoice_item_id,
			"tax" => $request->input('totaltax'),
			"subtotal_not_dis" => $sub_total_not_dis,
			"discount_amount" => $request->input('discountval'),
			"sub_total" => $request->input('subtotal'),
			"total_amount" => $request->input('wholetotal'),
			"created_at" => date('Y-m-d H:i:s'),

		]);

//echo "<pre>";print_r($invoice_item_amount);exit;

		$note = explode(',', $request['note']);
		foreach ($note as $notes) {
			$invoice_notes = invoice_notes::create([
				"invoice_id" => $invoice_id,
				"note" => $notes,
				"created_at" => date('Y-m-d H:i:s'),
			]);
		}

		$invoice_id = $getinvoices->invoice_id;
		if ($request->hasfile('images')) {
			foreach ($request->file('images') as $key => $file_name) {
				$name = $file_name->getClientOriginalName();
				$type = $file_name->getMimeType();
				$size = $file_name->getSize();
				//dd($file_name->getSize());
				$path = public_path() . '/files/' . $name;
				//dd($path);
				$file_name->move(public_path() . '/files/', $name);

				$invoice_attachments = invoice_attachments::create([

					"invoice_id" => $invoice_id,
					"file_name" => $name,
					"file_size" => $size,
					"file_type" => $type,
					"file_path" => $path,
					"created_at" => date('Y-m-d H:i:s'),
				]);
			}
		}

		$paid = 'Paid';
		$sent = 'Sent';

		$paymentsent = invoice_status_master::where('invoicestatus', $sent)->first();

		$sentpayment_id = $paymentsent->invoicestatus_id;

		$payment = invoice_status_master::where('invoicestatus', $paid)->first();

		$payment_id = $payment->invoicestatus_id;
		//echo "<pre>";print_r($payment_id);exit;

		if ($formdata['invoice_status_id'] == $payment_id) {

			$invoicepayment = $sentpayment_id;

		} else {

			$invoicepayment = $formdata['invoice_status_id'];
		}

		$updateData = [
			'invoice_number' => strtoupper(str_random(6)),
			// 'quote_no' => $request->input('quote_no'),
			'date' => date('Y-m-d', strtotime($date)),
			'expires_on' => date('Y-m-d', strtotime($expires_on)),
			'currency_id' => $formdata['currency_id'],
			'invoice_status_id' => $invoicepayment,
			'discount' => $formdata['discount'],
			'terms' => $formdata['terms'],
			'footer' => $formdata['footer'],
			'balance' => $formdata['balance'],
			'total' => $formdata['balance'],

		];
		//  }

		//$invoice_data=['expires_on'=>'' , 'invoice_number'=>'' , 'date'=>'' , 'discount'=>'' , 'currency_id'=>'' , 'summary'=>'' , 'terms'=>'' , 'footer'=>'' , 'invoice_template'=>'' , 'invoice_status_id'=>''];
		$invoice = invoice::where('invoice_id', $request->id)->update($updateData);

		return response::json(['error' => false, 'message' => "Stored successfully", 'tax_rate_id' => $tax_rate_id, 'invoice' => $invoice_id, 'invoice_item' => $invoice_item_id], 200);

	}



	public function destroy($id) {
		//echo $id;exit;
		$date = date('Y-m-d H:i:s');
		$invoice = invoice::where('invoice_id', $id)->update(['deleted_at' => $date]);
		return response::json(['error' => false, 'message' => "Deleted successfully"], 200);

	}


	public function invoicenumber(Request $request) {
		// echo "<pre>IN_NO";$request->invoice_number;
		$invoices = invoice::select('invoice_number')->where('invoice_number', $request->invoice_number)->first();

		if (isset($invoices)) {

			return response()->json(['status' => 0, 'message' => "already exist"]);
		}

		return response()->json(['status' => 1, "invoicenumberDetails" => $request->invoice_number], 200);
	}

	public function pdfview($id) {



		$invoice = invoice::select('invoice_number', 'user_id', 'terms', 'footer', 'date', 'expires_on',  'invoices.total', 'total_paid_amount', 'balance', 'currency_id')
			->leftJoin('invoice_items', 'invoices.invoice_id', '=', 'invoice_items.invoice_id')
			->where('invoice_items.invoice_id', $id)
			->first();
		$productData = invoice_item::select('product_name', 'description', 'quantity', 'price', 'tax_value', 'total')
			->where('invoice_id', $id)->get();

		$system = System::select('value')->where('key', '=', 'date')->first()['value'];
		$value = ["ca-ES" => 'DD/MM/YYYY', "fa-IR" => 'MM/DD/YYYY', "ja-JP" => 'YYYY/MM/DD', "da-DK" => 'DD-MM-YYYY', "sma-SE" => 'YYYY-MM-DD'];

		//echo "<pre>";print_r($value[$system]);exit;

		$date = date("$value[$system]", strtotime($invoice->date));

		$totalData = invoice_amount::select('item_sub_total', 'total_amount', 'item_tax_total', 'discount_amount')->where('invoice_id', $id)->first();

		$currency_id = $invoice->currency_id;

		$currency = Currencies::select('symbol')->where('id', $currency_id)->first();
		$currency_placement = Currencies::select('placement', 'decimal', 'thousands')->where('id', $currency_id)->first();

		//echo "<pre>";print_r($currency);exit;
		$company = invoice::where('invoice_id', $id)->first();
		//echo "<pre>";print_r($invoice->user_id);exit;
		$company_id = $company->company_id;

		$company = Company::select('company_name', 'address', 'city', 'state', 'country', 'zipcode', 'mobile', 'website')
			->first();

           //echo "<pre>";print_r($company);exit; 

		$invoice_item_amount = invoice_item_amount::select('subtotal_not_dis', 'sub_total', 'tax', 'discount_amount')->where('invoice_id', $id)->first();

		$logoimage = public_path('/') . '/files/index.jpg';


		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetMargins(10, 10, 10);
      $pdf->SetFont('freeserif', '', 11);
		$pdf->AddPage('P', 'A4');
		$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');
		$pdf->startTransaction();

//echo "<pre>";print_r($currency->symbol);exit;
        


		$header = '<html>
        <body>
                        <style>


                            .nameinvocie
                            {
                                 background: #205565 ;
                                -webkit-background-clip: text;
                                -webkit-text-fill-color: transparent;
                                color: #f9cc49;
                            }
                            .invoice th {
              
                                font-weight: 500;
                               background: #205565 ;

                                color: #f9cc49;
                                padding: 4px 12px;
                                text-align:center;
                                font-size:8px;
                            }
                            .title {
                                background-color: #000;
                                border: 1px solid #8f6B29;
                            }

                            h1
                            {font-size:12px !important;

                            }
                            .title1 {
                                background-color: #000;
                                border: 1px solid #8f6B29;
                                line-height: 16px;
                            }
                            span.dsa {
                                color: #f9cc49;
                                background-color: #000;
                                padding: 30px 20px;
                                border: 1px solid #94782b;
                                font-weight: 700;
                                font-size:11px;
                                display: block;
                                line-height: 20px;
                                width:10px;
                            }
                            span.dfdsa {
                                color: #000;
                                background-color: #f9cc49;
                                padding: 30px 20px;
                                border: 1px solid #94782b;
                                font-weight: 700;
                                 font-size:8px;
                                display: block;
                                line-height: 20px;
                                text-transform: uppercase;

                            }
                            .dsgfds
                            {
                                margin-left:50px;
                            }
                            .para
                            {
                                font-size:8px  !important;
                                text-align:center;
                                color:#000;
                                line-height: 20px;
                            }
                            td h2{
                                margin:0;
                                line-height: 5px;
                            }
                        .invoicetool
                        {
                
                                font-weight: 500;
                                background: #205565 ;

                                color: #fff;
                                padding: 4px 12px;
                                text-align:center;
                                font-size:11px;
                                background-color: #000;
                        }
                        .invoicetool1
                        {
                   
                                font-weight: 500;
                                background: #205565;

                                color: #000;
                                padding: 4px 12px;
                                text-align:center;
                                font-size:11px;
                                background-color: #f9cc49;
                        }     .invoicetoolsec
                        {
                    
                                font-weight: 500;

                                color: #000;
                                padding: 4px 12px;
                                text-align:center;
                                
                                background-color: #fff;
                        }
                        .cash
                        {
                         
                                font-weight: 500;

                                color: #000;
                                padding: 4px 12px;
                                text-align:center;
                                font-size:11px;
                                background-color: #fff;
                        }.card
                        {
                               border-top:1px solid #000;
                               border-bottom:1px solid #000;
                        }
                        // .invoicesection td
                        // {
                        //         background-color: #fff;
                        //         padding: 4px 12px;
                        //         text-align:center;
                        //         font-size:10px;
                        //         line-height: 20px;
                        // }
                        .namefile
                        {
                            font-size:8px;
                        }
                        .namefile1
                        {
                                border-top:1px solid #000;
                                border-bottom:1px solid #000;
                        }

                    </style>
                    <table cellspacing="0" cellpadding="1">
                        <tr>

                            <td rowspan="1">
                            <br> <br>
                             <img src="' . $logoimage . '" alt="HTML5 Icon" class="top" style="margin-top:-5px;width:80px;"></td>
                            <td></td>
                         <td rowspan="8"><h3>Company Address</h3>
                                    <h4>'.$company->company_name.' '.$company->address.' '.$company->city.','.' '.$company->state. ','.' '.$company->country.'.'.'<br>'.$company->website.'</h4>
                             </td>

                        </tr>

                    </table>
                            <br><br><br>

  <table cellspacing="6" cellpadding="3" class="invoicesection">

 <tr>
 <td rowspan="8">



 </td>
 <td></td>
 <td></td>
 <td></td>
    <td width="17%" style="border:1px solid #000;font-size:10px" class="invoicetool"># Invoice Number</td>
    <td width="17%" style="border:1px solid #000;font-size:10px" class="invoicetoolsec">' . $invoice->invoice_number . '</td>

  </tr>
  <tr>
   <td></td>
 <td></td>
 <td></td>
   <td width="17%" style="border:1px solid #000;font-size:10px" class="invoicetool">Date</td>
    <td width="17%" style="border:1px solid #000;font-size:10px" class="invoicetoolsec">' . $invoice->date . '</td>
  </tr>


                <br>

          </table>
            <table cellspacing="6" cellpadding="3" class="invoicesection">




            <br><br><br>

<tr>
                                                    <th width="20%" style="border:1px solid #000;font-size:10px" class="invoicetool">Item</th>

                            <th width="30%" style="border:1px solid #000;font-size:10px" class="invoicetool">Quantity</th>
                            <th width="15%" style="border:1px solid #000;font-size:10px" class="invoicetool">Price</th>
                        <th width="18%" style="border:1px solid #000;font-size:10px" class="invoicetool">Selling Price</th>
                        <th width="18%" style="border:1px solid #000;font-size:10px" class="invoicetool">Total</th>
                        </tr>
                         ';

		$body = '';
		for ($i = 0; $i < count($productData); $i++) {
			$body .= '

                        <tr>
                          <td style="border:1px solid #000;font-size:10px">' . $productData[$i]->product_name . '</td>
                            <td style="border:1px solid #000;font-size:10px">' . $productData[$i]->quantity . '</td>
                            <td style="border:1px solid #000;font-size:10px">' . $productData[$i]->price . '</td>
                        <td style="border:1px solid #000;font-size:10px">' . $productData[$i]->tax_value . ' %</td>
                        <td style="border:1px solid #000;font-size:10px"><b>&#8358;</b> ' .number_format($productData[$i]->total, $currency_placement->decimal) . ' ' . '</td>
                        </tr>';
		}

		$footer = '</table>

                    <br>    <br>

                    <br><br>
                    <table cellspacing="6" cellpadding="3" class="invoicesection">

             <tr>
                <th></th>
                <th> </th>
                <th></th>
                <th> </th>
            <th width="17%" style="border:1px solid #000;font-size:10px" class="invoicetool">Selling Price</th>
            <th width="17%" style="border:1px solid #000;font-size:10px" class="invoicetoolsec"><b>&#8358;</b> ' .number_format($totalData->item_sub_total, $currency_placement->decimal) . ' '  . '</th>
            </tr>

             <tr>
                <th></th>
                <th> </th>
                <th></th>
                <th> </th>
            <th width="17%" style="border:1px solid #000;font-size:10px" class="invoicetool">Selling Profit</th>
            <th width="17%" style="border:1px solid #000;font-size:10px" class="invoicetoolsec"><b>&#8358;</b> ' .number_format($invoice_item_amount->tax, $currency_placement->decimal) . ' ' . '</th>
            </tr>




            <tr>
                <th></th>
                <th> </th>
                <th></th>
                <th> </th>
            <th width="17%" style="border:1px solid #000;font-size:10px" class="invoicetool">Total</th>
            <th width="17%" style="border:1px solid #000;font-size:10px" class="invoicetoolsec"><b>&#8358;</b> ' .number_format($invoice->total, $currency_placement->decimal) . ' ' . '</th>
            </tr>




            <br><br><br>

                    </table>
                    <table  cellspacing="6" cellpadding="3" >
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>

    </body>';

		$tcpdf = $header . $body . $footer;


		$pdf->writeHTML($tcpdf, true, false, true, false, '');

		$file = $pdf->Output('invoice.pdf', 'I');
		return $file;

}
	public function edit($id) {
		$invoiceView = invoice::select('invoice_number', 'date', 'expires_on', 'invoices.discount', 'currency_id', 'invoice_status_id', 'terms', 'footer', 'invoice_template', 'invoice_notes.note', 'total_paid_amount', 'balance')

			->leftJoin('invoice_notes', 'invoices.invoice_id', '=', 'invoice_notes.invoice_id')
			->leftJoin('payments', 'invoices.invoice_id', '=', 'payments.invoice_id')
			->where('invoices.invoice_id', $id)->first();

		//echo "<pre>";print_r($invoice->client_name );exit;
		$invoice_item = invoice_item::select('product_id', 'product_name', 'description', 'quantity', 'price', 'tax_value')

			->where('invoice_id', $id)->get();

		$invoice_notes = invoice_notes::select('note')->where('invoice_id', $id)->get();

		$invoice_attachments = invoice_attachments::select('file_name', 'file_type', 'file_path')->where('invoice_id', $id)->get();

		$invoice = invoice::where('invoice_id', $id)->first();

		$currency_id = $invoice->currency_id;

		$System = System::select('id', 'key', 'value')->get();
		foreach ($System as $key => $system) {
			if ($system->key == "base_currency") {
				$currencyid = $system->value;
				//echo "<pre>";print_r($currencyid);exit;

			}

		}

		if ($currency_id == null && $currency_id == '') {

			$Currency_id = $currencyid;
		} else {
			$Currency_id = $currency_id;

		}
//echo "<pre>";print_r($Currency_id);exit;

		// echo "<pre>";print_r($currency_id);exit;

		$currency = Currencies::select('symbol', 'placement', 'decimal', 'thousands')->where('id', '=', $Currency_id)->first();

		$InvoiceArray = [

			'invoice_number' => $invoiceView->invoice_number,
			'date' => $invoiceView->date,
			'expires_on' => date("Y-m-d", strtotime($invoiceView->date . '+10 days')),
			'discount' => $invoiceView->discount,
			'currency_id' => $invoiceView->currency_id,
			'invoice_status_id' => $invoiceView->invoice_status_id,
			'terms' => $invoiceView->terms,
			'footer' => $invoiceView->footer,
			'invoice_template' => $invoiceView->invoice_template,

			'default_currency' => $invoiceView->default_currency,
			'total_paid_amount' => $invoiceView->total_paid_amount,
			'balance' => $invoiceView->balance,

			'status' => $invoiceView->status,

			'products' => $invoice_item,
			'notes' => $invoice_notes,
			'file' => $invoice_attachments,
			'download_path' => url('/') . '/' . 'files/',
			'currency_symbol' => $currency->symbol,
			'currency_placement' => $currency->placement,
			'currency_decimal' => $currency->decimal,
			'currency_thousands' => $currency->thousands,

		];

		//echo "<pre>";print_r($InvoiceArray);exit;

		$invoice = invoice::where('invoice_id', $id)->first();

		$user_id = $invoice->user_id;

		$company_id = $invoice->company_id;

		//echo "<pre>";print_r($company_id);exit;
		$companydetails = Company::select('company_name', 'address', 'city', 'state', 'country', 'zipcode', 'mobile', 'website')

			->where('id', $company_id)->first();

		return response::json(['error' => false, 'message' => "success", "invoiceviewdetails" => $InvoiceArray], 200);

	}


	 

	
	/**
	 * @SWG\Get(
	 *          path="/paymentview/{id}",
	 *          tags={"INVOICE"},
	 *          summary="Invoice Payment View",
	 *          operationId="Ipd",
	 *          @SWG\Parameter(
	 *              name="invoice_id",
	 *              description="Invoice Id",
	 *              required=true,
	 *              type="string",
	 *              in="path"
	 *          ),
	 *          @SWG\Parameter(
	 *             name="Authorization",
	 *             in="header",
	 *             description="auth number",
	 *             required=true,
	 *             type="string"
	 *      ),
	 *    @SWG\Response(
	 *         response=200,
	 *         description="Success"
	 *     ),
	 *  security={
	 *           {"Bearer": {}}
	 *       }
	 * )
	 *
	 */

	public function paymentView($id) {

		$invoice = invoice::select('payment_id', 'payment_amount', 'payment_methods.name as payment_name', 'payment_date', 'note', 'total_amount')
			->leftJoin('invoice_amounts', 'invoices.invoice_id', '=', 'invoice_amounts.invoice_id')
			->leftJoin('payments', 'invoices.invoice_id', '=', 'payments.invoice_id')
			->leftJoin('payment_methods', 'payments.paymenttype_id', '=', 'payment_methods.id')
			->where('payments.deleted_at', '=', NULL)
			->where('payments.invoice_id', '=', $id)
			->get();

		$paymentviewArray = array();
		foreach ($invoice as $key => $value) {
			$paymentviewArray[] = [

				"payment_id" => $value->payment_id,
				"payment_amount" => $value->payment_amount,
				"payment_date" => $value->payment_date,
				"paymenttype_id" => $value->payment_name,
				"note" => $value->note,
				"total_amount" => $value->total_amount,

			];

		}

		return response::json(['error' => false, 'message' => "success", "paymentviewDetails" => $paymentviewArray], 200);

	}



	public function copy(Request $request) {

		//$formdata = $request->all();
		//echo "<pre>";print_r($request->wholetotal);exit;
		// echo "<pre>";print_r($formdata);exit;
		//  $invoiceform= $request->input('invoiceform');
		//   $note=$request->input('note');
		//       $date=$invoiceform['date'];
		//     $expires_on = $invoiceform['expires_on'];

		$invoices = invoice::where('invoice_id', $request->id)->first();
		//echo "<pre>";print_r($request->balance);exit;
		$invoice_id = $invoices->client_id;
		$company_id = $invoices->company_id;
		//echo "<pre>";print_r($company_id);exit;
		$new_invoice_number = rand();

		// $invoices = invoice::where('invoice_id', $request->id)->first();

		$invoice = invoice::create([

			"client_id" => $invoice_id,
			"user_id" => Auth::User()->id,
			"company_id" => $company_id,
			"invoice_number" => $new_invoice_number,
			"date" => $request->date,
			"expires_on" => $request->expires_on,
			"currency_id" => $request->currency_id,
			"invoice_status_id" => $request->invoice_status_id,
			"discount" => $request->discount,
			"terms" => $request->terms,
			"footer" => $request->footer,
			"invoice_template" => $request->invoice_template,
			"balance" => $request->wholetotal,
			"total" => $request->wholetotal,
			"created_at" => date('Y-m-d H:i:s'),
		]);
		//echo "<pre>";print_r($request->wholetotal);exit;

		$invoice_id_2 = $invoice->invoice_id;

		$n = $request['number_of_products'];
		$formdata = $request->all();
		$product_names = array();
		$descriptions = array();
		$quantities = array();
		$prices = array();
		$tax_rate_ids = array();
		$totals = array();
		for ($i = 0; $i < $n; $i++) {
			array_push($product_names, $request['products' . $i . 'product_name']);
			array_push($descriptions, $request['products' . $i . 'description']);
			array_push($quantities, $request['products' . $i . 'quantity']);
			array_push($prices, $request['products' . $i . 'price']);
			array_push($tax_rate_ids, $request['products' . $i . 'tax_rate_id']);
			array_push($totals, $request['products' . $i . 'total']);

		}

		$temp_product_names = $product_names;
		$product_names = array();
		$product_weight = array();

		for ($i = 0; $i < $n; $i++) {

			$temp = explode("(", $temp_product_names[$i]);
			array_push($product_names, $temp[0]);

		}
//echo "<pre>"; print_r($totals);
		//echo"<pre>print";print_r($product_names);exit;

		$notes = $request->input('note');
		$file_names = $request->input('file_name');
		//echo "<pre>";print_r($formdata);

		$date = $request['date'];
		$expires_on = $request['expires_on'];

		for ($i = 0; $i < $n; $i++) {

			$getTaxRate = TaxRate::where('percentage', $tax_rate_ids[$i])->first();
			$tax_rate_id = $getTaxRate->id;
			$tax_rate_percentage = $getTaxRate->percentage;

			$getProduct = productmap::where('display_name', $product_names[$i])->first();
			$product_id = $getProduct->id;

			$invoice_item = invoice_item::create([

				"product_id" => $product_id,
				"invoice_id" => $invoice_id_2,
				"product_name" => $product_names[$i],
				"description" => $descriptions[$i],
				"tax_rate_id" => $tax_rate_id,
				"tax_value" => $tax_rate_ids[$i],
				"quantity" => $quantities[$i],
				"price" => $prices[$i],
				"total" => $totals[$i],

			]);
		}

		$invoice_amount = invoice_amount::create([

			"invoice_id" => $invoice_id_2,
			"item_tax_total" => $request->input('totaltax'),
			"discount_amount" => $request->input('discountval'),
			"item_sub_total" => $request->input('subtotal'),
			"total_amount" => $request->input('wholetotal'),
			"created_at" => date('Y-m-d H:i:s'),

		]);

		$invoice_item_id = $invoice_item->item_id;
		$sub_total_not_dis = 0;
		for ($j = 0; $j < count($totals); $j++) {
			$sub_total_not_dis += $totals[$j];
		}

		$invoice_item_amount = invoice_item_amount::create([
			"invoice_id" => $invoice_id_2,
			"item_id" => $invoice_item_id,
			"tax" => $request->input('totaltax'),
			"subtotal_not_dis" => $sub_total_not_dis,
			"discount_amount" => $request->input('discountval'),
			"sub_total" => $request->input('subtotal'),
			"total_amount" => $request->input('wholetotal'),
			"created_at" => date('Y-m-d H:i:s'),

		]);

		$note = explode(',', $request['note']);
		foreach ($note as $notes) {
			$invoice_notes = invoice_notes::create([
				"invoice_id" => $invoice_id_2,
				"note" => $notes,
				"created_at" => date('Y-m-d H:i:s'),
			]);
		}

		if ($request->hasfile('images')) {
			foreach ($request->file('images') as $key => $file_name) {
				$name = $file_name->getClientOriginalName();
				$type = $file_name->getMimeType();
				$size = $file_name->getSize();
//dd($file_name->getSize());
				$path = public_path() . '/files/' . $name;
//dd($path);
				$file_name->move(public_path() . '/files/', $name);

				$invoice_attachments = invoice_attachments::create([

					"invoice_id" => $invoice_id_2,
					"file_name" => $name,
					"file_size" => $size,
					"file_type" => $type,
					"file_path" => $path,
					"created_at" => date('Y-m-d H:i:s'),
				]);
			}
		}

		return response::json(['error' => false, 'message' => "Invoice Copied successfully", 'tax_rate_id' => $tax_rate_id, 'invoice' => $invoice_id_2, 'invoice_item' => $invoice_item_id], 200);
	}



	public function updateinvoice(Request $request) {

		//echo "<pre>";print_r($request->all());exit;

		$notes = $request->input('note');

		$date = $request['date'];
		$expires_on = $request['expires_on'];

		$invoice = invoice::where('invoice_id', $request->id)->first();
		$invoice_id = $invoice->invoice_id;

		$invoice_notes = invoice_notes::select('note')->where('invoice_id', $invoice_id)->get();

		$invoice_attachments = invoice_attachments::select('file_name', 'file_type', 'file_path')->where('invoice_id', $invoice_id)->get();

		$formdata = [
			'invoice_number' => $request->input('invoice_number'),
			'date' => date('Y-m-d', strtotime($date)),
			'expires_on' => date('Y-m-d', strtotime($expires_on)),
			'currency_id' => $request->input('currency_id'),
			'invoice_status_id' => $request->input('invoice_status_id'),
			'discount' => $request->input('discount'),
			'terms' => $request->input('terms'),
			'footer' => $request->input('footer'),
			'balance' => $request->input('balance'),
			'total' => $request->input('wholetotal'),
		];
		$invoice = invoice::where('invoice_id', $invoice_id)->update($formdata);

		// $invoice_item=invoice_item::where('invoice_id',$request->id)->first();
		// $invoice_id = $invoice_item->invoice_id;

		$n = $request['number_of_products'];
		$formdata = $request->all();
		$product_ids = array();
		$product_names = array();
		$descriptions = array();
		$quantities = array();
		$prices = array();
		$tax_rate_ids = array();
		$totals = array();
		$tax_rate_id = array();
		$A = array();

		for ($i = 0; $i < $n; $i++) {
			array_push($product_ids, $request['products' . $i . 'product_id']);
			array_push($product_names, $request['products' . $i . 'product_name']);
			array_push($descriptions, $request['products' . $i . 'description']);
			array_push($quantities, $request['products' . $i . 'quantity']);
			array_push($prices, $request['products' . $i . 'price']);
			array_push($tax_rate_ids, $request['products' . $i . 'tax_rate_id']);
			array_push($totals, $request['products' . $i . 'total']);
		}
		//echo "<pre>";print_r($request->all());exit;

		$temp_product_names = $product_names;
		$product_names = array();
		for ($i = 0; $i < $n; $i++) {
			// echo "<pre>"; print_r($temp_product_names[$i]);
			$temp = explode("(", $temp_product_names[$i]);
			array_push($product_names, $temp[0]);
		}
		//echo "<pre>"; print_r($product_names);exit;

		$getProduct = productmap::where('display_name', $product_names)
			->first();

		$product_id = $getProduct->id;

		//echo "<pre>";print_r($product_id);exit;

		$invoice_items = invoice_item::select('product_id')->where('invoice_id', $invoice_id)->get();
		foreach ($invoice_items as $key => $value) {
			array_push($A, $value->product_id);
		}
		if ($A) {
			foreach ($A as $key => $value) {
				$product_items = invoice_item::select('*')->where('product_id', $value)->get();
				for ($x = 0; $x < count($product_ids); $x++) {
					if ($product_ids[$x] === $value) {
						$invoice_item = [
							"description" => $descriptions[$x],
							"tax_value" => $tax_rate_ids[$x],
							"quantity" => $quantities[$x],
							"price" => $prices[$x],
							"total" => $totals[$x],
							"updated_at" => date('Y-m-d H:i:s'),
						];
						//  echo "<pre>U";print_r($invoice_item);exit;

						$invoice_item = invoice_item::where(['product_id' => $value, 'invoice_id' => $invoice_id])->update($invoice_item);
					}
				}
			}
		}
		//echo "<pre>";print_r($value->product_id);exit;

		$B = $product_ids;
		//echo"<pre>B ";print_r($product_ids);exit;
		// echo"<pre>A";print_r($A);
		$C = array_diff($B, $A);
		//echo"<pre>C";print_r($C);exit;
		for ($i = 0; $i < $n; $i++) {

			$getTaxRate = TaxRate::where('percentage', $tax_rate_ids[$i])->first();
			array_push($tax_rate_id, $getTaxRate->id);

		}

		if ($C) {
			foreach ($C as $key => $value) {
				for ($i = 0; $i < $n; $i++) {
					if ($product_ids[$i] == $value) {

						$invoice_item = invoice_item::create([
							"invoice_id" => $invoice_id,
							"product_id" => $value,
							"product_name" => $product_names[$i],
							"description" => $descriptions[$i],
							"tax_rate_id" => $tax_rate_id[$i],
							"tax_value" => $tax_rate_ids[$i],
							"total" => $totals[$i],
							"quantity" => $quantities[$i],
							"price" => $prices[$i],
							"total" => $totals[$i],
							"updated_at" => date('Y-m-d H:i:s'),

						]);
//echo "<pre>D";print_r( $invoice_item);exit;

					}

				}

			}

		}
		$D = array_diff($A, $B);
		//echo "<pre>D";print_r($D);exit();
		if ($D) {
			foreach ($D as $key => $value) {
				for ($i = 0; $i < $n; $i++) {
					//echo $id;exit;
					$date = date('Y-m-d H:i:s');
					$invoice_item = invoice_item::where(['product_id' => $value, 'invoice_id' => $request->id])->update(['deleted_at' => $date]);
				}
			}
		}

		$U = $C;
		//echo "<pre>U";print_r($B);

		if ($U) {
			foreach ($U as $key => $value) {
				for ($i = 0; $i < $n; $i++) {

					$invoice_item = invoice_item::where(['product_id' => $product_id, 'invoice_id' => $invoice_id]);

				}
			}
		}

		$A = array();
		$B = array();
		$C = array();
		$D = array();

		$invoice_notes = invoice_notes::select('note')->where(['invoice_id' => $invoice_id])->get();
		//echo "<pre>Notes:";print_r($invoice_notes);print_r($notes);
		foreach ($invoice_notes as $key => $value) {
			array_push($A, $value->note);
		}
		$B = explode(",", $notes);
		//  $B = $note_ids;
		//echo"<pre>A ";print_r($A);
		// echo"<pre>B ";print_r($B);
		$C = array_diff($B, $A);
		$D = array_diff($A, $B);
		//echo"<pre>C";print_r($C);

		if ($C) {
			foreach ($C as $note) {
				$invoice_notes = invoice_notes::create([
					"invoice_id" => $invoice_id,
					"note" => $note,
					"created_at" => date('Y-m-d H:i:s'),
				]);
			}
		}

		if ($D) {
			$date = date('Y-m-d H:i:s');
			foreach ($D as $note) {
				$invoice_notes = invoice_notes::where(['invoice_id' => $invoice_id, 'note' => $note])->delete();
			}
		}

		$A = array();
		$B = array();
		$D = array();

		$invoice_attachments = invoice_attachments::select('file_name')->where(['invoice_id' => $invoice_id])->get();
		//echo "<pre>Notes:";print_r($invoice_attachments);print_r($file_names);exit;
		foreach ($invoice_attachments as $key => $value) {
			array_push($A, $value->file_name);
		}

		if ($request->hasfile('images')) {
			foreach ($request->file('images') as $key => $file_name) {
				$name = $file_name->getClientOriginalName();
				$type = $file_name->getMimeType();
				$size = $file_name->getSize();
				//dd($file_name->getSize());
				$path = public_path() . '/files/' . $name;
				//dd($path);
				$file_name->move(public_path() . '/files/', $name);

				$invoice_attachments = invoice_attachments::create([

					"invoice_id" => $invoice_id,
					"file_name" => $name,
					"file_size" => $size,
					"file_type" => $type,
					"file_path" => $path,
					"created_at" => date('Y-m-d H:i:s'),
				]);
			}
		}

		$file_name = $request->input('bindfile');

		$B = explode(",", $file_name);
		//  $B = $note_ids;
		//  echo"<pre>A ";print_r($file_name);exit;

		//echo"<pre>B ";print_r($B);exit;

		$D = array_diff($A, $B);
		//echo"<pre>D";print_r($D);exit;

		if ($D) {
			$date = date('Y-m-d H:i:s');
			foreach ($D as $file_name) {
				$invoice_attachments = invoice_attachments::where(['invoice_id' => $invoice_id, 'file_name' => $file_name])->delete();
			}
		}

		//echo"<pre>D";print_r($D);exit;

		$invoiceamount = [
			"item_tax_total" => $request->input('totaltax'),
			"discount_amount" => $request->input('discountval'),
			"item_sub_total" => $request->input('subtotal'),
			"total_amount" => $request->input('wholetotal'),
			"updated_at" => date('Y-m-d H:i:s'),
		];

		$invoice_amount = invoice_amount::where('invoice_id', $invoice_id)->update($invoiceamount);

		$sub_total_not_dis = 0;
		for ($j = 0; $j < count($totals); $j++) {
			$sub_total_not_dis += $totals[$j];

		}

		$invoice_itemamount = [
			"tax" => $request->input('totaltax'),
			"subtotal_not_dis" => $sub_total_not_dis,
			"discount_amount" => $request->input('discountval'),
			"sub_total" => $request->input('subtotal'),
			"total_amount" => $request->input('wholetotal'),
			"updated_at" => date('Y-m-d H:i:s'),
		];

		// echo"<pre>";print_r($request->input('balance'));exit;

		$invoice_item_amount = invoice_item_amount::where('invoice_id', $invoice_id)->update($invoice_itemamount);

		$total = $request->input('balance');

		$updatetotal = [
			'balance' => $total,

		];

		$invoice = invoice::where('invoice_id', $request->id)->update($updatetotal);

		return response::json(['error' => false, 'message' => "Invoice updated successfully"], 200);
	}



	public function filter(Request $request) {
		$formData = $request->all();

		if ($request->Status != "0") {
			$invoice = Invoice_status_master::where('invoicestatus', '=', $request->Status)->first();
			$invoice_staus_id = $invoice->invoicestatus_id;
		} else {
			$invoice_staus_id = "0";
		}
		// echo"<pre>";print_r($invoice_staus_id);exit;
		//echo"<pre>";print_r($request->Status);exit;
		if ($request->Company != "0" && $invoice_staus_id != "0") {
			$invoicefilter = invoice::select('clients.client_name as client_name', 'invoice_amounts.total_amount as total_amount', 'invoices.invoice_id as invoice_id', 'invoice_number', 'date', 'total', 'expires_on', 'clients.status', 'balance', 'currency_id', 'invoice_status_master.invoicestatus')
				->leftJoin('clients', 'invoices.client_id', '=', 'clients.id')
				->leftJoin('invoice_amounts', 'invoices.invoice_id', '=', 'invoice_amounts.invoice_id')
				->leftJoin('invoice_item_amounts', 'invoices.invoice_id', '=', 'invoice_item_amounts.invoice_id')
				->leftjoin('invoice_status_master', 'invoices.invoice_status_id', '=', 'invoice_status_master.invoicestatus_id')
				->where('invoice_status_id', $invoice_staus_id)
				->where('company_id', $request->Company)
				->where('invoices.invoice_number', '!=', NULL)
				->where('invoices.deleted_at', '=', NULL)
				->get();
			$invoiceviewArray = array();
			foreach ($invoicefilter as $key => $value) {
				$CurrenciesData = Currencies::select('symbol', 'placement', 'thousands', 'decimal')->where('id', '=', $value->currency_id)->first();
				$today = date('Y-m-d');
				$date = $value->expires_on;
				$is_date = 0;
				if ($date <= $today) {
					$is_date = 1;
				}
				//echo "<pre>";print_r($value->date);exit;
				array_push($invoiceviewArray, [
					//"currency_id" => Currencies::select('symbol')->where('id','=',$value->currency_id)->first()['symbol'],
					"currencies_id" => $CurrenciesData['symbol'],
					"currencies_placement" => $CurrenciesData['placement'],
					"currencydecimal" => $CurrenciesData['decimal'],
					"currencythousands" => $CurrenciesData['thousands'],
					"invoice_id" => $value->invoice_id,
					"invoice_number" => $value->invoice_number,
					"date" => $value->date,
					"expires_on" => $value->expires_on,
					"client_name" => $value->client_name,
					"total_amount" => $value->total_amount,
					"balance" => $value->balance,
					"total" => $value->total,
					"status" => $value->invoicestatus,
					"is_date" => $is_date,
				]);
			}
		} else if ($request->Company == "0" && $invoice_staus_id != "0") {
			$invoicefilter = invoice::select('clients.client_name as client_name', 'invoice_amounts.total_amount as total_amount', 'invoices.invoice_id as invoice_id', 'invoice_number', 'date', 'total', 'expires_on', 'clients.status', 'balance', 'currency_id', 'invoice_status_master.invoicestatus')
				->leftJoin('clients', 'invoices.client_id', '=', 'clients.id')
				->leftJoin('invoice_amounts', 'invoices.invoice_id', '=', 'invoice_amounts.invoice_id')
				->leftJoin('invoice_item_amounts', 'invoices.invoice_id', '=', 'invoice_item_amounts.invoice_id')
				->leftjoin('invoice_status_master', 'invoices.invoice_status_id', '=', 'invoice_status_master.invoicestatus_id')
				->where('invoice_status_id', $invoice_staus_id)
				->where('invoices.invoice_number', '!=', NULL)
				->where('invoices.deleted_at', '=', NULL)
				->get();
			$invoiceviewArray = array();
			foreach ($invoicefilter as $key => $value) {
				$CurrenciesData = Currencies::select('symbol', 'placement', 'thousands', 'decimal')->where('id', '=', $value->currency_id)->first();
				$today = date('Y-m-d');
				$date = $value->expires_on;
				$is_date = 0;
				if ($date <= $today) {
					$is_date = 1;
				}
				//echo "<pre>";print_r($value->date);exit;
				array_push($invoiceviewArray, [
					//"currency_id" => Currencies::select('symbol')->where('id','=',$value->currency_id)->first()['symbol'],
					"currencies_id" => $CurrenciesData['symbol'],
					"currencies_placement" => $CurrenciesData['placement'],
					"currencydecimal" => $CurrenciesData['decimal'],
					"currencythousands" => $CurrenciesData['thousands'],
					"invoice_id" => $value->invoice_id,
					"invoice_number" => $value->invoice_number,
					"date" => $value->date,
					"expires_on" => $value->expires_on,
					"client_name" => $value->client_name,
					"total_amount" => $value->total_amount,
					"balance" => $value->balance,
					"total" => $value->total,
					"status" => $value->invoicestatus,
					"is_date" => $is_date,
				]);
			}

		} else if ($request->Company != "0" && $invoice_staus_id == "0") {
			$invoicefilter = invoice::select('clients.client_name as client_name', 'invoice_amounts.total_amount as total_amount', 'invoices.invoice_id as invoice_id', 'invoice_number', 'date', 'total', 'expires_on', 'clients.status', 'balance', 'currency_id', 'invoice_status_master.invoicestatus')
				->leftJoin('clients', 'invoices.client_id', '=', 'clients.id')
				->leftJoin('invoice_amounts', 'invoices.invoice_id', '=', 'invoice_amounts.invoice_id')
				->leftJoin('invoice_item_amounts', 'invoices.invoice_id', '=', 'invoice_item_amounts.invoice_id')
				->leftjoin('invoice_status_master', 'invoices.invoice_status_id', '=', 'invoice_status_master.invoicestatus_id')
				->where('company_id', $request->Company)
				->where('invoices.invoice_number', '!=', NULL)
				->where('invoices.deleted_at', '=', NULL)
				->get();
			$invoiceviewArray = array();
			foreach ($invoicefilter as $key => $value) {
				$CurrenciesData = Currencies::select('symbol', 'placement', 'thousands', 'decimal')->where('id', '=', $value->currency_id)->first();
				$today = date('Y-m-d');
				$date = $value->expires_on;
				$is_date = 0;
				if ($date <= $today) {
					$is_date = 1;
				}
				//echo "<pre>";print_r($value->date);exit;
				array_push($invoiceviewArray, [
					//"currency_id" => Currencies::select('symbol')->where('id','=',$value->currency_id)->first()['symbol'],
					"currencies_id" => $CurrenciesData['symbol'],
					"currencies_placement" => $CurrenciesData['placement'],
					"currencydecimal" => $CurrenciesData['decimal'],
					"currencythousands" => $CurrenciesData['thousands'],
					"invoice_id" => $value->invoice_id,
					"invoice_number" => $value->invoice_number,
					"date" => $value->date,
					"expires_on" => $value->expires_on,
					"client_name" => $value->client_name,
					"total_amount" => $value->total_amount,
					"balance" => $value->balance,
					"total" => $value->total,
					"status" => $value->invoicestatus,
					"is_date" => $is_date,
				]);
			}

		} else {
			$invoicefilter = invoice::select('clients.client_name as client_name', 'invoice_amounts.total_amount as total_amount', 'invoices.invoice_id as invoice_id', 'invoice_number', 'date', 'total', 'expires_on', 'clients.status', 'balance', 'currency_id', 'invoice_status_master.invoicestatus')
				->leftJoin('clients', 'invoices.client_id', '=', 'clients.id')
				->leftJoin('invoice_amounts', 'invoices.invoice_id', '=', 'invoice_amounts.invoice_id')
				->leftJoin('invoice_item_amounts', 'invoices.invoice_id', '=', 'invoice_item_amounts.invoice_id')
				->leftjoin('invoice_status_master', 'invoices.invoice_status_id', '=', 'invoice_status_master.invoicestatus_id')
				->where('invoices.invoice_number', '!=', NULL)
				->where('invoices.deleted_at', '=', NULL)
				->get();
			$invoiceviewArray = array();
			foreach ($invoicefilter as $key => $value) {
				$CurrenciesData = Currencies::select('symbol', 'placement', 'thousands', 'decimal')->where('id', '=', $value->currency_id)->first();
				$today = date('Y-m-d');
				$date = $value->expires_on;
				$is_date = 0;
				if ($date <= $today) {
					$is_date = 1;
				}
				//echo "<pre>";print_r($value->date);exit;
				array_push($invoiceviewArray, [
					//"currency_id" => Currencies::select('symbol')->where('id','=',$value->currency_id)->first()['symbol'],
					"currencies_id" => $CurrenciesData['symbol'],
					"currencies_placement" => $CurrenciesData['placement'],
					"currencydecimal" => $CurrenciesData['decimal'],
					"currencythousands" => $CurrenciesData['thousands'],
					"invoice_id" => $value->invoice_id,
					"invoice_number" => $value->invoice_number,
					"date" => $value->date,
					"expires_on" => $value->expires_on,
					"client_name" => $value->client_name,
					"total_amount" => $value->total_amount,
					"balance" => $value->balance,
					"total" => $value->total,
					"status" => $value->invoicestatus,
					"is_date" => $is_date,
				]);
			}
		}

		return response::json(['error' => false, 'message' => "success", "invoiceview" => $invoiceviewArray], 200);
	}



}

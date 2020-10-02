<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Loanpay;
use App\Models\Company;
use Illuminate\Http\Request;

use Auth;
use Input;
use Response;
use File;
use App\Models\Client;
use App\Models\User;
use Elibyy\TCPDF\TCPDFHelper;
use Carbon\Carbon;
use PDF;
use TCPDF;


class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $loan = Loan::select('loan_id', 'client_id', 'loans.amount as amount','loan_no', 'disburse_account', 'disburse_bank', 'payment_plan', 'note', 'date', 'clients.client_name as client_name', 'phone_number', 'balance', 'loans.status as status', 'paid_amount', 'monthly_payment', 'amount_disbursed')
        ->leftJoin('clients', 'loans.client_id', '=', 'clients.id')->where('loans.deleted_at', NULL)
         ->orderBy('loans.created_at', 'DESC')
         ->get();


         $loanArray = array();
    foreach ($loan as $key => $value) {

               
       $due_date =   Carbon::parse($value->date);
$duedate = $due_date->addMonths($value->payment_plan)->toDateString();





               
      $loanArray[] = [

        "loan_id" => $value->loan_id,
        "loan_no" => $value->loan_no,
         "balance_amount" => $value->balance,
        "phone_number" => $value->phone_number,
        "date" => $value->date,
        "expiredate" => $duedate,
        "client_name" => $value->client_name,
        "disburse_bank" => $value->disburse_bank,
         "status" => $value->status,
         "monthly_payment" => $value->monthly_payment,
         "amount_disbursed" => $value->amount_disbursed,
         "paid_amount" =>number_format($value->paid_amount, 2),
        "amount" => number_format($value->amount, 2)
        





      ];
    }

     return response()->json(['error' => false, 'message' => "success", "loandetails" => $loanArray], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function payment(Request $request) {


    $formData = $request->all();
//echo "<pre>";print_r($formData);exit;

    $loans = Loan::where('loan_id', $request->id)->first();

    $balance = $loans->balance;
    $total_amount = $loans->amount;


       //echo "<pre>";print_r($request->payment_no);

    //echo "<pre>";print_r($totalpayment);exit;

    $Payment = Loanpay::create([
       "client_id" => $loans->client_id,
      "loan_id" => $request->id,
      "payment_amount" => $request->amount,
      "total_paid_amount" => $request->amount,
      "payment_date" => $request->paymentdate,
      "paymenttype_id" => $request->paymentmethod,
      "note" => $request->paymentnotes,
      "payment_no"=>$request->payment_no,
      "no_month"=>$request->no_month,
      
      "created_at" => date('Y-m-d H:i:s'),

    ]);
 
    $balance = $balance - $request->amount;

    // echo "<pre>";print_r($balance);exit;

    $total_paid_amount = $total_amount - $balance;

    // echo "<pre>";print_r($balance);
    // echo "<pre>";print_r($total_paid);exit;

    $update = [
      'balance' => $balance,
      'paid_amount'=>$total_paid_amount

    ];

    $invoice_item_amount = Loan::where('loan_id', $request->id)->update($update);



    if ($balance != 0) {

      $paidstatus = 'Approve';

    } else {

      $paidstatus = 'Paid';

    }

    // echo "<pre>";print_r( $paidstatus);exit;

    //echo "<pre>";print_r( $paidstatus);exit;
    $updateloan = [
      'status' => $paidstatus,

    ];

    $quotes = Loan::where('loan_id', $request->id)->update($updateloan);

    return response()->json(['status' => 1, 'message' => "Payment stored successfully", "payment" => $Payment], 200);

    //    return response()->json(['status' => 1, 'message' => "success", ''id'' => $invoice_id], 200);
  }
    public function trydemo(Request $request)
    {
   $formData = $request->all();     
         //echo "<pre>";print_r( $formData);exit;
 
      //echo "<pre>";print_r($phone);exit;

        $tryDemoCompany =  trydemo_company::create([
            
            "company_name" => $formData["company"],
            "name" => $formData["name"],
            "email" => $formData["email"],
            
        ]);
     
            
        return response::json(['error' => false, 'message' =>"Try Your invoice"], 200);    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
 
        $formData = $request->all();     
        // echo "<pre>";print_r( $formData);exit;


 
      //echo "<pre>";print_r($formData['monthly_payment']);exit;

        $Loanform =  Loan::create([
            
            "client_id" => $formData["client_name"],
            "amount" => $formData["amount"],
            "balance" => $formData["amount"],
            "loan_no" => $formData["loan_no"],
            "disburse_account" => $formData["disburse_account"],
            "disburse_bank" => $formData["disburse_bank"],
            "payment_plan" => $formData["payment_plan"],
            "note" => $formData["note"],
            "user_id" => Auth::User()->id,
            "status" => $formData["status"],
            "amount_disbursed" =>$formData["amount_disbursed"],
            "monthly_payment" => $formData["monthly_payment"],
            "interest_rate" => $formData["interest_rate"],
            "date" => date('Y-m-d'),
             "month_year" => date('Y-m'),
            
        ]);
     
            
        return response::json(['status' =>1, 'message' =>"Loan Booked Successfuly"], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\tryDemo  $tryDemo
     * @return \Illuminate\Http\Response
     */
    public function show($id)


    {

        $loan = Loan::select('loan_id', 'client_id', 'loans.amount as amount','loan_no', 'disburse_account', 'disburse_bank', 'payment_plan', 'note', 'date', 'clients.client_name as client_name', 'phone_number', 'balance', 'loans.status as status', 'paid_amount')
        ->leftJoin('clients', 'loans.client_id', '=', 'clients.id')
        ->where('loans.deleted_at', NULL)
        ->where('loans.client_id', $id)

        ->get();


         $loanArray = array();
    foreach ($loan as $key => $value) {

               
       $due_date =   Carbon::parse($value->date);
$duedate = $due_date->addMonths($value->payment_plan)->toDateString();





               
      $loanArray[] = [

        "loan_id" => $value->loan_id,
        "loan_no" => $value->loan_no,
         "balance_amount" => $value->balance,
        "phone_number" => $value->phone_number,
        "date" => $value->date,
        "expiredate" => $duedate,
        "client_name" => $value->client_name,
        "disburse_bank" => $value->disburse_bank,
         "status" => $value->status,
         "paid_amount" =>number_format($value->paid_amount, 2),
        "amount" => number_format($value->amount, 2)
        





      ];


    }

     return response()->json(['error' => false, 'message' => "success", "loandetails" => $loanArray], 200);
        
    }


     public function loan_report($id)
  {


        $loan = Loan::select('loan_id', 'client_id', 'loans.amount as amount','loan_no', 'disburse_account', 'disburse_bank', 'payment_plan', 'note', 'date', 'clients.client_name as client_name', 'phone_number', 'balance', 'clients.address', 'paid_amount', 'monthly_payment', 'clients.city', 'clients.state', 'clients.email')
        ->leftJoin('clients', 'loans.client_id', '=', 'clients.id')->where('loans.deleted_at', NULL)
        ->where('loan_id', $id)
        ->first();



        $payment = LoanPay::select('payment_methods.name as payment_name','loanpays.loan_id', 'no_month','payment_no','payment_amount','paymenttype_id','payment_date','loanpays.note','payment_id','clients.client_name as client_name' )
        ->leftJoin('loans','loanpays.loan_id', '=', 'loans.loan_id')
        ->leftJoin('clients','loans.client_id', '=', 'clients.id')
        ->leftJoin('payment_methods','loanpays.paymenttype_id', '=', 'payment_methods.id')
         ->where('clients.deleted_at', '=', NULL)->where('loans.deleted_at', '=', NULL)->where('loanpays.deleted_at', '=', NULL)
         ->where('loanpays.loan_id', $id)    
        ->get();



    $company = Company::select('company_name', 'address', 'city', 'state', 'country', 'zipcode', 'mobile', 'website')
      ->first();

    // echo"<pre>";print_r($company);exit;

    $logoimage = public_path('/') . '/files/index.jpg';
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetMargins(10, 10, 10);
      $pdf->SetFont('freeserif', '', 11);
    $pdf->AddPage('P', 'A4');
    $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');
    $pdf->startTransaction();


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
                                font-family: sans-serif;
                                font-size: 12px;
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
                           
                                background-color: #000;
                        }

                                        .Payment
                        {
                               
                                font-weight: 500;
                    
                                padding: 4px 12px;
                                text-align:center;
                             
                        }


                        .invoicetool1
                        {
                                font-family: sans-serif;
                             font-size:8px;
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
                                font-size:11px;
                                background-color: #fff;
                        }
                        .cash
                        {
                                font-family: sans-serif;
                                 font-size:8px;
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
                                    <h4>' . $company->company_name . ' ' . $company->address . ' ' . $company->city . ' ' . $company->zipcode . ' ' . $company->state . ' ' . $company->country . ' ' . $company->mobile . ' ' . $company->website . '</h4>
                             </td>

                        </tr>
                       
                    </table>
                            <br><br><br>

  <table cellspacing="6" cellpadding="3" class="invoicesection">
                   
 <tr>
 <td rowspan="8" style="width:200px;"><h3>Bill To</h3>
  <h4>' . $loan->client_name . '<br>' . $loan->address . ', ' . $loan->city. ', '.'<br>' . $loan->state .'.'. '<br>'. $loan->email.'</h4>
   
   
 </td>
 <td></td>

    <td width="17%" style="border:1px solid #000" class="invoicetool"># Quote Number</td>
    <td width="17%" style="border:1px solid #000" class="invoicetoolsec">' . $loan->loan_no . '</td>
    
  </tr>
  <tr>
   <td></td>

   <td width="17%" style="border:1px solid #000" class="invoicetool">Date</td>
    <td width="17%" style="border:1px solid #000" class="invoicetoolsec">' . $loan->date . '</td>
  </tr>
   <tr>
    <td></td>

  <td width="17%" style="border:1px solid #000" class="invoicetool">Due Date</td>
    <td width="17%" style="border:1px solid #000" class="invoicetoolsec">' . Carbon::parse($loan->date)->addMonths($loan->payment_plan)->toDateString() . '</td>
  </tr>
             
                <br>
               
          </table>
            <table cellspacing="6" cellpadding="3" class="invoicesection">

          
           
           
            <br><br><br>

<tr>
                                                    <th width="15%" style="border:1px solid #000" class="invoicetool">Payment Date</th>

                            <th width="30%" style="border:1px solid #000" class="invoicetool">Payment ID</th>
                            <th width="20%" style="border:1px solid #000" class="invoicetool">Amount Paid</th>
               
                           <th width="20%" style="border:1px solid #000" class="invoicetool">Payment Plan</th>
                                <th width="20%" style="border:1px solid #000" class="invoicetool">Payment type</th>
                        </tr>
                         ';

    $body = '';
    for ($i = 0; $i < count($payment); $i++) {
      $body .= '

                        <tr>
                          <td style="border:1px solid #000">' . $payment[$i]->payment_date . '</td>

                            <td style="border:1px solid #000">' . $payment[$i]->payment_no . '</td>
                               <td style="border:1px solid #000"><b>&#8358;</b>' . ' ' . number_format($payment[$i]->payment_amount, 2). ' ' . '</td>
                   
                            <td style="border:1px solid #000"><b>&#8358;</b>' . ' ' . $payment[$i]->no_month . 'Months' . '</td>
                               <td style="border:1px solid #000"><b>&#8358;</b>' . ' ' . $payment[$i]->payment_name . ' ' . '</td>
                        
                     
                        </tr>';
    }




    $footer = '</table>

               
                
                    <br><br>
                    <table cellspacing="6" cellpadding="3" class="invoicesection">
                  
             <tr>
                <th></th>
                <th> </th>
                <th></th>
                <th> </th>
            <th width="17%" style="border:1px solid #000" class="invoicetool">Total Loan Amount</th>
            <th width="17%" style="border:1px solid #000" class="invoicetoolsec"><b>&#8358;</b>' .' ' . number_format($loan->amount, 2). '</th>
            </tr>


             
             <tr>
                <th></th>
                <th> </th>
                <th></th>
                <th> </th>
            <th width="17%" style="border:1px solid #000" class="invoicetool">Loan Repayed</th>
            <th width="17%" style="border:1px solid #000" class="invoicetoolsec"><b>&#8358;</b>' . ' ' . number_format($loan->paid_amount, 2) . ' ' . '</th>
            </tr>

            <tr>
                <th></th>
                <th> </th>
                <th></th>
                <th> </th>
            <th width="17%" style="border:1px solid #000" class="invoicetool">Balance</th>
            <th width="17%" style="border:1px solid #000" class="invoicetoolsec"><b>&#8358;</b>'  . ' ' . number_format($loan->balance, 2) . ' ' . '</th>
            </tr>

                        <tr>
                <th></th>
                <th> </th>
                <th></th>
                <th> </th>
            <th width="17%" style="border:1px solid #000" class="invoicetool">Monthly Payment</th>
            <th width="17%" style="border:1px solid #000" class="invoicetoolsec"><b>&#8358;</b>'  . ' ' . number_format($loan->monthly_payment, 2) . ' ' . '</th>
            </tr>
            <br><br><br>



       

              </table>
            <br><br><br>


 
    </body>';

    $tcpdf = $header . $body . $footer;

    $pdf->writeHTML($tcpdf, true, false, true, false, '');

    $file = $pdf->Output('quotes.pdf', 'I');
    return $file;


    // $path = public_path() . '/downloadpdf';
    // $excel_name = $request->quotes_id . '.pdf';
    // $path = $path . $excel_name;

    // $file = PDF::loadView('systimanx_quote', ['Client' => $Client, 'System' => $system, 'Company' => $company, 'currency' => $currency,  'quotes' => $quotes, 'details' => $details, 'quotes_item_amount' => $quotes_item_amount, 'currency' => $currency])->save('downloadpdf/' . $request->quotes_id . '.pdf');

    // $file->stream();
    // $fileatt = $file->Output($path, 'F');
    // $resultOutput['error'] = false;
    // $returnPath = url('/') . '/' . 'downloadpdf/' . $excel_name;
    // $resultOutput['outputfile'] = $returnPath;


    // return response()->json(['error' => false, 'message' => "success", 'data' => $resultOutput, 'name' => $excel_name], 200);
  }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\tryDemo  $tryDemo
     * @return \Illuminate\Http\Response
     */
    public function edit(tryDemo $tryDemo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\tryDemo  $tryDemo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, tryDemo $tryDemo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\tryDemo  $tryDemo
     * @return \Illuminate\Http\Response
     */
    public function destroy(tryDemo $tryDemo)
    {
        //
    }
}

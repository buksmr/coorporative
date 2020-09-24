<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\invoice;
use App\Models\Company;
use App\Models\Client;
use App\Models\invoice_item;
use App\Models\Quotes;
use App\Models\QuotesItem;
use App\Models\quotes_item_amount;
use App\Models\Currencies;
use App\Models\Loan;
use Carbon\Carbon;
use DateTime;
use App\Models\Contributors_payment;
use App\Models\Expense;
use DB;


use Elibyy\TCPDF\TCPDFHelper;
use PDF;
use TCPDF;



class ClientstatementController extends Controller
{

    /**
     *
     * @SWG\Post(
     *     path="/clientstatementpreview",
     *     description="clientstatementpreview Store",
     *     tags={"REPORT - CLIENTSTATEMENT"},
     *     summary="Client Store",
     *      @SWG\Parameter(
     *          name="company_profile",
     *          description="Company Profile",
     *          required=true,
     *          type="string",in="formData",
     *      ),
     *      @SWG\Parameter(
     *          name="client_id",
     *          description="Client Name",
     *          required=false,
     *          type="string",in="formData",
     *      ),
     *     @SWG\Parameter(
     *          name="fromdate",
     *          description="From Date",
     *          required=false,
     *          type="string",in="formData",
     *      ),
     * 
     *  @SWG\Parameter(
     *          name="todate",
     *          description="To Date",
     *          required=false,
     *          type="string",in="formData",
     *      ),
     * 
     *      @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          description="auth number",
     *          required=true,
     *          type="string"
     *      ),
     *    @SWG\Response(
     *         response=200,
     *         description="Success"
     *     ),
     *  security={
     *           {"Bearer": {}}
     *       }
     * )
     * )
     */

    public function statementTest(Request $request){


                $fromdate = $request->fromdate;
        $todate = $request->todate;

         //echo"<pre>quoteviewArray";print_r($todate.$fromdate);  



$loanarray = Loan::select(DB::raw("SUM(loans.amount) as amount"), DB::raw("SUM(loans.paid_amount) as paid_amount"),  DB::raw("DATE_FORMAT(date, '%Y-%m') as Month"), DB::raw("MONTHNAME(date) as month_name"))

           // ->where('date', '>=', $fromdate)
           //   ->where('date', '<=', $todate)
     
        ->where('loans.deleted_at', NULL)


         ->groupBy('Month', 'month_name')

        ->get();
 

        $amount_array = array();
        $paid_amount_array = array();
        $loan_calculation = array();

        foreach ($loanarray as $key => $value) {

            array_push($amount_array, $value->amount);
            array_push($paid_amount_array, $value->paid_amount);
            
        }

         $loan_amount_array_sum = array_sum($amount_array);
        $loan_paid_amount_array_sum = array_sum($paid_amount_array);




        $loan_calculation = [

            'loan_amount_array_sum' => $loan_amount_array_sum,
            'loan_paid_amount_array_sum' => $loan_paid_amount_array_sum,



        ];


        $purchases = quotes_item_amount::select(DB::raw("SUM(total_amount) as total_amount"), DB::raw("SUM(balance) as balance"), DB::raw("SUM(total_paid_amount) as total_paid_amount"),  DB::raw("DATE_FORMAT(created_at, '%Y-%m') as Month"), DB::raw("MONTHNAME(created_at) as month_name"))

 ->groupBy('Month', 'month_name')
                

        ->get();


    // $purchases = quotes_item_amount::select('client_name', 'quote_no', 'quotes.date as date', 'total_amount', 'balance', 'total_paid_amount', 'payment_plan')
    // ->leftJoin('quotes', 'quotes_item_amount.quotes_id', 'quotes.quotes_id')
    //  ->leftJoin('clients', 'quotes.client_id', '=', 'clients.id')
           // ->where('date', '>=', $fromdate)
           //   ->where('date', '<=', $todate)



        $balance_amount_array = array();
        $paid_amount_array = array();
        $total_amount_array = array();

        $purchase_calculation = array();

        foreach ($purchases as $key => $value) {

            array_push($balance_amount_array, $value->balance);
            array_push($paid_amount_array, $value->total_paid_amount);
            array_push($total_amount_array, $value->total_amount);
            
        }

              $balance_amount_array_sum = array_sum($balance_amount_array);
        $paid_amount_array_sum = array_sum($paid_amount_array);
           $total_amount_array_sum = array_sum($total_amount_array);



        $purchase_calculation = [

            'balance_amount_array_sum' => $balance_amount_array_sum,
            'paid_amount_array_sum' => $paid_amount_array_sum,
            'total_amount_array_sum' => $total_amount_array_sum,



        ];
     

    $contributors = Contributors_payment::select(DB::raw("SUM(payment_amount) as payment_amount"),  DB::raw("DATE_FORMAT(created_at, '%Y-%m') as Month"), DB::raw("MONTHNAME(created_at) as month_name"))

 ->groupBy('Month', 'month_name')
    ->get();


     //$contributors = Contributors_payment::select('payment_amount')




$contribute =  array();
foreach ($contributors as $keys => $values) {

    array_push($contribute, $values->payment_amount);

   
    
}

       $contribute_sum =number_format(array_sum($contribute),2 );



    $expenses = Expense::select(DB::raw("SUM(amount) as amount"),  DB::raw("DATE_FORMAT(created_at, '%Y-%m') as Month"), DB::raw("MONTHNAME(created_at) as month_name"))

 ->groupBy('Month', 'month_name')
    ->get();

   // $expenses = Expense::select('expense_no', 'amount', 'title', 'date')->get();


$expenses_array =  array();

foreach ($expenses as $keys => $values) {

    array_push($expenses_array, $values->amount);

   
    
}

       $expenses_sum =number_format(array_sum($expenses_array),2 );






    return response()->json(['status' => 1, 'message' => "success", "loanarray" => $loanarray, "loan_calculation" => $loan_calculation, "purchases" =>$purchases, 'purchase_calculation' => $purchase_calculation, 'contributors'=> $contributors, 'contribute_sum' => $contribute_sum, 'expenses'=>$expenses, 'expenses_sum'=>$expenses_sum], 200);





    }


    public function statementdownload(Request $request){


                $fromdate = $request->fromdate;
        $todate = $request->todate;

         //echo"<pre>quoteviewArray";print_r($todate.$fromdate);  



$loanarray = Loan::select(DB::raw("SUM(loans.amount) as amount"), DB::raw("SUM(loans.paid_amount) as paid_amount"),  DB::raw("DATE_FORMAT(date, '%Y-%m') as Month"), DB::raw("MONTHNAME(date) as month_name"))

           // ->where('date', '>=', $fromdate)
           //   ->where('date', '<=', $todate)
     
        ->where('loans.deleted_at', NULL)


         ->groupBy('Month', 'month_name')

        ->get();
 

        $amount_array = array();
        $paid_amount_array = array();
        $loan_calculation = array();

        foreach ($loanarray as $key => $value) {

            array_push($amount_array, $value->amount);
            array_push($paid_amount_array, $value->paid_amount);
            
        }

         $loan_amount_array_sum = array_sum($amount_array);
        $loan_paid_amount_array_sum = array_sum($paid_amount_array);




        $loan_calculation = [

            'loan_amount_array_sum' => $loan_amount_array_sum,
            'loan_paid_amount_array_sum' => $loan_paid_amount_array_sum,



        ];


        $purchases = quotes_item_amount::select(DB::raw("SUM(total_amount) as total_amount"), DB::raw("SUM(balance) as balance"), DB::raw("SUM(total_paid_amount) as total_paid_amount"),  DB::raw("DATE_FORMAT(created_at, '%Y-%m') as Month"), DB::raw("MONTHNAME(created_at) as month_name"))

 ->groupBy('Month', 'month_name')
                

        ->get();


    // $purchases = quotes_item_amount::select('client_name', 'quote_no', 'quotes.date as date', 'total_amount', 'balance', 'total_paid_amount', 'payment_plan')
    // ->leftJoin('quotes', 'quotes_item_amount.quotes_id', 'quotes.quotes_id')
    //  ->leftJoin('clients', 'quotes.client_id', '=', 'clients.id')
           // ->where('date', '>=', $fromdate)
           //   ->where('date', '<=', $todate)



        $balance_amount_array = array();
        $paid_amount_array = array();
        $total_amount_array = array();

        $purchase_calculation = array();

        foreach ($purchases as $key => $value) {

            array_push($balance_amount_array, $value->balance);
            array_push($paid_amount_array, $value->total_paid_amount);
            array_push($total_amount_array, $value->total_amount);
            
        }

              $balance_amount_array_sum = array_sum($balance_amount_array);
        $paid_amount_array_sum = array_sum($paid_amount_array);
           $total_amount_array_sum = array_sum($total_amount_array);



        $purchase_calculation = [

            'balance_amount_array_sum' => $balance_amount_array_sum,
            'paid_amount_array_sum' => $paid_amount_array_sum,
            'total_amount_array_sum' => $total_amount_array_sum,



        ];
     

    $contributors = Contributors_payment::select(DB::raw("SUM(payment_amount) as payment_amount"),  DB::raw("DATE_FORMAT(created_at, '%Y-%m') as Month"), DB::raw("MONTHNAME(created_at) as month_name"))

 ->groupBy('Month', 'month_name')
    ->get();


     //$contributors = Contributors_payment::select('payment_amount')




$contribute =  array();
foreach ($contributors as $keys => $values) {

    array_push($contribute, $values->payment_amount);

   
    
}

       $contribute_sum =number_format(array_sum($contribute),2 );



    $expenses = Expense::select(DB::raw("SUM(amount) as amount"),  DB::raw("DATE_FORMAT(created_at, '%Y-%m') as Month"), DB::raw("MONTHNAME(created_at) as month_name"))

 ->groupBy('Month', 'month_name')
    ->get();

   // $expenses = Expense::select('expense_no', 'amount', 'title', 'date')->get();


$expenses_array =  array();

foreach ($expenses as $keys => $values) {

    array_push($expenses_array, $values->amount);

   
    
}

       $expenses_sum =number_format(array_sum($expenses_array),2 );



          $company = Company::select('company_name', 'address', 'city', 'state', 'country', 'zipcode', 'mobile', 'website')
      ->first();






    $logoimage = public_path('/') . '/files/index.jpg';
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetMargins(10, 10, 10);
      $pdf->SetFont('freeserif', '', 10);
    $pdf->AddPage('P', 'A4');
    $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');
    $pdf->startTransaction();

     // for ($i = 0; $i < count($loanarray); $i++) {

     //       echo "<pre>product_names"; print_r($loanarray[$i]['amount'] - $loanarray[$i]['paid_amount']);exit;


     // }


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
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {

  text-align: left;
  padding: 8px;
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
         
                        .invoicetool
                        {
                         
                                font-weight: 500;
                               
                             
                                
                               
                                padding: 8px;
                                text-align:left;
                           
                              
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
                                 padding: 8px;
                                text-align:left;
                                font-size:11px;
                                background-color: #f9cc49;
                        }     .invoicetoolsec
                        {
                           
                                font-weight: 500;

                                color: #000;
                                padding: 8px;
                                text-align:left;
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
    
                        .namefile
                        {
                            font-size:8px;
                        }
                        .namefile1
                        {
                                border-top:1px solid #000;
                                border-bottom:1px solid #000;
                        }

                                 table
                        {
                                background-color: #fff;
                                padding: 0px;
                                margin: 0px;
                                 border-collapse: collapse;
                               
                        }

                    </style>
                    <table cellspacing="0" cellpadding="1" class = "invoice"  >
                        <tr>

                            <td rowspan="1">
                          
            <img src="' . $logoimage . '" alt="HTML5 Icon" class="top" style="margin-top:-5px;width:80px;">


            </td> 
                          
                             <td rowspan="8"><h3>Company Address</h3>
                                    <h4>' . $company->company_name . ' ' . $company->address . ' ' . $company->city . ' ' . $company->zipcode . ' ' . $company->state . ' ' . $company->country . ' ' . $company->mobile . ' ' . $company->website . '</h4>
                             </td>

                        </tr>
                       
                    </table>
                           
 <h2>Loans</h2> 

            <table  class="invoice">

          
        
       

         

<tr  >
                                    

                            <th width="12%"  class="invoicetool">Month </th>

                            <th width="15%" class="invoicetool">Amt disbursed</th>
            
                           <th width="15%" class="invoicetool">Repayed Amt</th>
                            <th width="14%" class="invoicetool">Balance</th>
        
                        </tr>
                         ';

    $loan_body = '';
    for ($i = 0; $i < count($loanarray); $i++) {
      $loan_body .= '

                        <tr>
                          <td >' . $loanarray[$i]['month_name'] . '</td>

                         
                   
            <td><b>&#8358;</b>' . ' ' . number_format($loanarray[$i]['amount'], 2) . ' ' . '</td>
            <td ><b>&#8358;</b>' . ' ' . number_format($loanarray[$i]['paid_amount'], 2) . ' ' . '</td>
             <td><b>&#8358;</b>' .   number_format(($loanarray[$i]['amount'] - $loanarray[$i]['paid_amount']), 2) . '</td>
                    
                     
                        </tr>';
    
 }  


     $loan_footer = '</table>

               
                
                   
                    <table  class="invoicesection">
                  
             <tr>
                <th></th>
                <th> </th>
                <th></th>
                <th> </th>
            <th width="17%" class="invoicetool">Total AMT Disbursed</th>
            <th width="17%" class="invoicetoolsec"><b>&#8358;</b>' .' ' . number_format($loan_calculation['loan_amount_array_sum'], 2). '</th>
            </tr>


             
             <tr>
                <th></th>
                <th> </th>
                <th></th>
                <th> </th>
            <th width="17%" class="invoicetool">Total Repayment</th>
    <th width="17%"  class="invoicetoolsec"><b>&#8358;</b>' . ' ' . number_format($loan_calculation['loan_paid_amount_array_sum'], 2) . ' ' . '</th>
            </tr>

            <tr>
                <th></th>
                <th> </th>
                <th></th>
                <th> </th>
            <th width="17%"  class="invoicetool">Balance</th>
            <th width="17%"  class="invoicetoolsec"><b>&#8358;</b>'  . ' ' . number_format($loan_calculation['loan_amount_array_sum'] - $loan_calculation['loan_paid_amount_array_sum'], 2) . ' ' . '</th>
            </tr>
            ';

  $purchase_header =   '</table>

          
 <h2>Purchases</h2> 
        <table class="invoicesection">

          
           
        

         

<tr>
                                     <th width="12%" class="invoicetool">Month</th>
               
                           <th width="15%"  class="invoicetool">Item AMT</th>
                            <th width="15%"  class="invoicetool">Repayed AMT</th>
            
                           <th width="15%" class="invoicetool">Balance</th>
       

 
                        </tr>
                         ';

    $purchase_body = '';
    for ($i = 0; $i < count($purchases); $i++) {
      $purchase_body .= '




                        <tr>
                          <td >' . $purchases[$i]['month_name'] . '</td>
                   
                    <td ><b>&#8358;</b>' . ' ' . number_format($purchases[$i]['total_amount'], 2) . ' ' . '</td>
                    <td ><b>&#8358;</b>' . ' ' . number_format($purchases[$i]['total_paid_amount'], 2) . ' ' . '</td>
                         <td ><b>&#8358;</b>' . ' ' . number_format($purchases[$i]['Balance'], 2) . ' ' . '</td>
                    
                     
                        </tr>';
    
   }   


        $purchase_footer = '</table>

               
            
                    <table  class="invoicesection">
                  
             <tr>
                <th></th>
                <th> </th>
                <th></th>
                <th> </th>
            <th width="17%"  class="invoicetool">Total Item Amount</th>
            <th width="17%" class="invoicetoolsec"><b>&#8358;</b>' .' ' . number_format($purchase_calculation['total_amount_array_sum'], 2). '</th>
            </tr>


             
             <tr>
                <th></th>
                <th> </th>
                <th></th>
                <th> </th>
            <th width="17%"  class="invoicetool">Total Repayment</th>
    <th width="17%" class="invoicetoolsec"><b>&#8358;</b>' . ' ' . number_format($purchase_calculation['paid_amount_array_sum'], 2) . ' ' . '</th>
            </tr>

            <tr>
                <th></th>
                <th> </th>
                <th></th>
                <th> </th>
            <th width="17%"  class="invoicetool">Balance</th>
            <th width="17%"  class="invoicetoolsec"><b>&#8358;</b>'  . ' ' . number_format($purchase_calculation['balance_amount_array_sum'], 2) . ' ' . '</th>
            </tr>
            ';




$contributions =   '</table>

      <h2>Contributions</h2>    

        <table  class="invoicesection">

          
           

        

     

<tr>
                                     <th width="12%"  class="invoicetool">Month</th>
               
                           <th width="15%"  class="invoicetool">Contributions</th>
                   
       

 
                        </tr>
                         ';

    $contributions_body = '';
    for ($i = 0; $i < count($contributors); $i++) {
      $contributions_body .= '





                        <tr>
                          <td >' . $purchases[$i]['month_name'] . '</td>
                   
                    <td><b>&#8358;</b>' . ' ' . number_format($purchases[$i]['payment_amount'], 2) . ' ' . '</td>

                    
                     
                        </tr>';
    
   }   


        $contributions_footer = '</table>

               
                
                    <br><br>
                    <table class="invoicesection">
                  
             <tr>
                <th></th>
                <th> </th>
                <th></th>
                <th> </th>
            <th width="17%"  class="invoicetool">Total contributions</th>
            <th width="17%"  class="invoicetoolsec"><b>&#8358;</b>' .' ' . $contribute_sum. '</th>
            </tr>



            <br><br><br>';
                         

           
               $expense_header =   '</table>

   <h2>Expenditure</h2>       

        <table  class="invoicesection">

          
           
           
          

         

            

<tr>
                                                     <th width="15%"  class="invoicetool">Month</th>

        
                           <th width="20%"  class="invoicetool">Amount AMT</th>


 
                        </tr>
                         ';

    $expense_body = '';
    for ($i = 0; $i < count($expenses); $i++) {
      $expense_body .= '


                        <tr>
                          <td >' . $expenses[$i]['month_name'] . '</td>

                    <td ><b>&#8358;</b>' . ' ' . number_format($expenses[$i]['amount'], 2) . ' ' . '</td>
                    
                        </tr>';
    
   }   


    $expense_footer = '</table>

               
                
                    <br><br>
                    <table  class="invoicesection">
                  
             <tr>
                <th></th>
                <th> </th>
                <th></th>
                <th> </th>
            <th width="17%"  class="invoicetool">Total Amt</th>
            <th width="17%"  class="invoicetoolsec"><b>&#8358;</b>' .' ' . $expenses_sum. '</th>
            </tr>





            <br><br><br>



            </table>


                                        
                
               



        


 
    </body>';



    $tcpdf = $header . $loan_body. $loan_footer. $purchase_header. $purchase_body. $purchase_footer. $contributions. $contributions_body.$contributions_footer. $expense_header. $expense_body. $expense_footer;

    $pdf->writeHTML($tcpdf, true, false, true, false, '');

    $file = $pdf->Output('quotes.pdf', 'I');
    return $file;





    }






    public function reportdownload($id)
    {


   
     $contributors = Contributors_payment::select('payment_amount', 'payment_date', 'payment_no')->where('client_id', $id)->get();



$contribute =  array();
foreach ($contributors as $keys => $values) {

    array_push($contribute, $values->payment_amount);





   
    
}




               
                $contribute_sum =  array_sum($contribute);

       

 $loanarray = Loan::select('loan_id', 'client_id', 'loans.amount as amount','loan_no', 'disburse_account', 'disburse_bank', 'payment_plan', 'note', 'date', 'clients.client_name as client_name', 'phone_number', 'balance', 'loans.status as status', 'paid_amount')
        ->leftJoin('clients', 'loans.client_id', '=', 'clients.id')
        ->where('loans.deleted_at', NULL)
        ->where('loans.client_id', $id)

        ->get();

 $client = client::where('id', $id)->select('id', 'client_name', 'email', 'address', 'city', 'state', 'postal_code', 'country', 'phone_number', 'fax_number', 'office_unit', 'amount', 'default_currency', 'tax_code', 'status')->first();



    $quotes = Quotes::select('clients.client_name as client_name', 'quotes.quotes_id as quotes_id', 'quote_no', 'date', 'quotes_status_master.quotesstatus', 'total', 'quotes.currencies_id as currencies_id', 'payment_plan')
      ->leftJoin('clients', 'quotes.client_id', '=', 'clients.id')
      ->leftJoin('quotes_status_master', 'quotes.quotes_status_id', '=', 'quotes_status_master.quotesstatus_id')
      ->where('quotes.deleted_at', '=', NULL)->where('quotes.quote_no', '!=', NULL)
      ->where('quotes.client_id', $id)
      ->get();

      
    $quoteviewArray = array();
    foreach ($quotes as $key => $value) {
      $CurrenciesData = Currencies::select('symbol', 'placement', 'thousands', 'decimal')->where('id', '=', $value->currencies_id)->first();

           // $payed_amount = Payment::select('payment_amount')->where('invoice_id', '=', $value->quotes_id)->get();  
      $balances = quotes_item_amount::select('total_amount', 'total_paid_amount')->where('quotes_id', '=', $value->quotes_id)->first(); 

               
       $due_date =   Carbon::parse($value->date);
$duedate = $due_date->addMonths($value->payment_plan)->toDateString();



               
      $quoteviewArray[] = [

        "payment_plan"=>$value->payment_plan,

        "currencies_id" => $CurrenciesData['symbol'],
        "currencies_placement" => $CurrenciesData['placement'],
        "currencies_thousands" => $CurrenciesData['thousands'],
        "currencies_decimal" => $CurrenciesData['decimal'],
        "quotes_id" => $value->quotes_id,
        "quote_no" => $value->quote_no,
        "date" => $value->date,
        "due_date" => $duedate,
        "client_name" => $value->client_name,
        "payed_amount_sum" => number_format($balances->total_paid_amount, 2),
         "balance_amount" =>number_format($balances->total_amount - $balances->total_paid_amount, 2),


        // "currency_id" => Currencies::select('symbol')->where('id', '=', $value->currency_id)->first()['symbol'],
        "totalamount" => number_format($balances->total_amount, 2),
        // "currencies_id" => $value->currencies_id,
        //  "status"=>$value->status,
        "quotesstatus" => $value->quotesstatus,

      ];



  }
 // echo"<pre>quoteviewArray";print_r($quoteviewArray[0]['quote_no']); 
  
   // echo"<pre>quoteviewArray";print_r($quoteviewArray); 



   
    $company = Company::select('company_name', 'address', 'city', 'state', 'country', 'zipcode', 'mobile', 'website')
      ->first();

 

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
  <h4>' . $client->client_name . '<br>' . $client->address . ', ' . $client->city. ', '.'<br>' . $client->state .'.'. '<br>'. $client->email.'</h4>
   
   
 </td>

 <td></td>

    <td width="17%" style="border:1px solid #000" class="invoicetool"># Client Name</td>
    <td width="17%" style="border:1px solid #000" class="invoicetoolsec">' . $client->client_name . '</td>
    
  </tr>
  <tr>
   <td></td>

   <td width="17%" style="border:1px solid #000" class="invoicetool">Start Date</td>
    <td width="17%" style="border:1px solid #000" class="invoicetoolsec">' .  Carbon::parse($client->created_at)->toFormattedDateString() . '</td>
  </tr>
   <tr>
    <td></td>

  <td width="17%" style="border:1px solid #000" class="invoicetool">Amount Contributed</td>
    <td width="17%" style="border:1px solid #000" class="invoicetoolsec">' . $client->amount. '</td>

  </tr>
             
                <br>



               
          </table>
            <table cellspacing="6" cellpadding="3" class="invoicesection">

          
           
           
            <br>
<h3>Purchases</h3>
            <br>

<tr>
                                                    <th width="15%" style="border:1px solid #000" class="invoicetool">Purchase ID</th>

                            <th width="15%" style="border:1px solid #000" class="invoicetool">Date</th>
                            <th width="20%" style="border:1px solid #000" class="invoicetool">Total Amount</th>
               
                           <th width="20%" style="border:1px solid #000" class="invoicetool">Total Paid</th>
                            <th width="18%" style="border:1px solid #000" class="invoicetool">Balance Amount</th>
            
                           <th width="12%" style="border:1px solid #000" class="invoicetool">Payment Plan</th>
                        </tr>
                         ';

    $body = '';
    for ($i = 0; $i < count($quoteviewArray); $i++) {
      $body .= '

                        <tr>
                          <td style="border:1px solid #000">' . $quoteviewArray[$i]['quote_no'] . '</td>

                            <td style="border:1px solid #000">' . $quoteviewArray[$i]['date'] . '</td>
                               <td style="border:1px solid #000"><b>&#8358;</b>' . ' ' . $quoteviewArray[$i]['totalamount']. ' ' . '</td>
                   
                            <td style="border:1px solid #000"><b>&#8358;</b>' . ' ' . $quoteviewArray[$i]['payed_amount_sum'] . ' ' . '</td>
                             <td style="border:1px solid #000"><b>&#8358;</b>' . ' ' . $quoteviewArray[$i]['balance_amount'] . ' ' . '</td>
                          <td style="border:1px solid #000"><b></b>' . ' ' . $quoteviewArray[$i]['payment_plan'] . 'Months' . '</td>
                     
                        </tr>';
    
 }  

  $loan =   '</table>

          

        <table cellspacing="6" cellpadding="3" class="invoicesection">

          
           
           
          

            <br>
<h3>Loans</h3>
            <br>

<tr>
                                                     <th width="15%" style="border:1px solid #000" class="invoicetool">Loan ID</th>

                            <th width="15%" style="border:1px solid #000" class="invoicetool">Date</th>
                            <th width="20%" style="border:1px solid #000" class="invoicetool">Total Amount</th>
               
                           <th width="20%" style="border:1px solid #000" class="invoicetool">Total Paid</th>
                            <th width="18%" style="border:1px solid #000" class="invoicetool">Balance Amount</th>
            
                           <th width="12%" style="border:1px solid #000" class="invoicetool">Payment Plan</th>

 
                        </tr>
                         ';

    $lo = '';
    for ($i = 0; $i < count($loanarray); $i++) {
      $lo .= '

                        <tr>
                          <td style="border:1px solid #000">' . $loanarray[$i]['loan_no'] . '</td>

                            <td style="border:1px solid #000">' . $loanarray[$i]['date'] . '</td>
                               <td style="border:1px solid #000"><b>&#8358;</b>' . ' ' . number_format($loanarray[$i]['amount'], 2). ' ' . '</td>
                   
                            <td style="border:1px solid #000"><b>&#8358;</b>' . ' ' . number_format($loanarray[$i]['paid_amount'], 2) . ' ' . '</td>
                             <td style="border:1px solid #000"><b>&#8358;</b>' . ' ' . number_format($loanarray[$i]['balance'], 2) . ' ' . '</td>
                          <td style="border:1px solid #000"><b></b>' . ' ' . $loanarray[$i]['payment_plan'] . 'Months' . '</td>
                     
                        </tr>

                           ';
    
   }   

 
                         

           
                $footer = '</table>

                    <table cellspacing="6" cellpadding="3" class="invoicesection">

                    <h2> Contributions</h2>
                  
             <tr>
                <th></th>
                <th> </th>
                <th></th>
                <th> </th>
            <th width="17%" style="border:1px solid #000" class="invoicetool">Total Loan Amount</th>
            <th width="17%" style="border:1px solid #000" class="invoicetoolsec"><b>&#8358;</b>' .' ' . number_format($contribute_sum, 2). '</th>
            </tr>


             

            <br><br><br>



       

              </table>
            <br><br><br>


 
    </body>';



    $tcpdf = $header . $body. $loan. $lo. $footer;

    $pdf->writeHTML($tcpdf, true, false, true, false, '');

    $file = $pdf->Output('quotes.pdf', 'I');
    return $file;



  }
    public function clientstatement(Request $request)
    {
        $formData = $request->all();
        // echo "<pre>";
        // print_r($formData);
        // exit;
        $client_id = $formData['client_id'];

        $client = Client::where('client_name', $client_id)->first();

        $id =  $client->id;




        $company_profile = $request->company_profile;
        $clientid = $id;
        $fromdate = $request->fromdate;
        $todate = $request->todate;


        // $clientstatement = DB::select("SELECT invoices.company_id,clients.id,clients.client_name,invoices.invoice_number,invoices.balance,invoices.total,invoices.discount,invoice_items.description FROM companies
        //                     LEFT JOIN invoices on invoices.company_id = companies.id
        //                     LEFT JOIN invoice_items on invoices.invoice_id = invoice_items.invoice_id
        //                     LEFT JOIN clients on invoices.client_id = clients.id 
        //                     where invoices.deleted_at IS NULL and invoices.company_id = '$company_profile' and invoices.client_id = '$clientid' and '$fromdate' and '$todate'
        //                     GROUP BY invoices.company_id,clients.id,clients.client_name,invoices.invoice_number,invoices.balance,invoices.total,invoice_items.description,invoices.discount");
        $company = Company::select('company_name', 'address', 'city', 'state', 'country', 'zipcode', 'mobile', 'website')
            ->where('id', $company_profile)->first();
        $client = Client::select('client_name')->where('id', $clientid)->first();

        $clientstatement = DB::select("SELECT invoices.company_id,companies.company_name,clients.id, invoice_item_amounts.tax,invoice_amounts.item_sub_total,invoice_amounts.discount_amount,clients.client_name,invoices.invoice_number,invoices.date,invoices.total_paid_amount,invoices.balance,invoices.total,invoices.discount,invoice_items.description,currencies.symbol,currencies.placement,currencies.thousands,currencies.decimal FROM companies
                            LEFT JOIN invoices on invoices.company_id = companies.id
                            LEFT JOIN invoice_items on invoices.invoice_id = invoice_items.invoice_id
                            LEFT JOIN invoice_amounts on invoices.invoice_id = invoice_amounts.invoice_id
                            LEFT JOIN invoice_item_amounts on invoices.invoice_id = invoice_item_amounts.invoice_id
                            LEFT JOIN clients on invoices.client_id = clients.id 
                            LEFT JOIN currencies on invoices.currency_id = currencies.id
                            where invoices.invoice_number IS NOT NULL and companies.deleted_at IS NULL  and invoices.deleted_at IS NULL and invoices.company_id = '$company_profile' and invoices.client_id = '$clientid' and invoices.date >= '$fromdate' and invoices.date <= '$todate'
                            GROUP BY currencies.placement,currencies.thousands,currencies.decimal,invoices.company_id,currencies.symbol,companies.company_name,clients.id,clients.client_name,invoices.invoice_number,invoice_item_amounts.tax,invoice_amounts.item_sub_total,invoice_amounts.discount_amount,invoices.date,invoices.total_paid_amount,invoices.balance,invoices.total,invoices.discount,invoice_items.description");
        // echo "<pre>";
        // print_r($clientstatement);
        // exit;
        $pdf = new TCPDFHelper('L', 'mm', 'ANSI_A', true, 'UTF-8', false);
        $pdf->SetMargins(5, 5, 5);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->AddPage('L', 'A4');
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
                                font-family: sans-serif;
                               font-size:8px;
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
                                font-family: sans-serif;
                               font-size:8px;
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
                            </td> 
                           <td></td>
                            
                   
                        </tr>
                       
                    </table>
                            <br><br><br>

                             
    <h5 style="font-weight: bold;font-size:13px;text-align:center;text-transform:uppercase">' . $company->company_name . '</h5>
  <p style="font-weight: bold;font-size:10px;text-align:center;">
  '  . $company->address . ' ' . '
  <br>
  '  . $company->city . ' - ' . $company->zipcode . '
  <br>
   '  . $company->state . ' ' . '
  
    '  . $company->country . ' ' . '
    <br>
    '  . $company->mobile . ' ' . '
   
     '  . $company->website . ' ' . '
     <br></p>
 <h5 style="font-weight: bold;font-size:13px;text-align:center;text-transform:uppercase">Client Statement</h5>
<p style="font-weight: bold;font-size:10px;text-align:center;text-transform:uppercase">Financial Date' . ' (' . $fromdate . ' - ' . $todate . ' )' . '</p>   
          
 <table cellspacing="1" cellpadding="10" class="invoicesection">

        <br><br><br>

                        <tr>
                            <td class="amount" style="font-weight: bold;border-bottom:1px solid #ddd;">Date</td>
                            <td class="amount" style="font-weight: bold;border-bottom:1px solid #ddd;">Invoice</td>
                            <td class="amount" style="font-weight: bold;border-bottom:1px solid #ddd;">Description</td>
                            <td class="amount" style="font-weight: bold;border-bottom:1px solid #ddd;">Subtotal</td>
                            <td class="amount" style="font-weight: bold;border-bottom:1px solid #ddd;">Discount</td>
                            <td class="amount" style="font-weight: bold;border-bottom:1px solid #ddd;">Tax</td>
                            <td class="amount" style="font-weight: bold;border-bottom:1px solid #ddd;">Total</td>
                            <td class="amount" style="font-weight: bold;border-bottom:1px solid #ddd;">Paid</td>
                            <td class="amount" style="font-weight: bold;border-bottom:1px solid #ddd;">Balance</td>
                      
                        </tr>';

        $body = '';
        for ($i = 0; $i < count($clientstatement); $i++) {
            $body .= '

                        <tr>
                        <td class="amount" style="font-weight: bold;border-bottom:1px solid #ddd;">' . $clientstatement[$i]->date . '</td>
                        <td class="amount" style="font-weight: bold;border-bottom:1px solid #ddd;">' . $clientstatement[$i]->invoice_number . '</td>
                        <td class="amount" style="font-weight: bold;border-bottom:1px solid #ddd;">' . $clientstatement[$i]->description . '</td>
                        <td class="amount" style="font-weight: bold;border-bottom:1px solid #ddd;">' . ($clientstatement[$i]->symbol) . ($clientstatement[$i]->item_sub_total)  . '</td>
                        <td class="amount" style="font-weight: bold;border-bottom:1px solid #ddd;">' . $clientstatement[$i]->discount . '</td>
                        <td class="amount" style="font-weight: bold;border-bottom:1px solid #ddd;">' . $clientstatement[$i]->tax . '</td>
                        <td class="amount" style="font-weight: bold;border-bottom:1px solid #ddd;">' . ($clientstatement[$i]->symbol) . ($clientstatement[$i]->total) .  '</td>
                        <td class="amount" style="font-weight: bold;border-bottom:1px solid #ddd;">' . ($clientstatement[$i]->symbol) . ($clientstatement[$i]->total_paid_amount)  . '</td>
                        <td class="amount" style="font-weight: bold;border-bottom:1px solid #ddd;">' . ($clientstatement[$i]->symbol) . ($clientstatement[$i]->balance)  . '</td>
                     
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
          
            </tr>

             <tr>
                <th></th>
                <th> </th>
                <th></th>
                <th> </th>
          
            </tr>

             
             <tr>
                <th></th>
                <th> </th>
                <th></th>
                <th> </th>
          
            </tr>

            <tr>
                <th></th>
                <th> </th>
                <th></th>
                <th> </th>
            
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
        $file = $pdf->Output('ClientStatement.pdf', 'I');
        return $file;
    }
}

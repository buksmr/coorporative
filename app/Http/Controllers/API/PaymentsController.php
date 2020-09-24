<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\invoice_item_amount;
use App\Models\invoice_status_master;
use App\Models\invoice_amount;
use App\Models\Currencies;
use App\Models\invoice;
use App\Models\quotes_status_master;
use App\Models\quotes_amount;
use App\Models\quotes_item_amount;
use App\Models\quote_notes;
use App\Models\quote_attachements;
use App\Models\Quotes;

use Input;
use Response;
class PaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */

    /**
     * @SWG\Get(
     *          path="/Payment",
     *          tags={"PAYMENT"},
     *          summary="Payment table",
     *          operationId="Peid",
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
    public function index()
    {
        $invoice = Payment::select('payment_methods.name as payment_name','quotes.quotes_id','quotes.quote_no','date','payment_amount','paymenttype_id','payment_date','note','payment_id','quotes.currencies_id as currencies_id','clients.client_name as client_name', 'payment_no')
        ->leftJoin('quotes','payments.invoice_id', '=', 'quotes.quotes_id')
        ->leftJoin('clients','quotes.client_id', '=', 'clients.id')
        ->leftJoin('payment_methods','payments.paymenttype_id', '=', 'payment_methods.id')
         ->where('clients.deleted_at', '=', NULL)->where('quotes.deleted_at', '=', NULL)->where('payments.deleted_at', '=', NULL)	
          ->orderBy('created_at', 'ASC')
		->get();
      
          $Paymentarray = array();
          foreach($invoice as $key => $value){



            
              
              $CurrenciesData = Currencies::select('symbol', 'placement', 'thousands','decimal')->where('id', '=', $value->currencies_id)->first();

             // echo "<pre>"; print_r($CurrenciesData);exit;
            
              $Paymentarray[] = [
                  //'currency_id' => Currencies::select('symbol')->where('id','=',$value->currency_id)->first()['symbol'],
                  "currencies_id" => $CurrenciesData['symbol'],
                  "currencies_placement" => $CurrenciesData['placement'],
                  "currencythousands" => $CurrenciesData['thousands'],
                   "currencydecimal" => $CurrenciesData['decimal'],
                  'invoice_id' => $value->quotes_id,
				           'payment_id' => $value->payment_id,
                  'invoice_number' => $value->payment_no,
                  'date' => $value->date,
				          'client_name'=>$value->client_name,
				          'payment_amount'=>$value->payment_amount,
				          'paymenttype_id'=>$value->payment_name,
                  'payment_date'=>$value->payment_date,
				           'note'=>$value->note != '' && $value->note != "undefined" ? $value->note : null				  
              ];

                
          }

        //echo "<pre>"; print_r($Paymentarray);exit;
              return response()->json(['status' => 1, 'message' => "success", "payment_Details" => $Paymentarray], 200);     
    }

     public function view_payment($id)
    {

        

         // echo "<pre>"; print_r($id);exit;
        $invoice = Payment::select('payment_methods.name as payment_name','quotes.quotes_id','quotes.quote_no', 'no_month','payment_no','date','payment_amount','paymenttype_id','payment_date','note','payment_id','quotes.currencies_id as currencies_id','clients.client_name as client_name')
        ->leftJoin('quotes','payments.invoice_id', '=', 'quotes.quotes_id')
        ->leftJoin('clients','quotes.client_id', '=', 'clients.id')
        ->leftJoin('payment_methods','payments.paymenttype_id', '=', 'payment_methods.id')
         ->where('clients.deleted_at', '=', NULL)->where('quotes.deleted_at', '=', NULL)->where('payments.deleted_at', '=', NULL)
         ->where('payments.invoice_id', $id)->get();
         // echo "<pre>"; print_r($request->all());exit;
          $Paymentarray = array();
          foreach($invoice as $key => $value){



            
              
              $CurrenciesData = Currencies::select('symbol', 'placement', 'thousands','decimal')->where('id', '=', $value->currencies_id)->first();

             // echo "<pre>"; print_r($CurrenciesData);exit;
            
              $Paymentarray[] = [
                  //'currency_id' => Currencies::select('symbol')->where('id','=',$value->currency_id)->first()['symbol'],
                  "currencies_id" => $CurrenciesData['symbol'],
                  "currencies_placement" => $CurrenciesData['placement'],
                  "currencythousands" => $CurrenciesData['thousands'],
                   "currencydecimal" => $CurrenciesData['decimal'],
                  'invoice_id' => $value->quotes_id,
                           'payment_id' => $value->payment_id,
                  'invoice_number' => $value->payment_no,
                    'no_month' => $value->no_month,
                  
                  'date' => $value->date,
                          'client_name'=>$value->client_name,
                          'payment_amount'=>$value->payment_amount,
                          'paymenttype_id'=>$value->payment_name,
                  'payment_date'=>$value->payment_date,
                           'note'=>$value->note != '' && $value->note != "undefined" ? $value->note : null                
              ];

                
          }

        //echo "<pre>"; print_r($Paymentarray);exit;
              return response()->json(['status' => 1, 'message' => "success", "payment_Details" => $Paymentarray], 200);     
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response 
     */
    public function store(Request $request)
    {
        // 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

     /**
     * @SWG\Get(
     *          path="/Payment/{id}",
     *          tags={"PAYMENT"},
     *          summary="Payment Edit",
     *          operationId="Peid",
     *          @SWG\Parameter(
     *              name="payment_id",
     *              description="Payment Id",
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
    public function show($id)
    {
         $payment = payment::select('payment_amount', 'paymenttype_id', 'payment_date', 'note') 
		 ->leftJoin('payment_methods','payments.paymenttype_id', '=', 'payment_methods.id')
		 ->where('payment_id', $id)->first();
			
     //echo "<pre>";print_r($id);exit;
     
$paymentShowArray = [
   
                'amount' =>  $payment->payment_amount,
                'paymentmethod' =>  $payment->paymenttype_id,
                'paymentdate' =>  $payment->payment_date,
                'paymentnotes' =>  $payment->note != '' && $payment->note != "undefined" ? $payment->note: null
                
	];
	return response::json(['error' => false, 'message' =>"Payment stored successfully",  "paymentdetails" => $paymentShowArray], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

      /**
     *
     * @SWG\Post(
     *     path="/Payment/{id}",
     *     description="Payment Update",
     *     tags={"PAYMENT"},
     *     summary="Payment Update",
      *          @SWG\Parameter(
     *              name="payment_id",
     *              description="Payment Id",
     *              required=true,
     *              type="string",
     *              in="path"
     *          ),
     * 
     *      @SWG\Parameter(
     *          name="payment_amount",
     *          description="Payment Amount",
     *          required=false,
     *          type="string",in="formData",
     *      ),
     *     @SWG\Parameter(
     *          name="paymentype_id",
     *          description="Payment Type",
     *          required=false,
     *          type="string",in="formData",
     *      ),
     * 
     *     @SWG\Parameter(
     *          name="payment_date",
     *          description="Payment Date",
     *          required=false,
     *          type="string",in="formData",
     *      ),
     * 
     *  @SWG\Parameter(
     *          name="note",
     *          description="Note",
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
    public function update(Request $request, $id)
    {
                  
    $formdata = $request->all(); 
    //echo "<pre>";print_r($formdata);exit;
    
    $payment = payment::where('payment_id',$id)->first();
          
                   $payment_amount = $payment-> payment_amount;
                   $invoice_id = $payment-> invoice_id;

	  //echo "<pre>";print_r($payment_amount);exit;
    $formdata = [
             
              'payment_amount' => $request->input('amount'),
              'paymenttype_id' => $request->input('paymentmethod'),
              'payment_date' => $request->input('paymentdate'),
              'note' => $request->input('paymentnotes'),
         
              ];
          


            $invoice = invoice::where('invoice_id',$invoice_id)->first();
                 $invoice_balance = $invoice-> balance;
                 $invoice_paid_amount = $invoice-> total_paid_amount;
     
           
            $balance = ($payment_amount-$request->input('amount'));
            $editbalance= ($balance + $invoice_balance);

            $total_payment = ( $request->input('amount') - $payment_amount);
            $total_paid = ($invoice_paid_amount + $total_payment);
            
        // echo "<pre>";print_r($total_paid);
        //  echo "<pre>";print_r($total_payment);
        //   echo "<pre>bal+";print_r($editbalance);
        //    echo "<pre>bal";print_r($balance); exit;
        
        

       $update = [
        'total_amount' => $editbalance,
        
     
     
    ];

        
       
     
		 
		 	$invoice_item_amount = invoice_item_amount::where('invoice_id', $invoice_id)->update($update);
       

               $paid = "Sent";

                $invoices = invoice::where('invoice_id', $invoice_id)->first();
            
        $invoice_status = $invoices -> invoice_status_id;
  
        $invoicestatus = invoice_status_master::where('invoicestatus', '=', $paid)->first();
            
          $invoice_status_id = $invoicestatus ->invoicestatus_id;
          //echo "<pre>";print_r($invoice_balance);

          if($invoice_balance !=0){

            $status=$invoice_status;

          }else{
 $status=$invoice_status_id;
          }


            $updateinvoice = [
                'balance' => $editbalance,
                'total_paid_amount' => $total_paid,
                'invoice_status_id'=> $status

            ];
        
            $total=[
                'total_amount' => $editbalance,
            ];
                
            
          $invoice = invoice::where('invoice_id', $invoice_id)->update($updateinvoice);
          $payment = payment::where('payment_id', $id)->update($formdata);
          $invoice_item_amount = invoice_item_amount::where('invoice_id', $invoice_id)->update($total);





            return response::json(['error' => false, 'message' =>"Payment Updated successfully"], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

     /**
     * @SWG\Delete(
     *          path="/Payment/{id}",
     *          tags={"PAYMENT"},
     *          summary="Payment Delete",
     *          operationId="Pdid",
     *          @SWG\Parameter(
     *              name="payment_id",
     *              description="Payment Id",
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
    public function destroy($id)
    {
       //echo $id;exit;
      // echo "<pre>";print_r($id);exit;
       $date = date('Y-m-d H:i:s');
       
        $payment_amount = payment::where('payment_id', $id)->first();
        $deletedamount = $payment_amount->payment_amount;
        $invoice_id = $payment_amount->invoice_id;


    $invoice = quotes_item_amount::where('quotes_id', $invoice_id)->first();

  

        $invoice_balance = $invoice->balance;
        $invoice_paid_amount = $invoice->total_paid_amount;

        $total_balance = ($invoice_balance + $deletedamount);
        $total_paid = ($invoice_paid_amount - $deletedamount);

             $paid = "Paid";

    $quotestatus_paid = quotes_status_master::where('quotesstatus', '=', $paid)->first();

    $quotes_status_id_paid = $quotestatus_paid->quotesstatus_id;

        $approved = "Approved";

    $quotestatus_approved = quotes_status_master::where('quotesstatus', '=', $approved)->first();

    $quotes_status_id_approve = $quotestatus_approved->quotesstatus_id;


            if ($total_balance != 0) {

      $paidstatus = $quotes_status_id_approve;

    } else {

      $paidstatus = $quotes_status_id_paid;

    }





        $updatequotes = [
      'quotes_status_id' => $paidstatus,

    ];

         //echo "<pre>";print_r($paidstatus);exit;
    $quotes = Quotes::where('quotes_id', $invoice_id)->update($updatequotes);




   $invoice = quotes_item_amount::where('quotes_id', $invoice_id)->update(['balance' => $total_balance, 'total_paid_amount' => $total_paid]);
   $payment = payment::where('payment_id', $id)->update(['deleted_at' => $date]);

   return response::json(['error' => false, 'message' =>"Payment Deleted successfully"], 200); 

    }

    /**
     * @SWG\Get(
     *          path="/paymethod",
     *          tags={"PAYMENT"},
     *          summary="Payment table",
     *          operationId="Pmid",
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
	
	public function paymethod()
	{
		$PaymentMethod = PaymentMethod::select('id','name')->get();
		
	$paymethodArray = array();
        foreach($PaymentMethod as $value){
          $paymethodArray[] = [
             "id"=>$value->id,
            "name" => $value->name,
			];
		}
		return response::json(['error' => false, 'message' =>"success", "paymethodDetails" => $paymethodArray], 200);
	}
}

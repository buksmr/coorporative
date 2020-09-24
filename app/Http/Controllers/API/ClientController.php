<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Currencies;
use App\Models\invoice;
use App\Models\Quotes;
use App\Http\Controllers\Controller;
use Response;

class ClientController extends Controller
{

    public function index()
    {

        $client = client::select('id', 'default_currency', 'client_name', 'email', 'address', 'city', 'state', 'postal_code', 'country', 'phone_number', 'fax_number', 'office_unit', 'amount', 'default_currency', 'tax_code', 'loan', 'status')
             ->orderBy('created_at', 'DESC')
            // ->leftJoin('invoices', 'clients.id', '=', 'invoices.client_id')
            // ->where('clients.deleted_at', '=', NULL)->where('invoices.deleted_at', '=', NULL)->distinct()->get();
            ->get();
        //  echo"<pre>client";print_r( $client );exit;
        $clientArray = array();
        foreach ($client as $key => $value) {
            $client_id = $value->id;
            // $balance = Quotes::join('clients', 'quotes.client_id', '=', 'clients.id')
            //     ->where('quotes.client_id', '=', $client_id)
            //     ->whereNull('quotes.deleted_at')->where('clients.deleted_at', '=', NULL)
            //     ->sum('balance');
            $CurrenciesData = Currencies::select('symbol', 'placement', 'thousands', 'decimal')->where('id', '=', $value->default_currency)->first();
            $clientArray[] = [
                'id' => $value->id,
                'client_name' => $value->client_name,
                
                "currencies_id" => $CurrenciesData['symbol'],
                "currencies_placement" => $CurrenciesData['placement'],
                "currencies_thousands" => $CurrenciesData['thousands'],
                "currencies_decimal" => $CurrenciesData['decimal'],
                // 'currency_id' => Currencies::select('symbol')->where('id', '=', $value->currency_id)->first()['symbol'],
                'email' => $value->email,
                'address' => $value->address,
                'city' =>  $value->city,
                'state' => $value->state,
                'postal_code' => $value->postal_code,
                'country' => $value->country,
                'phone_number' => $value->phone_number,
                'fax_number' => $value->fax_number,
                'office_unit' => $value->office_unit,
                'amount' => $value->amount,
                'loan' => $value->loan,
                'default_currency' => $value->default_currency,
                'tax_code' => $value->tax_code,


                'status' => $value->status == 1 ? "Active" : "Inactive",
            ];
        }
        return response()->json(['status' => 1, 'message' => "success", "clientDetails" => $clientArray], 200);
    }



    public function store(Request $request)
    {
        $formData = $request->all();
  //echo"<pre>client";print_r( $formData );exit;

        $clientcount = client::where('deleted_at', '=', NULL);
        $clientcount = client::where('email', $formData["email"])->count();
        if ($clientcount == 0) {
            Client::create([
                "client_name" => $formData["client_name"],
                'first_name' => $formData["first_name"],
                'surname' =>$formData["surname"],
                'last_name' =>$formData["last_name"],
                "email" => $formData["email"],
                "address" => $formData["address"],
                "city" =>  $formData["city"],
                "state" => $formData["state"],
                "postal_code" => $formData["postal_code"],
                'country' => $formData["country"],
                "phone_number" => $formData["phone_number"],
                "fax_number" => $formData["fax_number"],
                "office_unit" => $formData["office_unit"],
                "amount" => $formData["amount"],
                "default_currency" => $formData['default_currency'],
                "tax_code" => $formData["tax_code"],
                "status" => $formData["status"]


            ]);
            return response()->json(['status' => 1, 'message' => "Member stored successfully"], 200);
        } else {
            return response()->json(['status' => 0, 'message' => "Email Already exists"]);
         }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * @SWG\Get(
     *          path="/Client/{id}",
     *          tags={"CLIENTS"},
     *          summary="Clients edit",
     *          operationId="Ceid",
     *          @SWG\Parameter(
     *              name="id",
     *              description="Client Id",
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
        $clientCount = client::where('id', $id)->count();

        if ($clientCount > 0) {
            $client = client::where('id', $id)->select('id', 'client_name', 'first_name', 'surname', 'last_name', 'email', 'address', 'city', 'state', 'postal_code', 'country', 'phone_number', 'fax_number', 'office_unit', 'amount', 'default_currency', 'tax_code', 'status')->first();
            $clientArray1 = array();
            $clientArray1 = [
                'id' => $client->id,
                'client_name' => $client->client_name,
                'first_name' => $client->first_name,
                'surname' =>$client->surname,
                'last_name' =>$client->last_name,
                'email' => $client->email,
                'address' => $client->address,
                'city' => $client->city,
                'state' => $client->state,
                'postal_code' => $client->postal_code,
                'country' => $client->country,
                'phone_number' => $client->phone_number,
                'fax_number' => $client->fax_number,
                'office_unit' => $client->office_unit,
                'amount' => $client->amount,
                "default_currency" => $client->default_currency,
                'tax_code' => $client->tax_code,
                'status' => $client->status

            ];
            return response()->json(['status' => 1, 'message' => "success", "clientDetails" => $clientArray1], 200);
        } else {
            return response()->json(['status' => 0, 'message' => "no record found."], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */





    public function update(Request $request, $id)
    {
       
        $formData = $request->all();


        $clientCount = client::where([['id', '<>', $id]]);

        $clientmethodCounts = $clientCount->where('email', $formData["email"])->count();

        if ($clientmethodCounts == 0) {

            //echo"<pre>client";print_r( $formData );exit;

            $formdata = [
                'client_name' => $request->input('client_name'),
                'first_name' => $request->input('first_name'),
                'surname' =>$request->input('surname'),
                'last_name' =>$request->input('last_name'),
                'email' => $request->input('email'),
                'address' => $request->input('address'),
                'city' => $request->input('city'),
                'state' => $request->input('state'),
                'postal_code' => $request->input('postal_code'),
                'phone_number' => $request->input('phone_number'),
                'office_unit' => $request->input('office_unit'),
                'fax_number' => $request->input('fax_number'),
                'amount' => $request->input('amount'),
                'default_currency' => $request->input('default_currency'),
                'tax_code' => $request->input('tax_code'),
                'status' => $request->input('status')

            ];

            $client = client::where('id', $id)->update($formdata);
            return response::json(['status' => 1, 'message' => "Member updated successfully"], 200);
        } else {
            return response()->json(['status' => 0, 'message' => "Already exists"]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * @SWG\Delete(
     *          path="/Client/{id}",
     *          tags={"CLIENTS"},
     *          summary="Client Delete",
     *          operationId="Cid",
     *          @SWG\Parameter(
     *              name="id",
     *              description="Client Delete",
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
        $date = date('Y-m-d H:i:s');
        $client = client::where('id', $id)->update(['deleted_at' => $date]);

        return response::json(['status' => 1, 'message' => "member deleted Successfully.."], 200);
    }
}

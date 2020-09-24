<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\TaxRate;
use App\Models\CategoryMethod;
use App\Models\productmap;
use App\Models\ProductUnit;
use App\Models\invoice_item;

use Input;
use Response;
class ProductController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     /**
     * @SWG\Get(
     *          path="/Product",
     *          tags={"PRODUCTS - PRODUCTS"},
     *          summary="Product Table",
     *          operationId="Pid",
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
    public function index() {
       $Product = Product::select('id', 'category_id', 'product_name', 'status', 'total_quantity', 'total_quantity_sold')->where('deleted_at', NULL)
          ->orderBy('created_at', 'DESC')->get();

     
        $ProductArray = array();
        foreach ($Product as $key => $value) {




            $ProductArray[] = [
                'map_id' =>$value->id ,
                'category_id' => CategoryMethod::where('id', $value->category_id)->select('name')->first()['name'],
                  'product_name' =>$value->product_name,
                  'quantity'=>$value->total_quantity,
                  'quantity_sold'=>$value->total_quantity_sold,
                'status' => $value->status == 1 ? "Active" : "Inactive",

            ];
        }

        return response::json(['error' => false, 'message' => "success", "ProductDetails" => $ProductArray], 200);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     /**
     *
     * @SWG\Post(
     *     path="/Product",
     *     description="Product Store",
     *     tags={"PRODUCTS - PRODUCTS"},
     *     summary="Product Store",
     *      @SWG\Parameter(
     *          name="category_id",
     *          description="Category Id",
     *          required=false,
     *          type="string",in="formData",
     *      ),
     *     @SWG\Parameter(
     *          name="product_name",
     *          description="Product Name",
     *          required=false,
     *          type="string",in="formData",
     *      ),
     * 
     *          @SWG\Parameter(
     *          name="tax_rate_id",
     *          description="Tax Rate",
     *          required=false,
     *          type="string",in="formData",
     *      ),
     * 
     *    @SWG\Parameter(
     *          name="description",
     *          description="Description",
     *          required=false,
     *          type="string",in="formData",
     *      ),
     * 
     *    @SWG\Parameter(
     *          name="status",
     *          description="Status",
     *          required=false,
     *          type="boolean",in="formData",
     *      ),
     * 
     *    @SWG\Parameter(
     *          name="price",
     *          description="Price",
     *          required=false,
     *          type="string",in="formData",
     *      ),
     * 
     *    @SWG\Parameter(
     *          name="weight",
     *          description="Weight",
     *          required=false,
     *          type="string",in="formData",
     *      ),
     * 
     *    @SWG\Parameter(
     *          name="unit",
     *          description="Unit",
     *          required=false,
     *          type="string",in="formData",
     *      ),
     * 
     *    @SWG\Parameter(
     *          name="image",
     *          description="Image",
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

    public function store(Request $request) {
        $formData = $request->all();

          $displaycount = Product::where ('product_name' , '=' , $formData["product_name"] )->count();   
            //echo "<pre>";print_r($displaycount);exit;   
            if ($displaycount == 0) {
               
            

        
       $Product = Product::create([
          "category_id" => $formData["category_id"],
           "product_name" => $formData["product_name"],
              "description" => $formData["description"],
              "status" => $formData["status"],
              ]);

         $product_id = $Product->id;
         



        return response::json(['status' => 1,'success' => false, 'message' => "Product stored successfully"], 200);
    }else{
            return response::json(['status' => 0, 'message' => "Product name already exists.."], 200);
        }
 
       
  
}
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

     /**
     * @SWG\Get(
     *          path="/Product/{id}",
     *          tags={"PRODUCTS - PRODUCTS"},
     *          summary="Product Edit",
     *          operationId="Psid",
     *          @SWG\Parameter(
     *              name="product_id",
     *              description="Product Id",
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
    public function show($id) {

                $ProductCount = Product::where('id', $id)->count();
        if ($ProductCount > 0) {
             $Product = Product::select('id', 'category_id', 'product_name', 'description', 'status')->first();
            $ProductArray = array();
            //   echo"<pre>image";print_r(  $CategoryMethodUnit );
            $ProductArray = [
                'id' => $Product->id,
                'category_id' => $Product->category_id,
    
                'product_name' => $Product->product_name,

                'description' => $Product->description,
                   'status' => $Product->status,
            ];
            return response()->json(['status' => 1, 'message' => "success", "ProductDetails" => $ProductArray], 200);
        } else {
            return response()->json(['status' => 0, 'message' => "No Record Found."], 401);
        }

                 
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

      /**
     * @SWG\Delete(
     *          path="/Product/{id}",
     *          tags={"PRODUCTS - PRODUCTS"},
     *          summary="Product Delete",
     *          operationId="Pdid",
     *          @SWG\Parameter(
     *              name="product_id",
     *              description="Product Id",
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
     
    public function destroy($id) {
        //echo $id;exit;
        $date = date('Y-m-d H:i:s');
        //$Product = Product::where('id', $id)->update(['deleted_at' => $date]);
        $productmap = Product::where('id', $id)->delete();
        return response::json(['error' => false, 'message' => "Deleted successfully"], 200);
    }

    /**
     *
     * @SWG\Post(
     *     path="/Product/{id}",
     *     description="Product Update",
     *     tags={"PRODUCTS - PRODUCTS"},
     *     summary="Product Update",
     *          @SWG\Parameter(
     *              name="product_id",
     *              description="Product Id",
     *              required=true,
     *              type="string",
     *              in="path"
     *          ),
     *      @SWG\Parameter(
     *          name="category_id",
     *          description="Category Id",
     *          required=false,
     *          type="string",in="formData",
     *      ),
     *     @SWG\Parameter(
     *          name="product_name",
     *          description="Product Name",
     *          required=false,
     *          type="string",in="formData",
     *      ),
     * 
     *          @SWG\Parameter(
     *          name="tax_rate_id",
     *          description="Tax Rate",
     *          required=false,
     *          type="string",in="formData",
     *      ),
     * 
     *    @SWG\Parameter(
     *          name="description",
     *          description="Description",
     *          required=false,
     *          type="string",in="formData",
     *      ),
     * 
     *    @SWG\Parameter(
     *          name="status",
     *          description="Status",
     *          required=false,
     *          type="boolean",in="formData",
     *      ),
     * 
     *    @SWG\Parameter(
     *          name="price",
     *          description="Price",
     *          required=false,
     *          type="string",in="formData",
     *      ),
     * 
     *    @SWG\Parameter(
     *          name="weight",
     *          description="Weight",
     *          required=false,
     *          type="string",in="formData",
     *      ),
     * 
     *    @SWG\Parameter(
     *          name="unit",
     *          description="Unit",
     *          required=false,
     *          type="string",in="formData",
     *      ),
     * 
     *    @SWG\Parameter(
     *          name="image",
     *          description="Image",
     *          required=false,
     *          type="string",in="formData",
     *      ),
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

   
    public function productupdate(Request $request, $id) {
      
        $formdata = $request->all();


           //echo"<pre>image";print_r($formdata );

        $ProductCount = Product::where('product_name', $formdata["product_name"])->count();
        
        if ($ProductCount == 0) {
        $formdata = [
          'category_id' => $formdata['category_id'], 
          'product_name' => $formdata['product_name'],
           'description' => $formdata['description'],
           'status' => $formdata['status'],
          
             ];
        $Product = Product::where('id', $id)->update($formdata);
     
       
      
        
        return response::json(['status' => 1,'error' => false, 'message' => "Product updated successfully"], 200);
    }else{
        return response()->json(['status' => 0, 'message' => "This product Already exists"]);
    }
    }

    /**
     * @SWG\Get(
     *          path="/Autounit",
     *          tags={"PRODUCTS - PRODUCTS"},
     *          summary="Product Unit",
     *          operationId="Puid",
     *        
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

      public function autounit(Request $request)
    {

      // echo "<pre>";print_r($request);exit;
        
        $data = $request->unit;
         //echo "<pre>";print_r($data);exit;
        
        $unit = ProductUnit::where('unit','like','%'.$data.'%')->where('status','=',1)->get();
        $unitArray = array();
        foreach($unit as $key => $value){
            $unitArray[] =  $value->unit;
           // echo "<pre>";print_r($value->unit);exit;
            // $unitArray[] = $value->id;
        }
   
    return response::json (['error' => false, 'message' =>"success",  "units" => $unitArray], 200);
   
    }
    
   
}

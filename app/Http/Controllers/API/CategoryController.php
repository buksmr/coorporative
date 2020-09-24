<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CategoryMethod;
use DB;
use Illuminate\Http\Request;
use Image;
use Response;

class CategoryController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */

	/**
	 * @SWG\Get(
	 *          path="/Category",
	 *          tags={"CATEGORY"},
	 *          summary="Clients Table",
	 *          operationId="cid",
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
	public function index(Request $request) {
		$CategoryMethod = CategoryMethod::select('id', 'name', 'status')->where('deleted_at', NULL)
			->orderBy('created_at', 'ASC')->get();
		$CategoryMethodUnitArray = array();
		foreach ($CategoryMethod as $key => $value) {
			$CategoryMethodUnitArray[] = [
				'id' => $value->id,
				'name' => $value->name,
				'status' => $value->status == 1 ? "Active" : "Inactive",
			];
		}
		return response()->json(['status' => 1, 'message' => "success", "category_unitDetails" => $CategoryMethodUnitArray], 200);
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
	 *     path="/category",
	 *     description="Category Store",
	 *     tags={"CATEGORY"},
	 *     summary="Category Store",
	 *      @SWG\Parameter(
	 *          name="name",
	 *          description="Category name",
	 *          required=true,
	 *          type="string",in="formData",
	 *      ),
	 *      @SWG\Parameter(
	 *          name="description",
	 *          description="Description",
	 *          required=false,
	 *          type="string",in="formData",
	 *      ),
	 *     @SWG\Parameter(
	 *          name="image",
	 *          description="Image",
	 *          required=false,
	 *          type="string",in="formData",
	 *      ),
	 *
	 *  @SWG\Parameter(
	 *          name="position",
	 *          description="Position",
	 *          required=false,
	 *          type="string",in="formData",
	 *      ),
	 *
	 *  @SWG\Parameter(
	 *          name="parentcategory",
	 *          description="parentcategory",
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

		$CategoryMethodCount = CategoryMethod::where('name', $formData["name"])->count();

		if ($CategoryMethodCount == 0) {
			CategoryMethod::create([
				"name" => $request["name"],
		
				"description" => $request["description"],

				"status" => $request["status"],
			]);
			return response()->json(['status' => 1, 'message' => "Category stored successfully.."], 200);
		} else {
			return response()->json(['status' => 0, 'message' => "Category name already exists.."], 200);
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
	 *          path="/Category/{id}",
	 *          tags={"CATEGORY"},
	 *          summary="Category edit",
	 *          operationId="Ceid",
	 *          @SWG\Parameter(
	 *              name="id",
	 *              description="Category Id",
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

		$CategoryMethodUnitCount = CategoryMethod::where('id', $id)->count();
		if ($CategoryMethodUnitCount > 0) {
			$CategoryMethodUnit = CategoryMethod::where('id', $id)->select('id', 'name', 'description','status')->first();
			$CategoryMethodUnitArray1 = array();
			//   echo"<pre>image";print_r(  $CategoryMethodUnit );
			$CategoryMethodUnitArray1 = [
				'id' => $CategoryMethodUnit->id,
				'name' => $CategoryMethodUnit->name,
	
				'description' => $CategoryMethodUnit->description,

				'status' => $CategoryMethodUnit->status,
			];
			return response()->json(['status' => 1, 'message' => "success", "category_unitDetails" => $CategoryMethodUnitArray1], 200);
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
	 *
	 * @SWG\Post(
	 *     path="/Category/{id}",
	 *     description="Category Update",
	 *     tags={"CATEGORY"},
	 *     summary="Category Update",
	 *          @SWG\Parameter(
	 *              name="id",
	 *              description="Category Id",
	 *              required=true,
	 *              type="string",
	 *              in="path"
	 *          ),
	 *      @SWG\Parameter(
	 *          name="name",
	 *          description="Category name",
	 *          required=true,
	 *          type="string",in="formData",
	 *      ),
	 *      @SWG\Parameter(
	 *          name="description",
	 *          description="Description",
	 *          required=false,
	 *          type="string",in="formData",
	 *      ),
	 *     @SWG\Parameter(
	 *          name="image",
	 *          description="Image",
	 *          required=false,
	 *          type="string",in="formData",
	 *      ),
	 *
	 *  @SWG\Parameter(
	 *          name="position",
	 *          description="Position",
	 *          required=false,
	 *          type="string",in="formData",
	 *      ),
	 *
	 *  @SWG\Parameter(
	 *          name="parentcategory",
	 *          description="Parentcategory",
	 *          required=false,
	 *          type="string",in="formData",
	 *      ),
	 *
	 *  @SWG\Parameter(
	 *          name="status",
	 *          description="Status",
	 *          required=false,
	 *          type="boolean",in="formData",
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
	public function categoryupdate(Request $request, $id) {

		$formdata = $request->all();


        $CategoryMethodCount = CategoryMethod::where('name', $formdata["name"])->count();
		if ($CategoryMethodCount == 0) {
			$formdata = [

				'name' => $formdata['name'],

				'description' => $formdata['description'],
	
				'status' => $formdata['status'],

			];
			$CategoryMethod = CategoryMethod::where('id', $id)->update($formdata);

			return response::json(['status' => 1, 'message' => "Category Updated Successfully.."], 200);
		} else {
			return response::json(['status' => 0, 'message' => "Category name already exists.."], 200);
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
	 *          path="/Category/{id}",
	 *          tags={"CATEGORY"},
	 *          summary="Category Delete",
	 *          operationId="Cid",
	 *          @SWG\Parameter(
	 *              name="id",
	 *              description="Category Delete",
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
		$CategoryMethod = CategoryMethod::where('id', $id)->update(['deleted_at' => date('y-m-d')]);
		return response::json(['status' => 1, 'message' => "Category deleted Successfully.."], 200);
	}

	/**
	 * @SWG\Get(
	 *          path="/listCategory",
	 *          tags={"CATEGORY"},
	 *          summary="Category List",
	 *          operationId="Clid",
	 *          @SWG\Parameter(
	 *              name="id",
	 *              description="Category Id",
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
	public function getCategories() {
		$categories = DB::table('category_methods')->where('status', '=', 1)->get();
		// $categories = CategoryMethod::where('status', '=', 1)->get();
		// $get_category_list = getCategory($categories);
		return response()->json(['category' => $categories], 200);
	}
}

<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Response;
use Hash;              // class for hash code

class userAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {    
        //$formData=$request->all();

        $user = user::select('id as userid','name', 'lastname', 'email', 'user_privilege', 'status')->get();   //id as userid. the userid cannot be taken in vuejs it wrks only in laravel

        $userarr = array();
        foreach($user as $value){
          $userarr[] = [
            "id" => $value->userid,
             "name" => $value->name,
            "lastname" => $value->lastname,
            "email" => $value->email,
            "user_privilege" => $value->user_privilege,
           "status" => $value->status == 0 ? "Active" : "Inactive",    //it checks whether the status in active or inactive and gets displayed
           
          ];

    }
    return response::json(['error' => false, 'message' =>"success", "user" => $userarr], 200);   //array of the user gets passed
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $formData=$request->all(); 
        
       //echo "<pre>";print_R($formData);exit;                 
           $email = $formData ['email'];                          //just taking email from the form data
           $userCount = User::where ('email', $email )->count();    //email count cheking
           if ($userCount == 0){           
            User::create([
                "name" => $formData['name'],
                "lastname" => $formData['lastname'],
                "email" => $formData['email'],
                "status" => 1,
                "password" =>  Hash::make($formData['password']),
                 "user_privilege" => $formData['user_privilege'],
               
             ]);
             return response()->json(['status' => 1, 'message' => " User details Stored Successfully "], 200); 
           }
           return response()->json(['status' =>0 , 'message' => " The record already exists "]); 

            
 }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {    
       // $formData=$request->all();
       // echo "<pre>";print_R($id);exit;
        $userCount= user::
        select('id ','name','email','status')
        -> where('id',$id)
        ->count();
        
        if($userCount >0){
            $user = user::
            select('id as userid','name','email','status')
            -> where('id',$id)
            ->get();

            $userArr = array();
            foreach($user as $value){                           
                $userArr[] = [
      
                     "id" =>$value->userid ,
                     "name" => $value->name ,
                     "email" => $value->email,
                     "status" => $value->status

        ];
        return response()->json(['error' =>'false' , 'message' => "success" ,"user" => $userArr ] ,200);
        
    }
}else{
    return response()->json(['error' =>'false' , 'message' => "No data"],200);
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
        
        
         $formData=$request->all();

     //echo "<pre>";print_R($formData);exit;
      
        $formdata = [
              
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'lastname' => $request->input('lastname')
        ];
        $user = user::where('id', $id)->update($formdata);
        return response::json(['error' => false, 'message' =>"User Accounts updated Successfully"], 200);
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       
       $user = user::where('id', $id)->update(['status' => 1]);                 
       return response::json(['error' => false, 'status'=>1, 'message' =>"record deleted"], 200); 
    }
}

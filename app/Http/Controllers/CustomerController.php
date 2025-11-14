<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Customer;
use Illuminate\Validation\Rule;

class CustomerController extends Controller {
    
    /**
     * Display a customer list of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(){
        $pageTitle = 'Customer List';
        $pageDescription = 'Some description for the page';
        $customers = Customer::orderBy('id','DESC')->get();
        return view('customer.customers',compact('pageTitle', 'pageDescription','customers'));
    }

    /**
     * Display a add new customer of the resource.
     *
     * @return \Illuminate\View\View
     */

     public function create(){
        $pageTitle = 'Create new Competitor';
        $pageDescription = 'Some description for the page';
        return view('customer.new_customer',compact('pageTitle', 'pageDescription'));
     }

     /**
     * Store a newly created customer resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     public function store(Request $request){
         $validators = Validator::make($request->all(),[
             'name'=>'required',
             'email'=>'required|email|unique:customers',
             'phone'=>'required|numeric|unique:customers',
             'gender'=>'required',
             'profession'=>'required'
         ]);

         if($validators->fails()){
             return redirect()->route('customer.create')->withErrors($validators)->withInput();
         }else{
            $customer = new Customer();
            $customer->name = $request->name;
            $customer->email = $request->email;
            $customer->phone = $request->phone;
            $customer->gender = $request->gender;
            $customer->profession = $request->profession;
            $customer->address = $request->address;
            $customer->save();
            return redirect()->route('customer.list')->with('create','Customer created successfully !');
         }         
     }

     /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
     public function edit($id){
        $pageTitle = 'Edit Customer';
        $pageDescription = 'Some description for the page';
        $find_customer = Customer::where('id',$id)->get();
        return view('customer.edit_customer',compact('pageTitle', 'pageDescription','find_customer'));
     }

     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

     public function update(Request $request,$id){
        $validators = Validator::make($request->all(),[
            'name'=>'required',
            'email'=>['required','email',Rule::unique('customers')->ignore($id)],
            'phone'=>['required','numeric',Rule::unique('customers')->ignore($id)],
            'gender'=>'required',
            'profession'=>'required'
        ]);

        if($validators->fails()){
            return redirect()->route('customer.edit',$id)->withErrors($validators)->withInput();
        }else{
           $customer = Customer::findOrFail($id);
           $customer->name = $request->name;
           $customer->email = $request->email;
           $customer->phone = $request->phone;
           $customer->gender = $request->gender;
           $customer->profession = $request->profession;
           $customer->address = $request->address;
           $customer->save();
           return redirect()->route('customer.list')->with('update','Customer updated successfully !');
        }   
     }

     /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

     public function delete($id){
         $find_customer = Customer::findOrFail($id);
         $find_customer->delete();
         return redirect()->route('customer.list')->with('delete','Customer deleted successfully !');
     }
}
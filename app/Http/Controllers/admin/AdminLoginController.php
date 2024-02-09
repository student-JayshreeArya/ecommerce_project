<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\facades\Validator;

class AdminLoginController extends Controller
{
    public function index(){
        return view('admin.login');
    }
    public function authenticate(Request $request){
        //form  validation so that if user donot fill email or pass then an error will show
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validator->passes()){
            if(Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password],
            $request->get('remember'))){
                //if the user is customer then it will not send to the dasboard instead it will gwt the error of unauthorization
                $admin = Auth::guard('admin')->user();
                //this $admin willl get the info about user login
                if($admin->role ==2){
                    return redirect()->route('admin.dashboard');
                    //if email-passs is correct then it will login to dashboard
                }
                else{
                    Auth::guard('admin')->logout();  //user customer will logout
                    return redirect()->route('admin.login')->with('error','You are not authorized to access admin panel');
                }
            }
            else{
                return redirect()->route('admin.login')->with('error', 'Email/Password is incorrect');
                //otherwise sent back to the login page
            }
        }
        else{
            return redirect()->route('admin.login')
            //it will redirect the form with errors if occured
                    ->withErrors($validator)
                    ->withInput($request->only('email'));
        }
    }
}

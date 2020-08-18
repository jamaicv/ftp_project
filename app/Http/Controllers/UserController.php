<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function updatePassword(Request $request) {
        $user = Auth::user();
        if ($request->isMethod('post')) {
            $old_pwd = $request->input('old_password');
            $new_pwd = $request->input('new_password');
            $new_pwd_confirm = $request->input('new_password_confirm');

            if (!$user->password == Hash::make($old_pwd)) {
                $request->session()->flash('danger', 'Ancien mot de passe incorrect');
                return redirect()->back();
            }

            if ($new_pwd != $new_pwd_confirm) {
                $request->session()->flash('danger', 'Les deux mots de passe ne sont pas identiques');
                return redirect()->back();
            }

            $user->password = Hash::make($new_pwd);
            $user->save();
        }
        
        return view('user.update_password');
    }

    public function loadFile(Request $request) {
        return view('user.load_file');
    }

    public function downloadFiles() {
        $user = Auth::user();
        if ($user->isAdmin() || $user->isTeacher()) {
            
        } else {
            $request->session()->flash('status', 'Vous n\'avez pas accès à ce menu');
        }
    }
}
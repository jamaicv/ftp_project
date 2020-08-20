<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Sftp\SftpAdapter;
use League\Flysystem\Filesystem;
use App\Homework;

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
        $user = Auth::user();
        if ($request->isMethod('post')) { 
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $nom_devoir = 'test';
            $ext = explode('.', $filename)[1];

            $new_filename = strtolower($user->first_name . '_' .$user->last_name . '_' . $nom_devoir . '_' . date('U'));

            ## TODO - VERIFICATIONS
            $hw = new Homework();
            $hw->filename = $new_filename;
            $hw->location = 'public/homeworks';
            $hw->student_id = $user->id;
            $hw->save();


            $adapter = new SftpAdapter([
                'host' => '172.27.1.36',
                'port' => 22,
                'username' => 'serviej',
                'password' => 'BelGoss77',
                'root' => '/',
                'timeout' => 10,
                'directoryPerm' => 0755
            ]);

            $filesystem = new Filesystem($adapter);
            dd($filesystem->put($filename, $file));
            dd('ok');

            $path = $file->storeAs('public/homeworks', $new_filename);
            $request->session()->flash('success', 'Le fichier a été chargé avec succès');
            return redirect()->back();
        }

        return view('user.load_homework');
    }

    public function getHomeworks() {
        $user = Auth::user();
        $homeworks = array();

        if ($user->isAdmin() || $user->isTeacher()) {
            $homeworks = Homework::all();
        } else {
            $homeworks = Homework::where('student_id', $user->id)->get();
        }

        return view('user.homeworks', [
            'homeworks' => $homeworks
        ]);
    }

    public function downloadFile() {
        $user = Auth::user();
        if ($user->isAdmin() || $user->isTeacher()) {
            
        } else {
            $request->session()->flash('status', 'Vous n\'avez pas accès à ce menu');
        }
    }
}
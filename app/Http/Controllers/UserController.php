<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Sftp\SftpAdapter;
use League\Flysystem\Filesystem;
use App\Homework;
use App\User;

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

    public function updatePassword(Request $request, $id = null) {
        $user = Auth::user();
        $own_pwd = true;
        if (!$user->isAdmin() && $id != $user->id) {
            $request->session()->flash('danger', 'Vous n \'êtes pas autorisé à réaliser cette action');
            return redirect()->back();
        }

        if ($user->isAdmin() && $id != null && $id != $user->id) {
            $user =  User::find($id);
            $own_pwd = false;
        }

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

            $request->session()->flash('success', 'Le mot de passe a été mis à jour avec succès');
            return redirect()->route('home');
        }
        
        return view('user.update_password', [
            'own_pwd' => $own_pwd,
            'user_n' => $user->first_name . ' ' . $user->last_name
        ]);
    }

    public function loadFile(Request $request) {
        $user = Auth::user();
        if ($request->isMethod('post')) { 
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $nom_devoir = 'test';
            $ext = explode('.', $filename)[1];

            $new_filename = strtolower($user->first_name . '_' .$user->last_name . '_' . $nom_devoir . '_' . date('U'));

            $adapter = new SftpAdapter([
                'host' => '172.27.1.36',
                'port' => 22,
                'username' => 'serviej',
                'password' => 'BelGoss77',
                'timeout' => 10,
                'directoryPerm' => 0755
            ]);
            $resource = fopen($file->path(), 'r');
            $filesystem = new Filesystem($adapter);
            $filesystem->put($filename, $resource);

            ## TODO - VERIFICATIONS
            $path = $file->store('public/homeworks');

            $hw = new Homework();
            $hw->filename = $filename;
            $hw->location = $filename;
            $hw->correction_location = '';
            $hw->student_id = $user->id;
            $hw->save();

            $request->session()->flash('success', 'Le fichier a été chargé avec succès');
            return redirect()->back();
        }

        return view('user.load_homework');
    }

    public function deleteFile(Request $request, $id) {
        $user = Auth::user();
        $file = Homework::find($id);

        if (!$user->isAdmin() && $user->id != $file->student_id) {
            $request->session()->flash('danger', 'Vous n \'êtes pas autorisé à réaliser cette action');
            return redirect()->back();
        }

        $adapter = new SftpAdapter([
            'host' => '172.27.1.36',
            'port' => 22,
            'username' => 'serviej',
            'password' => 'BelGoss77',
            'timeout' => 10,
            'directoryPerm' => 0755
        ]);
        $filesystem = new Filesystem($adapter);

        // Si le fichier existe, on le supprimer
        if ($filesystem->has($file->location)) {
            $filesystem->delete($file->location);
        }
        $file->delete();

        $request->session()->flash('success', 'Le fichier a bien été supprimé');
        return redirect()->back();
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

    public function downloadFile(Request $request, $id) {
        $user = Auth::user();
        $file = Homework::find($id);

        if ($user->isAdmin() || $user->isTeacher() || $file->student_id == $user->id) {
            $adapter = new SftpAdapter([
                'host' => '172.27.1.36',
                'port' => 22,
                'username' => 'serviej',
                'password' => 'BelGoss77',
                'timeout' => 10,
                'directoryPerm' => 0755
            ]);
            $filesystem = new Filesystem($adapter);

            // Vérification de l'existence du fichier
            if (!$filesystem->has($file->location)) {
                $request->session()->flash('danger', 'Le fichier n\'existe pas sur le serveur');
                return redirect()->back();
            }

            // Lecture du fichier et téléchargement
            $stream = $filesystem->readStream($file->location);
            return response()->stream(function() use($stream) {
                fpassthru($stream);
            }, 200, [
                "Content-Type" => $filesystem->getMimetype($file->location),
                "Content-Length" => $filesystem->getSize($file->location),
                "Content-disposition" => "attachment; filename=\"" . basename($file->location) . "\"",
            ]);
        } else {
            $request->session()->flash('danger', 'Vous n\'avez pas accès à ce menu');
            return redirect()->back();
        }
    }
}
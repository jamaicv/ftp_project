<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        if (!$user->isAdmin() && $id != $user->id && $id != null) {
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

            if (!$user->isAdmin() && $own_pwd) {
                if (!$user->password == Hash::make($old_pwd)) {
                    $request->session()->flash('danger', 'Ancien mot de passe incorrect');
                    return redirect()->back();
                }
            }

            if ($new_pwd != $new_pwd_confirm) {
                $request->session()->flash('danger', 'Les deux mots de passe ne sont pas identiques');
                return redirect()->back();
            }

            $user->password = Hash::make($new_pwd);
            
            if (system('sudo usermod -p $(perl -e \'print crypt($ARGV[0], "password")\' \'' . $new_pwd . '\') ' . $user->name) === false) {
                $request->session()->flash('danger', 'Une erreur est survenue lors de la modification du mot de passe');
                return redirect()->back();
            }

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
            $login = $request->input('login');
            $password = $request->input('password');

            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $new_filename = $file->hashName();
            $ext = $file->extension();
   
            $adapter = new SftpAdapter([
                'host' => env('SFTP_HOST', 'localhost'),
                'port' => 22,
                'username' => $login,
                'password' => $password,
                'timeout' => 10,
                'directoryPerm' => 0755
            ]);
            $resource = fopen($file->path(), 'r');

            try {
                $filesystem = new Filesystem($adapter);
                $filesystem->put($filename, $resource);
            } catch (\Exception $e) {
                $request->session()->flash('danger', 'Connexion au serveur impossible. Vérifiez vos identifiants ou contactez l\'administrateur');
                return redirect()->back();
            }

            ## TODO - VERIFICATIONS
            $hw = new Homework();
            $hw->filename = $filename;
            $hw->hashed_name = $new_filename;
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

        // TODO

        if ($request->isMethod('post')) {
            if ($user->isAdmin() || $user->isTeacher() || $file->student_id == $user->id) {
                $login = $user->isAdmin() || $user->isTeacher() ? env('SFTP_USER', 'admin') : $request->input('login');
                $password = $user->isAdmin() || $user->isTeacher() ? env('SFTP_PASSWD', 'admin') : $request->input('password');
                $file_location = $user->isAdmin() || $user->isTeacher() ? '/home/' . $file->student()->get()[0]->name . '/' . $file->location : $file->location;
                if (!$user->isAdmin() && $user->id != $file->student_id) {
                    $request->session()->flash('danger', 'Vous n \'êtes pas autorisé à réaliser cette action');
                    return redirect()->back();
                }
                
                $adapter = new SftpAdapter([
                    'host' => env('SFTP_HOST', 'localhost'),
                    'port' => 22,
                    'username' => $login,
                    'password' => $password,
                    'timeout' => 10,
                    'directoryPerm' => 0755
                ]);
                $filesystem = new Filesystem($adapter);

                // Si le fichier existe, on le supprimer
                try {
                    if ($filesystem->has($file_location)) {
                        $filesystem->delete($file_location);
                    }
                } catch (\Exception $e) {
                    $request->session()->flash('danger', 'Connexion au serveur impossible. Vérifiez vos identifiants ou contactez l\'administrateur');
                    return redirect()->back();
                }
                $file->delete();

                $request->session()->flash('success', 'Le fichier a bien été supprimé');
                return redirect()->back();
            } else {
                $request->session()->flash('danger', 'Erreur');
                    return redirect()->back();
            }
        }
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

        if ($request->isMethod('post')) {
            if ($user->isAdmin() || $user->isTeacher() || $file->student_id == $user->id) {
                $login = $user->isAdmin() || $user->isTeacher() ? env('SFTP_USER', 'admin') : $request->input('login');
                $password = $user->isAdmin() || $user->isTeacher() ? env('SFTP_PASSWD', 'admin') : $request->input('password');
                $file_location = $user->isAdmin() || $user->isTeacher() ? '/home/' . $file->student()->get()[0]->name . '/' . $file->location : $file->location;

                $adapter = new SftpAdapter([
                    'host' => env('SFTP_HOST', 'localhost'),
                    'port' => 22,
                    'username' => $login,
                    'password' => $password,
                    'timeout' => 10,
                    'root' => '/',
                    'directoryPerm' => 0755
                ]);
                $filesystem = new Filesystem($adapter);

                // Vérification de l'existence du fichier
                try {
                    if (!$filesystem->has($file_location)) {
                        $request->session()->flash('danger', 'Le fichier n\'existe pas sur le serveur');
                        return redirect()->back();
                    }
                } catch (\Exception $e) {
                    var_dump($e);die;
                    $request->session()->flash('danger', 'Connexion au serveur impossible. Vérifiez vos identifiants ou contactez l\'administrateur');
                    return redirect()->back();
                }

                // Lecture du fichier et téléchargement
                $stream = $filesystem->readStream($file_location);
                return response()->stream(function() use($stream) {
                    fpassthru($stream);
                }, 200, [
                    "Content-Type" => $filesystem->getMimetype($file_location),
                    "Content-Length" => $filesystem->getSize($file_location),
                    "Content-disposition" => "attachment; filename=\"" . basename($file->filename) . "\"",
                ]);
            } else {
                $request->session()->flash('danger', 'Vous n\'avez pas accès à ce menu');
                return redirect()->back();
            }
        }
    }
}
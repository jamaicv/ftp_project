<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\User;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
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

    public function create(Request $request) { 
        $user = Auth::user();
        if ($user->isAdmin()) {
            if ($request->isMethod('post')) {
                $first_name = $request->input('first_name');
                $last_name = $request->input('last_name');
                $email = $request->input('email');
                $birth_date = $request->input('birth_date');
                $isAdmin = $request->input('is_admin');
                $isTeacher = $request->input('is_teacher');
                $date_password = str_replace('/', '', $birth_date);
                $date_password = str_replace('-', '', $date_password);
                //$date_naissance = \DateTime::createFromFormat('d/m/Y', $birth_date);
                $date_naissance = $birth_date;
                $errors = 0;

                if (empty($last_name)) {
                    $request->session()->flash('danger', 'Le nom est manquant pour la ligne ' . $i);
                    $errors++;
                }
                if (empty($first_name)) {
                    $request->session()->flash('danger', 'Le prénom est manquant pour la ligne ' . $i);
                    $errors++;
                }
                if (empty($birth_date)) {
                    $request->session()->flash('danger', 'La date de naissance est manquante pour la ligne ' . $i);
                    $errors++;
                }
                if (empty($email)) {
                    $request->session()->flash('danger', 'L\'email est manquant pour la ligne ' . $i);
                    $errors++;
                }

                if ($errors > 0) {
                    return redirect()->back();
                }

                if ($this->userExists($email)) {
                    $request->session()->flash('danger', 'Un compte existe déjà pour cet e-mail');
                    return redirect()->back();
                } else {
                    $user = new User();
                    $user->name = strtolower($first_name) . '.' . strtolower($last_name);
                    $user->first_name = $first_name;
                    $user->last_name = $last_name;
                    $user->password = Hash::make($date_password);
                    $user->birth_date = $date_naissance;
                    $user->email = $email;
                    $user->is_admin = $isAdmin != null ? true : false;
                    $user->is_teacher = $isTeacher != null ? true : false;

                    $res = system('sudo useradd -m -p $(perl -e \'print crypt($ARGV[0], "password")\' \'' . $date_password . '\') ' . $user->name);
                    if ($res === false || $res != '') {
                        $request->session()->flash('danger', 'Une erreur est survenue lors de la création du compte FTP.');
                        return redirect()->back();
                    }

                    $user->save();                
                    $request->session()->flash('success', 'Le compte a été créé avec succès.');
                }
            }

            return view('admin.create');
        } else {
            $request->session()->flash('danger', 'Vous n \'avez pas accès à ce menu.');
            return redirect()->back();
        }
    }

    public function massCreate(Request $request) {
        $user = Auth::user();
        if ($user->isAdmin()) {
            if ($request->isMethod('post')) {
                $file = $request->file('file');
                $ext = explode('.', $file->getClientOriginalName())[1];

                if ($ext != 'csv') {
                    $request->session()->flash('danger', 'Le fichier doit être au format csv');
                    return redirect()->back();
                } else {
                    $header = null;
                    $errors = 0;
                    $data = array();
                    $i = 0;

                    if (($handle = fopen($file, 'r')) !== false) {
                        while (($row = fgetcsv($handle, 1000, ';')) !== false) {
                            if (!$header) {
                                $header = $row;
                            } else {
                                $nom = $row[0];
                                $prenom = $row[1];
                                $naissance = $row[2];
                                $email = $row[3];
                                $isTeacher = $row[4];
                                $isAdmin = $row[5];

                                if (empty($nom)) {
                                    $request->session()->flash('danger', 'Le nom est manquant pour la ligne ' . $i);
                                    $errors++;
                                }
                                if (empty($prenom)) {
                                    $request->session()->flash('danger', 'Le prénom est manquant pour la ligne ' . $i);
                                    $errors++;
                                }
                                if (empty($naissance)) {
                                    $request->session()->flash('danger', 'La date de naissance est manquante pour la ligne ' . $i);
                                    $errors++;
                                }
                                if (empty($email)) {
                                    $request->session()->flash('danger', 'L\'email est manquant pour la ligne ' . $i);
                                    $errors++;
                                }
                                if ($this->userExists($email)) {
                                    $request->session()->flash('danger', 'Un compte existe déjà pour cet e-mail. Ligne ' . $i);
                                    $errors++;
                                }

                                if ($errors > 0) {
                                    return redirect()->back();
                                }

                                $date_naissance = \DateTime::createFromFormat('d/m/Y', $naissance);
                                $date_password = str_replace('/', '', $naissance);

                                $user = new User();
                                $user->name = strtolower($prenom) . '.' . strtolower($nom);
                                $user->first_name = $prenom;
                                $user->last_name = $nom;
                                $user->password = Hash::make($date_password);
                                $user->birth_date = $date_naissance;
                                $user->email = $email;
                                $user->is_admin = $isAdmin == '' ? false : $isAdmin;
                                $user->is_teacher = $isTeacher == '' ? false : $isTeacher;

                                $res = system('sudo useradd -m -p $(perl -e \'print crypt($ARGV[0], "password")\' \'' . $date_password . '\') ' . $user->name);
                                dd($res);
                                if ($res === false || $res != '') {
                                    $request->session()->flash('danger', 'Une erreur est survenue lors de la création du compte FTP.');
                                    return redirect()->back();
                                }

                                $user->save();
                                $request->session()->flash('success', 'Le compte ' . $i . ' a été créé avec succès.');
                                $i++;
                            }
                        }
                        fclose($handle);
                        if ($i > 1) {
                            $n = $i + 1;
                            $request->session()->flash('success', 'Les ' . $n . ' comptes ont été créés avec succès.');
                        }
                    }
                }
            }
            return view('admin.mass_create');
        } else {
            $request->session()->flash('danger', 'Vous n \'avez pas accès à ce menu.');
            return redirect()->back();
        }
    }

    public function getUsers() {
        return view('admin.users', [
            'users' => User::all()
        ]);
    }

    public function deleteUser(Request $request, $id) {
        if ($request->isMethod('post')) {
             
        }
        $user = User::find($id);
        $user->delete();
    }

    public function userExists($email) {
        $user = User::firstWhere('email', $email);
        if ($user) {
            return true;
        }
        return false;
    }
}

@extends('layouts.app')

@section('content')
<style>
.menu-container {
    display: flex;
    flex-direction: column;
}

.menu-container a {
    width: 60%;
    margin: 0 auto;
    margin-bottom: 3px;
}

.menu-container h2 {
    margin-top: 15px;
    margin-bottom: 15px;
}

.menu-container .firstH2 {
    margin-top: 0px;
}
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="menu-container col-md-12">
            @if (Auth::user()->isAdmin())
            <h2 class="text-center firstH2">Administration</h2>
            <a class="btn btn-success" href="{{ route('create_user') }}">Créer un compte</a>
            <a class="btn btn-success" href="{{ route('mass_create') }}">Créer des comptes en masse</a>
            @endif
            @if (Auth::user()->isTeacher()||Auth::user()->isAdmin())
            <h2 class="text-center">Gestion</h2>
            <a class="btn btn-success" href="{{ route('users') }}">Voir la liste des utilisateurs</a>
            @endif
            <h2 class="text-center">Menu</h2>
            @if (!Auth::user()->isTeacher() && !Auth::user()->isAdmin())
            <a class="btn btn-success" href="{{ route('load_homework') }}">Déposer un devoir</a>
            @endif
            <a class="btn btn-success" href="{{ route('get_homeworks') }}">Voir l'historique des devoirs déposés</a>
            <a class="btn btn-success" href="{{ route('update_password') }}">Modifier mon mot de passe</a>
        </div>
    </div>
</div>
@endsection

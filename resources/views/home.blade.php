@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if (Auth::user()->isAdmin())
            <a class="btn btn-success" href="{{ route('create_user') }}">Créer un compte</a>
            <a class="btn btn-success" href="{{ route('mass_create') }}">Créer des comptes en masse</a>
            <a class="btn btn-success" href="{{ route('mass_create') }}">Télécharger l'archive à jour</a>
            @elseif (Auth::user()->isTeacher()||Auth::user()->isAdmin())
            <a class="btn btn-success" href="{{ route('mass_create') }}">Télécharger l'archive à jour</a>
            @else
            <a class="btn btn-success" href="{{ route('load_homework') }}">Déposer un devoir</a>
            <a class="btn btn-success" href="{{ route('update_password') }}">Modifier mot de passe</a>
            @endif
            <a class="btn btn-success" href="{{ route('get_homeworks') }}">Voir l'historique des devoirs déposés</a>
        </div>
    </div>
</div>
@endsection

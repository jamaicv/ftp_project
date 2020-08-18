@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if (Auth::user()->isAdmin())
            <a class="btn btn-success" href="{{ route('create_user') }}">Créer un compte</a>
            <a class="btn btn-success" href="{{ route('mass_create') }}">Créer des comptes en masse</a>
            <a class="btn btn-success" href="{{ route('mass_create') }}">Liste des utilisateurs</a>
            @elseif (Auth::user()->isTeacher())
            <a class="btn btn-success" href="{{ route('mass_create') }}">Télécharger l'archive à jour</a>
            @else
            <a class="btn btn-success">Déposer un devoir</a>
            <a class="btn btn-success">Voir l'historique des devoirs déposés</a>
            <a class="btn btn-success">Modifier mot de passe</a>
            @endif
        </div>
    </div>
</div>
@endsection
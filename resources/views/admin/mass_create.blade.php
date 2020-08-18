@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Créer des comptes') }}</div>

                <div class="card-body">
                    @foreach (['danger', 'warning', 'success', 'info'] as $key)
                        @if(Session::has($key))
                            <p class="alert alert-{{ $key }}">{{ Session::get($key) }}</p>
                        @endif
                    @endforeach
                    <form method="post" action="{{ route('mass_create') }}" enctype="multipart/form-data">
                    @csrf
                        <div class="form-group">
                            <label for="file">Fichier à charger :</label><br>
                            <input name="file" type="file" id="file" aria-describedby="fileHelp">
                            <small id="fileHelp" class="form-text text-muted">Le fichier CSV doit contenir les colonnes suivantes: NOM, PRENOM, NAISSANCE, EMAIL, PROFESSEUR et ADMINISTRATEUR</small>
                        </div>
                
                        <button type="submit" class="btn btn-primary">Valider</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Créer un compte') }}</div>

                <div class="card-body">
                    <form method="post" action="{{ route('create_user') }}">
                    @csrf
                        <div class="form-group">
                            <label for="first_name">Prénom</label>
                            <input name="first_name" type="text" class="form-control" id="first_name">
                        </div>
                        <div class="form-group">
                            <label for="last_name">Nom</label>
                            <input name="last_name" type="text" class="form-control" id="last_name">
                        </div>
                        <div class="form-group">
                            <label for="birth_date">Date de naissace</label>
                            <input name="birth_date" type="date" class="form-control" id="birth_date">
                        </div>
                        <div class="form-group">
                            <label for="email">Adresse mail</label>
                            <input name="email" type="email" class="form-control" id="email">
                        </div>
                        <div class="form-group form-check">
                            <input name="is_admin" type="checkbox" class="form-check-input" id="is_admin">
                            <label class="form-check-label" for="is_admin">Administrateur</label>
                        </div>
                        <div class="form-group form-check">
                            <input name="is_teacher" type="checkbox" class="form-check-input" id="is_teacher">
                            <label class="form-check-label" for="is_teacher">Professeur</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Valider</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

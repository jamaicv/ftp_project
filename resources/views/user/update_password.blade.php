@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Créer un compte') }}</div>

                <div class="card-body">
                    @foreach (['danger', 'warning', 'success', 'info'] as $key)
                        @if(Session::has($key))
                            <p class="alert alert-{{ $key }}">{{ Session::get($key) }}</p>
                        @endif
                    @endforeach
                    <form method="post" action="{{ route('update_password') }}">
                    @csrf
                        <div class="form-group">
                            <label for="old_password">Ancien mot de passe</label>
                            <input name="old_password" type="password" class="form-control" id="old_password">
                        </div>
                        <div class="form-group">
                            <label for="new_password">Nouveau mot de passe</label>
                            <input name="new_password" type="password" class="form-control" id="new_password">
                        </div>
                        <div class="form-group">
                            <label for="new_password_confirm">Confirmation du nouveau mot de passe</label>
                            <input name="new_password_confirm" type="password" class="form-control" id="new_password_confirm">
                        </div>

                        <button type="submit" class="btn btn-primary">Valider</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

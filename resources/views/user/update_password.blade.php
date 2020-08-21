@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    @if ($own_pwd)
                    {{ __('Modification du mot de passe') }}
                    @else
                    {{ __('Modification du mot de passe pour ' . $user_n) }}
                    @endif
                </div>

                <div class="card-body">
                    <form method="post" action="{{ route('update_password') }}">
                    @csrf
                        @if (!Auth::user()->isAdmin() && $own_pwd)
                        <div class="form-group">
                            <label for="old_password">Ancien mot de passe</label>
                            <input name="old_password" type="password" class="form-control" id="old_password">
                        </div>
                        @endif
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

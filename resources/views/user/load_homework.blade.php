@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Charger un devoir') }}</div>

                <div class="card-body">
                    <form id="upForm" method="post" action="{{ route('load_homework') }}" enctype="multipart/form-data">
                    @csrf
                        <div class="form-group">
                            <label for="file">Fichier à charger :</label><br>
                            <input name="file" type="file" id="file" aria-describedby="fileHelp">
                            <small id="fileHelp" class="form-text text-muted">Le fichier doit être au format </small>
                        </div>
                        <input id="loginF" name="login" id type="hidden"/>
                        <input id="passwordF" name="password" type="hidden"/>
                        <input id="submitBtn" type="button" data-toggle="modal" data-target="#confirm-submit" class="btn btn-primary" value="Valider"/>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SFTP Credentials modal -->
<div class="modal fade" id="confirm-submit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Connexion SFTP
            </div>
            <div class="modal-body">
                <table>
                    <tr>
                        <td><label for="login">Identifiant</label></td>
                        <td><input type="text" name="login" id="login"/></td>
                    </tr>
                    <tr>
                        <td><label for="password">Mot de passe</label></td>
                        <td><input type="password" name="password" id="password"/></td>
                    </tr>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                <a href="#" id="submit" class="btn btn-success success">Valider</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#submitBtn').on('click', function() {
            //$('#lname').text($('#lastname').val());
            //$('#fname').text($('#firstname').val());
        });
        $('#submit').click(function() {
            $('#loginF').val($('#login').val());
            $('#passwordF').val($('#password').val())
            $('#upForm').submit();
        });
    });
</script>
@endpush
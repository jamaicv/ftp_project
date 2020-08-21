@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Devoirs déposés') }}</div>

                <div class="card-body">
                    <table id="mainTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Date</th>
                                <th>Correction</th>
                                <th>Date correction</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($homeworks as $h)
                            <tr>
                                <td>{{ $h->filename }}</td>
                                <td>{{ $h->created_at }}</td>
                                <td>
                                    @if ($h->corrected == 0)
                                    <i style="color: red" class="fa fa-times"></i>
                                    @else
                                    <i style="color: green" class="fa fa-check"></i>
                                    @endif
                                </td>
                                <td>{{ $h->corrected == 0 ? '/' : $h->updated_at }}</td>
                                <td>
                                    <a title="Télécharger le devoir" class="btn btn-success" href="{{ route('download_file', ['id' => $h->id]) }}"><i class="fa fa-download"></i></a>
                                    <a title="Supprimer le devoir" class="btn btn-danger confirm_delete" href="{{ route('delete_file', ['id' => $h->id]) }}"><i class="fa fa-trash"></i></a>
                                    @if (Auth::user()->isAdmin() || Auth::user()->isTeacher())
                                    <a title="Charger la devoir corrigé" class="btn btn-primary"><i class="fa fa-upload"></i></a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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
            <form action="{{ route('delete_file', ['id' => $h->id]) }}" method="post">
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
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#mainTable').DataTable({
            "language": {
                "url" : "/js/datatables.french.json"
            }
        });

        var elems = document.getElementsByClassName('confirm_delete');
        var confirmIt = function (e) {
            if (!confirm('Êtes-vous sûr(e) de vouloir supprimer ce fichier ?')) e.preventDefault();
        };
        for (var i = 0, l = elems.length; i < l; i++) {
            elems[i].addEventListener('click', confirmIt, false);
        }

        $('#submit').click(function() {
            $('#loginF').val($('#login').val());
            $('#passwordF').val($('#password').val())
            $('#upForm').submit();
        });
    });
</script>
@endpush
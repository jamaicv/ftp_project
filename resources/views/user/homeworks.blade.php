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
                                <th>Eleve</th>
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
                                <td>{{$h->student()->get()[0]->first_name . ' ' . $h->student()->get()[0]->last_name}}</td>
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
                                    <a title="Télécharger le devoir" class="btn btn-success" data-toggle="modal" data-target="#confirm-submit-dl-{{$h->id}}"><i class="fa fa-download"></i></a>
                                    <a title="Supprimer le devoir" class="btn btn-danger confirm_delete" data-toggle="modal" data-target="#confirm-submit-{{$h->id}}"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>

                            <!-- SFTP Credentials modal delete -->
                            <div class="modal fade" id="confirm-submit-{{$h->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            Connexion SFTP
                                        </div>
                                        <form id="delForm-{{$h->id}}" action="{{ route('delete_file', ['id' => $h->id]) }}" method="post">
                                        @csrf
                                            <div class="modal-body">
                                                <label for="login">Identifiant</label>
                                                <input type="text" name="login" id="login"/><br/>
                                                <label for="password">Mot de passe</label>
                                                <input type="password" name="password" id="password"/>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                                                <a href="#" id="submit-{{$h->id}}" onclick="$('#delForm-{{$h->id}}').submit();" class="btn btn-success success">Valider</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- SFTP Credentials modal download -->
                            <div class="modal fade" id="confirm-submit-dl-{{$h->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            Connexion SFTP
                                        </div>
                                        <form id="downloadForm-{{$h->id}}" action="{{ route('download_file', ['id' => $h->id]) }}" method="post">
                                            @csrf
                                            <div class="modal-body">
                                                <label for="login">Identifiant</label>
                                                <input type="text" name="login" id="login"/><br/>
                                                <label for="password">Mot de passe</label>
                                                <input type="password" name="password" id="password"/>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                                                <a href="#" id="submitDownload-{{$h->id}}" onclick="$('#downloadForm-{{$h->id}}').submit();" class="btn btn-success success">Valider</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
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

        /* $('#submit').click(function() {
            $('#delForm').submit();
        });
        $('#submitDownload').click(function() {
            $('#downloadForm').submit();
        }); */
    });
</script>
@endpush
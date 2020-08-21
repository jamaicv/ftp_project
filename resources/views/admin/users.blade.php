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
                                <th>Prénom</th>
                                <th>Date de naissance</th>
                                <th>E-mail</th>
                                <th>Professeur</th>
                                <th>Administrateur</th>
                                @if (Auth::user()->isAdmin())
                                <th>Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $u)
                            <tr>
                                <td>{{ $u->last_name }}</td>
                                <td>{{ $u->first_name }}</td>
                                <td>{{ $u->birth_date }}</td>
                                <td>{{ $u->email }}</td>
                                <td>
                                    @if ($u->is_teacher == 0)
                                    <i style="color: red" class="fa fa-times"></i>
                                    @else
                                    <i style="color: green" class="fa fa-check"></i>
                                    @endif
                                </td>
                                <td>
                                    @if ($u->is_admin == 0)
                                    <i style="color: red" class="fa fa-times"></i>
                                    @else
                                    <i style="color: green" class="fa fa-check"></i>
                                    @endif
                                </td>
                                @if (Auth::user()->isAdmin())
                                <td>
                                    <a title="Modifier le mot de passe" class="btn btn-primary" href="{{ route('update_password', ['id' => $u->id]) }}"><i class="fa fa-pen"></i></a>
                                </td>
                                @endif
                            </tr>
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
    });
</script>
@endpush
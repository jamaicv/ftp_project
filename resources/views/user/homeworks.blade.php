@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Devoirs') }}</div>

                <div class="card-body">
                    <table class="table table-stripped">
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
                                    <a title="Supprimer le devoir" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                    @if (Auth::user()->isAdmin())
                                    <a title="Charger la devoir corrigé" class="btn btn-primary"><i class="fa fa-check"></i></a>
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
@endsection

<script>
</script>
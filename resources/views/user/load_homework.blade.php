@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Charger un devoir') }}</div>

                <div class="card-body">
                    <form method="post" action="{{ route('load_homework') }}" enctype="multipart/form-data">
                    @csrf
                        <div class="form-group">
                            <label for="file">Fichier à charger :</label><br>
                            <input name="file" type="file" id="file" aria-describedby="fileHelp">
                            <small id="fileHelp" class="form-text text-muted">Le fichier doit être au format </small>
                        </div>
                
                        <button type="submit" class="btn btn-primary">Valider</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-3">Criar nova sessão</h2>
        <form action="{{ route('start') }}" method="POST">
            @csrf
            <input type="hidden" name="hostname" value="{{ $hostname }}">

            <div class="form-group mt-2">
                <label class="label-control">Nome da sessão</label>
                <input type="text" name="session" class="form-control @error('session') is-invalid @enderror"
                       value="{{ old('session') }}" required>

                @error('session')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mt-2">
                <button type="submit" class="btn btn-primary">Criar Sessão</button>
            </div>
        </form>
    </div>
@endsection

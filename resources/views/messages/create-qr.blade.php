@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-3">Criar QrCode</h2>
        <form action="{{ route('generate-qr') }}" method="POST">
            @csrf
            <div class="form-group mt-2">
                <label class="label-control">Nome da sessão</label>
                <input type="text" name="session" class="form-control @error('session') is-invalid @enderror"
                       value="{{ old('session', $session) }}" required>
                @error('session')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mt-2">
                <label class="label-control">Base64</label>
                <select name="base64" class="form-control @error('session') is-invalid @enderror" required>
                    <option value="">Selecione</option>
                    <option value="true">True</option>
                    <option value="false">False</option>
                </select>
                @error('base64')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mt-2">
                <button type="submit" class="btn btn-primary">Criar QrCode</button>
            </div>
        </form>
    </div>
@endsection

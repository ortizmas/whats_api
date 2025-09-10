@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-3">Lista de Workers</h2>
        <table class="table table-striped table-hover table-bordered">
            <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Host</th>
                <th>Data de Inicio</th>
                <th>Sessão</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody>
                @foreach($data['workers'] as $key => $work)
                    <tr>
                        <td>{{$key+1}}</td>
                        <td>{{$work['hostname']}}</td>
                        <td>{{$work['startedAt']}}</td>
                        <td>
                            <ul>
                                @foreach($work['sessions'] as $session)
                                    <li>{{ $session }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            <a href="{{route('create-session', $work['hostname'])}}">Criar Sessão</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="container">
        <h2 class="mb-3">Enviar Mensagem</h2>
        <form action="{{ route('send') }}" method="POST">
            @csrf
            <div class="form-group mt-2">
                <label class="label-control">Nome da sessão</label>
                <input type="text" name="session" class="form-control @error('session') is-invalid @enderror"
                       value="{{ old('session') }}" required>
                @error('session')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mt-2">
                <label class="label-control">Numero de Telefone</label>
                <input type="number" name="number" class="form-control @error('number') is-invalid @enderror"
                       value="{{ old('number') }}" required>
                @error('number')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mt-2">
                <label class="label-control">Mensagem</label>
                <input type="text" name="message" class="form-control @error('message') is-invalid @enderror"
                       value="{{ old('message') }}" required>
                @error('message')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mt-2">
                <label class="label-control">Nome do Servidor</label>
                <input type="text" name="hostname" class="form-control @error('hostname') is-invalid @enderror"
                       value="{{ old('hostname') }}" required>
                @error('hostname')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mt-2">
                <label class="label-control">Selecionar Servidor Aleatório</label>
                    <select name="random" class="form-control @error('random') is-invalid @enderror" required>
                    <option value="">Selecione</option>
                    <option value="true">Sim</option>
                    <option value="false">Não</option>
                </select>
                @error('base64')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mt-2">
                <button type="submit" class="btn btn-primary">Enviar Mensagem</button>
            </div>
        </form>
    </div>
@endsection

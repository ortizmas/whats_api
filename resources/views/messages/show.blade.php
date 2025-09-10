@extends('layouts.app')

@section('content')
    <div class="container">
        @if($data['send'])
            <h2 class="mb-3">Mensagem</h2>
            <div class="alert alert-success">
                <h4>{{$data['message']}}</h4>
            </div>
        @else
            <h2 class="mb-3">Lista de Sessão</h2>
            @if($data && $data['message'])
                <div class="alert alert-success">
                    <h4>{{$data['message']}}</h4>
                </div>
            @endif
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-dark">
                <tr>
                    <th>Mensagem</th>
                    <th>Host</th>
                    <th>Sessão</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                @if($data && $data['message'])
                    <tr>
                        <td>{{$data['message']}}</td>
                        <td>{{$data['hostname']}}</td>
                        <td>{{$data['session']}}</td>
                        <td>
                            <a href="{{route('create-qr', $data['session'])}}">Criar QrCode</a>
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        @endif

        <div class="mt-2">
            <a href="{{route('workers')}}" class="btn btn-info">Lista de Servidores</a>
        </div>
    </div>
@endsection

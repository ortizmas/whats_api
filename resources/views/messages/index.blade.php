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
                            <a href="{{route('start')}}">Criar Sessão</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

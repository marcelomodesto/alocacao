@extends('main')

@section('title', 'Salas')

@section('content')
  @parent 
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h1 class='text-center mb-5'>Salas</h1>

            <p class="text-right">
                <a class="btn btn-primary" href="{{ route('rooms.create') }}">
                    <i class="fas fa-plus-circle"></i>
                    Cadastrar Sala
                </a>
            </p>


            @if (count($salas) > 0)
                <div class="d-flex justify-content-center">
                    <div class="col-md-6">
                    <table class="table table-bordered table-striped table-hover" style="font-size:15px;">
                        <tr>
                            <th>Nome</th>
                            <th>Assentos</th>
                        </tr>

                        @foreach($salas as $sala)
                            <tr>
                                <td style="white-space: nowrap;">{{ $sala->nome }}</td>
                                <td>{{ $sala->assentos }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                </div>
            @else
                <p class="text-center">Não há salas cadastradas</p>
            @endif
        </div>
    </div>
</div>
@endsection
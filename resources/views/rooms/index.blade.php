@extends('main')

@section('title', 'Salas')

@section('content')
  @parent 
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h1 class='text-center mb-5'>Salas</h1>

            @if (count($salas) > 0)
                <div class="d-flex justify-content-center">
                    <div class="col-md-6">

                    <form id="distributesSchoolClassesForm" action="{{ route('rooms.distributes') }}" method="POST"
                        enctype="multipart/form-data"
                        >
                        <p class="text-right">

                            @method('patch')
                            @csrf
                            <button  class="btn btn-primary"
                                type="submit"
                                href="{{ route('rooms.distributes') }}"
                            >
                                Distribuir Turmas
                            </button>
                        </p>
                    </form>
                    <table class="table table-bordered table-striped table-hover" style="font-size:15px;">
                        <tr>
                            <th>Nome</th>
                            <th>Assentos</th>
                            <th></th>
                        </tr>

                        @foreach($salas as $sala)
                            <tr>
                                <td style="white-space: nowrap;">{{ $sala->nome }}</td>
                                <td>{{ $sala->assentos }}</td>
                                <td class="text-center" style="white-space: nowrap;">
                                    <a  class="btn btn-outline-dark btn-sm"
                                        data-toggle="tooltip" data-placement="top"
                                        title="Ver Sala"
                                        href="{{ route('rooms.show', $sala) }}"
                                    >Ver Sala
                                    </a>
                                </td>
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
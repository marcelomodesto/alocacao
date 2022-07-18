@extends('main')

@section('title', 'Turmas')

@section('content')
  @parent 
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h1 class='text-center mb-5'>Turmas</h1>
            @if($schoolterm)
                <h4 class='text-center mb-5'>{{ $schoolterm->period . ' de ' . $schoolterm->year }}</h4>
            @endif

            <div id="progressbar-div">
            </div>
            <br>
            <p class="text-right">
                <a  class="btn btn-primary"
                    title="Cadastrar" 
                    href="{{ route('schoolclasses.create') }}"
                >
                    <i class="fas fa-plus-circle"></i>
                    Cadastrar
                </a>
                
                <a  class="btn btn-primary"
                    title="Importar" 
                    href="{{ route('schoolclasses.import') }}"
                >
                    <i class="fas fa-file-upload"></i>
                    Importar do Jupiter
                </a>
                    
            </p>

            @if (count($turmas) > 0)
                <table class="table table-bordered table-striped table-hover" style="font-size:12px;">
                    <tr>
                        <th>Código da Turma</th>
                        <th>Código da Disciplina</th>
                        <th>Nome da Disciplina</th>
                        <th>Tipo da Turma</th>
                        <th>Horários</th>
                        <th>Professor(es)</th>
                        <th>Início</th>
                        <th>Fim</th>
                        <th></th>
                    </tr>

                    @foreach($turmas as $turma)
                        <tr style="font-size:12px;">
                            <td>{{ $turma->codtur }}</td>
                            <td>{{ $turma->coddis }}</td>
                            <td>{{ $turma->nomdis }}</td>
                            <td>{{ $turma->tiptur }}</td>
                            <td style="white-space: nowrap;">
                                @foreach($turma->classschedules as $horario)
                                    {{ $horario->diasmnocp . ' ' . $horario->horent . ' ' . $horario->horsai }} <br/>
                                @endforeach
                            </td>
                            <td style="white-space: nowrap;">
                                @foreach($turma->instructors as $instructor)
                                    {{ $instructor->getPronounTreatment() . $instructor->getNomAbrev()}} <br/>
                                @endforeach
                            </td>
                            <td>{{ $turma->dtainitur }}</td>
                            <td>{{ $turma->dtafimtur }}</td>
                            <td class="text-center" style="white-space: nowrap;">
                                <a class="text-dark text-decoration-none"
                                    data-toggle="tooltip" data-placement="top"
                                    title="Editar"
                                    href="{{ route('schoolclasses.edit', $turma) }}"
                                >
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a class="text-dark text-decoration-none"
                                    data-toggle="modal"
                                    data-target="#removalModal"
                                    title="Remover"
                                    href="{{ route(
                                        'schoolclasses.destroy',
                                        $turma
                                    ) }}"
                                >
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </table>
                @include('schoolclasses.modals.removal')
            @else
                <p class="text-center">Não há turmas cadastradas</p>
            @endif
        </div>
    </div>
</div>
@endsection
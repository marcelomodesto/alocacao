@extends('main')

@section('title', 'Turmas')

@section('content')
  @parent 
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h1 class='text-center mb-5'>Turmas de outras unidades</h1>
            @if($schoolterm)
                <h4 class='text-center mb-5'>{{ $schoolterm->period . ' de ' . $schoolterm->year }}</h4>
            @endif

            <h4 class='text-center mb-5'>Turmas que não serão ministradas no IME devem ser excluidas</h4>


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
                            <td>                                
                                <a class="text-dark" target="_blank"
                                    href="{{ $turma->tiptur=='Graduação' ? 'https://uspdigital.usp.br/jupiterweb/obterTurma?nomdis=&sgldis='.$turma->coddis : ''}}"
                                >
                                    {{ $turma->nomdis }}
                                </a>
                            </td>
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
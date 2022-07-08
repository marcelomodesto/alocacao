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
            @include('schoolclasses.modals.chooseSchoolTerm')
            @include('schoolclasses.modals.import')
            @include('schoolclasses.modals.addSchoolClass')
            <p class="text-right">
                <a  id="btn-addModal"
                    class="btn btn-primary"
                    data-toggle="modal"
                    data-target="#addSchoolClassModal"
                    title="Cadastrar" 
                >
                    <i class="fas fa-plus-circle"></i>
                    Cadastrar
                </a>

                <a  id="btn-chooseSchoolTermModal"
                    class="btn btn-primary"
                    data-toggle="modal"
                    data-target="#chooseSchoolTermModal"
                    title="Escolher Semestre" 
                >
                    Escolher Semestre
                </a>
                
                <a  id="btn-importModal"
                    class="btn btn-primary"
                    data-toggle="modal"
                    data-target="#importSchoolClassModal"
                    title="Importar" 
                >
                    <i class="fas fa-file-upload"></i>
                    Importar do Jupiter
                </a>
                
                <a  id="btn-searchModal" 
                    class="btn btn-primary" 
                    data-toggle="modal" 
                    data-target="#schoolclassesSearchModal"
                >
                    <i class="fas fa-search"></i>
                    Buscar
                </a>
                    
            </p>
            @include('schoolclasses.modals.search')

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
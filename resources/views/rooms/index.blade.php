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
                                @php
                                    $label = "";
                                    $first = true;
                                    $st = App\Models\SchoolTerm::getLatest();
                                    $turmas_nao_alocadas = App\Models\SchoolClass::whereBelongsTo($st)->whereDoesntHave("room")->whereDoesntHave("fusion")->get();

                                    foreach($turmas_nao_alocadas as $turma){
                                        if($sala->isCompatible($turma, $ignore_block=true, $ignore_estmtr=true)){
                                            if($first){
                                                $label .= "Compativel com:\n";
                                                $first = false;
                                            }
                                            $label .= $turma->coddis." ".($turma->tiptur=="Graduação" ? "T.".substr($turma->codtur, -2, 2) : "")." ".$turma->nomdis."\n";
                                        }
                                    }

                                    $dobradinhas_nao_alocadas = App\Models\Fusion::whereHas("schoolclasses", function ($query) use ($st){
                                                    $query->whereBelongsTo($st);
                                                })->whereHas("master", function ($query){
                                                    $query->whereDoesntHave("room");
                                                })->get();
                                    
                                    foreach($dobradinhas_nao_alocadas as $fusion){
                                        if($sala->isCompatible($fusion->master, $ignore_block=true, $ignore_estmtr=true)){
                                            if($first){
                                                $label .= "Compativel com:\n";
                                                $first = false;
                                            }
                                            foreach(range(0, count($fusion->schoolclasses)-1) as $y){
                                                $label .= $fusion->schoolclasses[$y]->coddis." ";
                                                $label .= $y != count($fusion->schoolclasses)-1 ? "/" : "\n";
                                            }
                                        }
                                    }
                                    if($first){
                                        if($turmas_nao_alocadas or $dobradinhas_nao_alocadas){ 
                                            $label .= "Nenhuma turma compativel";
                                        }
                                    }
                                    
                                @endphp
                                <td class="text-center" style="white-space: nowrap;">
                                    <a  class="btn btn-outline-dark btn-sm"
                                        data-toggle="tooltip" data-placement="top"
                                        title="{{$label}}"
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
@extends('main')

@section('title', 'Salas')

@section('content')
  @parent 
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h1 class='text-center mb-5'>Salas</h1>

            <div id="progressbar-div">
            </div>
            <br>
            @if (count($salas) > 0)
                <div class="d-flex justify-content-center">
                    <div class="col-md-6">
                        <div class="float-right" style="margin-bottom: 20px;">
                            <!--
                            <form style="display: inline;"  action="{{ route('rooms.makeReport') }}" method="GET"
                            enctype="multipart/form-data"
                            >
                                @csrf
                                <button  class="btn btn-primary"
                                    id="btn-report"
                                    type="submit"
                                >
                                    Gerar Relatório
                                </button>
                            </form>
                            -->
                            
                            <form id="distributesForm" style="display: inline;"action="{{ route('rooms.distributes') }}" method="POST"
                            enctype="multipart/form-data"
                            >
                                @method('patch')
                                @csrf
                                <button  class="btn btn-primary"
                                    id="btn-distributes"
                                    type="submit"
                                    onclick="return confirm('Você tem certeza? Redistribuir as turmas irá desfazer a distribuição atual!')" 
                                >
                                    Distribuir Turmas
                                </button>
                            </form>

                            <form id="emptyForm" style="display: inline;" action="{{ route('rooms.empty') }}" method="POST"
                            enctype="multipart/form-data"
                            >
                                @method('patch')
                                @csrf
                                <button  class="btn btn-primary"
                                    id="btn-empty"
                                    type="submit"
                                    onclick="return confirm('Você tem certeza? Esvaziar as salas irá desfazer a distribuição atual!')" 
                                >
                                    Esvaziar Salas
                                </button>
                            </form>

                            <form style="display: inline;"  action="{{ route('rooms.reservation') }}" method="GET"
                            enctype="multipart/form-data"
                            >
                                @csrf
                                <button  class="btn btn-primary"
                                    id="btn-reservation"
                                    type="submit"
                                    onclick="return confirm('Você tem certeza? Lembre-se de conferir a distribuição das turmas nas salas antes de enviar as reservas para o Urano!')" 
                                >
                                    Reservar Salas no Urano
                                </button>
                            </form>
                        </div>
                    <br>

                    <table class="table table-bordered table-striped table-hover" style="font-size:15px;">
                        <tr>
                            <th style="vertical-align: middle;">Nome</th>
                            <th style="vertical-align: middle;">Assentos</th>
                            <th>Distribuir<br>nas<br>Salas</th>
                            <th style="vertical-align: middle;">Esvaziar<br>Salas</th>
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
                                            if($fusion->schoolclasses->pluck("coddis")->unique()->count()==1){
                                                $label .= $fusion->master->coddis." ";
                                                foreach(range(0, count($fusion->schoolclasses)-1) as $y){
                                                    $label .= "T.".substr($fusion->schoolclasses[$y]->codtur,-2,2);
                                                    $label .= $y != count($fusion->schoolclasses)-1 ? "/" : "";
                                                }
                                                $label .= " ".$fusion->master->nomdis."\n";
                                            }else{
                                                foreach(range(0, count($fusion->schoolclasses)-1) as $y){
                                                    $label .= $fusion->schoolclasses[$y]->coddis." ";
                                                    $label .= $y != count($fusion->schoolclasses)-1 ? "/" : "\n";
                                                }
                                            }
                                        }
                                    }
                                    if($first){
                                        if($turmas_nao_alocadas or $dobradinhas_nao_alocadas){ 
                                            $label .= "Nenhuma turma compativel";
                                        }
                                    }
                                    
                                @endphp
                                <td>
                                    <input id="rooms_id" form="distributesForm" class="checkbox" type="checkbox" name="rooms_id[]" value="{{ $sala->id }}" {!! !in_array($sala->nome, ["B05","B04","B07","A249"]) ? 'checked' : '' !!}>
                                </td>
                                <td>
                                    <input id="rooms_id" form="emptyForm" class="checkbox" type="checkbox" name="rooms_id[]" value="{{ $sala->id }}" checked>
                                </td>
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

                        <tr>
                                <td style="white-space: nowrap;" colspan=4>Salas livres por horário</td>
                                <td class="text-center" style="white-space: nowrap;">
                                    <a  class="btn btn-outline-dark btn-sm"
                                        data-toggle="tooltip" data-placement="top"
                                        href="{{ route('rooms.showFreeTime',) }}"
                                    >Ver Salas
                                    </a>
                                </td>
                        </tr>
                    </table>
                </div>
                </div>
            @else
                <p class="text-center">Não há salas cadastradas</p>
            @endif

            @php
                $turmas_nao_alocadas = App\Models\SchoolClass::whereBelongsTo($st)->where("externa", false)->whereDoesntHave("room")->whereDoesntHave("fusion")->get();
                $dobradinhas_nao_alocadas = App\Models\Fusion::whereHas("schoolclasses", function ($query) use ($st){
                                                    $query->whereBelongsTo($st);
                                                })->whereHas("master", function ($query){
                                                    $query->whereDoesntHave("room");
                                                })->get();
            @endphp
            @if($turmas_nao_alocadas)
            <br>
            <h3 class='text-center mb-5'>Turmas não alocadas</h3>

                <table class="table table-bordered table-striped table-hover" style="font-size:12px;">
                    <tr>
                        <th>Código da Turma</th>
                        <th>Código da Disciplina</th>
                        <th>Nome da Disciplina</th>
                        <th>Tipo da Turma</th>
                        <th>Horários</th>
                        <th>Professor(es)</th>
                        <th>Salas<br>Compatíveis</th>
                    </tr>

                    @foreach($turmas_nao_alocadas as $turma)
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
                            <td style="white-space: nowrap;">
                                @php
                                    $rooms = App\Models\Room::all();
                                    $rooms = $rooms->filter(function($room)use($turma){
                                        return $room->isCompatible($turma,$ignore_estmtr=true, $ignore_block=true);
                                    })->values();
                                @endphp   
                                @foreach($rooms->isNotEmpty() ? range(0, count($rooms)-1) : [] as $x)
                                    <a class="text-dark" target="_blank"
                                        href="{{ route('rooms.show', $rooms[$x]) }}"
                                    >
                                        {{ $rooms[$x]->nome }}
                                    </a>
                                    @if(($x+1) % 3 == 0)
                                        <br>
                                    @elseif($x != count($rooms)-1)
                                        -
                                    @endif
                                @endforeach   
                            </td>
                        </tr>
                    @endforeach
                </table>
            @endif

            @if ($dobradinhas_nao_alocadas->count() > 0)
                <br>
                <h3 class='text-center mb-5'>Dobradinhas não alocadas</h3>

                <table class="table table-bordered" style="font-size:12px;">
                    <tr style="background-color:#F5F5F5">
                        <th>Nome da Dobradinha</th>
                        <th>Código da Turma</th>
                        <th>Código da Disciplina</th>
                        <th>Nome da Disciplina</th>
                        <th>Tipo da Turma</th>
                        <th>Horários</th>
                        <th>Professor(es)</th>
                        <th>Salas<br>Compatíveis</th>
                    </tr>

                    @foreach($dobradinhas_nao_alocadas as $fusion)
                        @foreach(range(0, count($fusion->schoolclasses)-1) as $x)
                            <tr style="font-size:12px;white-space: nowrap;">
                                @if($x == 0)
                                    <td rowspan="{{count($fusion->schoolclasses)}}" style="white-space: nowrap;
                                                                                    vertical-align: middle;">
                                        @if($fusion->schoolclasses->pluck("coddis")->unique()->count()==1)
                                            {{ $fusion->master->coddis }}
                                            @foreach(range(0, count($fusion->schoolclasses)-1) as $y)
                                                    {{ " T.".substr($fusion->schoolclasses[$y]->codtur,-2,2) }}     
                                                    {{ $y != count($fusion->schoolclasses)-1 ? "/" : "" }}    
                                            @endforeach
                                        @else
                                            @foreach(range(0, count($fusion->schoolclasses)-1) as $y)
                                                    {{ $fusion->schoolclasses[$y]->coddis }}     
                                                    {{ $y != count($fusion->schoolclasses)-1 ? "/" : "" }}    
                                            @endforeach
                                        @endif
                                    </td>
                                @endif
                                <td>{{ $fusion->schoolclasses[$x]->codtur }}</td>
                                <td>{{ $fusion->schoolclasses[$x]->coddis }}</td>
                                <td>
                                    @if($fusion->schoolclasses[$x]->tiptur == "Graduação")                    
                                        <a class="text-dark" target="_blank"
                                            href="{{ 'https://uspdigital.usp.br/jupiterweb/obterTurma?nomdis=&sgldis='.$fusion->schoolclasses[$x]->coddis }}"
                                        >
                                            {{ $fusion->schoolclasses[$x]->nomdis }}
                                        </a>
                                    @else
                                        {{ $fusion->schoolclasses[$x]->nomdis }}
                                    @endif
                                </td>
                                <td>{{ $fusion->schoolclasses[$x]->tiptur }}</td>
                                @if($x == 0)
                                    <td rowspan="{{count($fusion->schoolclasses)}}" 
                                    style="white-space: nowrap;vertical-align: middle;">
                                        @foreach($fusion->master->classschedules as $horario)
                                            {{ $horario->diasmnocp . ' ' . $horario->horent . ' ' . $horario->horsai }} <br/>
                                        @endforeach
                                    </td>
                                    <td rowspan="{{count($fusion->schoolclasses)}}" style="white-space: nowrap;
                                                                                    vertical-align: middle;">
                                        @foreach($fusion->master->instructors as $instructor)
                                            {{ $instructor->getPronounTreatment() . $instructor->getNomAbrev()}} <br/>
                                        @endforeach
                                    </td>
                                    <td rowspan="{{count($fusion->schoolclasses)}}">  
                                        @php
                                            $rooms = App\Models\Room::all();
                                            $rooms = $rooms->filter(function($room)use($fusion){
                                                return $room->isCompatible($fusion->master,$ignore_estmtr=true, $ignore_block=true);
                                            })->values();
                                        @endphp   
                                        @foreach($rooms->isNotEmpty() ? range(0, count($rooms)-1) : [] as $x)
                                            <a class="text-dark" target="_blank"
                                                href="{{ route('rooms.show', $rooms[$x]) }}"
                                            >
                                                {{ $rooms[$x]->nome }}
                                            </a>
                                            @if(($x+1) % 3 == 0)
                                                <br>
                                            @elseif($x != count($rooms)-1)
                                                -
                                            @endif
                                        @endforeach   
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @endforeach
                </table>
            @endif
        </div>
    </div>
</div>
@endsection

@section('javascripts_bottom')
@parent
<script>
$( function() {       
    function progress() {
        $.ajax({
            url: window.location.origin+'/monitor/getReportProcess',
            dataType: 'json',
            success: function success(json){
                if('progress' in json){
                    if(!json["failed"]){
                        if(document.getElementById('progressbar')){
                            $( "#progressbar" ).progressbar( "value", json['progress'] );
                        }else if(json['progress'] != 100){
                            $('#progressbar-div').append("<div id='progressbar'><div class='progress-label'></div></div>");
                            var progressbar = $( "#progressbar" ),
                            progressLabel = $( ".progress-label" );
                            progressbar.progressbar({
                                value: false,
                                change: function() {
                                    progressLabel.text( progressbar.progressbar( "value" ) + "%" );
                                },
                                complete: function() {
                                    $( "#progressbar" ).remove();
                                    window.clearTimeout(timeouthandle);
                                    location.replace(window.location.origin+'/rooms/downloadReport');
                                }
                            });
                        }
                    }
                }
                var timeouthandle = setTimeout( progress, 1000);
            }
        });
    }        
    setTimeout( progress, 50 ); 

    function progress2() {
        $.ajax({
            url: window.location.origin+'/monitor/getReservationProcess',
            dataType: 'json',
            success: function success(json){
                if('progress' in json){
                    if(!json["data"] && !json['failed']){
                        if(document.getElementById('progressbar')){
                            $( "#progressbar" ).progressbar( "value", json['progress'] );
                        }else if(json['progress'] != 100){
                            document.getElementById("btn-reservation").disabled = true;
                            //document.getElementById("btn-report").disabled = true;
                            document.getElementById("btn-distributes").disabled = true;
                            document.getElementById("btn-empty").disabled = true;
                            $('#progressbar-div').append("<div id='progressbar'><div class='progress-label'></div></div>");
                            var progressbar = $( "#progressbar" ),
                            progressLabel = $( ".progress-label" );
                            progressbar.progressbar({
                                value: false,
                                change: function() {
                                    progressLabel.text( progressbar.progressbar( "value" ) + "%" );
                                },
                                complete: function() {
                                    document.getElementById("btn-reservation").disabled = false;
                                    //document.getElementById("btn-report").disabled = false;
                                    document.getElementById("btn-distributes").disabled = false;
                                    document.getElementById("btn-empty").disabled = false;
                                    $( "#progressbar" ).remove();
                                    $('#flash-message').empty();
                                    $('#flash-message').append("<p id='success-message' class='alert alert-success'>As reservas foram feitas com sucesso.</p>");
                                }
                            });
                        }
                    }else if((JSON.parse(json["data"])["status"] == "failed") && !(document.getElementById('error-message'))){
                        document.getElementById("btn-reservation").disabled = false;
                        //document.getElementById("btn-report").disabled = false;
                        document.getElementById("btn-distributes").disabled = false;
                        document.getElementById("btn-empty").disabled = false;
                        $( "#progressbar" ).remove();

                        var schoolclass = JSON.parse(json["data"])["schoolclass"];
                        var sala = JSON.parse(json["data"])["room"];

                        $('#flash-message').empty();
                        $('#flash-message').append("<p id='error-message' class='alert alert-danger'>Não foi possivel realizar as reservas. A disciplina "+
                            schoolclass.coddis+" turma "+schoolclass.codtur+" não conseguiu reserva na sala "+sala+". Entre em contato com o administrador. </p>");
                    }else if(json['failed']){
                        document.getElementById("btn-reservation").disabled = false;
                        //document.getElementById("btn-report").disabled = false;
                        document.getElementById("btn-distributes").disabled = false;
                        document.getElementById("btn-empty").disabled = false;
                        $( "#progressbar" ).remove();
                        $('#flash-message').empty();
                        $('#flash-message').append("<p id='error-message' class='alert alert-danger'>Não foi possivel realizar as reservas. Falha critica. Entre em contato com o administrador. </p>");
                    }
                }
                var timeouthandle = setTimeout( progress2, 1000);
            }
        });
    }        
    setTimeout( progress2, 50 );
});
</script>
@endsection
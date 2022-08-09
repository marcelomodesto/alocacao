@extends('main')

@section('title', 'Horário das Disciplinas')

@section('content')
  @parent 
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @php
                $schoolterm = App\Models\SchoolTerm::getLatest();
                $schoolclasses = App\Models\SchoolClass::whereBelongsTo($schoolterm)->where("externa", false)->orderBy("coddis")->get();
            @endphp
            <h1 class='text-center mb-5'><b>Todas Turmas</b></h1>
            <h2 class='text-center mb-5'>{!! $schoolterm->period . ' de ' . $schoolterm->year !!}</h2>


            <br>
            @foreach(App\Models\Observation::whereBelongsTo($schoolterm)->get() as $observation)
                <div class="card">
                    <div class="card-body">
                        <h3 class='card-title' style="color:blue">{!! $observation->title !!}</h3>
                        @foreach(explode("\r\n", $observation->body) as $line)
                            <p class="card-text" style="color:blue">{!! $line !!} </p>
                        @endforeach
                    </div>
                </div>
            @endforeach
            <br>
            <br>
            
            @if (count($schoolclasses) > 0)
                <table class="table table-bordered table-striped table-hover" style="font-size:12px;">
                    <tr>
                        <th>Código da Turma</th>
                        <th>Código da Disciplina</th>
                        <th>Nome da Disciplina</th>
                        <th>Tipo da Turma</th>
                        <th>Sala</th>
                        <th>Horários</th>
                        <th>Professor(es)</th>
                    </tr>

                    @foreach($schoolclasses as $schoolclasse)
                        <tr style="font-size:12px;">
                            <td style="vertical-align: middle;">{{ $schoolclasse->codtur }}</td>
                            <td style="vertical-align: middle;">{{ $schoolclasse->coddis }}</td>
                            <td style="vertical-align: middle;">                                
                                <a class="text-dark" target="_blank"
                                    href="{{ $schoolclasse->tiptur=='Graduação' ? 'https://uspdigital.usp.br/jupiterweb/obterTurma?nomdis=&sgldis='.$schoolclasse->coddis : ''}}"
                                >
                                    {{ $schoolclasse->nomdis }}
                                </a>
                            </td>
                            <td style="vertical-align: middle;">{{ $schoolclasse->tiptur }}</td>
                            <td style="white-space: nowrap;vertical-align: middle;">{{ $schoolclasse->room ? $schoolclasse->room->nome : "Sem Sala" }}</td>
                            <td style="white-space: nowrap;vertical-align: middle;">
                                @foreach($schoolclasse->classschedules as $schedule)
                                    {{ $schedule->diasmnocp . ' ' . $schedule->horent . ' ' . $schedule->horsai }} <br/>
                                @endforeach
                            </td>
                            <td style="white-space: nowrap;vertical-align: middle;">
                                @foreach($schoolclasse->instructors as $instructor)
                                    {{ $instructor->getPronounTreatment() . $instructor->getNomAbrev()}} <br/>
                                @endforeach
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
@extends('main')

@section('title', 'Horário das Disciplinas')

@section('content')
  @parent 
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h1 class='text-center mb-5'>Horário das Disciplinas</h1>
            
            @php
                $schoolterm = App\Models\SchoolTerm::getLatest();
            @endphp

            <h4 class='text-center mb-5'>{{ $schoolterm->period . ' de ' . $schoolterm->year }}</h4>

            @foreach(App\Models\Course::whereNull("grupo")->get() as $course)                
                <a class="link"
                    href="{{ route(
                        'courseschedules.show',
                        $course
                    ) }}"
                >
                    {!! $course->nomcur." - ".ucfirst($course->perhab) !!}
                    @if($course->grupo)
                        {!! " Grupo ".$course->grupo !!}
                    @endif
                </a>
                <br>
            @endforeach            
            <a class="link"
                href="{{ route('courseschedules.showLicNot') }}"
            >
                Matemática Licenciatura - Noturno
            </a>    
            <br>    
            <a class="link"
                href="{{ route('courseschedules.showAll') }}"
            >
                Todas Turmas
            </a>

        </div>
    </div>
</div>
@endsection
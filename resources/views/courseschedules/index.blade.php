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

            <a class="link"
                href="{{ route('courseschedules.showAll') }}"
            >
                Todas Turmas
            </a>
            <div class="row">
                <div class="col-md-6">
                    <h4 class="my-3"><b>Gradução</b></h4>
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
                </div>
                <div class="col-md-6">
                    <h4 class="my-3"><b>Pós-Gradução</b></h4>   
                    
                        <form action="{{ route('courseschedules.showPos') }}" method="get"
                        enctype="multipart/form-data"
                        >
                            <input type="hidden" id="prefixo" name="prefixo" value="MAC">
                            @csrf
                            <button  class="button-link"
                                type="submit"
                            >
                                Ciência da Computação
                            </button>
                        </form>       
                        <form action="{{ route('courseschedules.showPos') }}" method="get"
                        enctype="multipart/form-data"
                        >
                            <input type="hidden" id="prefixo" name="prefixo" value="MAE">
                            @csrf
                            <button  class="button-link"
                                type="submit"
                            >
                                Estatística
                            </button>
                        </form>      
                        <form action="{{ route('courseschedules.showPos') }}" method="get"
                        enctype="multipart/form-data"
                        >
                            <input type="hidden" id="prefixo" name="prefixo" value="MAT">
                            @csrf
                            <button  class="button-link"
                                type="submit"
                            >
                                Matemática
                            </button>
                        </form>     
                        <form action="{{ route('courseschedules.showPos') }}" method="get"
                        enctype="multipart/form-data"
                        >
                            <input type="hidden" id="prefixo" name="prefixo" value="MAP">
                            @csrf
                            <button  class="button-link"
                                type="submit"
                            >
                                Matemática Aplicada
                            </button>
                        </form>     
                        <form action="{{ route('courseschedules.showPos') }}" method="get"
                        enctype="multipart/form-data"
                        >
                            <input type="hidden" id="prefixo" name="prefixo" value="MPM">
                            @csrf
                            <button  class="button-link"
                                type="submit"
                            >
                                Mestrado Profissional em Ensino de Matemática
                            </button>
                        </form>    
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
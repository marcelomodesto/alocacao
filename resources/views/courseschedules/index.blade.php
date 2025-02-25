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

            <div class="row">
                <div class="col-md-6">
                    <h4 class="my-3"><b>Gradução</b></h4>
                    @foreach(App\Models\Course::whereNull("grupo")->get() as $course)     
                        <li>           
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
                        </li>
                    @endforeach     
                    <li>                 
                    <a class="link"
                        href="{{ route('courseschedules.showLicNot') }}"
                    >
                        Matemática Licenciatura - Noturno
                    </a>    
                    </li>
                </div>
                <div class="col-md-6">
                    <h4 class="my-3"><b>Pós-Gradução</b></h4>   
                        <li>
                        <form action="{{ route('courseschedules.showPos') }}" method="get"
                        enctype="multipart/form-data"
                        >
                            <input type="hidden" id="prefixo" name="prefixo" value="MAC">
                            @csrf
                             <li>
                            <button  class="button-link"
                                type="submit"
                            >
                                Ciência da Computação
                            </button>
                            </li>
                        </form>       
                        <form action="{{ route('courseschedules.showPos') }}" method="get"
                        enctype="multipart/form-data"
                        >
                            <input type="hidden" id="prefixo" name="prefixo" value="MAE">
                            @csrf
                             <li>
                            <button  class="button-link"
                                type="submit"
                            >
                                Estatística
                            </button>
                            </li>
                        </form>      
                        <form action="{{ route('courseschedules.showPos') }}" method="get"
                        enctype="multipart/form-data"
                        >
                            <input type="hidden" id="prefixo" name="prefixo" value="MAT">
                            @csrf
                             <li>
                            <button  class="button-link"
                                type="submit"
                            >
                                Matemática
                            </button>
                            </li>
                        </form>     
                        <form action="{{ route('courseschedules.showPos') }}" method="get"
                        enctype="multipart/form-data"
                        >
                            <input type="hidden" id="prefixo" name="prefixo" value="MAP">
                            @csrf
                             <li>
                            <button  class="button-link"
                                type="submit"
                            >
                                Matemática Aplicada
                            </button>
                            </li>
                        </form>     
                        <form action="{{ route('courseschedules.showPos') }}" method="get"
                        enctype="multipart/form-data"
                        >
                            <input type="hidden" id="prefixo" name="prefixo" value="MPM">
                            @csrf
                             <li>
                            <button  class="button-link"
                                type="submit"
                            >
                                Mestrado Profissional em Ensino de Matemática
                            </button>
                            </li>
                        </form>    
                </div>
            </div>

            <h4 class='mt-5 mb-3'><b>Listagem completa</b></h4>

            <li>
            <a class="link"
                href="{{ route('courseschedules.showAll') }}"
            >
                Relação completa com todas as turmas oferecidas nas salas de aula do Instituto
            </a>
            </li>
        </div>
    </div>
</div>
@endsection
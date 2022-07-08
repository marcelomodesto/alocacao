@extends('main')

@section('title', 'Cadastrar turma')

@section('content')
  @parent 
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class='h5 font-weight-bold my-3'>
                Cadastrar turma
            </h1>

            <p class="alert alert-info rounded-0">
                <b>Atenção:</b>
                Os campos assinalados com * são de preenchimento obrigatório.
            </p>

            <form method="POST"
                action="{{ route('schoolclasses.store', $turma) }}"
            >
                @csrf
                <input name="periodoId" value="{{$turma->schoolterm->id}}" type="hidden">

                @include('schoolclasses.partials.form', ['buttonText' => 'Cadastrar'])
            </form>

            @include('schoolclasses.modals.addClassSchedule')
            @include('schoolclasses.modals.addInstructor')
        </div>
    </div>
</div>
@endsection
@extends('main')

@section('title', 'Períodos Letivos')

@section('content')
  @parent 
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h1 class='text-center mb-5'>Períodos Letivos</h1>

            <p class="text-right">
                <a class="btn btn-primary" href="{{ route('schoolterms.create') }}">
                    <i class="fas fa-plus-circle"></i>
                    Cadastrar período letivo
                </a>
            </p>


            @if (count($schoolterms) > 0)
                <div class="d-flex justify-content-center">
                    <div class="col-md-6">
                    <table class="table table-bordered table-striped table-hover" style="font-size:15px;">
                        <tr>
                            <th>Ano</th>
                            <th>Período</th>
                        </tr>

                        @foreach($schoolterms as $schoolterm)
                            <tr>
                                <td>{{ $schoolterm->year }}</td>
                                <td style="white-space: nowrap;">{{ $schoolterm->period }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                </div>
            @else
                <p class="text-center">Não há períodos letivos cadastrados</p>
            @endif
        </div>
    </div>
</div>
@endsection
@extends('main')

@section('title', 'Observações')

@section('content')
  @parent 
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h1 class='text-center mb-5'>Observações</h1>
            @if($schoolterm)
                <h4 class='text-center mb-5'>{{ $schoolterm->period . ' de ' . $schoolterm->year }}</h4>
            @endif

            <div id="progressbar-div">
            </div>
            <br>

            <div class="float-right" style="margin-bottom: 20px;">
                <p class="text-right" style="display: inline;" >
                    <a  class="btn btn-primary"
                        title="Cadastrar" 
                        href="{{ route('observations.create') }}"
                    >
                        <i class="fas fa-plus-circle"></i>
                        Cadastrar
                    </a>
                </p>
            </div>

            @if (count($observations) > 0)
                <table class="table table-bordered table-striped table-hover" style="font-size:12px;">
                    <tr>
                        <th>Titulo</th>
                        <th>Corpo</th>
                        <th></th>
                    </tr>

                    @foreach($observations as $observation)
                        <tr style="font-size:12px;">
                            <td>{{ $observation->title }}</td>
                            <td>{{ $observation->body }}</td>
                            <td class="text-center" style="white-space: nowrap;width:100px;">
                                <form method="get"  action="{{ route('observations.edit', $observation) }}" style="display: inline;">
                                    @csrf
                                    <button class="btn px-0">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </form>
                                <form method="post"  action="{{ route('observations.destroy',$observation) }}" style="display: inline;">
                                    @method('delete')
                                    @csrf
                                    <button class="btn px-0"
                                        onclick="return confirm('Você tem certeza que deseja excluir essa observação?')" >
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </table>
            @else
                <p class="text-center">Não há observações cadastradas</p>
            @endif
        </div>
    </div>
</div>
@endsection
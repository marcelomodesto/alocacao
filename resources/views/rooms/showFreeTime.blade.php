@extends('main')

@section('title', 'Sala')

@section('content')
  @parent 
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h1 class='text-center mb-5'>Salas livres por horário</h1>

            @php
                $st = App\Models\SchoolTerm::getLatest();
                $pallet = ['#F0FFF0','#FFFFF0','#F0E68C','#E6E6FA','#FFF0F5','#7CFC00','#D8BFD8','#ADD8E6','#F08080',
                          '#E0FFFF','#FAFAD2','#D3D3D3','#90EE90','#FFB6C1','#FFA07A','#20B2AA','#87CEFA','#778899',
                          '#B0C4DE','#FFFFE0','#F8F8FF','#F5FFFA','#FFE4E1','#FDF5E6','#FFDEAD','#EEE8AA','#AFEEEE'];
                $i = 0;
                $horarios = [
                    "08:00"=>"09:40",
                    "10:00"=>"11:40",
                    "14:00"=>"15:40",
                    "16:00"=>"17:40",
                    "19:20"=>"21:00",
                    "21:10"=>"22:50",
                ];

                $dias = ['seg', 'ter', 'qua', 'qui', 'sex'];  

            @endphp

            <div class="d-flex justify-content-center">
                <div class="col-md-12">
                    <table class="table table-bordered" style="font-size:15px;">
                        <tr style="background-color:#F5F5F5">
                            <th>Horário</th>
                            @foreach($dias as $dia)
                                <th style="min-width:150px">{{ $dia }}</th>
                            @endforeach
                        </tr>
                        @foreach($horarios as $horent=>$horsai)
                            <tr>
                                <td style="vertical-align: middle;">{{ $horent }}<br>às<br>{{ $horsai }}</td>
                                @foreach($dias as $dia)
                                    @php
                                        $rooms = App\Models\Room::whereDoesntHave("schedules", function($query)use($dia, $horent, $horsai){
                                            $query->where("diasmnocp", $dia)->where("horsai",">",$horent)->where("horent", "<", $horsai);
                                        })->get();
                                    @endphp                                
                                <td style="vertical-align: middle;">
                                    @foreach($rooms ? range(0, count($rooms)-1) : [] as $x)
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
                                @endforeach
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>                
        </div>
    </div>
</div>
@endsection

@section('javascripts_bottom')
@parent
<script>
$( function() {       
    function refresh() {
        document.location.reload();
        setTimeout( refresh, 2000);
    }        
    setTimeout( refresh, 2000 );
});
</script>
@endsection

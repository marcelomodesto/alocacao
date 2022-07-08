<div class="modal fade" id="chooseSchoolTermModal">
   <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Escolher outro Semestre</h4>
            </div>
            <form id="chooseSchoolTermForm" action="{{ route('schoolclasses.index') }}" method="GET"
            enctype="multipart/form-data"
            >

            @csrf
            <div class="modal-body">
                <div class="row custom-form-group align-items-center">
                    <div class="col-12 col-lg-6 text-lg-right">
                        <label for="periodoId">Período letivo</label>   
                    </div> 
                    <div class="col-12 col-md-5">

                        <select id="periodoId" name="periodoId" class="custom-form-control">
                            @foreach(App\Models\SchoolTerm::all() as $st)
                                <option value={{ $st->id }}>{{ $st->year . " " . $st->period }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="btn-chooseSchoolTerm" class="btn btn-default" type="submit">Buscar</button>
                <button class="btn btn-default" type="button" data-dismiss="modal">Fechar</button>
            </div>
            </form>
        </div>
    </div>
</div>
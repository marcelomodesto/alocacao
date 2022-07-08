<div class="modal fade" id="importSchoolClassModal">
   <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Importar turmas</h4>
            </div>
            <form id="importSchoolClassesForm" action="{{ route('schoolclasses.import') }}" method="POST"
            enctype="multipart/form-data"
            >

            @method('patch')
            @csrf
            <div class="modal-body">
                <div class="row custom-form-group align-items-center">
                    <div class="col-12 col-lg-6 text-lg-right">
                        <label for="periodoId">Período letivo</label>   
                    </div> 
                    <div class="col-12 col-md-5">

                        <select id="periodoId" name="periodoId" class="custom-form-control">
                            @foreach(App\Models\SchoolTerm::all() as $schoolterm)
                                <option value={{ $schoolterm->id }}>{{ $schoolterm->year . " " . $schoolterm->period }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="btn-importSchoolClasses" class="btn btn-default" type="submit">Importar</button>
                <button class="btn btn-default" type="button" data-dismiss="modal">Fechar</button>
            </div>
            </form>
        </div>
    </div>
</div>
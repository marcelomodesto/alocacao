<div class="modal fade" id="schoolclassesSearchModal">
   <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Buscar Turma</h4>
            </div>
            <form id="searchForm" action="{{ route('schoolclasses.search') }}" method="get">
                <div class="modal-body">
                        <div class="row custom-form-group align-items-center">
                            <div class="col-12 col-lg-6 text-lg-right">
                                <label for="coddis">Código da Disciplina </label>
                            </div>

                            <div class="col-12 col-md-4">
                                <input class="custom-form-control" type="text" name="coddis" id="coddis"
                                    value=''
                                />
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" type="submit">Buscar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                </div>
            </form>
        </div>
    </div>
</div>
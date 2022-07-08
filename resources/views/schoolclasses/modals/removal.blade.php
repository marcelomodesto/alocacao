<div class="modal fade" id="removalModal" 
role="dialog" aria-labelledby="removalModalLabel"
aria-hidden="true"
>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="removalModalLabel">
                    Confirmar remoção
                </h5>
            </div>
            <div class="modal-body">
                Deseja remover?
            </div>
            <div class="modal-footer">
                <form method="post">
                    @method('delete')
                    @csrf
                    <button class="btn btn-default">
                        Remover
                    </button>
                </form>
                <button type="button" class="btn btn-default"
                    data-dismiss="modal"
                >
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>
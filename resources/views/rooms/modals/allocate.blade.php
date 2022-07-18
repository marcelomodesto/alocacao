<div class="modal fade" id="allocateModal">
   <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Alocar Turma</h4>
            </div>
            <form id="allocateForm" action="{{ route('rooms.allocate',$room) }}" method="POST"
            enctype="multipart/form-data"
            >

            @method('patch')
            @csrf

            <input id="room_id" value="{{$room->id}}" type="hidden" disabled>
            <div class="modal-body">
                <div class="row custom-form-group align-items-center">
                    <div class="col-12 col-lg-2 text-lg-right">
                        <label for="periodoId">Turma</label>   
                    </div> 
                    <div class="col-12 col-md-10">

                        <select id="school_class_id" name="school_class_id" class="custom-form-control">
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="btn-allocateModal" class="btn btn-default" type="submit">Alocar</button>
                <button class="btn btn-default" type="button" data-dismiss="modal">Fechar</button>
            </div>
            </form>
        </div>
    </div>
</div>
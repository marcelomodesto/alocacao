<div class="modal fade" id="addClassScheduleModal">
   <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Adicionar horário</h4>
            </div>
            <div class="modal-body">
                <div class="row custom-form-group align-items-center">
                    <div class="col-12 col-lg-6 text-lg-right">
                        <label for="diasmnocp-add">Dia</label>   
                    </div> 
                    <div class="col-12 col-md-4">
                        <select id="diasmnocp-add" class="custom-form-control">
                            <option value="seg">Segunda</option>
                            <option value="ter">Terça</option>
                            <option value="qua">Quarta</option>
                            <option value="qui">Quinta</option>
                            <option value="sex">Sexta</option>
                            <option value="sab">Sábado</option>
                            <option value="dom">Domingo</option>
                        </select>
                    </div>
                </div>
                <div class="row custom-form-group align-items-center">
                    <div class="col-12" id="diasmnocp-add-error-div">
                    </div>
                </div>
                <div class="row custom-form-group align-items-center">
                    <div class="col-12 col-lg-6 text-lg-right">
                        <label for="horent-add">Horário de entrada</label>   
                    </div> 
                    <div class="col-12 col-md-4">
                        <input class="custom-form-control" type="time" name="horent-add"
                            id="horent-add" value=''
                        />
                    </div>
                </div>
                <div class="row custom-form-group align-items-center">
                    <div class="col-12" id="horent-add-error-div">
                    </div>
                </div>
                <div class="row custom-form-group align-items-center">
                    <div class="col-12 col-lg-6 text-lg-right">
                        <label for="horsai-add">Horário de saída</label>   
                    </div> 
                    <div class="col-12 col-md-4">
                        <input class="custom-form-control" type="time" name="horsai-add"
                            id="horsai-add" value=''
                        />
                    </div>
                </div>
                <div class="row custom-form-group align-items-center">
                    <div class="col-12" id="horsai-add-error-div">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="btn-addClassSchedule2" class="btn btn-default" type="button" data-dismiss="modal">Adicionar</button>
                <button class="btn btn-default" type="button" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
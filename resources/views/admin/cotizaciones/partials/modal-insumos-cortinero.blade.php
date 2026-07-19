<div class="modal fade" id="modalInsumosCortinero" tabindex="-1" role="dialog" aria-labelledby="modalInsumosCortineroLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalInsumosCortineroLabel">Insumos del cortinero</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3" id="modal-insumos-producto-nombre"></p>
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="8%">#</th>
                                <th>Insumo</th>
                                <th width="18%" class="text-right">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody id="modal-insumos-tbody"></tbody>
                    </table>
                </div>
                <p class="text-muted small mt-3 mb-0">
                    <i class="fas fa-info-circle mr-1"></i> Listado informativo. No modifica la cotización.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

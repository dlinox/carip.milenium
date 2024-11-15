<div class="modal fade" id="modalAddToProduct" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
        <form id="form_save_to_product" class="modal-content" onsubmit="event.preventDefault()">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddToProductTitle">Agregar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="serie">Producto</label>
                        <small class="text-primary font-weight-bold btn-create-product ws-normal"
                            style="cursor: pointer">[+
                            Nuevo]</small>
                        <select name="product" id="product" class="form-control">
                            <option value=""></option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">
                                    {{ $product->descripcion . ' - S/' . $product->precio_compra }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">El campo no debe estar vacío.</div>
                    </div>

                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="correlativo">Cantidad</label>
                        <div class="input-group">
                            <span class="input-group-text bootstrap-touchspin-down-product" style="cursor: pointer;"><i
                                    class="ti ti-minus me-sm-1"></i></span>
                            <input type="text" class="quantity-counter text-center form-control" value="1"
                                name="cantidad">
                            <span class="input-group-text bootstrap-touchspin-up-product" style="cursor: pointer;"><i
                                    class="ti ti-plus me-sm-1"></i></span>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label" for="precio">Precio <small style="font-size: 11px;"
                                class="text-info">(En base al precio de compra)</small></label>
                        <div class="input-group">
                            <span class="input-group-text" id="precio">S/</span>
                            <input type="text" id="precio" class="form-control" name="precio">
                            <div class="invalid-feedback">El campo no debe estar vacío.</div>
                        </div>
                    </div>

                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button class="btn btn-primary btn-save">
                            <span class="text-save">Guardar</span>
                            <span class="spinner-border spinner-border-sm me-1 d-none text-saving" role="status"
                                aria-hidden="true"></span>
                            <span class="text-saving d-none">Guardando...</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Refer & Earn Modal -->
<div class="modal fade" id="modalSuccess" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-simple modal-refer-and-earn" style="width: 28rem;">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body" style="padding: 0rem;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Compra <span class="correlative"></span></h3>
                </div>
                <div class="row">
                    <div class="col-12 col-lg-6 px-4">
                        <div class="d-flex justify-content-center mb-4 btn-print" style="cursor: pointer;">
                            <div class="modal-refer-and-earn-step bg-label-primary">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    width="45" height="40" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-printer">
                                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                    <path
                                        d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2">
                                    </path>
                                    <rect x="6" y="14" width="12" height="8"></rect>
                                </svg>
                            </div>
                        </div>
                        <div class="text-center btn-print" style="cursor: pointer;">
                            <h5>A4</h5>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 px-4 btn-download">
                        <div class="d-flex justify-content-center mb-4 btn-download" style="cursor: pointer;">
                            <div class="modal-refer-and-earn-step bg-label-primary">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    width="45" height="40" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-download">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                            </div>
                        </div>
                        <div class="text-center btn-download" style="cursor: pointer;">
                            <h5>Descargar</h5>
                        </div>
                    </div>
                </div>
                <hr style="margin-bottom: 1rem !important;" />
                <form class="row g-3" onsubmit="return false">
                    <div class="col-lg-12">
                        <div class="input-group">
                            <span class="input-group-text" id="input__phone">+51</span>
                            <input type="text" id="modalRnFEmail" class="form-control" name="input__phone"
                                placeholder="Ejemplo: +51 987654321" aria-label="Ejemplo: 987654321">
                            <span class="input-group-text btn-whatsapp" style="cursor: pointer;">
                                <span class="text-send"><i class="fa-brands fa-whatsapp fa-xs align-middle" style="font-size: 13px;"></i><span style="margin-left: 3px;">Enviar</span></span>
                                <span class="spinner-border spinner-border-sm text-sending d-none" role="status"
                                    aria-hidden="true"></span>
                                <span class="ml-25 align-middle text-sending d-none">Enviando...</span>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--/ Refer & Earn Modal -->

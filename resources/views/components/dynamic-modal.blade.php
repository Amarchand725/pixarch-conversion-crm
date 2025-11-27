<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content p-3 p-md-5">
            <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <h3 class="mb-2">{{ $title ?? '' }}</h3>
                </div>
                <form method="POST" 
                    action="{{ $action ?? '#' }}"
                    id="{{ $formId ?? 'dynamic-modal-form' }}"
                    class="pt-0 fv-plugins-bootstrap5 fv-plugins-framework submitBtnWithFileUpload" 
                    data-modal-id="{{ $id }}" 
                    enctype="multipart/form-data">
                    @csrf
                    
                    {{-- If $method is provided and not POST, Blade will render method spoofing --}}
                    @isset($method)
                        @if(strtoupper($method) !== 'POST')
                        @method($method)
                        @endif
                    @endisset

                    {{-- Slot: page will inject the fields here --}}
                    <div id="{{ $fieldsContainerId ?? 'dynamic-modal-fields' }}">
                        {{ $slot }}
                    </div>

                    <div class="col-12 mt-3 action-btn">
                        <div class="demo-inline-spacing sub-btn">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                            <button type="reset" class="btn btn-label-secondary btn-reset" data-bs-dismiss="modal" aria-label="Close">
                                Cancel
                            </button>
                        </div>
                        <div class="demo-inline-spacing loading-btn" style="display: none;">
                            <button class="btn btn-primary waves-effect waves-light" type="button" disabled="">
                            <span class="spinner-border me-1" role="status" aria-hidden="true"></span>
                            Loading...
                            </button>
                            <button type="reset" class="btn btn-label-secondary btn-reset" data-bs-dismiss="modal" aria-label="Close">
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
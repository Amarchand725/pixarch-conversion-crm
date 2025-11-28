<x-app-layout>
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card-header">
                            <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Home /</span> {{ $title }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Users List Table -->
            <div class="row g-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-datatable">
                            <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                                <div class="container">
                                    <!-- Add role form -->
                                    <form action="{{ route('back-office.roles.update', $role->id) }}" method="POST" class="pt-0 fv-plugins-bootstrap5 fv-plugins-framework" id="create-form">
                                        @csrf
                                        @method('PUT')

                                        <div class="row mt-4">
                                            <h5>{{ $title }}</h5>
                                            <div class="mb-3 fv-plugins-icon-container col-12">
                                                <label class="form-label" for="name">Role <span class="text-danger">*</span></label>
                                                <input type="text" readonly value="{{ $role->name }}" class="form-control" id="name" placeholder="Enter role" name="name">
                                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                                <span id="name_error" class="text-danger error"></span>
                                            </div>
                                            <div class="col-12 mb-3 action-btn d-flex justify-content-end">
                                                <div class="demo-inline-spacing sub-btn">
                                                    <button type="submit" class="btn btn-primary me-sm-3 me-1 submitBtn">Submit</button>
                                                    <a href="{{ route('back-office.roles.index') }}" class="btn btn-label-secondary btn-reset"> Cancel</a>
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
                                        </div>
                                        <div class="col-12">
                                            <!-- Permission table -->
                                            <x-sub-permissions :permissions="$permissions" :role="$role" />
                                            <!-- Permission table -->
                                        </div>

                                        <div class="col-12 mt-3 action-btn">
                                            <div class="demo-inline-spacing sub-btn">
                                                <button type="submit" class="btn btn-primary me-sm-3 me-1 submitBtn">Submit</button>
                                                <a href="{{ route('back-office.roles.index') }}" class="btn btn-label-secondary btn-reset"> Cancel</a>
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
                </div>
            </div>
        </div>
    </div>
    @push('js')
        <script src="{{ asset('back-office/assets/custom/check-permission-checkbox.js') }}"></script>
    @endpush
</x-app-layout>
<x-app-layout>
    @section('title', ($title ?? '').' - '. config('app.name', 'Laravel'))
    @push('css')
        <style>
            .status-row-wrapper {
                width: 100%;
            }

            .status-row {
                display: flex;
                white-space: nowrap;
                overflow-x: auto; /* horizontal scroll */
                padding-bottom: 10px;
            }

            .status-card {
                flex-shrink: 0;
                min-width: 33.333%; /* col-md-4 width for 3 visible cards */
                max-width: 33.333%;
                margin-right: 1rem;
            }

            .task-column {
                max-height: 300px; /* vertical scroll if many items */
                overflow-y: auto;
            }

            .task-card {
                cursor: move;
            }

            .sortable-ghost {
                opacity: 0.4;
            }
        </style>
    @endpush
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">{{ ($title ?? ''). ' /' }} </span> Drag &amp; Drop</h4>

        <!-- Cards Draggable -->
        <div class="row mb-4" id="sortable-cards">
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card drag-item cursor-move mb-lg-0 mb-4">
                <div class="card-body text-center">
                    <h2>
                    <i class="ti ti-shopping-cart text-success display-6"></i>
                    </h2>
                    <h4>Monthly Sales</h4>
                    <h5>2362</h5>
                </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card drag-item cursor-move mb-lg-0 mb-4">
                <div class="card-body text-center">
                    <h2>
                    <i class="ti ti-world text-info display-6"></i>
                    </h2>
                    <h4>Monthly Visits</h4>
                    <h5>687,123</h5>
                </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card drag-item cursor-move mb-lg-0 mb-4">
                <div class="card-body text-center">
                    <h2>
                    <i class="ti ti-gift text-danger display-6"></i>
                    </h2>
                    <h4>Products</h4>
                    <h5>985</h5>
                </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card drag-item cursor-move mb-lg-0 mb-4">
                <div class="card-body text-center">
                    <h2>
                    <i class="ti ti-user text-primary display-6"></i>
                    </h2>
                    <h4>Users</h4>
                    <h5>105,652</h5>
                </div>
                </div>
            </div>
        </div>
        <!-- /Cards Draggable ends -->

        <div class="status-row-wrapper">
            <div class="status-row d-flex overflow-auto pb-2">

                <!-- Card 1 -->
                <div class="status-card card flex-shrink-0 me-3" style="min-width: 33.333%;">
                    <div class="card-header text-center">
                        <h5 class="mb-0">Status 1</h5>
                        <small class="text-muted">Total: 5</small>
                    </div>
                    <ul class="list-group list-group-flush task-column">
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 1</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/1.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 2</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/2.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 3</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/3.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 4</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/4.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 5</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/5.png') }}" width="32">
                        </li>
                    </ul>
                </div>

                <!-- Card 2 -->
                <div class="status-card card flex-shrink-0 me-3" style="min-width: 33.333%;">
                    <div class="card-header text-center">
                        <h5 class="mb-0">Status 2</h5>
                        <small class="text-muted">Total: 4</small>
                    </div>
                    <ul class="list-group list-group-flush task-column">
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 6</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/1.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 7</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/2.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 8</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/3.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 9</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/4.png') }}" width="32">
                        </li>
                    </ul>
                </div>

                <!-- Card 3 -->
                <div class="status-card card flex-shrink-0 me-3" style="min-width: 33.333%;">
                    <div class="card-header text-center">
                        <h5 class="mb-0">Status 3</h5>
                        <small class="text-muted">Total: 5</small>
                    </div>
                    <ul class="list-group list-group-flush task-column">
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 10</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/1.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 11</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/2.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 12</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/3.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 13</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/4.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 14</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/5.png') }}" width="32">
                        </li>
                    </ul>
                </div>

                <!-- Card 1 -->
                <div class="status-card card flex-shrink-0 me-3" style="min-width: 33.333%;">
                    <div class="card-header text-center">
                        <h5 class="mb-0">Status 1</h5>
                        <small class="text-muted">Total: 5</small>
                    </div>
                    <ul class="list-group list-group-flush task-column">
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 1</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/1.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 2</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/2.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 3</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/3.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 4</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/4.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 5</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/5.png') }}" width="32">
                        </li>
                    </ul>
                </div>

                <!-- Card 2 -->
                <div class="status-card card flex-shrink-0 me-3" style="min-width: 33.333%;">
                    <div class="card-header text-center">
                        <h5 class="mb-0">Status 2</h5>
                        <small class="text-muted">Total: 4</small>
                    </div>
                    <ul class="list-group list-group-flush task-column">
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 6</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/1.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 7</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/2.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 8</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/3.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 9</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/4.png') }}" width="32">
                        </li>
                    </ul>
                </div>

                <!-- Card 3 -->
                <div class="status-card card flex-shrink-0 me-3" style="min-width: 33.333%;">
                    <div class="card-header text-center">
                        <h5 class="mb-0">Status 3</h5>
                        <small class="text-muted">Total: 5</small>
                    </div>
                    <ul class="list-group list-group-flush task-column">
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 10</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/1.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 11</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/2.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 12</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/3.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 13</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/4.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 14</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/5.png') }}" width="32">
                        </li>
                    </ul>
                </div>

                <!-- Card 1 -->
                <div class="status-card card flex-shrink-0 me-3" style="min-width: 33.333%;">
                    <div class="card-header text-center">
                        <h5 class="mb-0">Status 1</h5>
                        <small class="text-muted">Total: 5</small>
                    </div>
                    <ul class="list-group list-group-flush task-column">
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 1</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/1.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 2</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/2.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 3</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/3.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 4</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/4.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 5</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/5.png') }}" width="32">
                        </li>
                    </ul>
                </div>

                <!-- Card 2 -->
                <div class="status-card card flex-shrink-0 me-3" style="min-width: 33.333%;">
                    <div class="card-header text-center">
                        <h5 class="mb-0">Status 2</h5>
                        <small class="text-muted">Total: 4</small>
                    </div>
                    <ul class="list-group list-group-flush task-column">
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 6</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/1.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 7</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/2.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 8</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/3.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 9</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/4.png') }}" width="32">
                        </li>
                    </ul>
                </div>

                <!-- Card 3 -->
                <div class="status-card card flex-shrink-0 me-3" style="min-width: 33.333%;">
                    <div class="card-header text-center">
                        <h5 class="mb-0">Status 3</h5>
                        <small class="text-muted">Total: 5</small>
                    </div>
                    <ul class="list-group list-group-flush task-column">
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 10</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/1.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 11</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/2.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 12</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/3.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 13</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/4.png') }}" width="32">
                        </li>
                        <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                            <span>Lead 14</span>
                            <img class="rounded-circle" src="{{ asset('backend/assets/img/avatars/5.png') }}" width="32">
                        </li>
                    </ul>
                </div>

                <!-- Repeat similarly for Card 4 → Card 9 -->
                <!-- Change the header text and lead items as needed -->

            </div>
        </div>

        <!-- /Multiple Lists Draggable ends -->
    </div>
    @push('js')
        <!-- Page JS -->
        <script src="{{ asset('backend') }}/assets/vendor/libs/sortablejs/sortable.js"></script>
        <script src="{{ asset('backend') }}/assets/js/extended-ui-drag-and-drop.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                document.querySelectorAll(".task-column").forEach(column => {
                    new Sortable(column, {
                        group: "leadGroup",
                        animation: 150,
                        ghostClass: "sortable-ghost",
                        pull: true,
                        put: true
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
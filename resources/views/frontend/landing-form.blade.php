@extends('frontend.layouts.app')
@section('title', ($title ?? '').' - '. config('app.name', '100Keys UAE'))

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <!-- Pricing Plans -->
            <div class="pb-sm-5 pb-2 rounded-top">
                <div class="container py-5">
                    <!-- Application Logo & Name -->
                    <div class="app-brand text-center mb-4 mt-2">
                        <a href="{{ route('lead-capture.public', $model->uuid) }}" class="d-inline-flex flex-column align-items-center text-decoration-none">
                            <!-- Logo with subtle animation -->
                            <span class="app-brand-logo mb-2 animate-bounce">
                                <x-application-logo class="w-24 h-24 fill-current text-primary" />
                            </span>

                            <!-- App Name -->
                            <span class="app-brand-text fw-bold fs-4 text-dark">
                                {{ config('app.name', 'Laravel') }}
                            </span>
                        </a>
                    </div>

                    <h2 class="text-center mb-3 mt-0 mt-md-4 fw-semibold">
                        {{ $model->name }}
                    </h2>
                    <p class="text-center">
                        {{ $model->description ?? '' }}
                    </p>
                    <div class="row mx-0 gy-3 px-lg-5">
                        <form action="{{ route('lead-capture.store', $model->uuid) }}" method="POST" class="pt-0 fv-plugins-bootstrap5 fv-plugins-framework" id="create-form" data-modal-id="create-pop-up-modal-for-file" enctype="multipart/form-data">
                            @csrf

                            @foreach($model->fields as $field)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-semibold" for="{{ $field->name }}">
                                        {{ ucfirst($field->label) }}
                                        @if($field->required)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>

                                    @php
                                        $value = old($field->name, $field->value ?? '');
                                    @endphp

                                    @if(in_array($field->type, ['text', 'email', 'number']))
                                        <input
                                            type="{{ $field->type }}"
                                            class="form-control"
                                            id="{{ $field->name }}"
                                            name="{{ $field->name }}"
                                            placeholder="{{ $field->placeholder ?? '' }}"
                                            value="{{ $value }}"
                                        />
                                    @elseif($field->type === 'tel')
                                        <input
                                            type="{{ $field->type }}"
                                            class="form-control phoneNumber"
                                            id="{{ $field->name }}"
                                            name="{{ $field->name }}"
                                            value="{{ $value }}"
                                            placeholder="(999) - 12345678"
                                        />
                                    @elseif($field->type === 'textarea')
                                        <textarea
                                            class="form-control"
                                            id="{{ $field->name }}"
                                            name="{{ $field->name }}"
                                            placeholder="{{ $field->placeholder ?? '' }}"
                                        >{{ $value }}</textarea>
                                    @elseif($field->type === 'file')
                                        <input
                                            type="file"
                                            class="form-control"
                                            id="{{ $field->name }}"
                                            name="{{ $field->name }}"
                                        />
                                    @elseif($field->type === 'select')
                                        <select
                                            name="{{ $field->name }}"
                                            id="{{ $field->name }}"
                                            class="form-select select2"
                                        >
                                            <option value="">Select {{ $field->label }}</option>
                                            @if(!empty($field->options))
                                                @foreach(explode(',', $field->options) as $option)
                                                    <option value="{{ trim($option) }}" {{ trim($option) == $value ? 'selected' : '' }}>
                                                        {{ trim($option) }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    @endif
                                </div>
                            @endforeach

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
            <!--/ Pricing Plans -->
            <!-- FAQS -->
            <div class="pricing-faqs bg-alt-pricing rounded-bottom">
                <div class="container py-5 px-lg-5">
                <div class="row mt-0 mt-md-4">
                    <div class="col-12 text-center mb-4">
                    <h2 class="mb-2">FAQs</h2>
                    <p class="mb-2">Let us help answer the most common questions.</p>
                    </div>
                </div>
                <div class="row mx-4">
                    <div class="col-12">
                        <div id="faq" class="accordion accordion-without-arrow">
                            @if($faqs->count())
                                <div class="accordion" id="faqAccordion">
                                    @foreach($faqs as $index => $faq)
                                        <div class="card accordion-item">
                                            <h6 class="accordion-header" id="heading-{{ $index }}">
                                                <button
                                                    class="accordion-button {{ $index != 0 ? 'collapsed' : '' }}"
                                                    type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#collapse-{{ $index }}"
                                                    aria-expanded="{{ $index == 0 ? 'true' : 'false' }}"
                                                    aria-controls="collapse-{{ $index }}"
                                                >
                                                    {{ $faq->question }}
                                                </button>
                                            </h6>

                                            <div
                                                id="collapse-{{ $index }}"
                                                class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}"
                                                aria-labelledby="heading-{{ $index }}"
                                                data-bs-parent="#faqAccordion"
                                            >
                                                <div class="accordion-body">
                                                    {!! nl2br(e($faq->answer)) !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-center text-muted">No FAQs available at the moment.</p>
                            @endif
                        </div>
                    </div>
                </div>
                </div>
            </div>
            <!--/ FAQS -->
        </div>
    </div>
@endsection
@push('js')
    <script>
        $('select').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent(),
            });
        });
    </script>
@endpush
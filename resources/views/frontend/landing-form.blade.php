@extends('frontend.layouts.app')
@section('title', ($title ?? '').' - '. config('app.name', '100Keys UAE'))
@push('css')
    <style>
        .btn-primary {
            background: linear-gradient(90deg, #5a8dee, #36a3f7);
            border: none;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .form-control:focus {
            border-color: #36a3f7;
            box-shadow: 0 0 0 0.2rem rgba(54, 163, 247, 0.25);
        }
        .card:hover {
            transform: translateY(-2px);
            transition: transform 0.2s;
        }
        .g-recaptcha {
            margin: 15px 0;
        }
    </style>
@endpush
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="pb-5 rounded-top bg-light">
                <div class="container py-5">

                    <!-- Application Logo & Name -->
                    <div class="text-center mb-5">
                        <a href="{{ route('lead-capture.public', $model->uuid) }}" class="d-inline-flex flex-column align-items-center text-decoration-none">
                            <span class="app-brand-logo mb-3 animate-bounce">
                                <x-application-logo class="w-28 h-28 fill-current text-primary" />
                            </span>
                            <span class="fw-bold fs-3 text-dark">{{ config('app.name', 'Laravel') }}</span>
                        </a>
                    </div>

                    <!-- Form Card -->
                    <div class="card shadow-sm border-0 p-4 mx-auto" style="max-width: 900px;">
                        <h2 class="text-center fw-semibold mb-3">{{ $model->name }}</h2>
                        <p class="text-center text-muted mb-4">{{ $model->description ?? '' }}</p>

                        <form action="{{ route('lead-capture.store', $model->uuid) }}" method="POST" class="row g-3 needs-validation" id="create-form" enctype="multipart/form-data" novalidate>
                            @csrf

                            <input type="hidden" name="captcha_required" value="1">
                            <!-- Default Fields -->
                            @foreach (['name','phone','email','budget'] as $fieldKey)
                                @php
                                    $label = ucfirst($fieldKey);
                                    $type = $fieldKey === 'budget' ? 'number' : ($fieldKey === 'email' ? 'email' : ($fieldKey === 'phone' ? 'tel' : 'text'));
                                    $placeholder = "Enter $label";
                                    $value = old($fieldKey);
                                    if($label === 'Phone') {
                                        $placeholder = "Enter phone number e.g +14155552671";
                                    }
                                @endphp

                                <div class="col-md-6">
                                    <label for="{{ $fieldKey }}" class="form-label fw-semibold">
                                        {{ $label }}
                                        @if($fieldKey === 'name' || $fieldKey === 'phone') <span class="text-danger">*</span> @endif
                                    </label>

                                    @if($type === 'tel')
                                        <input
                                            type="tel"
                                            id="{{ $fieldKey }}"
                                            name="{{ $fieldKey }}"
                                            class="form-control shadow-sm phone-input"
                                            placeholder="{{ $placeholder }}"
                                            value="{{ $value }}"
                                            @if($fieldKey==='name') required @endif
                                        />
                                        <span id="{{ $fieldKey }}_error" class="text-danger error">{{ $errors->first($fieldKey) }}</span>
                                    @else
                                        <input
                                            type="{{ $type }}"
                                            id="{{ $fieldKey }}"
                                            name="{{ $fieldKey }}"
                                            class="form-control shadow-sm"
                                            placeholder="{{ $placeholder }}"
                                            value="{{ $value }}"
                                            @if($fieldKey==='name') required @endif
                                        />
                                        <span id="{{ $fieldKey }}_error" class="text-danger error">{{ $errors->first($fieldKey) }}</span>
                                    @endif

                                    <div class="invalid-feedback">
                                        {{ $errors->first($fieldKey) ?? "Please enter $label." }}
                                    </div>
                                </div>
                            @endforeach

                            <!-- Dynamic Fields -->
                            @foreach($model->fields as $field)
                                @php
                                    $value = old("fields.{$field->name}", $field->value ?? '');
                                @endphp
                                <div class="col-md-6">
                                    <label for="{{ $field->name }}" class="form-label fw-semibold">
                                        {{ ucfirst($field->label) }}
                                        @if($field->required)<span class="text-danger">*</span>@endif
                                    </label>

                                    @if(in_array($field->type, ['text','email','number']))
                                        <input
                                            type="{{ $field->type }}"
                                            id="{{ $field->name }}"
                                            name="fields[{{ $field->name }}]"
                                            class="form-control shadow-sm"
                                            placeholder="{{ $field->placeholder ?? '' }}"
                                            value="{{ $value }}"
                                            @if($field->required) required @endif
                                        />
                                    @elseif($field->type==='tel')
                                        <input
                                            type="tel"
                                            id="{{ $field->name }}"
                                            name="fields[{{ $field->name }}]"
                                            class="form-control phone-input shadow-sm"
                                            placeholder="Enter phone number e.g +14155552671"
                                            value="{{ $value }}"
                                            @if($field->required) required @endif
                                        />
                                    @elseif($field->type==='textarea')
                                        <textarea
                                            id="{{ $field->name }}"
                                            name="fields[{{ $field->name }}]"
                                            class="form-control shadow-sm"
                                            placeholder="{{ $field->placeholder ?? '' }}"
                                            @if($field->required) required @endif
                                        >{{ $value }}</textarea>
                                    @elseif($field->type==='file')
                                        <input
                                            type="file"
                                            id="{{ $field->name }}"
                                            name="fields[{{ $field->name }}]"
                                            class="form-control shadow-sm"
                                            onchange="imagePreview(event)"
                                        />

                                        <!-- Preview wrapper -->
                                        <div class="mb-3">
                                            <img id="image-preview" 
                                                alt="Image Preview" 
                                                class="img-thumbnail rounded-circle" 
                                                style="width: 80px; height: 80px; object-fit: cover; display: none;"
                                            >
                                        </div>
                                    @elseif($field->type==='select')
                                        <select
                                            id="{{ $field->name }}"
                                            name="fields[{{ $field->name }}]"
                                            class="form-select form-select-lg shadow-sm select2"
                                            @if($field->required) required @endif
                                        >
                                            <option value="">Select {{ $field->label }}</option>

                                            @if(!empty($field->options))
                                                @php
                                                    $options = is_array($field->options) ? $field->options : explode(',', $field->options);
                                                @endphp

                                                @foreach($options as $option)
                                                    <option value="{{ trim($option) }}" {{ trim($option)==$value ? 'selected' : '' }}>
                                                        {{ trim($option) }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    @endif

                                    <span id="{{ $field->name }}_error" class="text-danger error">
                                        {{ $errors->first("fields.{$field->name}") }}
                                    </span>

                                    <div class="invalid-feedback">Please enter {{ strtolower($field->label) }}.</div>
                                </div>
                            @endforeach

                            <!-- CAPTCHA -->
                            <div class="g-recaptcha"
                                data-sitekey="{{ config('recaptcha.site_key') }}">
                            </div>
                            <!-- Display CAPTCHA validation error -->
                            @error('g-recaptcha-response')
                                <div class="text-danger mt-1">
                                    {{ $message }}
                                </div>
                            @enderror

                            <!-- Submit Buttons -->
                            <div class="col-12 mt-4 d-flex flex-wrap justify-content-start gap-3 align-items-center">
                                <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm">Submit</button>
                                <button type="reset" class="btn btn-outline-secondary btn-lg px-4">Cancel</button>
                                <div class="spinner-border text-primary ms-2 d-none" id="loading-spinner" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!--/ Pricing Plans -->
            <!-- FAQS -->
            @if(count($faqs) > 0)
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
            @endif
            <!--/ FAQS -->
        </div>
    </div>
@endsection
@push('js')
    <!-- Load reCAPTCHA API -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        $('select').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent(),
            });
        });

        function imagePreview(event) {
            const input = event.target;
            const preview = document.getElementById('image-preview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block'; // show preview
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                // Reset preview if no file selected
                preview.src = '';
                preview.style.display = 'none';
            }
        }
    </script>
@endpush
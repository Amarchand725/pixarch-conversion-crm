@extends('frontend.layouts.app')
@section('title', ($title ?? '').' - '. config('app.name', '100Keys UAE'))

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <!-- Pricing Plans -->
            <div class="pb-sm-5 pb-2 rounded-top">
                <div class="container py-5">
                    <!-- Application Logo -->
                    <div class="app-brand justify-content-center mb-4 mt-2">
                        <a href="{{ route('lead-capture.public') }}" class="app-brand-link">
                            <span class="app-brand-logo demo">
                                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                            </span>
                            <span class="app-brand-text demo text-body fw-bold ms-1">{{ config('app.name', 'Laravel') }}</span>
                        </a>
                    </div>
                    <h2 class="text-center mb-2 mt-0 mt-md-4">{{ $model->name }}</h2>
                    <p class="text-center">
                        {{ $model->description ?? '' }}
                    </p>
                    <div class="row mx-0 gy-3 px-lg-5">
                        <form method="POST" class="pt-0 fv-plugins-bootstrap5 fv-plugins-framework submitBtnWithFileUpload" id="create-form" data-modal-id="create-pop-up-modal-for-file" enctype="multipart/form-data">
                            @csrf
        
                            @foreach($model->fields as $field)
                                <div class="col-12 mb-3">
                                    <label class="form-label" for="basic-default-{{ $field->id }}">
                                        {{ ucfirst($field->label) }} 
                                        @if($field->required)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    @if($field->type === 'text')    
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="basic-default-{{ $field->id }}"
                                            name="field_{{ $field->id }}"
                                            placeholder="{{ $field->placeholder ?? '' }}"
                                        /> 
                                    @elseif($field->type === 'email')
                                        <input
                                            type="email"
                                            class="form-control"
                                            id="basic-default-{{ $field->id }}"
                                            name="field_{{ $field->id }}"
                                            placeholder="{{ $field->placeholder ?? '' }}"
                                        />
                                    @elseif($field->type === 'textarea')
                                        <textarea
                                            class="form-control"
                                            id="basic-default-{{ $field->id }}"
                                            name="field_{{ $field->id }}"
                                            placeholder="{{ $field->placeholder ?? '' }}"
                                        ></textarea>
                                    @elseif($field->type === 'file')
                                        <input
                                            type="file"
                                            class="form-control"
                                            id="basic-default-{{ $field->id }}"
                                            name="field_{{ $field->id }}"
                                        />
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
            {{-- <div class="pricing-faqs bg-alt-pricing rounded-bottom">
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
                        <div class="card accordion-item">
                        <h6 class="accordion-header">
                            <button
                            class="accordion-button"
                            type="button"
                            data-bs-toggle="collapse"
                            aria-expanded="true"
                            data-bs-target="#faq-1"
                            aria-controls="faq-1"
                            >
                            What counts towards the 100 responses limit?
                            </button>
                        </h6>

                        <div id="faq-1" class="accordion-collapse collapse show" data-bs-parent="#faq">
                            <div class="accordion-body">
                            We count all responses submitted through all your forms in a month. If you already
                            received 100 responses this month, you won’t be able to receive any more of them until
                            next month when the counter resets.
                            </div>
                        </div>
                        </div>

                        <div class="card accordion-item">
                        <h6 class="accordion-header">
                            <button
                            class="accordion-button collapsed"
                            data-bs-toggle="collapse"
                            data-bs-target="#faq-2"
                            aria-expanded="false"
                            aria-controls="faq-2"
                            >
                            How do you process payments?
                            </button>
                        </h6>
                        <div id="faq-2" class="accordion-collapse collapse" data-bs-parent="#faq">
                            <div class="accordion-body">
                            We accept Visa®, MasterCard®, American Express®, and PayPal®. So you can be confident
                            that your credit card information will be kept safe and secure.
                            </div>
                        </div>
                        </div>

                        <div class="card accordion-item">
                        <h6 class="accordion-header">
                            <button
                            class="accordion-button collapsed"
                            data-bs-toggle="collapse"
                            data-bs-target="#faq-3"
                            aria-expanded="false"
                            aria-controls="faq-3"
                            >
                            What payment methods do you accept?
                            </button>
                        </h6>
                        <div id="faq-3" class="accordion-collapse collapse" data-bs-parent="#faq">
                            <div class="accordion-body">2Checkout accepts all types of credit and debit cards.</div>
                        </div>
                        </div>

                        <div class="card accordion-item">
                        <h6 class="accordion-header">
                            <button
                            class="accordion-button collapsed"
                            data-bs-toggle="collapse"
                            data-bs-target="#faq-4"
                            aria-expanded="false"
                            aria-controls="faq-4"
                            >
                            Do you have a money-back guarantee?
                            </button>
                        </h6>
                        <div id="faq-4" class="accordion-collapse collapse" data-bs-parent="#faq">
                            <div class="accordion-body">
                            Yes. You may request a refund within 30 days of your purchase without any additional
                            explanations.
                            </div>
                        </div>
                        </div>

                        <div class="card accordion-item mb-0 mb-md-4">
                        <h6 class="accordion-header">
                            <button
                            class="accordion-button collapsed"
                            data-bs-toggle="collapse"
                            data-bs-target="#faq-5"
                            aria-expanded="false"
                            aria-controls="faq-5"
                            >
                            I have more questions. Where can I get help?
                            </button>
                        </h6>
                        <div id="faq-5" class="accordion-collapse collapse" data-bs-parent="#faq">
                            <div class="accordion-body">
                            Please <a href="javascript:void(0);">contact</a> us if you have any other questions or
                            concerns. We’re here to help!
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
            </div> --}}
            <!--/ FAQS -->
        </div>
    </div>
@endsection
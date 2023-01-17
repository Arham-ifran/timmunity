<div class="modal fade" id="send-proforma-invoice" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <form action="{{ route('admin.quotation.send.email') }}" method="POST"  enctype="multipart/form-data">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title col-md-9 pl-0" id="exampleModalLongTitle">{{ __('Send By Email') }}</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fa fa-times"></i></span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{-- <div class="popup-box clearfix"> --}}
                        <div class="row">
                            <div class="form-group">
                                <h4>{{ __('Recipents') }}:</h4>
                                <input type="hidden" name="send_email[type]" value="1">
                                <input type="hidden" name="send_email[id]" value="{{ @$model->id }}">
                                <select class="form-control select2 send-email-recepient" multiple="multiple" name="send_email[email_recipients]"
                                    data-placeholder="Add Contacts to notify" style="width: 100%">
                                    @foreach ($customer as $cust)
                                        <option value="{{ $cust->id }}" @if( @$model->customer_id == $cust->id ) selected="selected" @endif>{{ $cust->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <h4>{{ __('Subject') }}:</h4>
                                <input class="form-control" type="text" name="send_email[email_subject]" value="@isset($model){{ __('Order Pro-forma Quotation Ref') }}( S{{ str_pad($model->id, 5, '0', STR_PAD_LEFT) }} @endisset )" placeholder="">
                            </div>
                            <br>
                            <div class="form-group">
                                <textarea class="textarea" name="send_email[email_body]" placeholder="Place some text here">
                                    @isset($model)
                                        {{ __('Hello') }}&nbsp;
                                        <strong>{{ $model->customer->name }}</strong>,<br>
                                        {{ __('Your pro-forma quotation') }}
                                        <strong>S{{ str_pad($model->id, 5, '0', STR_PAD_LEFT) }}</strong> {{ __('amounting in') }}
                                        <strong>{{ $model->currency_symbol }} {{ currency_format(str_replace(",","",$model->total)* $model->exchange_rate,'','',1) }} {{ $model->currency }}</strong> {{ __('is ready for review.') }}<br>{{ __('Do not hesitate to contact us if you have any questions.') }}@endisset
                                </textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="file-format">
                                        <label>{{ __('Attach a file') }}</label>
                                        <input type="file" multiple="multiple" class="custom-file-input" name="email_attachement[]"
                                            id="inputGroupFile02">
                                    </div>
                                    @isset($model)
                                    <div class="row mt-3">
                                        <div class="col-xs-2 col-sm-2 col-md-2 pl-0 pr-0 text-center">
                                            <a target="_blank" href="{{ asset('/storage/quotations/S'.str_pad($model->id, 5, '0', STR_PAD_LEFT).'.pdf' ) }}?download=true" title="S{{ str_pad($model->id, 5, '0', STR_PAD_LEFT) }}.pdf" aria-label="Download">
                                                <span class="o_image o_hover" data-mimetype="application/pdf" data-ext="pdf" role="img"></span>
                                            </a>
                                        </div>

                                        <div class="col-md-8 pl-0">
                                            <p class="mb-0"><a target="_blank" href="{{ asset('/storage/quotations/S'.str_pad($model->id, 5, '0', STR_PAD_LEFT).'.pdf' ) }}?download=true" title="Download S{{ str_pad($model->id, 5, '0', STR_PAD_LEFT) }}.pdf">S{{ str_pad($model->id, 5, '0', STR_PAD_LEFT) }}.pdf</a></p>
                                            <p class="mb-0"><a target="_blank" href="{{ asset('/storage/quotations/S'.str_pad($model->id, 5, '0', STR_PAD_LEFT).'.pdf' ) }}?download=true" title="Download S{{ str_pad($model->id, 5, '0', STR_PAD_LEFT) }}.pdf"><b>pdf</b></a></p>
                                        </div>
                                    </div>
                                    @endisset

                                </div>
                                {{-- <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Use Template') }}</label>
                                        <select class="form-control select-email-template">
                                            <option>---{{ __('Select a template')}}---</option>
                                            @foreach($email_templates as $e_t)
                                                <option value="{{ $e_t->id }}">{{ $e_t->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 text-left">
                                <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">{{ __('Cancel') }}</button>
                                <button type="button" class="btn btn-primary send-proforma-email-btn-submit">{{ __('Send') }}</button>
                            </div>
                            <div class="col-md-6 pull-right">
                                {{-- <button type="button" class="btn btn-primary">Save as a new
                                    Template</button> --}}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">


                    </div>
                </div>
            </div>
        </form>
    </div>

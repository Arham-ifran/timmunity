<div class="modal fade in" id="contact-model" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true"
    data-keyboard="false" data-backdrop="static">
    <div class="contact-big-model modal-dialog modal-dialog-centered"
        role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="col-sm-8" id="exampleModalLongTitle">{{ __('Open: Contact Address') }}</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span>
                </button>
            </div>
            <form class="model-form-validate" id="contact-address-form"  @if($action == 'edit') action="{{ route('admin.contact-address.update', @$model->id) }}"   @endif enctype="multipart/form-data">
            <div class="modal-body ">
                <!-- Form Start Here Contact Address  -->

                    @csrf
                    @if($action == 'edit')
                        @method('PATCH')
                        <input type="hidden" name="contact_id" id="contact_id" value="">
                        <input type="hidden" name="id" id="hidden_id" value="{{ @$model->id }}">
                    @endif

                    <input type="hidden" name="action" value="{!! $action !!}">
                    <div class="col-md-12 tab-form">
                        <div class="clearfix mt-2">
                            <div class="col-md-12 pl-0">
                                <div class="col-md-12 pt-1 customer-radio-button pb-3">
                                    <input type="radio" class="update-field-visibility-d" id="contact-address-radio" name="type" value="0"  @if (isset($model->type) && $model->type == 0) checked @endif />
                                    <label for="contact-address-radio">{{ __('Contact') }}</label>
                                    <input type="radio" class="update-field-visibility-d" id="invoice-address" name="type" value="1" @if (isset($model->type) && $model->type == 1) checked @endif />
                                    <label for="invoice-address">{{ __('Invoice Address') }}</label>
                                    <input type="radio" class="update-field-visibility-d" id="delivery-address" name="type" value="2" @if (isset($model->type) && $model->type == 2) checked @endif />
                                    <label for="delivery-address">{{ __('Delivery Address') }}</label>
                                    <input type="radio" class="update-field-visibility-d" id="other-address" name="type" value="3" @if (isset($model->type) && $model->type == 3) checked @endif />
                                    <label for="other-address">{{ __('Other Address') }}</label>
                                    <input type="radio" class="update-field-visibility-d" id="private-address" name="type" value="4" @if (isset($model->type) && $model->type == 4) checked @endif />
                                    <label for="private-address">{{ __('Private Address') }}</label>
                                </div>
                            </div>
                            <div class="modal-form-input-val">

                            <div class="form-group col-md-3">
                                <label>{{ __('Contact Name'); }}</label>
                                <input type="text" class="form-control @error('contact_name') is-invalid @enderror" id="contact-name" name="contact_name" value="{{ old('contact_name', $model->contact_name ?? '') }}" maxlength="255" aria-describedby="contact_name" />
                                <div style="" id="contact-name-error"
                                    class="invalid-feedback animated  add">
                                </div>
                                @error('contact_name')
                                    <div id="contact_name-error" class="invalid-feedback animated fadeInDown">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="contact-id"></div>
                            <div class="form-group col-md-3"
                                id="job-position-address">
                                <label>{{ __('Job Position') }}</label>
                                <input class="form-control job-position" type="text" id="job-position" name="job_position"  value="{{ old('job_position', $model->job_position ?? '') }}" />
                            </div>
                            <div class="form-group col-md-3">
                                <label>{{ __('Email') }}</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="contact-email" name="email" value="{{ old('email', $model->email ?? '') }}" maxlength="255" aria-describedby="email" />
                                <div style="" id="email-error" class="invalid-feedback animated  add"></div>

                                @error('email')
                                    <div id="email-error" class="invalid-feedback animated fadeInDown">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group col-md-3">
                                <label>{{ __('Phone') }}</label>
                                <input class="form-control" type="number" name="phone" id="phone" value="{{ old('phone', $model->phone ?? '') }}" />
                            </div>
                            <div class="form-group col-md-3">
                                <label>{{ __('Mobile') }}</label>
                                <input class="form-control" type="number" name="mobile" id="mobile" value="{{ old('mobile', $model->mobile ?? '') }}" />
                            </div>
                        </div>

                        <div class="form-group col-md-3" id="select-title-address">
                            <label>{{ __('Title') }}</label>
                            <select class="form-control" name="title_id"
                                id="title_id" style='color:gray'
                                oninput='style.color="black"'>
                                <option style="display:none" value="">---{{ __('Select a title') }} ---</option>
                                @if ($contact_titles->count() > 0)
                                    @foreach ($contact_titles as $title)
                                        <option value="{{ $title->id }}" @if (isset($model) && $title->id == $model->title_id) selected @endif>
                                            {{ $title->title }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>{{ __('Notes') }}</label>
                            <textarea class="form-control" rows="4" cols="50" name="notes" id="notes">{{translation(@$model->id,5,app()->getLocale(),'street_2',@$model->notes) ?? '' }}</textarea>
                        </div>
                        <div class="col-lg-3">
                            <div class="avatar-upload form-group">
                                <div class="avatar-fileds hover-effect">
                                    <div class="avatar-edit">
                                        <input type='file' id="image-upload" accept="image/*" name="contact_image" class="form-control @error('contact_image') is-invalid @enderror" maxlength="255" aria-describedby="contact_image" />
                                        <label for="image-upload"></label>
                                    </div>
                                </div>

                                <div class="avatar-preview">

                                    <img id="contact-address-image"
                                        src="{!! checkImage(asset('storage/uploads/contact-address/' . Hashids::encode(@$model->id) . '/' . @$model->contact_image), 'avatar5.png') !!}" width="100%" height="100%">
                                </div>
                                @error('image')
                                    <div id="image-error"
                                        class="invalid-feedback animated fadeInDown">
                                        {{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row" id="contact-address-area">
                            <h4 class="col-md-12">{{ __('Address'); }}</h4>
                            <div class="form-group col-md-3">
                                <input class="form-control" type="text" name="street_1" id="street_1" placeholder="Street no 1" value="{{ old('street_1', translation(@$model->id,5,app()->getLocale(),'street_1',@$model->street_1) ?? '') }}" />
                            </div>
                            <div class="form-group col-md-3">
                                <input class="form-control" type="text" name="street_2" id="street_2" placeholder="Street no 2" value="{{ old('street_2', translation(@$model->id,5,app()->getLocale(),'street_2',@$model->street_2) ?? '') }}" />
                            </div>
                            <div class="form-group col-md-3">
                                <input class="form-control" type="text" name="city" id="city" placeholder="City" value="{{ old('city', translation($model->id,5,app()->getLocale(),'city',$model->city) ?? '') }}" />
                            </div>
                            <input type="hidden" class="form-control" name="state_id" />
                            {{-- <div class="form-group col-md-3">
                                <select class="form-control" name="state_id" id="state_id" style='color:gray' oninput='style.color="black"'>
                                    <option style="display:none" value="">---{{ __('Select a state') }}---</option>
                                    @if ($contact_fed_states->count() > 0)
                                        @foreach ($contact_fed_states as $state)
                                            <option value="{{ $state->id }}" @if (isset($model) && $state->id == $model->state_id) selected @endif>
                                                {{ $state->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div> --}}
                            <div class="form-group col-md-3">
                                <input class="form-control" type="number" name="zipcode" id="zipcode" placeholder="Zip Code" value="{{ old('zipcode', @$model->zipcode ?? '') }}" />
                            </div>
                            <div class="form-group col-md-3">
                                <select class="form-control" name="country_id" id="country_id" style='color:gray' oninput='style.color="black"'>
                                    <option style="display:none" value="">---{{ __('Select a country') }}---</option>
                                    @if ($contact_countries->count() > 0)
                                        @foreach ($contact_countries as $country)
                                            <option value="{{ $country->id }}" @if (isset($model) && $country->id == $model->country_id) selected @endif> {{ $country->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        </div>
                    </div>
                    <input type="hidden" id="txtId">
                    <!-- End Here -->
            </div>
            <!-- Footer model popupp -->
            <div class="modal-footer ">
                @php
                    if($action == 'edit') {
                        $action_type = "edit";
                    }
                    else{
                        $action_type = "add";
                    }
                @endphp
                <button type="button" class="btn btn-success" data-modal="close" action-type="{{$action_type}}" id="save-change">
                    {{ __('Save & Close') }}
                </button>
                {{-- <button type="button" data-modal="save-and-new" action-type="{{$action_type}}" class="btn btn-success save-and-new" id="save-change">
                    {{ __('Save & New') }}
                </button> --}}
                <button type="button" class="btn btn-success" id="save-and-new-update">
                    {{ __('Save & New') }}
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"
                    id="discard-btn">{{ __('Discard') }}</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"
                    id="remove-contact-address-btn">{{ __('Remove') }}</button>
            </div>
            </form>
        </div>
    </div>
</div>

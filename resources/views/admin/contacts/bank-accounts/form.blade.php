@extends('admin.layouts.app')
@section('title',  __('Bank Accounts'))
@section('styles')
@endsection
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
   <section class="content-header top-header" style="margin-left: 0px; margin-right: 0px;">
      <div class="row">
         <div class="col-md-6">
            <h2>
                {{ __('Contact Bank Account') }} /  @if($action == "Add") {{ __('Add') }} @else {{ __('Edit') }} @endif
            </h2>
         </div>
         <div class="col-md-6">
            <div class="search-input-das">
               <form>
                  <input type="text" name="search" placeholder="{{ __('Search') }}...">
               </form>
            </div>
         </div>
      </div>
   </section>
   <!-- Table content -->
   <section class="content">
      <div class="main-box box">
         <div class="row mt-3">
            <div class="col-xs-12">
               <div class="box box-success box-solid">
                  <div class="box-header with-border">
                     <h3 class="box-title">@if($action == "Add") {{ __('Add New Contact Bank Account') }} @else {{ __('Edit Contact Bank Account') }} @endif</h3>
                     <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                     </div>
                     <!-- /.box-tools -->
                  </div>
                  <!-- /.box-header -->
                  <div class="row">
                    <div class="col-md-12">
                       <form class="timmunity-custom-dashboard-form mt-2 form-validate" action="{{ route('admin.contacts-bank-accounts.store') }}" method="post" enctype="multipart/form-data">
                          @csrf
                          <input type="hidden" name="id" value="{!! Hashids::encode(@$model->id) !!}">
                          <input type="hidden" name="action" value="{!!$action!!}">
                          <div class="row">
                              <div class="col-md-4">
                                <div class="form-group">
                                    <label for="account_number">{{ __('Account No') }}<small class="asterik" style="color:red">*</small></label>
                                    <input type="text" class="form-control @error('account_number') is-invalid @enderror" id="account_number" name="account_number"  value="{{ old('account_number', $model->account_number ?? '') }}" maxlength="255" aria-describedby="account_number" required />
                                    @error('account_number')
                                    <div id="account_number-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                    @enderror
                                 </div>

                              </div>
                              <div class="col-md-4">
                                <div class="form-group">
                                    <label for="account_type">{{ __('Account Type') }}<small class="asterik" style="color:red">*</small></label>
                                    <input type="text" class="form-control @error('account_type') is-invalid @enderror" id="account_type" name="account_type" value="{{ old('account_type', $model->account_type ?? '') }}" maxlength="255" aria-describedby="account_type" required />
                                    @error('account_type')
                                    <div id="account_type-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                    @enderror
                                 </div>
                              </div>
                              <div class="col-md-4">
                                <div class="form-group">
                                    <label for="account_title">{{ __('Account Title') }}<small class="asterik" style="color:red">*</small></label>
                                    <input type="text" class="form-control @error('account_title') is-invalid @enderror" id="account_title" name="account_title" value="{{ old('account_title', $model->account_title ?? '') }}" maxlength="255" aria-describedby="account_title" required />
                                    @error('account_title')
                                    <div id="account_title-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                    @enderror
                                 </div>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-md-4">
                                <div class="form-group">
                                    <label for="account_holder_name">{{ __('Account Holder Name') }}<small class="asterik" style="color:red">*</small></label>
                                    <input type="text" class="form-control @error('account_holder_name') is-invalid @enderror" id="account_holder_name" name="account_holder_name" value="{{ old('account_holder_name', $model->account_holder_name ?? '') }}" maxlength="255" aria-describedby="account_holder_name" required />
                                    @error('account_holder_name')
                                    <div id="account_holder_name-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                    @enderror
                                 </div>
                              </div>
                              <div class="col-md-4">
                                <div class="form-group">
                                    <label for="bank-id">{{ __('Bank') }}<small class="asterik" style="color:red">*</small></label>
                                     <select id="bank-id" name="bank_id" class="form-control @error('bank_id') is-invalid @enderror" aria-describedby="bank_id" required>
                                        <option value="">---{{ __('Select a bank') }}---</option>
                                        @if($contact_banks->count() > 0)
                                        @foreach($contact_banks as $bank)
                                        <option value="{{$bank->id}}"
                                            @if(isset($model) && $bank->id == $model->bank_id) selected @endif>{{$bank->name}}
                                        </option>
                                        @endforeach
                                        @endif
                                      </select>
                                      @error('bank-id')
                                      <div id="bank-id-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                      @enderror
                                </div>
                              </div>
                              <div class="col-md-4">
                                <div class="form-group">
                                    <label for="currency-id">{{ __('Currency') }}<small class="asterik" style="color:red">*</small></label>
                                     <select id="currency-id" name="currency_id" class="form-control @error('currency_id') is-invalid @enderror" aria-describedby="currency_id" required>
                                        <option value="">---{{ __('Select a currency') }}---</option>
                                        @if($contact_currency->count() > 0)
                                        @foreach($contact_currency as $currency)
                                        <option value="{{$currency->id}}"
                                            @if(isset($model) && $currency->id == $model->currency_id)  selected @endif>{{$currency->currency}}</option>
                                        @endforeach
                                        @endif
                                      </select>
                                      @error('currency-id')
                                      <div id="currency-id-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                      @enderror
                                 </div>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-md-3">
                                <div class="form-group">
                                 <button type="submit" class="skin-green-light-btn btn ">{{ __('Save') }}</button>
                                 <a style="border-bottom: 2px solid #009a71;" class=" btn ml-2" href="{{ route('admin.contacts-bank-accounts.index') }}">{{ __('Discard') }}</a>

                                </div>
                            </div>
                          </div>
                     </div>

                    </div>
                 </div>
                        </div>
                     </div>

                    </form>
                  </div>
                </div>
               </div>
            </div>
            <!-- /.box-body -->
         </div>
      </div>
    </div>
<!-- /.box -->
  </div>
 </div>
 </section>
<!-- /.content -->
</div>

@endsection
@section('scripts')
<script src="{{ asset('backend/dist/js/custom.js') }}"></script>
@endsection

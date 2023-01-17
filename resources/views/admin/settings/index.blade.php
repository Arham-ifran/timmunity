@extends('admin.layouts.app')
@section('title', __('Settings'))
@section('styles')
<link href="{{ asset('backend/dist/css/loader.css') }}" rel="stylesheet" type="text/css">
@endsection
@canany(['View General Settings','View Sales Settings'])
@section('header')
<!-- Top Header Section -->
<section class="content-header top-header" style="margin: 0px; border: 1px solid #ddd;">
	<div class="row">
		<div class="col-md-4">
			<h2>
				{{ __('Settings') }}
			</h2>
		</div>
	</div>
	<div class="row">
		<div class="box-header">
            @can('View General Settings')
            <div class="row">
				<div class="col-md-4">
					<a class="skin-gray-light-btn btn" href="#">{{ __('Save') }}</a>
					<a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2" href="#">{{ __('Discard') }}</a>
				</div>

			</div>
            @endcan

		</div>
	</div>
</section>
@endsection
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper setting-page-content-wrapper">
	<div class="loader-parent" id="ajax_loader">
		<div class="loader setting-loader">
			<div class="square"></div>
			<div class="path">
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<div></div>
			</div>
		</div>
	</div>
	<!-- Main content -->
    @can('View General Settings')
      <section class="content">
		<div class="row">
			<!-- Users -->
			<div class="col-sx-12" id="Users">
				<div class="row">
					<div class="col-md-12">
						<div class="box">
							<div class="box-header with-border">
								<h3 class="box-title">{{ __('Users') }}</h3>

								<div class="box-tools pull-right">
									<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
									</button>
								</div>
								<!-- /.box-tools -->
							</div>
							<div class="box-body">
								<div class="row">
									<div class="contact-box col-md-6" style="max-width: 45%;">
										<h3 class="setting-status-h3"><i class="fa fa-users"></i>&nbsp; <strong id="user_count">{{ $count_active_users }}</strong> {{ __('Active Users') }}
										</h3>
										<div class="form-group pt-2">
											<a href="{{ route('admin.admin-user.index') }}" class="btn btn-primary" type="button">
												<i class="fa fa-arrow-right"></i>
												{{ __('Manage Users') }}
											</a>
										</div>
									</div>
									<div class="contact-box col-md-6 ml-2" style="margin-left: 45px; padding-bottom: 9px;">
										<h3 class="setting-status-h3">{{ __('Invite new users') }} </h3>
										<div class="row tab-form">
											<div class="form-group">
												<input class="form-control" type="email" name="email" id="email">
											</div>
											<div class="form-group">
												<button class="btn btn-primary" type="button" id="invite_new_user">
													<i class="fa fa-arrow-right"></i>
													{{ __('Invite') }}
												</button>
											</div>
										</div>
                                        @if(count($pending_admins) > 0)
                                            <p class="pt-2">{{ __('Pending Invitations:') }}</p>
                                            <div id="update_url">
                                                @foreach($pending_admins as $pending_admin)
                                                    <a href="{{ route('admin.admin-user.edit',['admin_user'=> Hashids::encode($pending_admin->id)]) }}">
                                                        <span class="badge badge-success"> {{ $pending_admin->email }} </span>
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
									</div>
								</div>
							</div>
						</div>
						<!-- /.box -->
					</div>
				</div>
			</div>
			<!-- Companies -->
			<div class="col-sx-12" id="Companies">
				<div class="row">
					<div class="col-md-12">
						<div class="box">
							<div class="box-header with-border">
								<h3 class="box-title">{{ __('Companies') }} </h3>
								<div class="box-tools pull-right">
									<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
									</button>
								</div>
								<!-- /.box-tools -->
							</div>
							<div class="box-body">
								<div class="row">
                                    <div class="contact-box col-md-6">
										<h3 class="setting-status-h3"><strong> {{ $companies_count }}</strong> {{ __('Companies') }}</h3>
										<div class="form-group pt-2">
                                            <a href="{{ route('admin.companies.index') }}" class="btn btn-primary" type="button">
												<i class="fa fa-arrow-right"></i>
												{{ __('Manage Companies') }}
											</a>
										</div>
									</div>
                                    {{-- <div class="contact-box col-md-6" style="max-width: 45%;">
                                        <div class="form-group">
                                            <h3 class="setting-status-h3">{{ $companies->name ?? '' }}</h3>
                                            <h4 class="dark-gray">{{ $companies->countries->name ?? '' }}</h4>

                                        </div>
                                        <div class="form-group">
                                            <a href="{{ isset($companies) ? route('admin.companies.edit',['company'=> Hashids::encode($companies->id)]) : '' }}" class="btn btn-primary" type="button">
                                                <i class="fa fa-arrow-right"></i>
                                                {{ __('Update Info') }}
                                            </a>
                                        </div>
                                    </div> --}}
								</div>
							</div>
						</div>
						<!-- /.box -->
					</div>
				</div>
			</div>
			<!-- Languages -->
			<div class="col-sx-12" id="Languages">
				<div class="row">
					<div class="col-md-12">
						<div class="box">
							<div class="box-header with-border">
								<h3 class="box-title">{{ __('Languages') }}</h3>
								<div class="box-tools pull-right">
									<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
									</button>
								</div>
								<!-- /.box-tools -->
							</div>
							<div class="box-body">
								<div class="row">
									<div class="contact-box col-md-6" style="max-width: 45%;">
										<h3 class="setting-status-h3"><i class="fa fa-language"></i>&nbsp; <strong id="language_count">{{ $active_language_count }}</strong>&nbsp;{{ __('Active Languages') }}</h3>
										<div class="form-group pt-2">
											<button class="btn btn-primary" type="button" onclick="activateUpdate($(this))" title="Add Language">
												<i class="fa fa-plus"></i>
												{{ __('Activate Language') }}
											</button>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- /.box -->
					</div>
				</div>
			</div>
			<!-- Add Language Model -->
			<!-- Main currency -->
			<div class="col-sx-12" id="main-currency">
				<div class="row">
					<div class="col-md-12">
						<div class="box">
							<div class="box-header with-border">
								<h3 class="box-title">{{ __('Currencies') }}</h3>
								<div class="box-tools pull-right">
									<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
									</button>
								</div>
								<!-- /.box-tools -->
							</div>

							<div class="box-body">
								<div class="row">
									<div class="contact-box col-md-6" style="max-width: 45%;">
										<h3 class="setting-status-h3">{{ __('Default Currency') }}</h3>
										<div class="form-group">
											<span class="setting-status-span">{{ __('Default Currency of website.') }}</span>
										</div>
										<div class="row tab-form setting-input-box">
											<h3 class="col-sm-4 pl-0 default-currency">{{ $default_currency == null ? 'EUR' : $default_currency->code ?? ''.' '.'('.$default_currency->symbol ?? ''.')' }}</h3>
											{{-- <div class="form-group  col-sm-7">
                                                <button class="btn btn-primary mt-2 currency-modal-btn" type="button" data-default="{{  $default_currency ? $default_currency->id  : '' }}" data-toggle="modal" data-target="#currency_model" data-type="deafult">
                                                    <i class="fa fa-plus"></i>
                                                    {{ __('Update Default Currency') }}
                                                </button>
											</div> --}}
										</div>

									</div>
									<div class="contact-box col-md-6 ml-2" style="margin-left: 45px; padding-bottom: 9px;">
										<div class="form-group">
											{{-- <button class="btn btn-primary mt-2 currency-modal-btn" type="button"  data-toggle="modal" data-target="#currency_model" data-type="add"> --}}
											<button class="btn btn-primary mt-2 currency-modal-btn" type="button"  data-type="add">
												<i class="fa fa-plus"></i>
												{{ __('Add Currencies') }}
											</button>
										</div>
										<div class="row tab-form setting-input-box">
											<h3 class="col-sm-12 pl-0">{{ __('Exchange Currency Rates:') }}  </h3>
											<div class="col-md-6">
                                                <button class="btn btn-primary mt-2 exchange_currency_btn" type="button">
                                                    <i class="fa fa-exchange"></i>
                                                    {{ __('Exchange Rates') }}
                                                </button>
                                            </div>
											<div class="col-md-6">
                                                <a href="{{ route('admin.currencies.index') }}" target="_blank" class="btn btn-primary mt-2 exchange_currency_btn">
                                                    <i class="fa fa-eye"></i>
                                                    {{ __('View Rates') }}
                                                </a>
                                            </div>
										</div>

									</div>
								</div>
								<!-- Add Language Model -->
								<div class="modal fade" id="language_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							        <div class="modal-dialog" role="document">
							            <div class="modal-content">
							                <div class="modal-header">
							                    <h3 class="modal-title lang_modal_title col-md-9 pl-0" id="exampleModalLabel"></h3>
							                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
							                        <i class="fa fa-times"></i>
							                    </button>
							                </div>
							                <div class="modal-body">
							                    <div id="lang_active_msg"></div>
							                    <div class="form-group" id="lang_modal_body" style="display:none">
							                        <label for="message-text" class="col-form-label">{{ __('Language') }}</label>
							                        <select class="form-control" name="language" id="language">
							                        	<option value=''>---{{ __('Select a language') }}---</option>
							                            @foreach($languages as $language)
							                            <option value="{{ Hashids::encode($language->id) }}">{{ $language->name }}</option>
							                            @endforeach
							                        </select>
							                    </div>
							                </div>
							                <div class="modal-footer" id="add_lang_section">
							                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
							                    <button type="button" class="btn btn-success" onclick="addLanguage($(this))" data-table-flag = "0">{{ __('Add') }}</button>
							                </div>
							                <div class="modal-footer" id="swith_lang_footer">
							                    <div class="col-md-12 text-left" id="switch_to_lang">
							                    </div>
							                </div>
							            </div>
							        </div>
							    </div>
								<!-- End Language Model -->
								<!-- Add Currency Model -->
								<div class="modal fade" id="currency_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<h3 class="modal-title col-md-9 pl-0" id="exampleModalLabel">{{ __('Add Currency') }}</h3>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<i class="fa fa-times"></i>
												</button>
											</div>
											<div class="modal-body">
												<div class="form-group">
													<label for="message-text" class="col-form-label">{{ __('Currency') }}</label>
													<select class="form-control" name="currency" id="currency">
														<option value=''>--- {{ __('Select a currency') }} ---</option>
														@foreach($currencies as $currency)
														<option value="{{$currency->id}}">{{ $currency->code ?? ''.' '.'('.$currency->symbol ?? ''.')' }}</option>
														@endforeach
													</select>
												</div>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
												<button type="button" class="btn btn-success" id="currency-model-submit" >{{ __('Add') }}</button>
											</div>
										</div>
									</div>
								</div>
								<!-- End Currency Model -->
							</div>
							<!-- /.box -->
						</div>
					</div>
				</div>
			</div>
	</section>
    @endcan

	<!-- /.content -->
</div>
<!-- /.content-wrapper -->
@endsection
@endcanany
@section('scripts')
<script type="text/javascript">
	$('#invite_new_user').on('click', function() {
		// Validate Email Field
		$(".invalid-feedback").hide();
		var hasError = false;
		var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

		var invite_email = $('#email').val();
		if (invite_email == '') {
			$("#email").after('<span class="invalid-feedback">{{__('Please enter your email address.') }}</span>');
			hasError = true;
		} else if (!emailReg.test(invite_email)) {
			$("#email").after('<span class="invalid-feedback">{{ __('Enter a valid email address.') }}</span>');
			hasError = true;
		}

		if (hasError == true) {
			return false;
		}

		var fd = new FormData();
		fd.append('_token', "{{ csrf_token() }}");
		fd.append('invite_email', invite_email);
		$.ajax({
			url: '{{ route("admin.invite.user") }}',
			data: fd,
			type: 'POST',
			processData: false,
			contentType: false,
			beforeSend: function() {
				// Show loader container
				$("#ajax_loader").show();
			},
			success: function(resp) {
				if (resp['success']) {
					var html = '&nbsp;<a href="' + resp['url'] + '" class="new_invitation"><span class="badge badge-success" id="new_invitation">' + invite_email + '</span></a>';
					$('#invite_email').val('');
					$("#update_url").append(html);
					$("#user_count").html(resp['updated_count']);
					Swal.fire("{{ __('Invited') }}", resp['success'], "success");
				} else {
					$('#invite_email').val('');
					Swal.fire("{{ __('Error') }}", resp['error'], "error");
				}
			},
			complete: function(data) {
				// Hide loader container
				$("#ajax_loader").hide();
			}
		});
	});
</script>
<script type="text/javascript">
    var type = 'add';
    var currencies = [];

    $('body').on('click', '.currency-modal-btn', async function(){
        let resp = await prepare_ajax_request('{{ route("admin.settings") }}',null, 'get');
        currencies = resp['currencies'];

        switch ($(this).attr('data-type')){
            case 'add':
                $('#exampleModalLabel').html("{{ __('Add Currency') }}");
                $('#currency-model-submit').html("{{ __('Add') }}");
                type = 'add';
                option_html = '';
                currencies.forEach(function callbackFn(currency, index) {
                    if(currency.is_active == 0)
                    {
                        option_html += '<option value="'+currency.id+'"> '+currency.code+'</option>';
                    }
                });
                if(option_html == ''){

                    Swal.fire("{{ __('Warning') }}", "{{ __('No In-Active Currency! All currencies are added') }}", "warning");
                }else{
                    $('#currency_model').modal('show');
                    $('#currency').find('option')
                        .remove()
                        .end()
                        .append(option_html);
                }
                break;
            case 'deafult':
                option_html = '';
                currencies.forEach(function callbackFn(currency, index) {
                    if(currency.is_active == 1)
                    {
                        if(currency.ise_default == 1){
                            option_html += '<option value="'+currency.id+'" selected="selected"> '+currency.code+'</option>';
                        }else{
                            option_html += '<option value="'+currency.id+'"> '+currency.code+'</option>';
                        }
                    }
                });
                $('#currency_model').modal('show');
                $('#currency')
                    .find('option')
                    .remove()
                    .end()
                    .append(option_html);
                $('#exampleModalLabel').html("{{ __('Default Currency') }}");
                $('#currency-model-submit').html("{{ __('Make Default') }}");
                type = "default";
                break;
        }
    });

    $('body').on('click', '#currency-model-submit', function(){
        switch (type){
            case 'add':
                addCurrency();
                break;
            case 'default':
                defaultCurrency();
                break;
            default:
                Swal.fire("{{ __('Something went wrong.') }}", "{{ __('Refresh the webpade and try again.')}}", "Failure");
                break;
        }
    });
    $('body').on('click', '.exchange_currency_btn', exchangeCurrency);
	function addCurrency() {
		// Validatation
		$(".invalid-feedback").hide();
		$("#currency_model").modal("show");
		var hasError = false;
		var currency = $('#currency').val();
		if (currency == '') {
			$("#currency").after('<span class="invalid-feedback">"{{ __('Please select a currency.') }}"</span>');
			hasError = true;
		}

		if (hasError == true) {
			return false;
		}

		var fd = new FormData();
		fd.append('_token', "{{ csrf_token() }}");
		fd.append('id', $('#currency option:selected').val());
		$.ajax({
			url: '{{ route("admin.add.currency") }}',
			data: fd,
			type: 'POST',
			processData: false,
			contentType: false,
			success: function(resp) {
				if (resp['success']) {
					$('#currency').val('');
					$("#currency_model").modal("hide");
					Swal.fire("{{ __('Activated') }}", resp['currency'] + ' ' + resp['success'], "success");
				}
			}
		});
	}
    function defaultCurrency() {
		var currency = $('#currency').val();
        var hasError = false;
		if (currency == '') {
			$("#currency").after('<span class="invalid-feedback">"{{ __('Please select a currency.') }}"</span>');
			hasError = true;
		}

		if (hasError == true) {
			return false;
		}

		var fd = new FormData();
		fd.append('_token', "{{ csrf_token() }}");
		fd.append('id', $('#currency option:selected').val());
		$.ajax({
			url: '{{ route("admin.default.currency") }}',
			data: fd,
			type: 'POST',
			processData: false,
			contentType: false,
			success: function(resp) {
				if (resp['success']) {
					$('#currency').val('');
					$('.default-currency').html(resp['currency']);
					$("#currency_model").modal("hide");
					// Swal.fire("Activated", resp['currency'] + ' ' + resp['success'], "success");
                    Swal.fire({
                        title: resp['currency'] + ' ' + resp['success'],
                        text: "{{ __('Do want to update the Exchange Rates?') }}",
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: "{{ __('Yes!') }}"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            exchangeCurrency();
                        }
                    })
				}
			}
		});
    }
    function exchangeCurrency()
	{
        Swal.fire({
            title: "{{ __('Are you sure?') }}",
            text: "{{ __('Are you sure that you want to update the Exchange Rates?') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ __('Yes!') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $('#ajax_loader').fadeIn();
                var fd = new FormData();
                fd.append('_token', "{{ csrf_token() }}");
                $.ajax({
                    url: '{{ route("admin.exchange.rates.currency") }}',
                    type: 'POST',
                    data: fd,
                    processData: false,
                    contentType: false,
                    success: function(success) {
                        if(success == 'true')
                        {
                            Swal.fire({
                                title: "{{ __('Rates Updated') }}",
                                text: "{{ __('Do you want to view the rates?') }}",
                                icon: 'success',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: "{{ ('Yes!') }}"
                            }).then((result) => {
                                if(result.isConfirmed) {
                                    location.href = "{{ route('admin.currencies.index') }}";
                                }
                            })
                        }
                        else
                        {
                            Swal.fire("{{ __('Warning') }}", "{{ __('Select the Default Currency First!') }}", "warning");
                        }
                    },
                    complete: function() {
                        $('#ajax_loader').fadeOut();
                    }
                });
            }
        })
    }
</script>
<script type="text/javascript">
   // Language Activation URL
   var add_language_url = '{{ route("admin.add.language") }}';
   var modal_title = "{{ __('Add Language') }}";
   var modal_change_title = "{{ __('Language Pack') }}";
   var switch_btn_title = "{{ __('Switch to') }}";
   var close_btn_title = "{{ __('Close') }}";
   var lang_validation_msg = "{{ __('Please select a language.') }}";
</script>
@endsection

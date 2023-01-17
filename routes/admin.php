<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Side Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Must verfied before redirect to dashboard
Route::get('/email/verify', function () {
	return view('admin.auth.verify');
})->middleware('auth:admin')->name('verification.notice');
Route::get('lang/{locale}', 'LocalizationController@lang');
Route::group(
	[
	// 'domain' => '{subdomain}.'.config('app.base_domain'),
		'prefix' => LaravelLocalization::setLocale(),
		'middleware' => ['localeCookieRedirect','localeSessionRedirect','localizationRedirect']
	],
	function() {
	Route::prefix('/admin')->name('admin.')->namespace('Admin')->group(function () {
		Route::get('/CalculateTotalPayable', function() {
			$re = Artisan::call('command:CalculateTotalPayable');
			dd($re);
		});
		Route::namespace('Auth')->group(function () {
			/*Admin Register Routes */
			Route::get('/register', 'RegisterController@showRegistrationForm')->name('register');
			Route::post('/register', 'RegisterController@register');
			Route::get('/reset-password', 'RegisterController@verifyAdminUser')->name('verify.admin');
			Route::post('/reset-password', 'RegisterController@resetPassword')->name('password.store');
			/*Admin Login Routes*/
			Route::get('/', 'LoginController@showLoginForm')->name('login');
			Route::get('/login', 'LoginController@showLoginForm')->name('login.form');
			Route::post('/login', 'LoginController@login')->name('login.submit');
			Route::get('/logout', 'LoginController@logout')->name('logout');
			/*Admin Email Verificatoin Routes*/
			Route::get('/email/verify', 'VerificationController@show')->name('verification.notice');
			Route::get('/email/verify/{id}/{hash}', 'VerificationController@verify')->name('verification.verify');
			Route::post('/email/resend', 'VerificationController@resend')->name('verification.resend');
			/*Admin Forgot Password Routes*/
			Route::get('/password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
			Route::post('/password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
			/*Admin Reset Password Routes*/
			Route::get('/password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
			Route::post('/password/reset', 'ResetPasswordController@reset')->name('password.update');
		});

		Route::group(['middleware' => ['auth:admin','verified:admin.verification.notice']], function ($router) {
			$router->get('lang/{locale}', 'LocalizationController@lang');
			$router->get('/dashboard', 'DashboardController@index')->name('dashboard');
			$router->get('settings/site-settings', 'SiteSettingsController@index')->name('site.settings');
			$router->post('settings/site-settings/update', 'SiteSettingsController@update')->name('site.settings.update');
			$router->get('/settings', 'SettingsController@index')->name('settings');
			$router->post('/invite-new-user', 'SettingsController@inviteNesUser')->name('invite.user');
			$router->post('/add-new-language', 'SettingsController@addLanguage')->name('add.language');
			$router->post('/make-archive-language', 'SettingsController@archiveLanguage')->name('make.archive.language');
			$router->post('/add-new-currency', 'SettingsController@addCurrency')->name('add.currency');
			$router->post('/add-default-currency', 'SettingsController@defaultCurrency')->name('default.currency');
			$router->post('/exchange-rates-currency', 'CurrencyController@exchangeRatesCurrency')->name('exchange.rates.currency');
			$router->resource('/settings/cms', 'CmsPagesController');
			$router->post('/settings/cms/duplicate/{id}', 'CmsPagesController@duplicate')->name('cms.duplicate');
			$router->get('/settings/cms-check-slug', 'CmsPagesController@check_slug')->name('cms.check_slug');
			$router->resource('/settings/roles', 'RolesController');
			$router->resource('/settings/permissions', 'PermissionsController');
			$router->get('/settings/admin-user/duplicate/{id}', 'AdminUserController@duplicate')->name('admin-user.duplicate');
			$router->post('archive-admin-user', 'AdminUserController@isArchiveUser')->name('archive.record');
			$router->delete('delete-bulk-records', 'AdminUserController@bulkDelete')->name('bulk.delete');
			$router->resource('/settings/admin-user', 'AdminUserController');
			// $router->resource('/settings/companies', 'CompaniesController');
			$router->post('resend-email-invitation', 'InvitationMailController@resendInvitationEmail')->name('invitation.resend-email');
			$router->post('/update-language-status', 'LanguagesController@updaStatus')->name('update.status');
			$router->delete('bulk-delete-lanugues', 'LanguagesController@bulkDelete')->name('bulk.delete.languages');
			$router->post('archive-language', 'LanguagesController@isArchiveLang')->name('archive.language');
			$router->resource('/settings/languages', 'LanguagesController');
			$router->get('/settings/sales', 'SettingsController@salesSettings')->name('sales.settings');
			$router->post('/save/settings/sales', 'SettingsController@saveSalesSettings')->name('sales.settings.update');

			$router->get('/settings/language-modules', 'LanguageModuleController@index')->name('language-modules.index');
			$router->resource('settings/label-translations', 'LabelTranslationController');
			$router->resource('settings/text-translations', 'TextTranslationController');

			$router->get('settings/language-translations/partial-translate', 'LanguageTranslationController@partialTranslate');
			$router->post('settings/language-translations/partial-translate', 'LanguageTranslationController@addPartialTranslate');
			$router->resource('settings/language-translations', 'LanguageTranslationController');
			$router->resource('settings/email-templates', 'EmailTemplateController');
			$router->resource('settings/email-template-labels', 'EmailTemplateLabelController');
			/* =============contacts management route============= */

			$router->resource('/contacts', 'ContactController');
			$router->post('/contact-actions', 'ContactController@contactActions');
			$router->post('/contact-address-model', 'ContactController@LoadContactAddressModel');
			$router->get('/export-contacts', 'ContactController@exportContacts')->name('contact.export');
			$router->get('/contact-impersonate/{user_id}', 'ContactController@impersonateUser')->name('contact.impersonate');
			$router->get('/resend-invite-email/{user_id}', 'ContactController@resendInviteEmail')->name('contact.resend-verification-email');



			//$router->resource('/contact', 'ContactsController');


			$router->get('/contacts-address/on-modal', 'ContactController@createModal');
			$router->post('/contacts-address', 'ContactController@contactsAddress');
			$router->get('/contacts-address/edit/{id}', 'ContactController@contactsAddressEdit');

			/* =============contacts address route============= */
			$router->resource('/contact-address', 'ContactAddressController');
			$router->delete('/contact-address-dlt/{id}', 'ContactController@addressDlt')->name('address-delete');


			/* =============contact tags route============= */

			$router->resource('/contacts-tags','ContactTagController');


			/* =============contact titles route============= */
			$router->resource('/contacts-titles','ContactTitleController');

			/* =============contact sectors activities route============= */
			$router->resource('/contacts-sectors-activities','ContactSectorsActivityController');

			/* =============contact fed states route============= */
			$router->resource('/contacts-fed-states','ContactFedStatesController');
			/* =============Fsecure Subscription Routes============= */
			$router->get('f-secure/logs', 'FsecureController@fescureLog')->name('f-secure.fescureLog');
			$router->resource('/f-secure', 'FsecureController');
			$router->get('kss/licences', 'KSSController@licenses')->name('kss.licenses');
			$router->get('kss/vouchers', 'KSSController@vouchers')->name('kss.vouchers');
			$router->get('f-secure/cancel-license/{id}', 'FsecureController@cancelLicense')->name('license.cancelLicense');


			$router->post('/license-activation', 'KasperskySubcriptionController@licenseActivation')->name('license.activation');

			$router->post('/license-hold', 'KasperskySubcriptionController@licenseHold')->name('license.hold');
			$router->post('/license-resumed', 'KasperskySubcriptionController@licenseResume')->name('license.resumed');
			$router->post('/license-blocked', 'KasperskySubcriptionController@licenseBlocked')->name('license.blocked');
			$router->get('f-secure/duplicate/{id}', 'KasperskySubcriptionController@duplicate')->name('f-secure.duplicate');
			/* =============activities routes============= */
			$router->post('/add-log-note', 'AcitvityNotesController@addLogNote')->name('log.note');
			$router->post('/remove-note-file', 'AcitvityNotesController@removeNoteFile')->name('log.remove-note-file');
			$router->post('/send-messages', 'ActivitySendMessageController@sendNewMessage')->name('send.message');
			$router->post('/remove-msg-file', 'ActivitySendMessageController@removeMessageFile')->name('log.remove-msg-file');
			$router->post('/add-new-contact', 'ActivitySendMessageController@addNewContact')->name('log.add-new-contact');
			$router->post('/following', 'ActivitySendMessageController@userFollowing')->name('log.user-following');
			$router->post('/unfollow', 'ActivitySendMessageController@userUnFollow')->name('log.user-un-follow');
			$router->post('/schedule-activity', 'SchedualActivityController@scheduleActivity')->name('schedule.activity');
			$router->post('/update-planned-activity', 'SchedualActivityController@updatesPlannedActivity')->name('schedule.update.activity');
			$router->post('/cancel-planned-activity', 'SchedualActivityController@cancelPlannedActivity')->name('schedule.cancel.activity');
			$router->post('/mark-as-done-planned-activity', 'SchedualActivityController@donePlannedActivity')->name('schedule.done.activity');
			$router->post('/remove-sa-file', 'SchedualActivityController@removeScheduleActivityFile')->name('log.remove-sa-file');
			/* =============contacts management route============= */

			/* ==============Contacts Voucher Redeemed and order quotation and voucher routes in contacts */

			$router->get('/get-voucher-redeem','ContactController@voucherRedeemedContacts')->name('voucher.redeemed.contacts');
			$router->get('/get-order-quotations','ContactController@orderQuotationForContacts')->name('voucher.order.quotation.contacts');
			$router->get('/get-order-vouchers','ContactController@orderVouchersForContacts')->name('voucher.order.vouchers.contacts');
			/* End */
			/* =============contact countries route============= */
			$router->resource('/contacts-countries', 'ContactCountriesController');

			/* =============contact countries groups route============= */
			$router->resource('/contacts-countries-groups', 'ContactCountryGroupsController');

			/* =============contact banks  route============= */
			$router->resource('/contacts-banks', 'ContactBankController');

			/* =============contact bank accounts route============= */
			$router->resource('/contacts-bank-accounts', 'ContactBankAccountController');

			/* =============contact currencies route============= */
			$router->post('/companies-contact-member', 'CompaniesController@CompanyContactMember')->name('companies.contact.member');
			$router->post('/companies-contact-member-update', 'CompaniesController@UpdateContactMember')->name('companies.contact.member.update');
			$router->post('/companies-contact-member-delete', 'CompaniesController@DeleteContactMember')->name('companies.contact.member.delete');
			$router->post('/companies-check-email', 'CompaniesController@checkEmail')->name('companies.check.email');
			$router->resource('/currencies', 'CurrencyController');
			$router->post('/change-currency-status/{id}/{status}', 'CurrencyController@changeStatus')->name('currency.change.status');

			$router->resource('/companies', 'CompaniesController');

			// Reports
			Route::prefix('/reports')->group(function($router){

				$router->get('/sales-analysis', 'ReportController@salesDashboard')->name('reports.sales-report-dashboard');
				$router->get('/website-analysis', 'ReportController@websiteDashboard')->name('reports.website-dashboard');
				$router->get('/voucher-orders','ReportController@orders')->name('reports.voucher.orders');

				$router->get('/licenses', 'ReportController@licenseAnalysis')->name('reports.licenses');
				$router->get('/abandoned-carts', 'ReportController@abondedCart')->name('reports.abandoned.carts');
				$router->match(['get','post'],'/manufacturers-analysis', 'ReportController@getManufacturers')->name('reports.manufacturers');

				$router->match(['get','post'],'download-report','ReportController@exportSales')->name('reports.sales.export');
				$router->match(['get','post'],'download-report-website','ReportController@exportWebsiteAnalysis')->name('reports.website.export');

				$router->get('invoices','ReportController@invoicesAnalysis')->name('reports.invoices');

				$router->get('manufacturer-product','ReportController@getManufacturerProduct')->name('reports.manufacturer.product');
				$router->get('voucher-payment','ReportController@voucherPayment')->name('reports.voucher.payment');
				$router->get('distributor-voucher-payment','ReportController@distributorVoucherPayment')->name('reports.voucher.distributorVoucherPayment');
				$router->get('market-place-orders','ReportController@marketPlaceOrdersList')->name('reports.voucher.market-place-orders');
				$router->get('distributors','ReportController@distributors')->name('reports.voucher.distributors');
				$router->get('kaspersky/licenses','ReportController@kssLicenses')->name('reports.voucher.kss.licenses');
				$router->get('kaspersky/vouchers','ReportController@kssVouchers')->name('reports.voucher.kss.vouchers');
			});
			// sales-management

			Route::prefix('/sales-management')->group(function ($router) {

				$router->get('/dashboard', 'DashboardController@sales_dashboard')->name('sales-dashboard');
				$router->get('/sales_report', 'DashboardController@generateSalesReportExcel')->name('sales-report');

				$router->post('products/eccomerce-image-remove', 'ProductsController@remove_eccomerce_image')->name('products.remove.eccomerceimage');
				$router->resource('products', 'ProductsController');
				$router->get('check-product-order-number', 'ProductsController@checkOrderNumber')->name('products.check.order.number');
				$router->resource('price-lists', 'PricelistsController');
				$router->resource('attribute', 'ProductAttributesController');
				$router->resource('eccomerce-categories', 'ProductEccomerceCategoriesController');
				$router->get('/settings/eccomerce-categories-check-slug', 'ProductEccomerceCategoriesController@check_slug')->name('eccomerce-categories.check_slug');
				$router->get('/cancel-vouchers-against-product/{product_id}/{variation_id}', 'VoucherController@cancelVochersAgainstProductVariation')->name('cancel.voucher.product');

				$router->post('/check-attribute-value-usage', 'ProductsController@check_product_variation_attribute_value_usage_quotation')->name('products.check.attribute.value.usage');
				$router->post('/remove-variation-based-attribute', 'ProductsController@remove_variation_based_attribute')->name('products.remove.attribute.value.variation');

				Route::prefix('/quotations')->group(function ($router) {
					$router->post('/product-attributes-options', 'ProductsController@product_attributes_options')->name('product-attributes-options');
				});

				$router->post('/delete-product', 'ProductsController@deleteProducts')->name('products.delete');
				$router->post('/archive-product', 'ProductsController@archiveProducts')->name('products.archive');
				$router->post('/unarchive-product', 'ProductsController@unarchiveProducts')->name('products.unarchive');

				$router->get('search-products-listing', 'ProductsController@searchProducts')->name('products.search');
				$router->get('attribute-search-listing', 'ProductAttributesController@searchAttribute')->name('attribute.search');
				$router->get('get-attribute-values', 'ProductAttributesController@searchAttributeValues')->name('attribute.values');
				$router->post('add-attribute-value/{product_attribute_id}', 'ProductAttributesController@addNewAttributeValue')->name('attribute.value.add');

				$router->get('configure_variants/{product_id}', 'ProductsController@configureVariants')->name('products.configure.variants');
				$router->get('configure_variants_edit/{id}', 'ProductsController@configureVariantsEdit')->name('products.configure.variants.edit');
				$router->get('configure_variants_change_status/{id}', 'ProductsController@configureVariantsChangeStatus')->name('products.configure.variants.status');
				$router->post('configure_variants_edit_post/', 'ProductsController@configureVariantsEditPost')->name('products.configure.variants.edit.post');

				$router->get('/export-attribute', 'ProductAttributesController@exportAttributes');

				$router->get('/sales-orders', 'ProductQuotationsController@sales_order_listing')->name('quotation.sales.orders');
				$router->get('/order-to-invoice', 'ProductQuotationsController@order_to_invoice_listing')->name('quotation.sales.orders.toinvoice');

				Route::prefix('/quotations')->group(function ($router) {
					$router->get('/get_quotation_pdf/{id}', 'ProductQuotationsController@get_pdf')->name('quotation.get_pdf');
					$router->get('/order-line-option', 'ProductQuotationsController@order_line_options')->name('order-line-option');
					$router->post('/save-order-line-option', 'ProductQuotationsController@save_order_line_options')->name('save.order-line-option');
					$router->post('/update-order-line-option', 'ProductQuotationsController@update_order_line_options')->name('update.order-line-option');
					$router->post('/save-optional-product', 'ProductQuotationsController@save_optional_products')->name('save.quotation.optional-products');
					$router->post('/get_contact_addresses/{contact_id}/{type}', 'ProductQuotationsController@get_contact_addresses')->name('quotation.contact.addresses');
					$router->post('/get-tax-details', 'ProductQuotationsController@get_tax_details')->name('taxes.details');
					$router->post('/delete-order-line/{id}', 'ProductQuotationsController@delete_order_line')->name('quotation.orderline.delete');
					$router->post('/delete-optionalorder-line/{id}', 'ProductQuotationsController@delete_optional_order_line')->name('quotation.optionalorderline.delete');
					$router->post('/send_email', 'ProductQuotationsController@send_email')->name('quotation.send.email');
					$router->post('/change_status', 'ProductQuotationsController@change_status')->name('quotation.status.change');
					$router->post('/duplicate/{id}', 'ProductQuotationsController@duplicate')->name('quotation.duplicate');
					$router->post('/filter', 'ProductQuotationsController@filter_records')->name('quotation.filter');
					$router->post('/update-prices', 'ProductQuotationsController@update_prices')->name('quotation.update.prices');

					$router->get('/generate_payment_link/{id}', 'ProductQuotationsController@generate_payment_link')->name('quotation.payment.link');
					$router->get('/payment_link/{id}', 'ProductQuotationsController@payment_link')->name('quotation.payment.pay.link');

					$router->get('/payment-success/{quotationid}','ProductQuotationsController@paymentredirect')->name('quotation.payment.redirect');
					$router->get('/attach_licences/{quotationid}','ProductQuotationsController@attachLicencesPost')->name('quotation.license.attach');
					$router->get('/attach_vouchers/{quotationid}','ProductQuotationsController@attachVouchersPost')->name('quotation.voucher.attach');
					$router->get('/vouchers/{quotationid}','ProductQuotationsController@vouchersList')->name('quotation.voucher.list');
					$router->get('/voucher-change-status/{voucher_id}/{status}','ProductQuotationsController@changeVoucherStatus')->name('quotation.voucher.change-status');
					Route::prefix('/invoice')->group(function ($router) {
						$router->get('/create/{id}', 'InvoicesController@create_invoice')->name('quotation.invoice.create');
						$router->get('/list/{quotation_id}', 'InvoicesController@index')->name('quotation.invoice.index');
						$router->get('/show/{quotation_id}', 'InvoicesController@show')->name('quotation.invoice.show');
						$router->post('/status/{invoice_id}/{status}', 'InvoicesController@change_invoice_status')->name('quotation.invoice.status');
						$router->post('/register-payment', 'InvoicesController@register_payment')->name('quotation.invoice.register-payment');
						$router->post('/refund-payment', 'InvoicesController@refund_payment')->name('quotation.invoice.refund-payment');
					});
				});

				/*  Start Manufacturer Routes */

					Route::prefix('/manufacturers')->group(function ($router) {

						$router->resource('manufacturer','ManufacturerController');
						$router->get('/','ManufacturerController@index')->name('manufacturer.index');
						$router->get('create','ManufacturerController@create')->name('manufacturer.create');
						$router->get('edit/{id?}','ManufacturerController@edit')->name('manufacturer.edit');
						$router->post('store','ManufacturerController@store')->name('manufacturer.store');
						$router->post('addNewMember','ManufacturerController@addNewMember')->name('manufacturer.add.member');
						$router->get('getMembersList/{id}','ManufacturerController@getMembersList')->name('manufacturer.get.members');


						$router->match(['get','post'],'/delete-manufacturer/{id?}', 'ManufacturerController@deleteManufacturer')->name('manufacturer.delete');

					});

				/*  End Manufacturer Routes */

				Route::prefix('/invoices')->group(function ($router) {
					$router->get('/', 'InvoicesController@showAllInvoices')->name('invoices.index');
				});
				$router->resource('/quotations', 'ProductQuotationsController');
				$router->post('/get-email-template/{id}', 'EmailTemplateController@get_template_detail')->name('email.tempate.detail.ajax');
				// $router->resource('email-templates', 'EmailTemplateController');

				$router->resource('/customers', 'ProductCustomersController');
				$router->post('/save-customer-address', 'ProductCustomersController@saveContactAddress')->name('customer.address.save');
				$router->post('/delete-customer-address', 'ProductCustomersController@deleteCustomerAddress')->name('customer.address.delete');
				$router->post('/get-customer-address-detail', 'ProductCustomersController@getCustomerAddressDetail')->name('customer.address.detail');
				$router->post('/check-duplicate-email', 'ProductCustomersController@checkDuplicateEmail')->name('customer.check.email');
				$router->post('/check-duplicate-email-customer', 'ContactController@checkDuplicateEmail')->name('contact.check.email');

				$router->resource('/price-list', 'PricelistsController');
				$router->post('/insert-price-rule', 'PricelistsController@insertPriceListRule')->name('pricelist.rule.insert');
				$router->post('/get-price-rule', 'PricelistsController@getPriceListRule')->name('pricelist.rule.get');
				$router->post('/remove-price-rule', 'PricelistsController@deletePriceListRule')->name('pricelist.rule.remove');

				$router->post('/delete-price-rule', 'PricelistsController@deletePriceList')->name('pricelist.delete');
				$router->post('/archive-price-rule', 'PricelistsController@archivePriceList')->name('pricelist.archive');
				$router->post('/unarchive-price-rule', 'PricelistsController@unarchivePriceList')->name('pricelist.unarchive');

				$router->resource('product-variant', 'ProductVariantController');
				$router->post('/add-product-variant/{id}', 'ProductVariantController@addNewVariant')->name('products.add_one_variant');
				$router->post('/delete-product-variants', 'ProductVariantController@deleteProductVariants')->name('product-variant.delete');
				$router->post('/change-product-variant-status/{id}/{status}', 'ProductVariantController@changeStatus')->name('product-variant.change.status');

				$router->get('/sales-team-analytics', 'SalesTeamController@analytics')->name('sales-team.analytics');
				$router->get('/sales-analysis', 'SalesTeamController@analysis')->name('sales-team.analysis');
				$router->get('/sales-analysis-download', 'SalesTeamController@analysis')->name('sales-team.analysis.download');
				$router->get('/sales_analysis_quotations', 'SalesTeamController@sales_analysis_quotations')->name('sales-team.analysis.quotation.table');
				// $router->resource('edit-product-variant/{id}{v_id}', 'ProductVariantController@edit')->name('product-variant.edit-p');
				Route::prefix('/configuration')->group(function ($router) {
					$router->resource('/taxes', 'TaxController');
					$router->delete('sale-team-delete-bulk', 'SalesTeamController@bulkDelete')->name('sale-team.bulk.delete');
					$router->post('archive-sale-sale-user', 'SalesTeamController@isArchiveSaleTeam')->name('sale-team.archive.record');
					$router->get('/sales-team/duplicate/{id}', 'SalesTeamController@duplicateSaleTeam')->name('sale-team.duplicate');
					$router->get('admin-users-list', 'SalesTeamController@userList')->name('admin-users.list');
					$router->resource('/sales-team', 'SalesTeamController');
					$router->post('remove-team-member', 'TeamMemberController@removeTeamMember')->name('sale-team.remove.member');
					$router->delete('bulk-member-selection', 'SalesTeamController@memberSelection')->name('bulk.member.selection');
					$router->resource('/sales-team-member', 'TeamMemberController');
					$router->post('/update-team-member', 'TeamMemberController@updateTeamMember')->name('sales-team-member.update.member');
				});

				$router->get('channel-pilot-sales', 'ChannelPilotController@analytics')->name('channel-pilot-sales-analytics');

				$router->get('export-feed-to-channel-pilot', 'ChannelPilotController@export_feed_to_channel_pilot')->name('channel-pilot-export-feed');
				$router->get('export-channel-pilot-products-data/{type}', 'ChannelPilotController@export_products_data')->name('channel-pilot.export.products.data');
				$router->get('channel-pilot-api-logs', 'ChannelPilotController@getLogs')->name('channel-pilot.api.logs');

				$router->get('channel-pilot-get-marketplace-orders', 'ChannelPilotController@getMarketPlaceOrders')->name('channel-pilot.marketplace.orders.get');
				$router->get('channel-pilot-list-marketplace-orders', 'ChannelPilotController@marketPlaceOrdersList')->name('channel-pilot.marketplace.orders');
				$router->get('channel-pilot-get-marketplace-orders-detail/{id}', 'ChannelPilotController@marketPlaceOrdersDetail')->name('channel-pilot.marketplace.order.detail');
			});
			// voucher
			Route::prefix('/voucher')->group(function (){
				Route::get('/dashboard','VoucherController@dashboard')->name('voucher.dashboard');
				Route::get('/orders','VoucherController@orders')->name('voucher.orders');
				Route::get('/invoices','VoucherController@invoices')->name('voucher.invoices');
				Route::get('/distributor-invoices','VoucherController@distributorInvoices')->name('voucher.distributor-invoices');
				Route::get('/invoice-pdf/{id}','VoucherController@invoicePDF')->name('voucher.invoice.pdf');
				Route::get('/view-payment/{id}','VoucherController@viewPayment')->name('voucher.orders.payment.detail');
				Route::post('/register_payment/{id}','VoucherController@registerPayment')->name('voucher.orders.payment.register');
				Route::post('/refund_payment/{id}','VoucherController@refundPayment')->name('voucher.orders.payment.refund');
				Route::post('/orderVoucher','VoucherController@orderVoucherPost')->name('voucher.orderVoucherPost');
				Route::get('/order-change-status/{id}/{status}/{product_name}','VoucherController@orderChangeStatus')->name('voucher.order.change-status');
				Route::get('/order-vouchers/{id}','VoucherController@orderVouchers')->name('voucher.order.vouchers');
				Route::get('/export-order-vouchers/{id}','VoucherController@exportVouchers')->name('voucher.order.vouchers.export');
				Route::get('/change-order-voucher-status/{id}/{status}','VoucherController@changeOrderVoucherStatus')->name('change.voucher.order.vouchers.status');
				Route::post('/change-bulk-order-voucher-status','VoucherController@changeBulkOrderVoucherStatus')->name('change.bulk.voucher.order.vouchers.status');
				Route::get('/payment/{id}','VoucherController@voucherPayment')->name('voucher.payment');
				Route::get('/voucher-payment/{id}','VoucherController@makeVoucherPayment')->name('make.voucher.payment');
				Route::get('/voucher-payment/success/{id}','VoucherController@voucherPaymentSuccess')->name('voucher.payment.success');
				Route::get('/voucher-report','VoucherController@generateVoucherReport')->name('voucher.generate.report');
			});
			// website
			Route::prefix('/website')->group(function (){
				Route::get('/dashboard','WebsiteController@dashboard')->name('website.dashboard');
				Route::get('/abandoned-carts','WebsiteController@getAbandonedCart')->name('website.abandoned.carts');
				Route::get('/visitors','WebsiteController@getVisitors')->name('website.visitors');
				Route::get('/visitor/{id}','WebsiteController@getVisitorDetails')->name('website.visitor.detail');
				Route::get('/views','WebsiteController@getViewVisits')->name('website.views');
				Route::get('/resellers','WebsiteController@resellerListing')->name('website.resellers');
				Route::get('/reseller-redeemed-pages/{id}','WebsiteController@CreateResellerRedeemedPage')->name('reseller.redeemed');
				Route::post('/reseller-redeemed-pages-add','WebsiteController@AddResellerRedeemedPage')->name('website.reseller.redeemed.page.add');
				Route::get('/projects','WebsiteController@getProjectsList')->name('website.projects');

				// Lawful Interception
				Route::get('/lawful-interception','WebsiteController@lawfulInterception')->name('website.lawfulinterception');
				// Reseller Lawful Interception Routes
				Route::get('/lawful-interception/reseller-pdf/{contact_id}','WebsiteController@lawfulInterceptionResellerPdf')->name('website.lawfulinterception.resellerpdf');
				Route::get('/lawful-interception/order-pdf/{contact_id}','WebsiteController@lawfulInterceptionOrderPdf')->name('website.lawfulinterception.orderpdf');
				Route::get('/lawful-interception/voucher-pdf/{contact_id}','WebsiteController@lawfulInterceptionVoucherPdf')->name('website.lawfulinterception.voucherpdf');
				Route::get('/lawful-interception/voucher-payment-pdf/{contact_id}','WebsiteController@lawfulInterceptionVoucherPaymentPdf')->name('website.lawfulinterception.voucherpaymentpdf');
				// Customer Lawful Interception Routes
				Route::get('/lawful-interception/customer-pdf/{contact_id}','WebsiteController@lawfulInterceptionCustomerPdf')->name('website.lawfulinterception.customerpdf');
				Route::get('/lawful-interception/customer-order-pdf/{contact_id}','WebsiteController@lawfulInterceptionCustomerOrderPdf')->name('website.lawfulinterception.customerorderpdf');
				Route::get('/lawful-interception/customer-invoices-pdf/{contact_id}','WebsiteController@lawfulInterceptionCustomerInvoicesPdf')->name('website.lawfulinterception.customerinvoices');
				Route::get('/lawful-interception/customer-carts-pdf/{contact_id}','WebsiteController@lawfulInterceptionCartsPdf')->name('website.lawfulinterception.customercarts');
				Route::get('/lawful-interception/customer-export-all/{contact_id}','WebsiteController@lawfulInterceptionCustomerExportAllZip')->name('website.lawfulinterception.customer.export.all.zip');
				// End Customer Lawful Interception Routes
				Route::delete('bulk-delete-faqs', 'FaqsController@bulkDelete')->name('bulk.delete.faqs');
				Route::resource('/faqs', 'FaqsController');
				Route::resource('/contact-us-queries', 'ContactQueries');
				Route::get('/lawful-interception/voucher-export-all/{contact_id}','WebsiteController@lawfulInterceptionExportAllZip')->name('website.lawfulinterception.export.all.zip');

				Route::resource('/faqs', 'FaqsController');

				// Payment Gateways
				Route::get('/payment-gateways', 'WebsiteController@paymentGateways')->name('website.payment.gateways');
				Route::post('/update-payment-gateways', 'WebsiteController@updatePaymentGateways')->name('website.payment.gateways.update');
			});

			$router->resource('/distributor', 'DistributorController');

			$router->resource('/reseller-package', 'ResellerPackageController');
			$router->post('/add-reseller-package-rule', 'ResellerPackageController@storeRule')->name('reseller-package.store-rule');
			$router->post('/get-reseller-package-rule', 'ResellerPackageController@getRule')->name('reseller.package.rule.get');
			$router->post('/remove-reseller-package-rule', 'ResellerPackageController@removeRule')->name('reseler.package.rule.remove');

			Route::match(['get','post'],'manufacturer-reset-password-link/{id?}','ManufacturerController@resetPasswordLink')->name('manufacturer.reset.password.link');
			$router->get('/license/download-report','LicenseController@downloadLicenseReportInExcel')->name('license.download.report');
			$router->get('/license/download-excel','LicenseController@exportLicenseInExcel')->name('license.download.excel');
			$router->post('/license/import', 'LicenseController@importLicenseKeys')->name('license.import');
			$router->get('/license/dashboard', 'LicenseController@dashboard')->name('license.dashboard');
			$router->post('/license/change/status/{license_id}/{status}', 'LicenseController@changeStatus')->name('license.change.status');
			$router->post('/license/change/bulk/status/', 'LicenseController@changeBulkStatus')->name('change.bulk.license.status');
			$router->resource('/license', 'LicenseController');
			$router->get('/license-files', 'LicenseController@licenseFileListing')->name('license.files');
			$router->post('/license-delete/{id}', 'LicenseController@deleteLicenseFile')->name('license.file.delete');
			$router->get('/license-view/{id}', 'LicenseController@viewLicenseFileContent')->name('license.file.view');
			/* =============Taxes route============= */
		});
	});
});

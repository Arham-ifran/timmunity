@extends('frontside.layouts.app')
@section('title')   Shop @endsection
@section('style')
<style>
    .mb-5 {
        margin-bottom: 40px;
    }
    .custom-row-section {
        display: flex;
        flex-wrap: wrap;
    }
    /*the container must be positioned relative:*/
    .autocomplete {
        position: relative;
        display: inline-block;
    }

    .autocomplete-items {
        position: absolute;
        border: 1px solid #d4d4d4;
        border-bottom: none;
        border-top: none;
        z-index: 99;
        /*position the autocomplete items to be the same width as the container:*/
        top: 100%;
        left: 0;
        right: 0;
    }
    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }
    .autocomplete-items div {
        padding: 10px;
        cursor: pointer;
        background-color: #fff;
        border-bottom: 1px solid #d4d4d4;
    }

    /*when hovering an item:*/
    .autocomplete-items div:hover {
        background-color: #e9e9e9;
    }

    /*when navigating through the items using the arrow keys:*/
    .autocomplete-active {
        background-color: #009b72 !important;
        color: #ffffff;
    }
    .autocomplete-active a{
        color: white;
    }
    #sortBy ul.dropdown-submenu {
        padding: 0;
        margin: auto;
    }
    #sortBy li.dropdown-item {
        padding: 5px 20px;
    }
    #sortBy li.dropdown-item:hover {
        background: #009b72;
        color: white;
        cursor: pointer;
    }
    .modal-header .modal-title{
        font-size: 24px;
    }
</style>
@endsection
@section('content')

	<!-- Product Section -->
	<section class="content-section" id="shop-page">
		<div class="container">

			<!-- Base Top Search Bar -->
			<div class="row bottom-space">
				<div class="container">
					<div class="col-lg-12 shop-search-bar">
						<div class="top-base col-lg-12">
							<div class="search-input left-side col-lg-6 pl-0">
                                <form autocomplete="off">
                                    <i class="fa fa-search"></i>
                                    <input type="text" name="search" id="productSearch" placeholder="{{ __('Search...') }}">
                                    <div class="autocomplete" style="width:300px;">
                                    </div>
                                </form>
							</div>
							<div class="right-side col-lg-6 pull-right">
								{{-- <a href="#"><i class="active fa fa-th-large" aria-hidden="true"></i></a>
								<a href="#"><i class="fa fa-th-list" aria-hidden="true"></i></a> --}}
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" style="text-decoration: none; color: black;">{{ __('Sort by') }} <span style="color:#009a71;" class="caret"></span></a>
                                <div class="dropdown-menu" id="sortBy">
                                    <ul class="dropdown-submenu">
                                        <li  class="dropdown-item" data-action="0">{{ __('Price: Low to High') }}</li>
                                        <li class="dropdown-item" data-action="1">{{ __('Price: High to Low') }}</li>
                                        <li class="dropdown-item" data-action="2">{{ __('Product Name: A to Z') }}</li>
                                        <li class="dropdown-item" data-action="3">{{ __('Product Name: Z to A') }}</li>
                                    </ul>
                                 </div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Start Shop List Row One-->
			{{-- <div class="row custom-row-section clearfix bottom-space" id="productSpace"> --}}
			<div class="row custom-row-section" id="productSpace">
				@include('frontside.shop.partials.shop-products')
			</div>

		</div>
        @csrf
	</section>
    @if( Auth::user() && Auth::user()->contact->type == 3)
        @include('frontside.reseller.partials.voucher_order_form')
    @endif
@endsection
@section('script')
    @if( Auth::user() && Auth::user()->contact->type == 3) 
        <script src="{{ asset('frontside\dist\js\reseller.js') }}"></script>
    @endif
    <script>
        var currency = "{{ Session::get('currency_symbol') == null ? $default_currency->symbol : Session::get('currency_symbol') }}";
        var currency_code = "{{ Session::get('currency_code') == null ? $default_currency->code : Session::get('currency_code') }}";
        /** Product Search Auto complete Start **/
        var currentFocus;
        var currentRequest = null;
        inp = document.getElementById('productSearch');
        thiselement = null;
        var url = "{{ route('get-product-variations', ':id') }}";;
        var variation_select_default_text = "{{__('Select Variation')}}";
        var detail_url = "{{ route('get-product-variation-detail', ':id') }}";
        @php
            foreach($products as $ind => $product){
                $products[$ind]->hashed_id = Hashids::encode($product->id);
                $products[$ind]->secondary_projects = $product->secondary_projects_array;
            }
        @endphp
        var products = @json($products);
        var product_label = "{{ __('Product') }}";
        var select_product_label = "{{ __('Select Product') }}";
        var secondary_project_label = "{{ __('Secondary Products') }}";
        var variation_label = "{{ __('Variation') }}";
        var price_label = "{{ __('Price') }}";
        var quantity_label = "{{ __('Quantity') }}";

        /*execute a function when someone writes in the text field:*/
        inp.addEventListener("input", function(e) {
            thiselement = this;
            makeAjaxSearchCall($(this).val(),thiselement.parentNode);
        });
        /*execute a function presses a key on the keyboard:*/
        inp.addEventListener("keydown", function(e) {
            // var x = document.getElementById(thiselement.id + "autocomplete-list");
            // if (x) x = x.getElementsByTagName("div");
            if (e.keyCode == 40) {
                x = document.getElementById(thiselement.id + "autocomplete-list");
                if (x) x = x.getElementsByTagName("div");
                /*If the arrow DOWN key is pressed,
                increase the currentFocus variable:*/
                currentFocus++;
                /*and and make the current item more visible:*/
                addActive(x);
            } else if (e.keyCode == 38) { //up
                /*If the arrow UP key is pressed,
                decrease the currentFocus variable:*/
                x = document.getElementById(thiselement.id + "autocomplete-list");
                if (x) x = x.getElementsByTagName("div");
                currentFocus--;
                /*and and make the current item more visible:*/
                addActive(x);
            } else if (e.keyCode == 13) {
                /*If the ENTER key is pressed, prevent the form from being submitted,*/
                e.preventDefault();
                x = document.getElementById(thiselement.id + "autocomplete-list");
                if (x) x = x.getElementsByTagName("div");
                if (currentFocus > -1) {
                /*and simulate a click on the "active" item:*/
                if (x) {
                    // $(x[currentFocus]).find('a').click();
                    location.href = $(x[currentFocus]).find('a').attr('href');
                    // x[currentFocus].click();
                }
                }
            }
        });
        function addActive(x) {
            /*a function to classify an item as "active":*/
            if (!x) return false;
            /*start by removing the "active" class on all items:*/
            removeActive(x);
            if (currentFocus >= x.length) currentFocus = 0;
            if (currentFocus < 0) currentFocus = (x.length - 1);
            /*add class "autocomplete-active":*/
            x[currentFocus].classList.add("autocomplete-active");
        }
        function removeActive(x) {
            /*a function to remove the "active" class from all autocomplete items:*/
            for (var i = 0; i < x.length; i++) {
            x[i].classList.remove("autocomplete-active");
            }
        }
        function closeAllLists(elmnt) {
            /*close all autocomplete lists in the document,
            except the one passed as an argument:*/
            var x = document.getElementsByClassName("autocomplete-items");
            for (var i = 0; i < x.length; i++) {
            if (elmnt != x[i] && elmnt != inp) {
                x[i].parentNode.removeChild(x[i]);
            }
            }
        }
        function makeAjaxSearchCall(q, parentNode) {

            var a, b, i, val = thiselement.value;
            /*close any already open lists of autocompleted values*/
            closeAllLists();
            var fd = new FormData();
            fd.append('_token', $('input[name="_token"]').val());
            fd.append('q', q);
            currentRequest = $.ajax({
                url: '{{ route("frontside.shop.search") }}',
                data: fd,
                type: 'POST',
                processData: false,
                contentType: false,
                beforeSend : function()    {
                    if(currentRequest != null) {
                        currentRequest.abort();
                    }
                },
                success: function (data) {
                    arr = data.data;
                    currentFocus = -1;
                    /*create a DIV element that will contain the items (values):*/
                    a = document.createElement("DIV");
                    a.setAttribute("id", thiselement.id + "autocomplete-list");
                    a.setAttribute("class", "autocomplete-items");

                    /*append the DIV element as a child of the autocomplete container:*/
                    parentNode.appendChild(a);

                    /*for each item in the array...*/
                    for (i = 0; i < arr.length; i++) {
                        url = "{{ route('frontside.shop.product-details',':id') }}";
                        // url = url.replace(':id', arr[i].hashed_id);
                        url = url.replace(':id', arr[i].slug);
                        /*create a DIV element for each matching element:*/
                        b = document.createElement("DIV");
                        b.setAttribute("class", "row");

                        imagesrc = "{{ asset('storage/uploads/sales-management/products/:image') }}";
                        imagesrc = imagesrc.replace(':image', arr[i].image);
                                // innerHTML += "<img class='col-md-4' src='"+ imagesrc +"'/>";
                                innerHTML = "<a class='col-md-8' href='"+ url +"'>" + arr[i].product_name + "</a>";
                        /*make the matching letters bold:*/
                        b.innerHTML = innerHTML;
                        // b.innerHTML += arr[i].product_name.substr(val.length);
                        /*insert a input field that will hold the current array item's value:*/
                        a.appendChild(b);
                    }
                },
            });
        }
        /*execute a function when someone clicks in the document:*/
        document.addEventListener("click", function (e) {
            closeAllLists(e.target);
        });

        /** Product Search Auto complete End **/

        /** Product Listing Sorting Start **/
        $('body').on('click', '#sortBy ul li', function(){
            $('#productSpace').html('');
            $.ajax({
                url: '{{ route("frontside.shop.index") }}?sort_type='+ $(this).data('action'),
                type: 'GET',
                processData: false,
                contentType: false,
                beforeSend : function()    {

                },
                success: function (data) {
                    $('#productSpace').html(data.html);
                }
            });
        });

        $('body').on('click','.voucher-btn',function(){
            $('#getVoucherModal').modal('show');
            $('[name="product_id[]"]').val($(this).data('id'));
            $('[name="product_id[]"]').change();
        })
         // On product Change from get more vouchers form
         $('body').on('change', 'select[name=product_id]', function(){
            variation_count = $(this).find(':selected').data('variation-count');
                url = "{{ route('get-product-variations', ':id') }}";
                url = url.replace(":id", $(this).val());
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        if(data['success'] == 'true'){
                            // console.log(data)
                            if(variation_count > 0){
                                variations = data.data.variations;
                                $('select[name=variation_id]').find('option').remove().end()
                                $('select[name=variation_id]').append('<option value="">{{__("Select Variation")}}</option>');
                                $.each(variations,function(index,value){
                                    $('select[name=variation_id]').append('<option value="'+value.hashedid+'">'+data.data.product_name+' '+value.variation_name+'</option>')
                                })
                                $('select[name=variation_id]').attr('required','required');
                                $('.variation_selection').show();
                            }else{
                                $('select[name=variation_id]').removeAttr('required','required');
                                $('.variation_selection').hide();
                            }
                            product_price = data.data.product_price;
                            if(data.data.end_product_price != 0 && data.data.product_price < data.data.end_product_price){
                                product_price += ' - '+data.data.end_product_price;
                            }
                            $('.product_price').show();
                            $('.product_price_label strong').html(currency+product_price+' '+currency_code);
                        }
                    },
                    complete:function(data){

                    }
                })
            html = '';
            length = $('option:selected',this).data('secondary-projects').length;
            $('option:selected',this).data('secondary-projects').forEach(function(val, ind){
                html += val;
                if(ind < length - 1){
                    html += ', ';
                }
            });
            if(length > 0){
                $('.secondary_projects_div').show();
                $('.secondary_projects_div .data').html(html);
            }else{
                $('.secondary_projects_div').hide();
                $('.secondary_projects_div .data').html('');
            }
        });
        $('body').on('change', 'select[name=variation_id]', function(){
            url = "{{ route('get-product-variation-detail', ':id') }}";
                url = url.replace(":id", $(this).val());
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        if(data['success'] == 'true'){
                            // console.log(data)
                            product_price = data.data;
                            $('.product_price').show();
                            $('.product_price_label strong').html(currency+product_price+' '+currency_code);
                        }
                    },
                    complete:function(data){

                    }
                })
        });
        // On changing the country
        $('body').on('change', '[name=country_id]',function(){
            vat_percentage = 0;
            vat_label = '';
            selected_option = $('option:selected',this);
            if(selected_option.data('is_default_vat') == 1){
                vat_percentage = selected_option.data('default_vat');
                if(selected_option.data('vat_label')){
                    vat_label = selected_option.data('vat_label');
                }
                else{
                    vat_label = 'VAT';
                }
            }else{
                vat_percentage =  selected_option.data('vat_vercentage');
                if(selected_option.data('vat_label')){
                    vat_label = selected_option.data('vat_label');
                }
                else{
                    vat_label = 'VAT';
                }
                
            }
            $("[name=vat_percentage]").val(vat_percentage)
            $(".vat_percentage").html(vat_percentage)
            $(".vat-label-d").html(vat_label)
        })

    </script>
@endsection

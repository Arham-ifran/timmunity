{{-- Start Edit Contact Model --}}
<div class="modal fade in " id="edit-contact-model" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="contact-big-model modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="col-sm-8" id="exampleModalLongTitle">{{ __('Edit Contact Address') }}</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span>
                </button>
            </div>
            {{-- <div class="modal-body clearfix"> --}}
            <!-- Form Start Here Contact Address  -->

            <form class="model-form-validate" method="POST" action="{{ route('admin.contact-address.update', $model->id) }}"
                id="edit-contact-address-form" @if($action == 'edit') action="{{ route('admin.contact-address.update', @$model->id) }}"   @endif  enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <input type="hidden" name="id" id="hidden_id" value="{{ $model->id }}">
                <div class="col-md-12 tab-form">
                    <div class="clearfix mt-2">
                        <div class="col-md-12 pl-0">

                            <div class="col-md-12 pt-1 customer-radio-button pb-3">
                                <input type="radio" class="chkd" id="edit-contact-address-radio" name="type" value="0" @if (isset($model->type) && $model->type == 0) checked @endif>
                                <label for="edit-contact-address-radio">{{ __('Contact') }}</label>

                                <input type="radio" class="chkd" id="edit-invoice-address" name="type" value="1" @if (isset($model->type) && $model->type == 1) checked @endif>
                                <label for="edit-invoice-address">{{ __('Invoice Address') }}</label>

                                <input type="radio" class="chkd" id="edit-delivery-address" name="type" value="2" @if (isset($model->type) && $model->type == 2) checked @endif>
                                <label for="edit-delivery-address">{{ __('Delivery Address') }}</label>

                                <input type="radio" class="chkd" id="edit-other-address" name="type" value="3" @if (isset($model->type) && $model->type == 3) checked @endif>
                                <label for="edit-other-address">{{ __('Other Address') }}</label>

                                <input type="radio" class="chkd" id="edit-private-address" name="type" value="4" @if (isset($model->type) && $model->type == 4) checked @endif>
                                <label for="edit-private-address">{{ __('Private Address') }}</label>

                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label>{{ __('Contact Name') }}</label>
                            <input type="text"
                                class="form-control @error('contact_name') is-invalid @enderror" id="edit-contact-name"
                                name="contact_name" value="{{ old('contact_name', $model->contact_name ?? '') }}"
                                maxlength="255" aria-describedby="contact_name" >

                                <div id="edit-contact-name-error" class="invalid-feedback animated fadeInDown edit">
                                </div>

                            @error('contact_name')
                                <div id="contact_name-error" class="invalid-feedback animated fadeInDown">
                                    {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-3" id="edit-job-position-address">
                            <label>{{ __('Job Position') }}</label>
                            <input class="form-control" type="text" name="job_position" id="job-position"
                                value="{{ old('job_position', $model->job_position ?? '') }}">
                        </div>
                        <div class="form-group col-md-3">
                            <label>{{ __('Notes') }}</label>
                            <input class="form-control" type="text" name="notes" id="notes"  value="{{ old('notes', $model->notes ?? '') }}">
                        </div>

                        <div class="form-group col-md-3">
                            <label>{{ __('Email') }}</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email', $model->email ?? '') }}" maxlength="255"
                                aria-describedby="email" >
                            @error('email')
                                <div id="email-error" class="invalid-feedback animated fadeInDown">
                                    {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-3">
                            <label>{{ __('Phone') }}</label>
                            <input class="form-control" type="number" name="phone"
                                value="{{ old('phone', $model->phone ?? '') }}">
                        </div>
                        <div class="form-group col-md-3">
                            <label>{{ __('Mobile') }}</label>
                            <input class="form-control" type="number" name="mobile"
                                value="{{ old('mobile', $model->mobile ?? '') }}">
                        </div>
                    </div>

                    <div class="form-group col-md-3" id="edit-select-title-address">
                        <label>{{ __('Title') }}</label>
                        <select class="form-control" name="title_id" style='color:gray' oninput='style.color="black"'>
                            <option style="display:none" value="">---{{ __('Select a title') }} ---</option>
                            @if ($contact_titles->count() > 0)
                                @foreach ($contact_titles as $title)
                                    <option value="{{ $title->id }}" @if (isset($model) && $title->id == $model->title_id) selected @endif>
                                        {{ $title->title }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <div class="avatar-upload form-group">
                            <div class="avatar-fileds hover-effect">
                                <div class="avatar-edit">
                                    <input type='file' id="editimageUpload" name="contact_image" maxlength="255"
                                        aria-describedby="image" accept="image/*" value="{{$model->contact_image}}"  />
                                    <label for="editimageUpload"></label>
                                </div>
                            </div>
                            <div class="avatar-preview">

                            <img id="image-preview" src="{!!checkImage(asset('storage/uploads/contact-address/'.'/'. $model->contact_image),'avatar5.png')!!}" width="100%" height="100%">

                            </div>




                        </div>
                    </div>
                    <div class="row" id="edit-contact-address-area">
                        <h4 class="col-md-12">{{ __('Address') }}</h4>
                        <div class="form-group col-md-3">
                            <input class="form-control" type="text" name="street_1" placeholder="Street no 1"  value="{{ old('street_1', $model->street_1 ?? '') }}">
                        </div>
                        <div class="form-group col-md-3">
                            <input class="form-control" type="text" name="street_2" placeholder="Street no 2" value="{{ old('street_2', $model->street_2 ?? '') }}">
                        </div>
                        <div class="form-group col-md-3">
                            <input class="form-control" type="text" name="city" placeholder="City" value="{{ old('city', $model->city ?? '') }}">
                        </div>
                        <input type="hidden" class="form-control" name="state_id" />
                        {{-- <div class="form-group col-md-3">
                            <select class="form-control" name="state_id" style='color:gray'
                                oninput='style.color="black"'>
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
                            <input class="form-control" type="number" name="zipcode" placeholder="Zip Code" value="{{ old('zipcode', $model->zipcode ?? '') }}">
                        </div>
                        <div class="form-group col-md-3">
                            <h4>{{ __('Country') }}</h4>
                            <select class="form-control" name="country_id" style='color:gray'
                                oninput='style.color="black"'>
                                <option value="">---{{ __('Select a country') }}---</option>
                                @if ($contact_countries->count() > 0)
                                    @foreach ($contact_countries as $country)
                                        <option value="{{ $country->id }}" @if (isset($model) && $country->id == $model->country_id) selected @endif>
                                            {{ $country->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                    </div>
                </div>
                <!-- End Here -->

                {{-- </div> --}}
                <!-- Footer model popupp -->
                <div class="modal-footer ">
                    <button type="submit" class="btn btn-success" id="edit-save-change">{{ __('Save & Close') }}</button>
                    <button type="submit" class="btn btn-success" id="edit-save-and-new">{{ __('Save & New') }}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="discard-btn">{{ __('Discard') }}</button>
                    <button type="submit" class="btn btn-secondary" id="edit-contact-address-remove"
                        data-id="{{ $model->id }}">{{ __('Remove') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- End Edit Contact Model --}}
</div>

<script type="text/javascript">
$('#edit-contact-address').ready(function() {
    var multiple_addresses = [];
            let status = $('.chkd:checked').val();
            console.log(status ,'status checked');
                if (status == 0) {
                    $('#edit-select-title-address').show();
                    $('#edit-job-position-address').show();
                    $('#edit-contact-address-area').hide();
                }
                else if(status == 1 || status == 2 || status == 3 || status == 4){

                    $('#edit-contact-address-area').show();
                    $('#edit-select-title-address').hide();
                    $('#edit-job-position-address').hide();
                }


    $(document).ready(function() {

            $('#edit-contact-address-radio').on('change', function() {
                $('#edit-contact-address-area').hide();
                $('#select-title-address').show();
                $('#job-position-address').show();
            })

            $('#edit-invoice-address, #edit-delivery-address, #edit-other-address, #edit-private-address')
            .on('change', function() {
                $('#edit-contact-address-area').show();
                $('#edit-select-title-address').hide();
                $('#edit-job-position-address').hide();
            });






    });

            //=========Contact Address Edit Model checked===========/




        $('#edit-save-and-new').on('click',function(){
            setTimeout(()=>{
            $("#edit-other-address:radio").prop('checked',true);
            $('#edit-contact-address-area').show();
            $('#edit-select-title-address').hide();
            $('#edit-job-position-address').hide();

            },1.0)
        });

        $('#edit-save-change').click(function() {
            $('#edit-contact-model').modal('hide');
        });


        $('#edit-save-and-new').click(function() {
        setTimeout(()=>{
        $("#edit-other-address:radio").prop('checked',true);
        $('#edit-contact-address-area').show();
        $('#edit-select-title-address').hide();
        $('#edit-job-position-address').hide();
        },1.0);
        });

        $('#edit-save-change').click(function() {
        let mdl =  $('#edit-contact-name').val();
       console.log(mdl,'name val empty');
        // if(mdl === ""){
          console.log('edit val no ');
        //   $('#contact-model').modal('show');
        //   $("#edit-contact-model").modal("show");

        // }else{
            console.log(mdl,'name val null')
        console.log('edit val in');
        // $("#edit-contact-model").modal("show");
        // }
        });

        $('#discard-btn').click(function() {
        $('#edit-contact-name-error').empty();
        });

$('#edit-contact-address-form').on('submit', function(e) {

e.preventDefault();
$('#edit-contact-name').on('keypress',function()
{
    $('#edit-contact-name-error').empty()
})
let route = $(this).attr('action');
let formData = new FormData(this);
console.log(route);
// formData.append('contact_name',$('#contact-name').val());
// alert(JSON.stringify(formData))
// return

$.ajax({
        url:route,
        type: "post",
        contentType: false,
        processData: false,
        cache:false,
        data: formData,
        dataType:'JSON',
        success: (data) => {
            console.log(data,'edit data response');
            var [contact_addresses] = data;
            var {contact_addresses} = contact_addresses;
            multiple_addresses.push(data)
            $('#contact-address-row').empty().append();
            $('#contact-address-row').append(contact_addresses.map((contact, index) => {
                var {id,contact_image} = contact;
                return '<div class="col-sm-6 col-md-4 mt-2">'+
                            '<a href="javascript:void(0)" id="edit-contact-address" data-contact-address-id="'+id+'" >'+
                                '<input type="hidden" class="contact-address-id" value="'+id+'">'+
                                '<div class="customer-box">'+
                               ' <div class="customer-img">'+
                                // ' <img id="contact-address-image" src="{{asset('storage/uploads/contact-address/')}}/'+id+'/'+contact_image+' "avatar5.png" "  width="100%" height="100%">'+
                                ' <img id="contact-address-image" src="{{asset('storage/uploads/contact-address/')}}/'+contact_image+' "avatar5.png" "  width="100%" height="100%">'+
                               ' </div>'+
                                '<div class="customer-content col-md-6">'+
                                   ' <h5 class="sub-heading">edit modl:'+contact.contact_name+'</h5>'+
                                   '<a href="#"> <span class="email">'+contact.email+'</span></a>'+
                                   ' <h5 class="customer-heading">"{{ __('Phone') }}":'+(contact.phone ? contact.phone : '' )+'</h5>'+
                                   ' <h5 class="customer-heading">"{{ __('Mobile') }}":'+(contact.mobile ? contact.mobile : '' )+'</h5>'+
                                '</div>'+
                          '  </div>'+
                       ' </a>'+
                       ' </div>';
            }))


            },
            error: function(xhr, error,status){

                        $.each(xhr.responseJSON,function(key,item){
                            $('#edit-contact-name-error').empty().append(item.contact_name);
                            $('#email-error').empty().append(item.email);
                        });
                        $('#contact-model').modal('show');


             }

       });
   });

        jQuery(document).ready(function($) {
            $('#delete_image').hide();
            $('#edit_image').hide();
            $('[name="image"]').hide();

            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#image-preview').show();
                        $('#image-preview').attr('src', e.target.result);
                        $('#add_image').hide();
                        $('#edit_image').show();
                        $('#delete_image').show();
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            $('[name="contact_image"]').change(function() {
                readURL(this);
            });

            $('#add_image,#edit_image').click(function(event) {
                $('[name="contact_image"]').trigger('click');
            });

            $('#delete_image').click(function(event) {
                $('[name="contact_image"]').val('');
                $('#image-preview').attr('src', '');
                $('#image-preview').hide();
                $('#delete_image').hide();
                $('#edit_image').hide();
                $('#add_image').show();
            });
        });
    })



</script>

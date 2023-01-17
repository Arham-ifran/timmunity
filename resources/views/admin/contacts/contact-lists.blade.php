@if ($contacts->count() > 0)
    @foreach ($contacts as $ind => $contact)
        @if($ind == 0 || $ind % 3 == 0 ) <div class="row"> @endif
            <div class="{{$ind % 3 == 2}} col-lg-4 col-md-4 col-sm-12">
                <div class="contact-box-wrapp">
                    <div class="contact-tag @if($contact->type == 0)
                        contact-ribbon
                    @elseif($contact->type == 1)
                        admin-ribbon
                    @elseif($contact->type == 2)
                        customer-ribbon
                    @elseif($contact->type == 3)
                        reseller-ribbon
                    @elseif($contact->type == 4)
                        guest-ribbon
                    @endif">
                        <span class=" ">
                            @if($contact->type == 0)
                                {{ __('Contact') }}
                            @elseif($contact->type == 1)
                                {{ __('Admin') }}
                            @elseif($contact->type == 2)
                                {{ __('Customer') }}
                            @elseif($contact->type == 3)
                                {{ __('Reseller') }}
                            @elseif($contact->type == 4)
                                {{ __('Guest') }}
                            @endif
                        </span>
                    </div>
                    @if($contact->admin_id != null && $contact->user_id == null)
                      <a href="{{route('admin.admin-user.edit', Hashids::encode( $contact->admin_id))}}">
                    @else
                      <a href="{{route('admin.contacts.edit', Hashids::encode( $contact->id))}}">
                    @endif
                        <div class="customer-box">
                            <div class="customer-img">
                                @if($contact->admin_id == null)
                                <img id="imagePreview" src="{!! checkImage(asset('storage/uploads/contact/' . Hashids::encode($contact->id) . '/' . @$contact->image), 'avatar5.png') !!}"
                                    width="100%" height="100%">
                                @else
                                <img id="imagePreview" src="{!! checkImage(asset('storage/uploads/admin/' . Hashids::encode(@$contact->admin_users->id) . '/' . @$contact->admin_users->image), 'avatar5.png') !!}"
                                    width="100%" height="100%">
                                @endif
                            </div>
                            <div class="customer-content">
                                <h3 class="customer-heading">{{$contact->name}}</h3>
                                <h5 class="sub-heading">{{$contact->contact_countries->name ?? '' }}</h5>
                                <span class="email">{{$contact->email}}</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @if($ind == ($contacts->count() - 1) || $ind % 3 == 2 ) </div> @endif
    @endforeach
@else
    <h5 class="text-center">{{ __('Not Record Found') }}</h5>
@endif


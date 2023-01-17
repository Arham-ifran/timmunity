<div class="modal fade" id="kss_modal" tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span>
                    </button>
                </div>
            </div>
            <div class="modal-body ">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <h3 class="col-sm-12 text-center" id="exampleModalLongTitle">{{ __('Kaspersky Exchange Program') }}</h3>
                        <p> {{__("If you're using a Kaspersky license and want to be in the exchange program click below button.")}}</p>
                        <a href="{{route('frontside.page.KasperskyExchangePage')}}" class="btn btn-primary mt-2">{{__('Visit Exchange Program')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

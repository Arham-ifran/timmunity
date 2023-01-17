<div class="modal fade" id="alert_message_modal" tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body ">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <h1 class="alert-icon"> 
                            {{-- <i class="fa fa-check"></i> --}}
                           
                            @if ($message = Session::get('alert-success'))
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" style="fill: green"><path d="M 16 3 C 8.800781 3 3 8.800781 3 16 C 3 23.199219 8.800781 29 16 29 C 23.199219 29 29 23.199219 29 16 C 29 14.601563 28.8125 13.207031 28.3125 11.90625 L 26.6875 13.5 C 26.886719 14.300781 27 15.101563 27 16 C 27 22.101563 22.101563 27 16 27 C 9.898438 27 5 22.101563 5 16 C 5 9.898438 9.898438 5 16 5 C 19 5 21.695313 6.195313 23.59375 8.09375 L 25 6.6875 C 22.699219 4.386719 19.5 3 16 3 Z M 27.28125 7.28125 L 16 18.5625 L 11.71875 14.28125 L 10.28125 15.71875 L 15.28125 20.71875 L 16 21.40625 L 16.71875 20.71875 L 28.71875 8.71875 Z"/></svg>
                            @elseif ($message = Session::get('alert-error'))
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" style="fill: red"><path d="M 16 3 C 8.832031 3 3 8.832031 3 16 C 3 23.167969 8.832031 29 16 29 C 23.167969 29 29 23.167969 29 16 C 29 8.832031 23.167969 3 16 3 Z M 16 5 C 22.085938 5 27 9.914063 27 16 C 27 22.085938 22.085938 27 16 27 C 9.914063 27 5 22.085938 5 16 C 5 9.914063 9.914063 5 16 5 Z M 12.21875 10.78125 L 10.78125 12.21875 L 14.5625 16 L 10.78125 19.78125 L 12.21875 21.21875 L 16 17.4375 L 19.78125 21.21875 L 21.21875 19.78125 L 17.4375 16 L 21.21875 12.21875 L 19.78125 10.78125 L 16 14.5625 Z"/></svg>
                            @elseif ($message = Session::get('alert-warning'))
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" style="fill: yellow"><path d="M 16 4 C 9.382813 4 4 9.382813 4 16 C 4 22.617188 9.382813 28 16 28 C 22.617188 28 28 22.617188 28 16 C 28 9.382813 22.617188 4 16 4 Z M 16 6 C 21.535156 6 26 10.464844 26 16 C 26 21.535156 21.535156 26 16 26 C 10.464844 26 6 21.535156 6 16 C 6 10.464844 10.464844 6 16 6 Z M 15 10 L 15 18 L 17 18 L 17 10 Z M 15 20 L 15 22 L 17 22 L 17 20 Z"/></svg>
                            @elseif ($message = Session::get('alert-info'))
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" style="fill:#4cd3e9"><path d="M 16 3 C 8.832031 3 3 8.832031 3 16 C 3 23.167969 8.832031 29 16 29 C 23.167969 29 29 23.167969 29 16 C 29 8.832031 23.167969 3 16 3 Z M 16 5 C 22.085938 5 27 9.914063 27 16 C 27 22.085938 22.085938 27 16 27 C 9.914063 27 5 22.085938 5 16 C 5 9.914063 9.914063 5 16 5 Z M 15 10 L 15 12 L 17 12 L 17 10 Z M 15 14 L 15 22 L 17 22 L 17 14 Z"/></svg>
                            @endif
                        </h1>
                        {{-- <p> {{__('If you\'re using a Kaspersky license and want to be in the exchange program click below button.')}}</p> --}}
                        <p style="font-size: 1.5rem"> {{ $message }}</p>
                        <button type="button" class="btn btn-lg btn-primary mt-2" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">{{ __('OK') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .alert-icon svg {
        max-width: 80px;
    }
</style>

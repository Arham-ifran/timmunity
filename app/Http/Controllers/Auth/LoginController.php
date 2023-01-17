<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Controllers\Frontside\CartController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\App;
use Auth;
use Illuminate\Support\Facades\Log;
use Session;
use \Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except(['logout']);
    }

    public function showLoginForm()
    {
        if(Auth::user()){
            return redirect()->route('frontside.home.index');
        }
        return view('auth.login');
    }
    public function login(Request $request)
    {
        
            $this->validate($request, [
                 'g-recaptcha-response' => __('required|recaptcha'),
            ], [
                    'g-recaptcha-response.recaptcha' => __('Captcha verification failed'),
                    'g-recaptcha-response.required' => __('Please complete the captcha'),
            ]);
        $session_cart = Session::get('cart_items');
        $session_coupon_code = Session::get('coupon_code');
        $user = User::with('contact')->where('email', $request->input('email'))->first();
        if( $user && $user->is_active != 1 && $user->contact->type == 2) {
            return redirect()->back()->with(session()->flash('alert-error', __('Your account is not active yet! Kindly contact admin.')));
        }

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password]))
        {
            if($session_cart){
                // Check if the cart of the user isset if not create it
                $cart = Cart::firstOrCreate([
                    'customer_id' => Auth::user()->id,
                    'is_checkout' => 0
                ]);
                CartItem::where('cart_id', $cart->id)->delete();
                foreach($session_cart as $session_cart_item){
                    $cart_item = new CartItem;
                        $cart_item->cart_id = $cart->id;
                        $cart_item->product_id = $session_cart_item->product_id;
                        $cart_item->variation_id = $session_cart_item->variation_id;
                        $cart_item->is_variable = $session_cart_item->is_variable;
                        $cart_item->unit_price = $session_cart_item->unit_price;
                        $cart_item->qty = $session_cart_item->qty;
                    $cart_item->save();
                }
                if($session_coupon_code){
                    $cart_controller = new CartController;
                    $cart_controller->applyCoupon($session_coupon_code);
                }
            }
            if(Auth::user()->contact->type == 3 && Auth::user()->last_active_at == null )
            {
                return redirect()->route('user.dashboard.profile')->with(session()->flash('alert-warning',__("Your profile isn't approved yet. Complete your profile and wait for admin approval.")));

            }
            // dd($this->sendLoginResponse($request));
            return $this->sendLoginResponse($request);
            // reurn redirect()->intended($this->redirectPath());
        }else{
            return redirect()->back()->with(session()->flash('alert-error',__("The credentials does not match our records")));
        }
    }

    public function logout(Request $request)
    {
        $locale = session()->get('locale');
        Auth::logout();
        App::setLocale($locale);
        session()->put('locale', $locale);

        return redirect()->route('frontside.home.index');
    }

    public function redirectPath()
    {
        if (property_exists($this, 'redirectPath')) {
            return $this->redirectTo;
        }
        if(auth()->check())
        {
            LaravelLocalization::setLocale(App::getlocale());
            return LaravelLocalization::getLocalizedURL(App::getlocale(), $this->redirectTo, [], true);
        }
        return route('frontside.home.index');
    }
}

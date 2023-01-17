<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Models\Admin;
use App\Models\Languages;
use Hashids;
use Auth;

class LocalizationController extends Controller
{
    public function lang($locale_id)
    {
        $locale_id = Hashids::decode($locale_id)[0];
        $model = Admin::where('id', Auth::user()->id)->first();
        $model->update(['lang_id' => $locale_id]);
        $locale = Languages::where('id', $locale_id)->first();
        App::setLocale($locale->iso_code);
        //session()->put('locale', $locale->iso_code);
        return redirect()->back();
    }
}

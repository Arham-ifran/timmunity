<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Languages;
use Alert;

class TextTranslationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('Create Text Translations'))
        access_denied();
        return view('admin.settings.languages-text-translations.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $languages = Languages::where('is_active',1)->get();

        foreach ($languages as $language)
        {
            $labels_file = base_path().'/resources/lang/'.$language->iso_code.'.json';
            if(!file_exists($labels_file)){
                $fp = fopen($labels_file, 'w');
                fwrite($fp,'');
                fclose($fp);
                // dd($labels_file);
            }
            $get_labels = file_get_contents($labels_file);
            $label_array = json_decode($get_labels,true);

            if($language->iso_code == 'en')
            {
                $label_array[$request->text] = $request->text;
            }
            else
            {
                $label_array[$request->text] = translationByDeepL($request->text,$language->iso_code);
            }

            file_put_contents(base_path().'/resources/lang/'.$language->iso_code.'.json', json_encode($label_array,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT));
        }

        Alert::success(__('Success'), __('Text Translations has been created successfully.'))->persistent('Close')->autoclose(5000);
        return redirect('admin/settings/text-translations');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Languages;
use Alert;

class LabelTranslationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('Create Label Translations'))
        access_denied();

        $data['languages'] = Languages::where('is_active',1)->whereNotIn('iso_code', ['en'])->get();
        return view('admin.settings.languages-label-translations.form')->with($data);
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

        $labels_file = base_path().'/resources/lang/en.json';
        $get_labels = file_get_contents($labels_file);
        $label_array = json_decode($get_labels,true);
        $translated_labels_array = [];

        if($input['translate_language'] && $input['translate_language'] <> '')
        {
            $languages = Languages::where('id',$input['translate_language'])->get();
        }
        else
        {
            $languages = Languages::where('is_active',1)->whereNotIn('iso_code', ['en'])->get();
        }

        foreach ($languages as $language)
        {
            foreach($label_array as $key => $value)
            {
                if(is_array($value))
                {
                    foreach($value as $innerKey => $innerValue)
                    {
                        $translated_labels_array[$key][$innerKey] = translationByDeepL($innerValue,$language->iso_code);
                    }
                }
                else
                {
                    $translated_labels_array[$key] = translationByDeepL($value,$language->iso_code);
                }
            }

            file_put_contents(base_path().'/resources/lang/'.$language->iso_code.'.json', json_encode($translated_labels_array,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT));
        }

        Alert::success(__('Success'), __('Label Translations has been created successfully.'))->persistent('Close')->autoclose(5000);
        return redirect('admin/settings/label-translations');
    }
}

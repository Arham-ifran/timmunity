<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\LanguageTranslation;
use App\Models\LanguageModule;
use App\Models\Languages;
use App\Jobs\TranslationJob;
use Session;
use Hashids;
use Auth;
use DataTables;
use Alert;

class LanguageTranslationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('Language Translations Listing'))
        access_denied();
        $data = [];
        $data['language_modules'] = LanguageModule::orderBy('name', 'asc')->get();
        $data['languages'] = Languages::where('is_active',1)->get();

        if($request->ajax())
        {
            $db_record = LanguageTranslation::orderBy('id','asc');

            if($request->has('language_module_id') && !empty($request->language_module_id))
            {
                $db_record = $db_record->where('language_module_id',$request->language_module_id);
            }

            if($request->has('language_id') && !empty($request->language_id))
            {
                $db_record = $db_record->where('language_id',$request->language_id);
            }

            $datatable = Datatables::of($db_record);
            $datatable = $datatable->addIndexColumn();

            $datatable = $datatable->addColumn('language_module', function($row)
            {
                return $row->languageModule->name;
            });

            $datatable = $datatable->addColumn('language_name', function($row)
            {
                return $row->language->name;
            });

            $datatable = $datatable->editColumn('custom', function($row)
            {
                $custom = '<span class="label label-danger">No</span>';
                if ($row->custom == 1)
                {
                    $custom = '<span class="label label-success">Yes</span>';
                }
                return $custom;
            });

            $datatable = $datatable->addColumn('action', function($row)
            {

                $actions = '<span class="actions">';
                if (auth()->user()->hasAnyPermission('Edit Language Translations')) {
                $actions .= '&nbsp;<a class="btn btn-primary" target="_blank" href="'.url("admin/settings/language-translations/" . Hashids::encode($row->id).'/edit').'" title="Edit"><i class="fa fa-pencil-square-o"></i></a>';

                $actions .= '</span>';
                }
                return $actions;
            });

            $datatable = $datatable->rawColumns(['custom','action']);
            $datatable = $datatable->make(true);
            return $datatable;
        }

        return view('admin.settings.language-translations.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('Language Bulk Translate'))
        access_denied();
        $data['model'] = new LanguageTranslation();
        $data['language_modules'] = LanguageModule::orderBy('name', 'asc')->get();
        $data['languages'] = Languages::where('is_active',1)->whereNotIn('iso_code', ['en'])->get();
        $data['action'] = "Add";
        return view('admin.settings.language-translations.form')->with($data);
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

        if($input['action'] == 'Add')
        {
            dispatch( new TranslationJob( $request->language_module_id, $input['translate_language'], $request->translation_flag ) );

            // $language_module = LanguageModule::find($request->language_module_id);
            // $table = $language_module->table;
            // $columns = explode(',', $language_module->columns);
            // $records = \DB::table($table)->get();
            // if($input['translate_language'] && $input['translate_language'] <> '')
            // {
            //     $languages = Languages::where('id',$input['translate_language'])->get();
            // }
            // else
            // {
            //     $languages = Languages::where('is_active',1)->whereNotIn('iso_code', ['en'])->get();
            // }
            // foreach ($languages as $language)
            // {
            //     foreach ($records as $record)
            //     {
            //         foreach ($columns as $column)
            //         {
            //             $language_translation = LanguageTranslation::where(
            //                 [
            //                     'language_module_id' => $language_module->id,
            //                     'language_id'        => $language->id,
            //                     'item_id'            => $record->id,
            //                     'column_name'        => $column
            //                 ])
            //                 ->first();

            //             if($request->translation_flag == 1 || empty($language_translation) || $language_translation->custom == 0)
            //             {
            //                 $item_value = '';
            //                 $item_value = translationByDeepL($record->$column,$language->iso_code);
            //                 // if($record->id == 12 && $column == 'description')
            //                 // {
            //                 //     echo($item_value);
            //                 //     dd('$item_value');
            //                 // }
            //                 LanguageTranslation::updateOrCreate(
            //                     [
            //                         'language_module_id' => $language_module->id,
            //                         'language_id'        => $language->id,
            //                         'item_id'            => $record->id,
            //                         'column_name'        => $column,
            //                     ],
            //                     [
            //                         'language_module_id' => $language_module->id,
            //                         'language_id'        => $language->id,
            //                         'language_code'      => $language->iso_code,
            //                         'item_id'            => $record->id,
            //                         'column_name'        => $column,
            //                         'item_value'         => $item_value,
            //                         'custom'             => 0,
            //                     ]
            //                 );
            //             }
            //         }
            //     }
            // }

            Alert::success(__('Success'), __('Language Translation is being created on background.'))->persistent('Close')->autoclose(5000);
            // Alert::success(__('Success'), __('Language Translation has been created successfully.'))->persistent('Close')->autoclose(5000);
            return redirect('admin/settings/language-translations');
        }
        else
        {
            $model = LanguageTranslation::findOrFail($input['id']);
            $model->item_value = $input['item_value'];
            $model->custom = $input['custom'];
            $model->save();

            Alert::success(__('Success'), __('Language Translation has been updated successfully.'))->persistent('Close')->autoclose(5000);
            return redirect('admin/settings/language-translations/'.Hashids::encode($input['id']).'/edit');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        if(!auth()->user()->can('Edit Language Translations'))
        access_denied();
        if(!isset(Hashids::decode($id)[0]))
            abort(404);

        $id = Hashids::decode($id)[0];
        $data['action'] = "Edit";
        $data['model'] = LanguageTranslation::findOrFail($id);
        return view('admin.settings.language-translations.form')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function partialTranslate()
    {
        if(!auth()->user()->can('Language Partial Translate'))
        access_denied();
        $data['language_modules'] = LanguageModule::orderBy('name', 'asc')->get();
        $data['languages'] = Languages::where('is_active',1)->whereNotIn('iso_code', ['en'])->get();
        return view('admin.settings.language-translations.partial_translate')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addPartialTranslate(Request $request)
    {
        $input = $request->all();

        if($input['translate_language'] == 'all')
        {
            $languages = Languages::where('is_active',1)->whereNotIn('iso_code', ['en'])->get();
        }
        else
        {
            $languages = Languages::where('id',$input['translate_language'])->get();
        }

        foreach ($languages as $language)
        {
            LanguageTranslation::updateOrCreate(
                [
                    'language_module_id' => $request->language_module_id,
                    'language_id'        => $language->id,
                    'item_id'            => $request->item_id,
                    'column_name'        => $request->column_name,
                ],
                [
                    'language_module_id' => $request->language_module_id,
                    'language_id'        => $language->id,
                    'language_code'      => $language->iso_code,
                    'item_id'            => $request->item_id,
                    'column_name'        => $request->column_name,
                    'item_value'         => translationByDeepL($request->text,$language->iso_code),
                    'custom'             => 0
                ]
            );
        }

        Alert::success(__('Success'), __('Language Translation has been created successfully.'))->persistent('Close')->autoclose(5000);
        return redirect('admin/settings/language-translations');
    }
}

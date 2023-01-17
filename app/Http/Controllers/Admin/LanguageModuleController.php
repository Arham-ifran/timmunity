<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LanguageModule;
use DataTables;

class LanguageModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('Language Modules Listing'))
        access_denied();
        $data = [];

        if($request->ajax())
        {

            $db_record = LanguageModule::all();

            $datatable = Datatables::of($db_record);
            $datatable = $datatable->addIndexColumn();
            $datatable = $datatable->make(true);

                return $datatable;
        }

        return view('admin.settings.language-modules.index',$data);
    }
}

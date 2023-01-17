<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Languages;
use App\Models\Admin;
use DataTables;
use Hashids;
use Form;
use Alert;
use Auth;
class LanguagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
       public function index(Request $request)
    {

        if(!auth()->user()->can('Languages Listing'))
        access_denied();
        $data = [];
        $data['languages'] = Languages::where('is_archive', 0)->get();
        if ($request->ajax()) {
            $archive = $request->get('is_archive');
            $data = Languages::where(function ($data) use ($archive) {
                    if (isset($archive) && $archive != "") {
                        $data->where('is_archive', $archive);
                    }
                })
            ->orderBy('is_active','desc')
            ->get();
            $datatable = Datatables::of($data);
            $datatable->setRowId(function ($row) {
                return 'tr_' . $row->id;
            });
            $datatable->addColumn('delete_check', function (Languages $row) {
                $indv_check = '';
                $indv_check = '<input type="checkbox" name="deleteLangChecks[]" class="del_lang_sub_chk checkbox-input countLangChecks" onclick="checkBoxActions(this)" data-id="' . Hashids::encode($row->id) . '">';
                return $indv_check;
            });
            $datatable->editColumn('is_active',function($row){
                return ($row->is_active)? '<span class="badge badge-success">'.__('Active').'</span>' : '<span class="badge badge-danger">'.__('In Active').'</span>';
            });
            $datatable->addColumn('action', function ($row) {

                $actions = '';
                if (auth()->user()->hasAnyPermission(['Edit Languages','Delete Languages','Activate / Update Languages'])) {
                $actions .= auth()->user()->can('Edit Languages') ? '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/settings/languages/" . Hashids::encode($row->id) . '/edit') . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>' : '';

                if(auth()->user()->can('Delete Languages')) {
                    $actions .= '&nbsp;' . Form::open([
                        'method' => 'DELETE',
                        'url' => ['admin/settings/languages', Hashids::encode($row->id)],
                        'style' => 'display:inline'
                    ]);
                    $actions .= Form::button('<i class="fa fa-trash fa-fw" title='.__('Delete').'></i>', ['onclick'=>'deleteAlert(this)','class' => 'delete-form-btn btn btn-default btn-icon']);
                    $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);
                    $actions .= Form::close();
                }

                if($row->is_archive == 0 && $row->is_active == 0) {
                    if(auth()->user()->can('Activate / Update Languages')) {
                    $actions .= '&nbsp;<a class="btn btn-warning btn-icon" type="button" onclick="activateUpdate(this)" title="Activate / Update" data-id = "'. Hashids::encode($row->id) .'"><i class="fa fa-refresh"></i>&nbsp;'.__('Activate / Update').'</a>';
                    }
                }else{
                    if(auth()->user()->can('Activate / Update Languages')) {
                    $actions .= '&nbsp;<a class="btn btn-danger btn-icon" type="button" onclick="archiveUpdate(this)" title="Archive / De-Activate" data-name="'.$row->name.'" data-id = "'. Hashids::encode($row->id) .'"><i class="fa fa-archive"></i>&nbsp;'.__('Archive / De-Activate').'</a>';
                    }
                }


             }
                return $actions;
            });
            $datatable = $datatable->rawColumns(['delete_check', 'is_active', 'action']);
            return $datatable->make(true);
        }
        return view('admin.settings.languages.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('Add New Languages'))
        access_denied();
        $data = [];
        $data['action'] = 'Add';
        return view('admin.settings.languages.form')->with($data);
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
        if ($input['action'] == 'Edit') {
            $id = Hashids::decode($input['id']);
            $model = Languages::findOrFail($id)[0];
            if (Admin::where('lang_id', '=', $id)->where('id', Auth::user()->id)->exists()) {
            Alert::warning(__('Warning'), __('Cannot deactivate a language that is currently used on a website.'))->persistent(__('Ok'));
              return redirect()->back();
            }
            else if (Admin::where('lang_id', '=', $id)->where('id','!=', Auth::user()->id)->exists()) {
                 Alert::warning(__('Warning'), __('Cannot deactivate a language that is currently used by users'))->persistent(__('Ok'));
                 return redirect()->back();
            }
            else {
                $model->update($input);
                Alert::success(__('Success'), __('Language updated successfully!'))->persistent('Close')->autoclose(5000);
           }
        } else {
            $this->validate($request, [
                'name' => 'required|string|max:100|unique:languages',
                'iso_code' => 'required|string|max:30|unique:languages',
                'local_code' => 'required|string|max:10|unique:languages',
           ], [
                'name.unique' => __('The name of the language must be unique !'),
                'iso_code.unique' => __('The ISO code of the language must be unique !'),
                'local_code.unique' => __('The local code of the language must be unique !')
            ]);
            $model = new Languages();
            $model->fill($input)->save();
            Alert::success(__('Success'), __('Language added successfully!'))->persistent('Close')->autoclose(5000);
        }

        return redirect('admin/settings/languages');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!auth()->user()->can('Edit Languages'))
        access_denied();
        $data = [];
        $id = Hashids::decode($id);
        $data['action'] = 'Edit';
        $data['model'] = Languages::find($id)[0];
        return view('admin.settings.languages.form')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!auth()->user()->can('Delete Languages'))
        access_denied();
        $id = Hashids::decode($id);
        $model = Languages::find($id)[0];
        $model->delete();
        Alert::success('Success', __('Language deleted Successfully.'))->persistent('Close')->autoclose(5000);
        return redirect('admin/settings/languages');
    }


    public function bulkDelete(Request $request)
    {
        if(!auth()->user()->can('Delete Languages'))
        access_denied();
        $ids = $request->ids;
        $idsArr = explode(",", $ids);
        $deocdedIds = array();
        foreach($idsArr as $id) {
           $deocdedIds[] = Hashids::decode($id)[0];
        }
        Languages::whereIn('id', $deocdedIds)->delete();
        $response = response()->json(['success' => __('Languages Deleted successfully.')]);

        return $response;
    }


    public function isArchiveLang(Request $request)
    {
        $input = $request->all();
        $id = Hashids::decode($input['id'])[0];
        $login_id = isset(Hashids::decode($input['login_id'])[0]) ? Hashids::decode($input['login_id'])[0] : null;
        if (Admin::where('lang_id', '=', $id)->where('id', $login_id)->exists()) {
            $response = response()->json(['warning' => __('Cannot archived a language that is currently used on a website.')]);
        }
        else if (Admin::where('lang_id', '=', $id)->where('id','!=', $login_id)->exists()) {
             $response = response()->json(['warning' => __('Cannot archived a language that is currently used by users.')]);
        }
         else {
            $archive = $input['is_archive'];
            if ($archive == 1)
                $archiveMSg = __('Archived');
            else
                $archiveMSg = __('Unarchived');
            Languages::where('id', $id)->update(['is_archive' => $archive]);

            $response = response()->json(['success' => __('Language').' '. $archiveMSg .' '. __('Successfully.')]);
        }
        return $response;
    }
}

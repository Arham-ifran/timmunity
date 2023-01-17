<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CmsPages;
use DataTables;
use Hashids;
use Form;
use File;
use Image;
use Alert;

class CmsPagesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('CMS Page Listing'))
        access_denied();
        $data = [];
        if ($request->ajax()) {
            $data = CmsPages::all();
            $datatable = Datatables::of($data);
            $datatable->editColumn('title',function($row){
                return makeToPopover($row->title);
            });
            $datatable->editColumn('meta_title',function($row){
                return makeToPopover($row->meta_title);
            });
            $datatable->editColumn('description',function($row){
                return makeToPopover($row->description);
            });
            $datatable->editColumn('is_active',function($row){
                return ($row->is_active)? '<span class="badge badge-success">'.__('Active').'</span>' : '<span class="badge badge-danger">'.__('In Active').'</span>';
            });
            $datatable->addColumn('action', function ($row) {
                $actions = '';
                if (auth()->user()->hasAnyPermission(['Delete CMS Page','Edit CMS Page','Duplicate CMS Page'])) {
                $actions .= auth()->user()->can('Edit CMS Page') ? '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/settings/cms/" . Hashids::encode($row->id) . '/edit') . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>':'';

                if(auth()->user()->can('Delete CMS Page')) {
                $actions .= '&nbsp;' . Form::open([
                    'method' => 'DELETE',
                    'url' => ['admin/settings/cms', Hashids::encode($row->id)],
                    'style' => 'display:inline'
                ]);
                $actions .= Form::button('<i class="fa fa-trash fa-fw" title='.__('Delete').'></i>', ['onclick'=>'deleteAlert(this)','class' => 'delete-form-btn btn btn-default btn-icon']);
                $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                $actions .= Form::close();
                }
                if(auth()->user()->can('Duplicate CMS Page')) {
                $actions .= '&nbsp;' . Form::open([
                    'method' => 'POST',
                    'url' => ['admin/settings/cms/duplicate', Hashids::encode($row->id)],
                    'style' => 'display:inline'
                ]);

                // $actions .= Form::submit('<i class="fa fa-clone fa-fw" title="Duplicate"></i>', ['class' => 'delete-form-btn btn btn-default btn-icon']);
                // $actions .= Form::submit('Duplicate', ['class' => 'hidden duplicateAlert']);
                $actions .= '<input type="submit" class="btn btn-primary btn-icon" title='.__('Duplicate').' value="||"/>';

                $actions .= Form::close();
                }
                }
                return $actions;
            });
            $datatable = $datatable->rawColumns(['title','meta_title','description','is_active', 'action']);
            return $datatable->make(true);
        }
        return view('admin.settings.cms_pages.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('Add New CMS Page'))
        access_denied();
        $data = [];
        $data['action'] = 'Add';
        return view('admin.settings.cms_pages.form')->with($data);
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
        $input['is_homepage_listing'] = isset($input['is_homepage_listing']) ? 1 : 0;
        $input['show_in_header'] = isset($input['show_in_header']) ? 1 : 0;
        $input['is_static'] = isset($input['is_static']) ? 1 : 0;
        $input['show_in_footer'] = isset($input['show_in_footer']) ? 1 : 0;
        if ($input['action'] == 'Edit') {
            $id = Hashids::decode($input['id']);
            $model = CmsPages::findOrFail($id)[0];
            $this->validate($request, [
                'title' => 'required|max:100',
            ]);

            $model->update($input);

            Alert::success(__('Success'), __('CMS Page updated successfully!'))->persistent('Close')->autoclose(5000);
        } else {
            $this->validate($request, [
                'title' => 'required|max:100',
            ]);

            $model = new CmsPages();
            $model->fill($input)->save();

            Alert::success(__('Success'), __('CMS Page added successfully!'))->persistent('Close')->autoclose(5000);
        }
        //Upload Profile Picture
        if (!empty($request->files) && $request->hasFile('image')) {
            $file = $request->file('image');
            $type = strtolower($file->getClientOriginalExtension());

            if ($type == 'jpg' or $type == 'PNG' or $type == 'png' or $type == 'jpeg' or $type == 'svg' or $type == 'SVG') {
                $file_temp_name = 'cms-page' . Hashids::encode($model->id) . '.' . $type;
                $upload_path = 'storage/uploads/cms';
                $old_file = $upload_path . '/' . $file_temp_name;

                if (File::exists($old_file)) {
                    //delete previous file
                    unlink($old_file);
                }
                if (!File::exists(public_path() . '/storage/uploads/cms/' . $file_temp_name)) {

                    File::makeDirectory($upload_path, 0777, true);
                }
                $path = public_path('storage/uploads/cms') . '/' . $file_temp_name;
                $img = Image::make($file)->save($path);
                $input['site_logo'] = $file_temp_name;
                $model->image = $file_temp_name;
                $model->save();
            } else {
                $request->session()->flash('error_message', __('File type should be .PNG, .JPG or .JPEG'));
                return redirect()->back();
            }
        }


        return redirect('admin/settings/cms');
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
        if(!auth()->user()->can('Edit CMS Page'))
        access_denied();
        $data = [];
        $id = Hashids::decode($id);
        $data['action'] = 'Edit';
        $data['model'] = CmsPages::find($id)[0];
        return view('admin.settings.cms_pages.form')->with($data);
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
        if(!auth()->user()->can('Delete CMS Page'))
        access_denied();
        $id = Hashids::decode($id);
        $model = CmsPages::find($id)[0];
        $model->delete();

        Alert::success(__('Success'), __('CMS Page deleted Successfully.'))->persistent('Close')->autoclose(5000);
        return redirect('admin/settings/cms');
    }

    /**
     * Check Slug Availibility
     *
     */
    public function check_slug(Request $request)
    {
        $page = CmsPages::where('seo_url', $request->slug)->first();
        $slug = $request->slug;
        if($page){
            while($page){
                $slug = $slug.'-copy';
                $page = CmsPages::where('seo_url', $slug)->first();
            }
        }
        return $slug;
    }

    /**
     * Duplicate CMS Page
     *
     */
    public function duplicate($id)
    {
        try {
            $id = Hashids::decode($id)[0];
        } catch (\Throwable $th) {
        }
        $old_page = CmsPages::where('id', $id)->first();

        $page = CmsPages::where('seo_url', $old_page->seo_url)->first();
        $slug = $old_page->seo_url;
        if($page){
            while($page){
                $slug = $slug.'-copy';
                $page = CmsPages::where('seo_url', $slug)->first();
            }
        }

        $new_page = $old_page->replicate();

            $new_page->title = $new_page->title.' - copy';
            $new_page->seo_url = $slug;
        $new_page->save();
        Alert::success(__('Success'), __('CMS Page cloned successfully!'))->persistent('Close')->autoclose(5000);

        return redirect(url("admin/settings/cms/"));
    }
}

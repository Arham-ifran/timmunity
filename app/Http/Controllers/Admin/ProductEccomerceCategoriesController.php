<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EccomerceCategory;
use DataTables;
use Hashids;
use Alert;
use Form;

class ProductEccomerceCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('Ecommerce Categories Listing'))
        access_denied();

        $data = [];
        if ($request->ajax()) {
            $data = EccomerceCategory::with('parent')->orderBy('id','desc')->get();

            $datatable = Datatables::of($data);
            $datatable->addColumn('category_name', function ($row) {
                return $row->category_name;
            });
            $datatable->addColumn('parent_category',function($row){
                return $row->parent_category == null ? '<p style="color:#ccc;">'.__('No Parent').'</p>' : $row->parent->category_name;
            });
            $datatable->addColumn('action', function ($row) {
                $actions = '';
                if (auth()->user()->hasAnyPermission(['Edit Ecommerce Categories','Delete Ecommerce Categories']))
                {
                    $actions .= auth()->user()->can('Edit Ecommerce Categories') ? '&nbsp;<a class="btn btn-primary btn-icon" href="' . route("admin.eccomerce-categories.edit", Hashids::encode($row->id) ) . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>' : '';
                    if(auth()->user()->can('Delete Ecommerce Categories')) {
                        $actions .= '&nbsp;' . Form::open([
                                'method' => 'DELETE',
                                'url' => [route("admin.eccomerce-categories.destroy", Hashids::encode($row->id) )],
                                'style' => 'display:inline'
                            ]);

                        $actions .= Form::button('<i class="fa fa-trash fa-fw" title='.__('Delete').'></i>', ['type' => 'submit', 'class' => 'delete-form-btn btn btn-default btn-icon']);
                        $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                        $actions .= Form::close();
                    }
                }
                return $actions;
            });
            $datatable = $datatable->rawColumns(['parent_category','action']);
            return $datatable->make(true);
        }
        return view('admin.eccomerce-categories.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('Add Ecommerce Categories'))
        access_denied();

        $data = [];
        $data['action'] = 'Add';
        $data['parent_categories'] = EccomerceCategory::with('parent')->where(function($query){
            $query->where('parent_category', null);
            $query->orWhere('parent_category', 0);
        })->get();
        return view('admin.eccomerce-categories.form')->with($data);
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
            $model = EccomerceCategory::findOrFail($id)[0];
            $this->validate($request, [
                'category_slug' => 'required|string|max:255',
                'category_name' => 'required|string|max:255',
            ]);
            $model->update($input);

            Alert::success(__('Success'), __('Ecommerce category updated successfully!'))->persistent('Close')->autoclose(5000);
        } else {

            $this->validate($request, [
                'category_slug' => 'required|string|max:255',
                'category_name' => 'required|string|max:255',
            ]);

            $model = new EccomerceCategory();
            $model->fill($input)->save();


            Alert::success(__('Success'), __('Ecommerce category added successfully!'))->persistent('Close')->autoclose(5000);
        }


        return redirect()->route('admin.eccomerce-categories.index');
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
    public function edit($id, Request $request)
    {
        if(!auth()->user()->can('Edit Ecommerce Categories'))
        access_denied();

        $data = [];
        $data['action'] = 'Edit';
        $id = Hashids::decode($id)[0];
        $data['parent_categories'] = EccomerceCategory::where('id','!=',$id)->where(function($query){
            $query->where('parent_category', null);
            $query->orWhere('parent_category', 0);
        })->get();
        $data['model'] = EccomerceCategory::where('id',$id)->first();
        return view('admin.eccomerce-categories.form')->with($data);
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
        if(!auth()->user()->can('Delete Ecommerce Categories'))
        access_denied();

        $id = Hashids::decode($id)[0];

        $is_parent_category = EccomerceCategory::where('parent_category',$id)->first();
        if(!$is_parent_category){
            EccomerceCategory::where('id',$id)->delete();
             Alert::success(__('Success'), __('Ecommerce category deleted successfully!'))->persistent('Close')->autoclose(5000);
        }else{
            Alert::success(__('Warning'), __('Can not delete parent category!'))->persistent('Close')->autoclose(5000);
        }

        return redirect()->route('admin.eccomerce-categories.index');
    }

    /**
     * Check Slug Availibility
     *
     */
    public function check_slug(Request $request)
    {

        $cat_query = EccomerceCategory::where('category_slug', $request->slug);
        if($request->id != null && $request->id != '' && $request->id != 0)
        {
            $id = Hashids::decode($request->id);
            if(isset($id[0])){
                $cat_query->where('id', '!=', $id[0]);
            }
        }
        $cat = $cat_query->first();

        $slug = $request->slug;
        if($cat){
            while($cat){
                $slug = $slug.'-copy';
                $cat_query = EccomerceCategory::where('category_slug', $slug);
                if($request->id != null && $request->id != '' && $request->id != 0)
                {
                    $id = Hashids::decode($request->id);
                    if(isset($id[0])){
                        $cat_query->where('id', '!=', $id[0]);
                    }
                }
                $cat = $cat_query->first();
            }
        }
        return $slug;
    }
}

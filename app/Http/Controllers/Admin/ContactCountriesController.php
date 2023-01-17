<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactCountry;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Hashids;
use Form;
use Alert;
use File;
use Image;

class ContactCountriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('Contact Countries Listing'))
        access_denied();
        $data = [];
        if ($request->ajax()) {
            $data = ContactCountry::orderBy('id','desc');
            $datatable = Datatables::of($data);
            $datatable->editColumn('vat_in_percentage', function ($row) {
                $vat_label = $row->vat_label ? $row->vat_label : 'VAT';
                return $vat_label.' '.$row->vat_in_percentage;
            });
            $datatable->addColumn('action', function ($row) {
                $actions = '';
                if (auth()->user()->hasAnyPermission(['Edit Contact Countries','Delete Contact Countries'])) {

                $actions .= auth()->user()->can('Edit Contact Countries') ? '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/contacts-countries/" . Hashids::encode($row->id) . '/edit') . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>' : '';
                if(auth()->user()->can('Delete Contact Countries')) {
                $actions .= '&nbsp;' . Form::open([
                    'method' => 'DELETE',
                    'url' => ['admin/contacts-countries', Hashids::encode($row->id)],
                    'style' => 'display:inline'
                ]);

                $actions .= Form::button('<i class="fa fa-trash fa-fw" title='.__('Delete').'></i>', ['type' => 'submit', 'class' => 'delete-form-btn btn btn-default btn-icon']);
                $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                $actions .= Form::close();
                 }
                }
                return $actions;
            });
            $datatable = $datatable->rawColumns(['action']);
            return $datatable->make(true);
        }
        return view('admin.contacts.countries.index', $data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('Add New Contact Countries'))
        access_denied();
        $data = [];
        $data['action'] = 'Add';

        return view('admin.contacts.countries.form')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $data = [];
        $input = $request->all();
        if ($input['action'] == 'Edit') {
          // dd($input);
            $id = Hashids::decode($input['id']);
            $model = ContactCountry::findOrFail($id)[0];
            $this->validate($request, [
                'name' => ['required', 'string', 'max:20'],
                "country_code" => ['required', 'string', 'max:5'],
                "vat_label" => ['required', 'string', 'max:20'],
                "country_calling_code" => ['required', 'string', 'max:5'],
                "currency" => ['required', 'string', 'max:10'],

            ]);

            $upload_path = public_path() . '/storage/uploads/countries/' . Hashids::encode($model->id);
            if (!File::exists(public_path() . '/storage/uploads/countries/' . Hashids::encode($model->id))) {

                File::makeDirectory($upload_path, 0777, true);
            }
            if (!empty($request->files) && $request->hasFile('image')) {
                $file      = $request->file('image');
                $file_name = $file->getClientOriginalName();
                $type      = $file->getClientOriginalExtension();
                $real_path = $file->getRealPath();
                $size      = $file->getSize();
                $size_mbs  = ($size / 1024) / 1024;
                $mime_type = $file->getMimeType();

                if ($type == 'jpg' or $type == 'JPG' or $type == 'PNG' or $type == 'png' or $type == 'jpeg' or $type == 'JPEG') {
                    $file_temp_name = 'contact-countries-' . time() . '.' . $type;

                    $old_file  = public_path() . '/storage/uploads/countries/' . Hashids::encode($model->id) . '/' . $model->image;
                    if (file_exists($old_file) && !empty($model->image)) {
                        //delete previous file
                        unlink($old_file);
                    }

                    $path = public_path('storage/uploads/countries/') . Hashids::encode($model->id) . '/' . $file_temp_name;

                    // if ($size_mbs >= 2) {
                    //     $img = Image::make($file)->fit(300, 300)->save($path);
                    // } else {
                    //     $img = Image::make($file)->resize(300, 300)->save($path);
                    // }
                    $img = Image::make($file)->resize(300, 300, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($path);

                    $input['image'] = $file_temp_name;
                }
            }

            $model->update($input);

            Alert::success(__('Success'), __('Contact Countries updated successfully!'))->persistent('Close')->autoclose(5000);

        }
        else {

        $this->validate($request, [
            'name' => ['required', 'string', 'max:20'],
            "country_code" => ['required', 'string', 'max:5'],
            "vat_label" => ['required', 'string', 'max:20'],
            "country_calling_code" => ['required', 'string', 'max:5'],
            "currency" => ['required', 'string', 'max:10'],

        ]);
        $model = new ContactCountry();


        $upload_path = public_path() . '/storage/uploads/countries/' . Hashids::encode($model->id);
        if (!File::exists(public_path() . '/storage/uploads/countries/' . Hashids::encode($model->id))) {

            File::makeDirectory($upload_path, 0777, true);
        }
        if (!empty($request->files) && $request->hasFile('image')) {
            $file      = $request->file('image');
            $file_name = $file->getClientOriginalName();
            $type      = $file->getClientOriginalExtension();
            $real_path = $file->getRealPath();
            $size      = $file->getSize();
            $size_mbs  = ($size / 1024) / 1024;
            $mime_type = $file->getMimeType();

            if ($type == 'jpg' or $type == 'JPG' or $type == 'PNG' or $type == 'png' or $type == 'jpeg' or $type == 'JPEG') {
                $file_temp_name = 'contact-countries-' . time() . '.' . $type;

                $old_file  = public_path() . '/storage/uploads/countries/' . Hashids::encode($model->id) . '/' . $model->image;
                if (file_exists($old_file) && !empty($model->image)) {
                    //delete previous file
                    unlink($old_file);
                }

                $path = public_path('storage/uploads/countries/') . Hashids::encode($model->id) . '/' . $file_temp_name;

                // if ($size_mbs >= 2) {
                //     $img = Image::make($file)->fit(300, 300)->save($path);
                // } else {
                //     $img = Image::make($file)->resize(300, 300)->save($path);
                // }
                $img = Image::make($file)->resize(300, 300, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($path);

                $input['image'] = $file_temp_name;
            }
        }
      }
        $model->fill($input)->save();
        Alert::success(__('Success'), __('Contact Countries added successfully!'))->persistent('Close')->autoclose(5000);

        return redirect()->route('admin.contacts-countries.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!auth()->user()->can('Edit Contact Countries'))
        access_denied();
        $data = [];
        $id = Hashids::decode($id);
        $data['action'] = 'Edit';
        $data['model'] = ContactCountry::find($id)[0];
        return view('admin.contacts.countries.form')->with($data);
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!auth()->user()->can('Delete Contact Countries'))
        access_denied();
        $id = Hashids::decode($id);
        $model = ContactCountry::find($id)[0];
        $model->delete();
        Alert::success(__('Success'), __('Contact Countries deleted Successfully.'))->persistent('Close')->autoclose(5000);
        return redirect()->back();
    }
}

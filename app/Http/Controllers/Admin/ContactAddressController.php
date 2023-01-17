<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\ContactAddress;
use Illuminate\Http\Request;
use App\Models\ContactCountry;
use App\Models\ContactFedState;
use App\Models\ContactTitle;
use Hashids;
use File;
use Image;
use Illuminate\Support\Facades\Session;
class ContactAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            $this->validate($request, [
                'contact_name' => ['required', 'string', 'max:100'],
                'email' => ['required','email' ,'unique:contact_addresses'],
            ]);

            $model = new ContactAddress();
            $upload_path = public_path() . '/storage/uploads/contact-address/';
            if (!File::exists(public_path() . '/storage/uploads/contact-address/')) {

                File::makeDirectory($upload_path, 0777, true);
            }
            if (!empty($request->files) && $request->hasFile('contact_image')) {
                $file      = $request->file('contact_image');
                $file_name = $file->getClientOriginalName();
                $type      = $file->getClientOriginalExtension();
                $real_path = $file->getRealPath();
                $size      = $file->getSize();
                $size_mbs  = ($size / 1024) / 1024;
                $mime_type = $file->getMimeType();

                if ($type == 'jpg' or $type == 'JPG' or $type == 'PNG' or $type == 'png' or $type == 'jpeg' or $type == 'JPEG') {
                    $file_temp_name = 'contact-address-' . time() . '.' . $type;

                    $old_file  = public_path() . '/storage/uploads/contact-address/';
                    if (file_exists($old_file) && !empty($model->contact_image)) {
                        //delete previous file
                        unlink($old_file);
                    }

                    $path = public_path('storage/uploads/contact-address/') .$file_temp_name;

                    // if ($size_mbs >= 2) {
                    //     $img = Image::make($file)->fit(300, 300)->save($path);
                    // } else {
                    //     $img = Image::make($file)->resize(300, 300)->save($path);
                    // }
                    $img = Image::make($file)->resize(300, 300, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($path);

                    $input['contact_image'] = $file_temp_name;
                }
            }
            $model->fill($input)->save();

            return response()->json($model);

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


         $data = [];

        $data['contact_countries'] = ContactCountry::all();
        $data['contact_fed_states'] = ContactFedState::all();
        $data['contact_titles'] = ContactTitle::all();
        $data['contacts_address'] = ContactAddress::all();
        $data['model'] = ContactAddress::find($id);

        return view('admin.contacts.contact-address-edit-model')->with($data);
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
        $input = $request->all();
        //$c_id = Session::get('c_id');


        $c_id =  Hashids::decode($request->contact_id)[0];
        unset($input['contact_id']);
        $model = ContactAddress::findOrFail($id);

        $this->validate($request, [
            'contact_name' => ['required', 'string', 'max:100'],
        ]);
        $upload_path = public_path() . '/storage/uploads/contact-address/';
        if (!File::exists(public_path() . '/storage/uploads/contact-address/')) {
            File::makeDirectory($upload_path, 0777, true);
        }

        if (!empty($request->files) && $request->hasFile('contact_image')) {
            $file      = $request->file('contact_image');
            $file_name = $file->getClientOriginalName();
            $type      = $file->getClientOriginalExtension();
            $real_path = $file->getRealPath();
            $size      = $file->getSize();
            $size_mbs  = ($size / 1024) / 1024;
            $mime_type = $file->getMimeType();

            if ($type == 'jpg' or $type == 'JPG' or $type == 'PNG' or $type == 'png' or $type == 'jpeg' or $type == 'JPEG') {
                $file_temp_name = 'contact-address-' . time() . '.' . $type;

                $old_file  = public_path() . '/storage/uploads/contact-address/' . $model->id . '/' . $model->contact_image;
                if (file_exists($old_file) && !empty($model->contact_image)) {
                    //delete previous file
                    unlink($old_file);
                }

                $path = public_path('storage/uploads/contact-address/') . $file_temp_name;

                // if ($size_mbs >= 2) {
                //     $img = Image::make($file)->fit(300, 300)->save($path);
                // } else {
                //     $img = Image::make($file)->resize(300, 300)->save($path);
                // }
                $img = Image::make($file)->resize(300, 300, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($path);
                $input['contact_image'] = $file_temp_name;
            }
        }

        $model->update($input);
        $model =  Contact::with('contact_addresses')->find($c_id)->toArray();

        return response()->json($model);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = ContactAddress::find($id);
        if($model){
             $model->delete();
        }
         $c_id = Session::get('c_id');
         $model =  Contact::with('contact_addresses')->find($c_id);
        return response()->json($model);


    }

}

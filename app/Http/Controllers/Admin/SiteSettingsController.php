<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSettings;
use Illuminate\Http\Request;
use Alert;
use Storage;
use File;
use Image;

class SiteSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!auth()->user()->can('View Site Settings'))
        access_denied();
        $data['model'] = SiteSettings::first();
        return view('admin.site-settings.form', $data);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $input = $request->all();
        //Upload Profile Picture
        if (!empty($request->files) && $request->hasFile('site_logo')) {
            $file = $request->file('site_logo');
            $type = strtolower($file->getClientOriginalExtension());

            if ($type == 'jpg' or $type == 'PNG' or $type == 'png' or $type == 'jpeg' or $type == 'svg' or $type == 'SVG') {
                $file_temp_name = 'site_logo.' . $type;
                $upload_path = 'storage/uploads';
                $old_file = $upload_path . '/' . $file_temp_name;

                if (File::exists($old_file)) {
                    //delete previous file
                    unlink($old_file);
                }
                $path = public_path('storage/uploads') . '/' . $file_temp_name;
                $img = Image::make($file)->save($path);
                $input['site_logo'] = $file_temp_name;
            } else {
                $request->session()->flash('error_message', __('File type should be .PNG, .JPG or .JPEG'));
                return redirect()->back();
            }
        }
        $sites = SiteSettings::first();
        if($sites == null){
            $sites = new SiteSettings;
        }
        $sites->fill($input)->save();
        Alert::success(__('Success'), __('Site settings updated successfully!'))->persistent('Close')->autoclose(5000);
        return redirect()->route('admin.site.settings');
    }
}

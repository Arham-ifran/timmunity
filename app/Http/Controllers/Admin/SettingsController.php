<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\InvitationMailController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Companies;
use App\Models\Languages;
use App\Models\Currency;
use App\Models\Contact;
use App\Models\SalesSettings;
use Hashids;
use Alert;
use Auth;
use DB;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()){
            $data['currencies'] = Currency::all();
            $data['default_currency'] = Currency::where('is_default',1)->where('is_active',1)->first();
            return $data;
        }
        $data = [];
        $data['active_users'] = Admin::where('is_archive', 0)->get();
        $data['pending_admins'] = Admin::where('password', null)->get();
        $data['count_active_users'] = $data['active_users']->count();
        $data['companies'] = Companies::with('countries')->orderBy('id', 'asc')->first();
        $data['companies_count'] = Companies::count();
        $data['languages'] = Languages::where('is_archive', 0)->get();
        $data['active_language_count'] = Languages::where('is_active', 1)->count();
        $data['currencies'] = Currency::all();
        $data['default_currency'] = Currency::where('is_default',1)->where('is_active',1)->first();
        return view('admin.settings.index', $data);
    }

    public function inviteNesUser(Request $request)
    {
        $input = $request->all();
        $trimName = explode('@', $input['invite_email']);

        DB::beginTransaction();
        try {

            $validator = Validator::make($request->all(), [
                'invite_email' => 'required|string|email|max:100|unique:admins,email|unique:contacts,email',
            ], [
                'invite_email.unique' => __('The email has already been taken please use other email.')
            ]);
            $check_contact = Contact::where('email',$input['invite_email'])->first();
            if($check_contact){
                $update_count = Admin::where('is_archive', 0)->count();
                return response()->json(['error' => __('Email already registered'), 'url' => "#.", "updated_count" => $update_count]);
            }
            $model = new Admin();
            $model->firstname = $trimName[0];
            $model->email = $input['invite_email'];
            $model->invitation_code = sha1(time());
            $model->email_verified_at = date('Y-m-d H:i:s');
            $model->lang_id = Auth::user()->lang_id;
            $model->timezone_id = Auth::user()->timezone_id;
            $model->is_active = 1;
            $model->save();

            $user_model = new Contact;
            $user_model->created_by = Auth::user()->id;
                $user_model->admin_id = $model->id;
                $user_model->name = $trimName[0];
                $user_model->email = $input['invite_email'];
                $user_model->type = 1;
            $user_model->save();
            $update_count = Admin::where('is_archive', 0)->count();
            // Sent Invitation Email
            InvitationMailController::sendInvitationMail($model->firstname, $model->email, $model->invitation_code,'admin');
            DB::commit();
            return response()->json(['success' => __('The Invitation email has been sent successfully!'), 'url' => route('admin.admin-user.edit', ['admin_user' => Hashids::encode($model->id)]), "updated_count" => $update_count]);
        } catch (\Exception $e) {
            DB::rollback();
            if (!$validator->passes()) {
                return response()->json(['error' => implode(', ', $validator->errors()->all())]);
            } else {
                return response()->json(['error' => $e->getMessage()]);
            }
        }
    }
    public function addLanguage(Request $request)
    {
        $input = $request->all();


        DB::beginTransaction();
        try {
            $id = Hashids::decode($input['id'])[0];
            $model = Languages::where('id', $id)->first();
            $model->update(['is_active' => 1]);
            $lang_update_count = Languages::where('is_active', 1)->count();
            DB::commit();
            $lang_switch_url = url("admin/lang/".$input['id']);
            return response()->json(['success' => __(' has been successfully installed.Users can choose his favorite language in their preferences.'), "lang_update_count" => $lang_update_count, "lang" => $model->name, "lang_switch_url" => $lang_switch_url]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()]);
        }
    }
    public function archiveLanguage(Request $request)
    {
        $input = $request->all();
        DB::beginTransaction();
        try {
            $id = Hashids::decode($input['id'])[0];
            $model = Languages::where('id', $id)->first();
            $model->update(['is_active' => 0]);
            DB::commit();
            return response()->json(['success' => __(' has been successfully archived.'), "lang" => $model->name]);
        }   catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function addCurrency(Request $request)
    {
        $input = $request->all();
        // DB::beginTransaction();
        try {
            $id = $input['id'];
            $model = Currency::where('id', $id)->first();
            $model->update(['is_active' => 1]);
            return response()->json(['success' => __('has been activated successfully.'), "currency" => $model->code]);
        } catch (\Exception $e) {
            // DB::rollback();
            return response()->json(['error' => $e->getMessage()]);
        }
    }
    public function defaultCurrency(Request $request)
    {
        $input = $request->all();
        DB::beginTransaction();
        try {
            $id = $input['id'];
            // Setting up the default currency
            Currency::where('is_default', 1)->update(['is_default'=>0]);
            // $old_model->is_default = 0;
            // $g = $model->save();

            $model = Currency::where('id', $id)->first();
            $model->is_default = 1;
            $g = $model->save();

            // Removing the previous default currency
            // $model = Currency::where('id', $id)->first();
            DB::commit();
            return response()->json(['success' => __('has been made as default currency successfully.'), "currency" => $model->code]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function salesSettings()
    {
        if(!auth()->user()->can('View Sales Settings'))
        access_denied();
        $sales_settings = SalesSettings::pluck('variable_value','variable_name')->toArray();
        return view('admin.settings.sales.index')->with('sales_settings', $sales_settings);
    }

    public function saveSalesSettings(Request $request){
        $input = $request->all();
        SalesSettings::where('variable_name','product_catalog_variants')
                ->update([ 'variable_value' => isset($input['product_catalog_variants']) ? '1' : '0' ]);

        SalesSettings::where('variable_name','product_catalog_deliver_content_email')
                ->update([ 'variable_value' => isset($input['product_catalog_deliver_content_email']) ? '1' : '0' ]);

        SalesSettings::where('variable_name','product_catalog_product_configurator')
                ->update([ 'variable_value' => isset($input['product_catalog_product_configurator']) ? '1' : '0' ]);

        SalesSettings::where('variable_name','pricing_discount')
                ->update([ 'variable_value' => isset($input['pricing_discount']) ? '1' : '0' ]);

        SalesSettings::where('variable_name','pricing_pricelist')
                ->update([ 'variable_value' => isset($input['pricing_pricelist']) ? '1' : '0' ]);

        SalesSettings::where('variable_name','orders_online_signature')
                ->update([ 'variable_value' => isset($input['orders_online_signature']) ? '1' : '0' ]);

        SalesSettings::where('variable_name','orders_online_payment')
                ->update([ 'variable_value' => isset($input['orders_online_payment']) ? '1' : '0' ]);

        SalesSettings::where('variable_name','orders_proforma_invoice')
                ->update([ 'variable_value' => isset($input['orders_proforma_invoice']) ? '1' : '0' ]);

        SalesSettings::where('variable_name','orders_customer_address')
                ->update([ 'variable_value' => isset($input['orders_customer_address']) ? '1' : '0' ]);

        SalesSettings::where('variable_name','orders_lock_confirmed_sale')
                ->update([ 'variable_value' => isset($input['orders_lock_confirmed_sale']) ? '1' : '0' ]);

        SalesSettings::where('variable_name','invoicing_policy')
                ->update([ 'variable_value' => $input['invoicing_policy'] ? $input['invoicing_policy'] : '0' ]);


        Alert::success(__('Success'), __('Sales settings updated successfully!'))->persistent('Close')->autoclose(5000);

        return redirect()->route('admin.sales.settings');
    }
}

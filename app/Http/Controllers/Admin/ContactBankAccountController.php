<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactCountry;
use Illuminate\Http\Request;
use App\Models\ContactBank;
use App\Models\ContactBankAccount;
use App\Models\Currency;
use DataTables;
use Hashids;
use Form;
use Alert;

class ContactBankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('Contact Bank Accounts Listing'))
        access_denied();
        $data = [];
        if ($request->ajax()) {
            $data = ContactBankAccount::with('contact_banks')->latest()->get();
            $datatable = Datatables::of($data);
            $datatable->addColumn('action', function ($row) {
                $actions = '';
                if (auth()->user()->hasAnyPermission(['Edit Contact Bank Accounts','Delete Contact Bank Accounts'])) {
                $actions .= auth()->user()->can('Edit Contact Bank Accounts') ? '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/contacts-bank-accounts/" . Hashids::encode($row->id) . '/edit') . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>':'';
                if(auth()->user()->can('Delete Contact Bank Accounts')) {
                $actions .= '&nbsp;' . Form::open([
                    'method' => 'DELETE',
                    'url' => ['admin/contacts-bank-accounts', Hashids::encode($row->id)],
                    'style' => 'display:inline'
                ]);

                $actions .= Form::button('<i class="fa fa-trash fa-fw" title='._('Delete').'></i>', ['onclick'=>'deleteAlert(this)', 'class' => 'delete-form-btn btn btn-default btn-icon']);
                $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                $actions .= Form::close();
                    }
                }
                return $actions;
            })
            ->addColumn('contact_banks', function($row) {

                return $row->contact_banks->name;

             });
            $datatable = $datatable->rawColumns(['contact_banks','action']);
            return $datatable->make(true);
        }
        return view('admin.contacts.bank-accounts.index', $data);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('Add New Bank Accounts'))
        access_denied();
        $data = [];
        $data['action'] = 'Add';
        $data['contact_banks'] = ContactBank::all();
        $data['contact_currency'] = Currency::all();
        return view('admin.contacts.bank-accounts.form')->with($data);

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
            $model = ContactBankAccount::findOrFail($id)[0];
            $this->validate($request, [
                'account_number' => ['required', 'string', 'max:20'],
                "account_type" => ['required', 'string', 'max:20'],
                "account_title" => ['required', 'string', 'max:50'],
                "account_holder_name" => ['required', 'string', 'max:30'],
                "bank_id" => ['required'],
                "currency_id" => ['required'],

            ]);

            $model->update($input);

            Alert::success(__('Success'), __('Contact Bank Account updated successfully!'))->persistent('Close')->autoclose(5000);
        } else {

            $this->validate($request, [
                'account_number' => ['required', 'string', 'max:20'],
                "account_type" => ['required', 'string', 'max:20'],
                "account_title" => ['required', 'string', 'max:50'],
                "account_holder_name" => ['required', 'string', 'max:30'],
                "bank_id" => ['required'],
                "currency_id" => ['required'],
            ]);

            $model = new ContactBankAccount();
            $model->fill($input)->save();


            Alert::success(__('Success'), __('Contact Bank Account added successfully!'))->persistent('Close')->autoclose(5000);
        }


        return redirect('admin/contacts-bank-accounts');
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
        if(!auth()->user()->can('Edit Contact Bank Accounts'))
        access_denied();
        $data = [];
        $id = Hashids::decode($id);
        $data['action'] = 'Edit';
        $data['contact_banks'] = ContactBank::all();
        $data['contact_currency'] = Currency::all();
        $data['model'] = ContactBankAccount::find($id)[0];
        return view('admin.contacts.bank-accounts.form')->with($data);
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
        if(!auth()->user()->can('Delete Contact Bank Accounts'))
        access_denied();
        $id = Hashids::decode($id);
        $model = ContactBankAccount::find($id)[0];
        $model->delete();
        Alert::success(__('Success'), __('Contact Bank Account Deleted Successfully.'))->persistent('Close')->autoclose(5000);
        return redirect()->back();
    }
}

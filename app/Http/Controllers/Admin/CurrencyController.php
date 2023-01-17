<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactCountry;
use App\Models\Currency;
use Illuminate\Http\Request;
use DataTables;
use Hashids;
use Form;
use Alert;
use AmrShawky\LaravelCurrency\Facade\Currency as CurrencyConvert;


class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('Contact Currencies Listing'))
        access_denied();
        $data = [];
        if ($request->ajax()) {
            $data = Currency::with(['contact_countries'])->get();
            $datatable = Datatables::of($data);
            $datatable->addColumn('action', function ($row) {
                $actions = '';
                //     $currency = array('AED', 'AUD', 'BGN', 'BRL', 'CAD', 'CHF', 'CZK','DKK', 'EUR', 'GBP', 'HKD', 'HRK','HUF', 'ILS', 'ISK', 'JPY', 'MXN', 'MYR', 'NOK','NZD', 'PHP', 'PLN', 'RON', 'RUB', 'SEK', 'SGD', 'THB', 'TWD', 'USD','ZAR');
                //     if (auth()->user()->hasAnyPermission(['Edit Contact Currencies','Delete Contact Currencies'])) {
                //         if (in_array($row->code, $currency)) {
                //     $actions .= auth()->user()->can('Edit Contact Currencies') ? '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/currencies/" . Hashids::encode($row->id) . '/edit') . '" title='.('Edit').'><i class="fa fa-pencil"></i></a>' : '';
                //     if(auth()->user()->can('Delete Contact Currencies')) {
                //     $actions .= '&nbsp;' . Form::open([
                //             'method' => 'DELETE',
                //             'url' => ['admin/currencies', Hashids::encode($row->id)],
                //             'style' => 'display:inline'
                //         ]);

                //     $actions .= Form::button('<i class="fa fa-trash fa-fw" title='.__('Delete').'></i>', ['type' => 'submit', 'class' => 'delete-form-btn btn btn-default btn-icon']);
                //     $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                //     $actions .= Form::close();
                //     }
                //     }
                //    }
                if($row->is_active == 0){
                    $actions .= '<div style="display:inline-flex">';

                    $actions .= '&nbsp;' . Form::open([
                        'method' => 'POST',
                        'url' => [route('admin.currency.change.status',[ Hashids::encode( $row->id ), 1 ] )],
                        'style' => 'display:inline'
                    ]);

                    // $actions .= Form::button('<i class="fa fa-times fa-fw" title="Reject Voucher Order"></i>', ['type' => 'submit','class' => 'status-action-btn btn btn-default btn-icon']);
                    $actions .= Form::button(__('Activate'), ['type' => 'submit','class' => 'status-action-btn btn btn-success btn-icon']);
                    $actions .= Form::submit('Activate', ['class' => 'hidden deleteSubmit']);

                    $actions .= Form::close();
                    $actions .= '</div>';
                }
                else{
                    $actions .= '<div style="display:inline-flex">';

                    $actions .= '&nbsp;' . Form::open([
                        'method' => 'POST',
                        'url' => [route('admin.currency.change.status',[ Hashids::encode( $row->id ), 0 ] )],
                        'style' => 'display:inline'
                    ]);

                    // $actions .= Form::button('<i class="fa fa-times fa-fw" title="Reject Voucher Order"></i>', ['type' => 'submit','class' => 'status-action-btn btn btn-default btn-icon']);
                    $actions .= Form::button(__('De-Activate'), ['type' => 'submit','class' => 'status-action-btn btn btn-danger btn-icon']);
                    $actions .= Form::submit('De-Activate', ['class' => 'hidden deleteSubmit']);

                    $actions .= Form::close();
                    $actions .= '</div>';
                }
                return $actions;
            })
            ->editColumn('is_active', function($row) {

                if($row->is_active == 1){
                    return '<span class="badge badge-success">'.__('Active').'</span>';
                }
                else{
                    return '<span class="badge badge-danger">'.__('Archived').'</span>';
                }

             })
            ->editColumn('is_default', function($row) {

                return $row->is_default == 1 ? '<span class="badge badge-success">'.__('Default').'</span>' : '';

             });
            $datatable = $datatable->rawColumns(['is_default','is_active','contact_countries','action']);
            return $datatable->make(true);
        }
        return view('admin.contacts.currencies.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('Add New Contact Currencies'))
        access_denied();
        $data = [];
        $data['contact_countries'] = ContactCountry::all();
        $data['action'] = 'Add';
        return view('admin.contacts.currencies.form')->with($data);
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
            $model = Currency::findOrFail($id)[0];
            $this->validate($request, [
                'currency' => 'required|string|max:100',
                'code' => 'required|string|max:100',
                'symbol' => 'required|string|max:100',
                'country_id' => 'required',
            ]);

            $model->update($input);

            Alert::success(__('Success'), __('Contact Currency updated successfully!'))->persistent('Close')->autoclose(5000);
        } else {

            $this->validate($request, [
                'currency' => 'required|string|max:100',
                'code' => 'required|string|max:100',
                'symbol' => 'required|string|max:100',
                'country_id' => 'required',
            ]);

            $model = new Currency();
            $model->fill($input)->save();


            Alert::success(__('Success'), __('Contact Currency added successfully!'))->persistent('Close')->autoclose(5000);
        }


        return redirect('admin/currencies');
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

        if(!auth()->user()->can('Edit Contact Currencies'))
        access_denied();
        $data = [];
        $id = Hashids::decode($id);
        $data['action'] = 'Edit';
        $data['contact_countries'] = ContactCountry::all();
        $data['model'] = Currency::find($id)[0];
        return view('admin.contacts.currencies.form')->with($data);
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
        if(!auth()->user()->can('Delete Contact Currencies'))
        access_denied();
        $id = Hashids::decode($id);
        $model = Currency::find($id)[0];
        $model->delete();
        Alert::success(__('Success'), __('Contact Currency Deleted Successfully.'))->persistent('Close')->autoclose(5000);
        return redirect('admin/currencies');
    }

    /**
     * Update Exchange rates based on the base currency
     *
     *
     */
    public function exchangeRatesCurrency()
    {
        $currencies = Currency::where('is_active', 1)->get();
        $default_currency = Currency::where('is_active', 1)->where('is_default', 1)->first();
        if($default_currency)
        {
            foreach($currencies as $currency)
            {
                $exchange_rate = CurrencyConvert::convert()
                    ->from($default_currency->code)
                    ->to($currency->code)
                    ->get();
                $currency->exchange_rate = $exchange_rate;
                $currency->save();

            }
            Alert::success(__('Success'), __('Exchange rates updated.'))->persistent('Close')->autoclose(5000);
            return 'true';
        }
        else
        {
            return 'false';
        }
    }

    /**
     * Switch Currency
     *
     */
    public function switchCurrency($code)
    {
        $currency = Currency::where('code', $code)->first();
        $currency = $currency ? $currency : Currency::where('code', 'EUR')->first();
        if($currency)
        {
            session()->put('exchange_rate', $currency->exchange_rate);
            session()->put('currency_symbol', $currency->symbol);
            session()->put('currency_code', $code);
        }
        else
        {
            session()->put('exchange_rate', 1);
            session()->put('currency_symbol','€');
            session()->put('currency_code', 'EUR');
        }
        return redirect()->back();
    }

    public function changeStatus($id, $status)
    {
        $id = Hashids::decode($id);
        $currency = Currency::where('id', $id)->update(['is_active'=>$status]);
        return redirect()->route('admin.currencies.index');
    }
}

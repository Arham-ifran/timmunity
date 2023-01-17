<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductPricelistConfiguration;
use App\Models\ProductPricelistRule;
use App\Models\ProductVariationDetail;
use App\Models\ContactCountry;
use App\Models\ContactCountryGroup;
use App\Models\ProductPriceList;
use App\Models\ProductVariation;
use App\Models\productCategorie;
use App\Models\Currency;
use App\Models\Products;
use Yajra\DataTables\DataTables;
use Auth;
use Hashids;
use File;
use Form;
use Image;
use Alert;

class PricelistsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('Price Lists Listing'))
        access_denied();

        $data = [];
        if ($request->ajax()) {
            $data = ProductPriceList::with('rules','configuration')->whereNull('parent_id')->orderBy('id','desc')->get();
            $datatable = Datatables::of($data);
            $datatable->addColumn('name', function ($row) {
                return auth()->user()->can('Edit Price List') ? '<a href="' .route('admin.price-lists.edit',Hashids::encode($row->id)). '">'.$row->name.'</a>' : $row->name;
            });
            // $datatable->addColumn('currency', function ($row) {
            //     return $row->currency ? $row->currency->code : '';
            // });
            $datatable->addColumn('selectable', function ($row) {
                return @$row->configuration->selectable == 1 ? __('Yes') : __('No');
            });
            $datatable->addColumn('status', function ($row) {
                return $row->is_active == 1 ? 'Active' : 'Archived';
            });
            $datatable->addColumn('action', function ($row) {
                $actions = '';
                if (auth()->user()->hasAnyPermission(['Edit Product','Delete Product']))
                {
                    $actions = '<div style="display:inline-flex">';
                    $actions .= auth()->user()->can('Edit Price List') ? '&nbsp;<a class="btn btn-primary btn-icon" href="' . route('admin.price-lists.edit',Hashids::encode($row->id)) . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>&nbsp;' : '';
                    if(auth()->user()->can('Delete Price List')) {
                        $actions .= '&nbsp;' . Form::open([
                            'method' => 'DELETE',
                            'url' => [route('admin.price-lists.destroy',Hashids::encode($row->id))],
                            'style' => 'display:inline'
                        ]);

                        $actions .= Form::button('<i class="fa fa-trash fa-fw" title='.__('Delete').'></i>', ['onclick' => 'deleteAlert(this)', 'class' => 'delete-form-btn btn btn-default btn-icon']);                $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                        $actions .= Form::close();
                    }
                    $actions .= '</div>';
                }
                return $actions;
            });
            $datatable = $datatable->rawColumns(['name','action']);
            return $datatable->make(true);
        }


        return view('admin.sales.price-lists.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('Add Price List'))
        access_denied();

        $data = [];

        $data['id'] = null;
        $data['action'] = 'Add';
        $data['currency'] = Currency::all();
        $data['country_group'] = ContactCountryGroup::all();
        $data['countries'] = ContactCountry::all();
        $data['products'] = Products::all();
        $data['product_variants'] = ProductVariation::with('product','variation_details')->get();
        // $data['product_variants'] = Products::whereHas('variations')->with('variations.variation_details')->get();
        $data['product_category'] = productCategorie::all();

        return view('admin.sales.price-lists.pricelist_form')->with($data);
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
        $input['currency_id'] = isset($input['currency_id']) ? Hashids::decode($input['currency_id'])[0] : null;
        $input['is_active'] = isset($input['is_active']) ? (int)$input['is_active'] : 1;
        $pricelist_rule_ids = explode(',',$input['pricelist_rule_ids']);
        if (isset($input['action']) && $input['action'] == 'Edit') {

            $id = Hashids::decode($input['id']);
            $model = ProductPriceList::findOrFail($id)[0];
            $this->validate($request, [
                'name' => ['required', 'string', 'max:100'],
            ]);
            $model->update($input);
            $model->is_active = $input['is_active'];
            $model->type = $input['type'];
            $model->save();
            Alert::success(__('Success'), __('Price List updated successfully!'))->persistent('Close')->autoclose(5000);
            ProductPricelistRule::where('pricelist_id', $id)->whereNotIn('id',$pricelist_rule_ids)->delete();
            ProductPricelistConfiguration::where('pricelist_id', $id)->delete();

        } else {
            $this->validate($request, [
                'name' => ['required', 'string', 'max:100'],
            ]);
            $model = new ProductPriceList();
            $model->fill($input)->save();
            $model->type = $input['type'];
            $model->save();

        }
        ProductPricelistRule::whereIn('id', $pricelist_rule_ids)->update([ 'pricelist_id' => $model->id ]);

        $input['config']['pricelist_id'] = $model->id;
        $input['config']['country_group_id'] = $input['config']['country_group_id'] ? Hashids::decode($input['config']['country_group_id'])[0] : null;
        $input['config']['country_id'] = $input['config']['country_id'] ? Hashids::decode($input['config']['country_id'])[0] : null;
        $model = new ProductPricelistConfiguration();

        $model->fill($input['config'])->save();

        Alert::success(('Success'), __('Price List added successfully!'))->persistent('Close')->autoclose(5000);

        return redirect()->route('admin.price-lists.index');
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
        if(!auth()->user()->can('Edit Price List'))
        access_denied();

        $data = [];

        $data['id'] = $id;
        $id = Hashids::decode($id)[0];
        $data['action'] = 'Edit';
        $data['model'] = ProductPriceList::with('rules','configuration')->where('id', $id)->first();
        $data['currency'] = Currency::all();
        $data['country_group'] = ContactCountryGroup::all();
        $data['countries'] = ContactCountry::all();
        $data['products'] = Products::all();
        $data['product_variants'] = ProductVariation::with('product','variation_details')->get();
        // $data['product_variants'] = Products::whereHas('variations')->with('variations.variation_details')->get();
        $data['product_category'] = productCategorie::all();

        return view('admin.sales.price-lists.pricelist_form')->with($data);
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
        if(!auth()->user()->can('Delete Price List'))
        access_denied();

        $id = Hashids::decode($id)[0];
        ProductPriceList::where('id', $id)->delete();
        Alert::success(__('Success'), __('Price List deleted successfully!'))->persistent('Close')->autoclose(5000);
        return redirect()->route('admin.price-lists.index');
    }

    /**
     *  Insert Price List Rules Temporary
     *
     */
    public function insertPriceListRule(Request $request)
    {
        $input = $request->all();

        $input['pricelist_id'] = 0;
        $input['product_id'] = isset($input['product_id']) ? Hashids::decode($input['product_id'])[0] : null;
        $input['category_id'] = isset($input['category_id']) ? Hashids::decode($input['category_id'])[0] : null;
        $input['variation_id'] = isset($input['variation_id']) ? Hashids::decode($input['variation_id'])[0] : null;

        if( $input['apply_on'] == null || $input['apply_on'] == ''
            || $input['min_qty'] == null || $input['min_qty'] == ''
            || $input['start_date'] == null || $input['start_date'] == ''
            || $input['end_date'] == null || $input['end_date'] == ''
            || $input['price_computation'] == null || $input['price_computation'] == ''
            || $input['amount'] == null || $input['amount'] == ''
            )
            {
                return 'false';
        }
        switch ($input['price_computation'])
        {
            case 0:
                $input['fixed_value'] = $input['amount'];
                break;
            case 1:
                $input['percentage_value'] = $input['amount'];
                break;
        }
        $model = null;
        if(isset($input['rule_id']) && $input['rule_id'] != null){
            $model = ProductPricelistRule::where('id', $input['rule_id'])->first();
        }else{
            $model = new ProductPricelistRule();
        }
        $model->fill($input)->save();


        return $model->id;
    }
     /**
     *  Get Price List Rules Temporary
     *
     */
    public function getPriceListRule(Request $request)
    {
        $model = ProductPricelistRule::where('id', (int)$request->pricelist_rule_id)->first();
        $model->start_date = \Carbon\Carbon::parse($model->start_date)->format('Y-m-d');
        $model->end_date = \Carbon\Carbon::parse($model->end_date)->format('Y-m-d');
        $model->category_id = Hashids::encode($model->category_id);
        $model->product_id = Hashids::encode($model->product_id);
        $model->variation_id = Hashids::encode($model->variation_id);
        return $model;
    }

    /**
     * Delete the Price List
     *
     */
    public function deletePriceList( Request $request )
    {
        $priceListIds = explode(',',$request->ids);
        ProductPriceList::whereIn('id',$priceListIds)->delete();

        Alert::success(__('Success'), __('Price Lists deleted successfully!'))->persistent('Close')->autoclose(5000);

        return redirect()->route('admin.price-lists.index');
    }
    /**
     * Delete the Price List Rule
     *
     */
    public function deletePriceListRule( Request $request )
    {
        $price_list_id = ProductPricelistRule::where('id',(int)$request->pricelist_rule_id)->first()->pricelist_id;

        ProductPricelistRule::where('id',$request->pricelist_rule_id)->delete();

        $price_list_ids = implode(',',ProductPricelistRule::where('pricelist_id',$price_list_id)->pluck('id')->toArray());

        return $price_list_ids;
    }
    /**
     * Archive the Price List
     *
     */
    public function archivePriceList( Request $request )
    {
        if(!auth()->user()->can('Archive / Unarchive Price List'))
        access_denied();

        $priceListIds = explode(',',$request->ids);
        ProductPriceList::whereIn('id',$priceListIds)->update(['is_active' => 1]);

        Alert::success(__('Success'), __('Price List archived successfully!'))->persistent('Close')->autoclose(5000);

        return redirect()->route('admin.price-lists.index');
    }
    /**
     * Un Archive the Price List
     *
     */
    public function unarchivePriceList( Request $request )
    {
        if(!auth()->user()->can('Archive / Unarchive Price List'))
        access_denied();

        $priceListIds = explode(',',$request->ids);
        ProductPriceList::whereIn('id',$priceListIds)->update(['is_active' => 0]);

        Alert::success(__('Success'), __('Price List unarchived successfully!'))->persistent('Close')->autoclose(5000);

        return redirect()->route('admin.price-lists.index');
    }
}

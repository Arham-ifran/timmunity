<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ResellerPackage;
use App\Models\ResellerPackageRule;
use DataTables;
use Hashids;
use Alert;
use App\Models\Products;
use App\Models\ProductVariation;
use Form;
class ResellerPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [];
        $data = ResellerPackage::orderBy('id','desc')->get();
        if ($request->ajax()) {

            $data = ResellerPackage::orderBy('id','desc')->get();
            $datatable = Datatables::of($data);
            $datatable->editColumn('model',function($row){
                switch ($row->model) {
                    case 0:
                        return __('Incremental');
                        break;
                    case 1:
                        return __('Decremental');
                        break;
                }
            });
            $datatable->editColumn('percentage',function($row){
               return $row->percentage.'%';
            });
            $datatable->editColumn('status',function($row){
                return ($row->is_active)? '<span class="badge badge-success">'.__('Active').'</span>' : '<span class="badge badge-danger">'.__('Inactive').'</span>';
            });
            $datatable->addColumn('action', function ($row) {
                $actions = '';
                $actions .= '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/reseller-package/" . Hashids::encode($row->id) . '/edit') . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>' ;
                $actions .= '&nbsp;' . Form::open([
                    'method' => 'DELETE',
                    'url' => ['admin/reseller-package', Hashids::encode($row->id)],
                    'style' => 'display:inline'
                ]);
                $actions .= Form::button('<i class="fa fa-trash fa-fw" title='.__('Delete').'></i>', ['type' => 'submit', 'class' => 'delete-form-btn btn btn-default btn-icon']);
                $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                $actions .= Form::close();
                return $actions;
            });
            $datatable = $datatable->rawColumns(['status','action']);
            return $datatable->make(true);
        }
        return view('admin.website.reseller-package.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [];
        $data['action'] = 'Add';
        $data['products'] = Products::all();
        $data['product_variants'] = ProductVariation::with('product','variation_details')->get();
        return view('admin.website.reseller-package.form')->with($data);
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
            $model = ResellerPackage::findOrFail($id)[0];
            $this->validate($request, [
                'id' => 'required',
                'package_name' => 'required|string|max:100',
                'model' => 'required',
                'is_active' => 'required',
                'percentage' => 'required',
                'rule_ids' => 'required'
            ]);
            // $input['is_active'] = 1;
            $model->update($input);
            ResellerPackageRule::whereIn('id', explode(',',$input['rule_ids']))->update(['package_id'=>$model->id]);
            Alert::success(__('Success'), __('Package successfully!'))->persistent('Close')->autoclose(5000);
        } else {

            $this->validate($request, [
                'package_name' => 'required|string|max:100',
                'model' => 'required',
                'is_active' => 'required',
                'percentage' => 'required',
                'rule_ids' => 'required'
            ]);
            $model = new ResellerPackage();
            $model->fill($input)->save();

            ResellerPackageRule::whereIn('id', explode(',',$input['rule_ids']))->update(['package_id'=>$model->id]);


            Alert::success(__('Success'), __('Package added successfully!'))->persistent('Close')->autoclose(5000);
        }


        return redirect('admin/reseller-package');
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
        $id = Hashids::decode($id);
        $data['action'] = 'Edit';
        $data['model'] = ResellerPackage::find($id)[0];
        $data['rule_ids'] = ResellerPackageRule::where('package_id', $id)->pluck('id')->toArray();
        $data['products'] = Products::all();
        $data['product_variants'] = ProductVariation::with('product','variation_details')->get();
        return view('admin.website.reseller-package.form')->with($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = Hashids::decode($id);
        $model = ResellerPackage::find($id)[0];
        $model->delete();
        Alert::success(__('Success'), __('Package Deleted Successfully.'))->persistent('Close')->autoclose(5000);
        return redirect('admin/reseller-package');
    }

    public function storeRule(Request $request)
    {
        $reseller_package_rule = null;
        if($request->package_rule_id != null){
            $reseller_package_rule = ResellerPackageRule::where('id',$request->package_rule_id)->first();
        }else{
            $reseller_package_rule = new ResellerPackageRule;
        }
        $reseller_package_rule->apply_on = $request->apply_on;
        $reseller_package_rule->model = $request->model;
        $reseller_package_rule->percentage = $request->percentage;
        $reseller_package_rule->product_id = $request->product_id ? Hashids::decode($request->product_id)[0] : null;
        $reseller_package_rule->variation_id = $request->variation_id ? Hashids::decode($request->variation_id)[0] : null;
        $reseller_package_rule->use_default = $request->use_default;
        $reseller_package_rule->save();

        $reseller_package_rule->product_name = '';
        if($reseller_package_rule->variation){
            $reseller_package_rule->product_name = $reseller_package_rule->variation->product->product_name.' '.$reseller_package_rule->variation->variation_name;
        }elseif($reseller_package_rule->product){
            $reseller_package_rule->product_name = $reseller_package_rule->product->product_name;
        }

        return $reseller_package_rule;
    }
    public function getRule(Request $request)
    {
        $reseller_package_rule = ResellerPackageRule::where('id', $request->rule_id)->first();
        $reseller_package_rule->product_id = Hashids::encode($reseller_package_rule->product_id);
        $reseller_package_rule->variation_id = Hashids::encode($reseller_package_rule->variation_id);
        return $reseller_package_rule;
    }
    public function removeRule(Request $request)
    {
        $package_id = ResellerPackageRule::where('id',$request->rule_id)->first()->package_id;

        ResellerPackageRule::where('id',$request->rule_id)->delete();

        $rule_ids = implode(',',ResellerPackageRule::where('package_id',$package_id)->pluck('id')->toArray());

        return $rule_ids;
    }
}

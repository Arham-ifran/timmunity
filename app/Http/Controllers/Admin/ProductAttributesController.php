<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductAttachedAttribute;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Auth;
use Hashids;
use File;
use Image;
use Alert;
use DataTables;
use Form;

class ProductAttributesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('Attributes Listing'))
        access_denied();

        $data = [];
        if ($request->ajax()) {
            $data = ProductAttribute::orderBy('id','desc')->get();

            $datatable = Datatables::of($data);
            $datatable->addColumn('name', function ($row) {

                $attribute_name = translation($row->id,13,app()->getLocale(),'attribute_name',$row->attribute_name);
                return auth()->user()->can('Edit Attributes') ? '<a href="' .route('admin.attribute.edit',Hashids::encode($row->id)). '">'.$attribute_name.'</a>': $attribute_name;
            });
            $datatable->addColumn('display_type', function ($row) {
                if($row->display_type ==  1){
                    return __('Radio');
                }elseif($row->display_type ==  2){
                    return __('Select');
                }elseif($row->display_type ==  3){
                    return __('Color');
                }
            });
            $datatable->addColumn('creation_mode', function ($row) {
                if($row->variants_creation_mode ==  1)
                {
                    return __('Instant');
                }
                elseif($row->variants_creation_mode ==  2)
                {
                    return __('Dynamic');
                }
                elseif($row->variants_creation_mode ==  3)
                {
                    return __('Never');
                }
            });
            $datatable->addColumn('action', function ($row) {
                $actions = '';
                if (auth()->user()->hasAnyPermission(['Edit Attributes','Delete Attributes']))
                {
                    $actions .= auth()->user()->can('Edit Attributes') ? '&nbsp;<a class="btn btn-primary btn-icon" href="' . route('admin.attribute.edit',Hashids::encode($row->id)) . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>' : '';
                    if(auth()->user()->can('Delete Attributes')) {
                        $actions .= '&nbsp;' . Form::open([
                                'method' => 'DELETE',
                                'url' => [route('admin.attribute.destroy', Hashids::encode($row->id))],
                                'style' => 'display:inline'
                            ]);
                        $actions .= Form::button('<i class="fa fa-trash fa-fw" title='.__('Delete').'></i>', ['onclick' => 'deleteAlert(this)', 'class' => 'delete-form-btn btn btn-default btn-icon']);

                        $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                        $actions .= Form::close();
                    }
                }
                return $actions;
            });
            $datatable = $datatable->rawColumns(['action','name']);
            return $datatable->make(true);
        }
        return view('admin.sales.configuration.attribute.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('Add Attributes'))
        access_denied();

        $data = [];
        $data['action'] = 'Add';
        return view('admin.sales.configuration.attribute.attribute_form')->with($data);
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

        if (isset($input['action']) && $input['action'] == 'Edit') {

            $id = Hashids::decode($input['id']);
            $model = ProductAttribute::findOrFail($id)[0];
            $this->validate($request, [
                'attribute_name' => ['required', 'string', 'max:100'],
            ]);
            $model->update($input);
            Alert::success(__('Success'), __('Attribute updated successfully!'))->persistent('Close')->autoclose(5000);

        } else {
            $this->validate($request, [
                'attribute_name' => ['required', 'string', 'max:100'],
                'attribute_value' => ['required'],
            ]);

            $model = new ProductAttribute();

        }
        $model->fill($input)->save();
        $check_usage = ProductAttachedAttribute::where('attribute_id',$model->id)->first();
        if(!$check_usage){

            ProductAttributeValue::where('product_attribute_id', $model->id)->delete();
            foreach($input['attribute_value'] as $attr_val)
            {

                $attr_model = new ProductAttributeValue;
                $attr_model->product_attribute_id = $model->id;
                $attr_model->attribute_value = $attr_val;
                $attr_model->save();
            }
            Alert::success(__('Success'), __('Attribute added successfully!'))->persistent('Close')->autoclose(5000);
        }else{
            Alert::success(__('Success'), __('Attribute updated but values were associated. Could not update those values!'))->persistent('Close')->autoclose(5000);

        }

        return redirect()->route('admin.attribute.index');
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
        if(!auth()->user()->can('Edit Attributes'))
        access_denied();

        $data = [];
        $id = Hashids::decode($id);
        $data['action'] = 'Edit';
        $data['model'] = ProductAttribute::find($id)[0];
        $data['model_values'] = ProductAttributeValue::where('product_attribute_id',$id)->get();

        return view('admin.sales.configuration.attribute.attribute_form')->with($data);

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
        if(!auth()->user()->can('Delete Attributes'))
        access_denied();

        $id = Hashids::decode($id)[0];
        $check_usage = ProductAttachedAttribute::where('attribute_id',$id)->first();
        if($check_usage){
            Alert::warning(__('Warning'), __('You cannot delete the attribute because it is used on some products'))->persistent('Close')->autoclose(5000);
            return redirect()->back();
        }
        ProductAttribute::where('id', $id)->delete();
        ProductAttributeValue::where('product_attribute_id', $id)->delete();
        Alert::success(__('Success'), __('Attribute Deleted successfully!'))->persistent('Close')->autoclose(5000);
        return redirect()->route('admin.attribute.index');
    }

    public function exportAttributes()
    {
        $contacts = ProductAttribute::all()->toArray();
        $customer_array[] = array('Attribure Name', 'Display Type', 'Varriants Creation Mode');

        foreach ($contacts as $contact) {
            $customer_array[] = [
                'Name' => $contact['attribute_name'],
                'Email' => $contact['display_type'],
                'Mobile' => $contact['variants_creation_mode'],
            ];
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        for ($i = 0; $i < count($customer_array); $i++) {
            //set value for indi cell
            $row = $customer_array[$i];
            //writing cell index start at 1 not 0
            $j = 1;
            foreach ($row as $x => $x_value) {
                $sheet->setCellValueByColumnAndRow($j, $i + 1, $x_value);
                $j = $j + 1;
            }
        }

        ob_clean();
        $writer = new Xlsx($spreadsheet);
        //$writer->save('hello world.xlsx');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="download.xlsx"');
        $writer->save("php://output");
    }

    public function searchAttribute(Request $request){
        if ($request->ajax()) {
            $attributes = ProductAttribute::with('attributeValue')->where('attribute_name', 'LIKE', '%'.$request->q.'%')->get();
            foreach($attributes as $index => $attribute)
            {
                $attributes[$index]->attribute_name = translation( $attribute->id,13,app()->getLocale(),'attribute_name',$attribute->attribute_name);
            }
            return ($attributes);
        }
    }
    public function searchAttributeValues(Request $request){
        if ($request->ajax()) {
            $attribute_values = ProductAttributeValue::where('product_attribute_id', $request->product_attribute_id)->get();

            return ($attribute_values);
        }
    }
    public function addNewAttributeValue($product_attribute_id, Request $request){
        if ($request->ajax()) {
                $new_value = new ProductAttributeValue;
                $new_value->product_attribute_id = $product_attribute_id;
                $new_value->attribute_value = $request->attribute_value;
                $new_value->save();

                return 'true';
        }
    }
}

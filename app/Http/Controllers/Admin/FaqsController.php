<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;
use Yajra\DataTables\DataTables;
use Hashids;
use Form;
use Alert;

class FaqsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('FAQs Listing'))
        access_denied();
        $data = [];
        if ($request->ajax()) {
            $data = Faq::orderBy('id','desc')->get();
            $datatable = Datatables::of($data);
            $datatable->setRowId(function ($row) {
                return 'tr_' . $row->id;
            });
            $datatable->addColumn('delete_check', function (Faq $row) {
                $indv_check = '';
                if (auth()->user()->can('Delete FAQ')) {
                $indv_check = '<input type="checkbox" name="deleteFaqChecks[]" class="del_faq_sub_chk checkbox-input countFaqChecks" onclick="checkBoxActions(this)" data-id="' . Hashids::encode($row->id) . '">';
                }
                return $indv_check;
            });
            $datatable->editColumn('question', function ($row) {

                $question = translation($row->id,27,app()->getLocale(),'question',$row->question);
                return $question;
            });
            $datatable->editColumn('status', function ($row) {
                return ($row->status) ? '<span class="badge badge-success">'.__('Active').'</span>' : '<span class="badge badge-danger">'.__('In Active').'</span>';
            });
            $datatable->addColumn('action', function ($row) {
                $actions = '';
                if (auth()->user()->hasAnyPermission(['Edit FAQ','Delete FAQ'])) {
                $actions .= auth()->user()->can('Edit User') ? '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/website/faqs/" . Hashids::encode($row->id) . '/edit') . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>':'';
                if(auth()->user()->can('Delete FAQ')) {
                $actions .= '&nbsp;' . Form::open([
                    'method' => 'DELETE',
                    'url' => ['admin/website/faqs', Hashids::encode($row->id)],
                    'style' => 'display:inline'
                ]);

                $actions .= Form::button('<i class="fa fa-trash fa-fw" title='.__('Delete').'></i>', ['onclick' => 'deleteAlert(this)', 'class' => 'delete-form-btn btn btn-default btn-icon']);
                $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                $actions .= Form::close();
                 }
               }
                return $actions;
            });
            $datatable = $datatable->rawColumns(['delete_check','question','status', 'action']);
            return $datatable->make(true);
        }
        return view('admin.website.faqs.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('Create FAQ'))
        access_denied();
        $data = [];
        $data['action'] = 'Add';
        return view('admin.website.faqs.form')->with($data);
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
            $model = Faq::findOrFail($id)[0];
            $this->validate($request, [
                'question' => 'required|max:200',
                'answer' => 'required',
                'display_order' => 'required|max:255|unique:faqs,display_order,'.$id[0],
            ]);

            $model->update($input);

            Alert::success(__('Success'), __('FAQ updated successfully!'))->persistent('Close')->autoclose(5000);
        } else {
            $this->validate($request, [
                'question' => 'required|max:200',
                'answer' => 'required',
                'display_order' => 'required|max:255|unique:faqs'
            ]);

            $model = new Faq();
            $model->fill($input)->save();

            Alert::success(__('Success'), __('FAQ added successfully!'))->persistent('Close')->autoclose(5000);
        }
        return redirect('admin/website/faqs');
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
        if(!auth()->user()->can('Edit FAQ'))
        access_denied();
        $data = [];
        $id = Hashids::decode($id);
        $data['action'] = 'Edit';
        $data['model'] = Faq::find($id)[0];
        return view('admin.website.faqs.form')->with($data);
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
        if(!auth()->user()->can('Delete FAQ'))
        access_denied();
        $id = Hashids::decode($id);
        $model = Faq::find($id)[0];
        $model->delete();

        Alert::success(__('Success'), __('FAQ deleted Successfully.'))->persistent('Close')->autoclose(5000);
        return redirect('admin/website/faqs');
    }
    // Bulk Deletion FAQs Records.
    public function bulkDelete(Request $request)
    {
        if(!auth()->user()->can('Delete FAQ'))
        access_denied();

        $ids = $request->ids;
        $idsArr = explode(",", $ids);
        $deocdedIds = array();
        foreach($idsArr as $id) {
           $deocdedIds[] = Hashids::decode($id)[0];
        }
        Faq::whereIn('id', $deocdedIds)->delete();
        $response = response()->json(['success' => __('FAQs Deleted successfully.')]);

        return $response;
    }

}

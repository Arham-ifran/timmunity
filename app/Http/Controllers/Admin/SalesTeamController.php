<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Interfaces\PartialViewsRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Models\SalesTeam;
use Hashids;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Followers;
use App\Models\ActivityAttachments;
use App\Models\ActivityMessages;
use App\Models\QuotationOrderLineTax;
use App\Models\ActivityLogNotes;
use App\Models\ActivityTypes;
use App\Models\ScheduleActivities;
use App\Models\Contact;
use App\Models\SalesTeamsMembers;
use App\Models\ContactCountry;
use App\Models\Products;
use Alert;
use App\Models\Quotation;
use App\Models\QuotationOrderLine;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;
use DataTables;
use Form;
use Auth;

class SalesTeamController extends Controller
{
    /**
     * @var PartialViewsRepositories.
    */
    protected $salesTeamRepository;
    /**
     * PartialViewsRepositories Constructor.
     *
     * @param PartialViewsRepositories $salesTeamRepository
     */
    public function __construct(PartialViewsRepositoryInterface $salesTeamRepository)
    {
        $this->salesTeamRepository = $salesTeamRepository;
    }

    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('Sales Team Listing'))
        access_denied();

        $data = [];
        if ($request->ajax()) {
            $archive = $request->get('is_archive');
            $data = SalesTeam::with('team_leads')->latest()
            ->where(function ($data) use ($archive) {
                    if (isset($archive) && $archive != "") {
                        $data->where('is_archive', $archive);
                    }
                })
                ->orderBy('id','desc')->get();
            $datatable = Datatables::of($data);
            $datatable->setRowId(function ($row) {
                return 'tr_' . $row->id;
            });
            $datatable->addColumn('delete_check', function (SalesTeam $row) {
                $indv_check = '';
                $indv_check = '<input type="checkbox" name="teamDeleteCheck[]" class="sale_team_sub_chk checkbox-input teamCountChecks" onclick="checkBoxActions(this)" data-id="' . $row->id . '">';
                return $indv_check;
            });
            $datatable->editColumn('sales_team', function ($row) {
                return auth()->user()->can('View Sales Team') ? '<a href="' .route('admin.sales-team.show',Hashids::encode($row->id)). '">'.ucfirst($row->name).'</a>' : $row->name;
            });
            $datatable->addColumn('team_leader', function ($row) {
                if (isset($row->team_leads->firstname)) {
                    return $row->team_leads->firstname .' '. $row->team_leads->lastname;
                } else {
                    return '';
                }
            });
            $datatable->addColumn('action', function ($row) {
                $actions = '';
                if (auth()->user()->hasAnyPermission(['Edit Sales Team','Delete Sales Team','View Sales Team']))
                {
                    $actions .= auth()->user()->can('Edit Sales Team') ? '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/sales-management/configuration/sales-team/" . Hashids::encode($row->id) . '/edit') . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>' : '';
                    $actions .= auth()->user()->can('View Sales Team') ?'&nbsp;<a class="btn btn-warning btn-icon" href="' . route('admin.sales-team.show',Hashids::encode($row->id)) . '" title='.__('View').'><i class="fa fa-eye"></i></a>' : '';
                    if(auth()->user()->can('Delete Sales Team')) {
                        if($row->id != 1) {
                            $actions .= '&nbsp;' . Form::open([
                                'method' => 'DELETE',
                                'url' => ['admin/sales-management/configuration/sales-team', Hashids::encode($row->id)],
                                'style' => 'display:inline'
                            ]);

                            $actions .= Form::button('<i class="fa fa-trash fa-fw" title='.__('Delete').'></i>', ['onclick' => 'deleteAlert(this)', 'class' => 'delete-form-btn btn btn-default btn-icon']);
                            $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                            $actions .= Form::close();
                        }
                    }
                }
                return $actions;
            });
            $datatable = $datatable->rawColumns(['delete_check', 'sales_team', 'team_leader', 'action']);
            return $datatable->make(true);
        }
        return view('admin.sales.sales-team.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('Add Sales Team'))
        access_denied();

        $data = [];
        $data['action'] = 'Add';
        $data['team_leads'] = Admin::where('is_active', 1)->get();
        return view('admin.sales.sales-team.sales_team_form')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        parse_str($_POST['form_data'], $input);
        if($input['team_lead_id'] != "")
            $team_lead_id = Hashids::decode($input['team_lead_id'])[0];
        else
            $team_lead_id = null;
        $member_ids = explode(',', $input['member_list_ids']);
        $remove_member_ids = explode(',', $input['remove_member_ids']);
        $request = new Request([
            'name' => $input['name'],
            'id' => $input['id'],
        ]);
        $messages = [
            'name.required' => __('This field is required!'),
            'id.required' => __('ID is required!'),
        ];

        if ($input['action'] == 'Edit') {

            $this->validate($request, [
                'name' => 'required|string|max:100',
                'id' => 'required|string|max:100',
            ], $messages);
            $id = Hashids::decode($input['id']);
            $model = SalesTeam::findOrFail($id)[0];
            $input['team_lead_id'] = $team_lead_id;
            $model->update($input);
            $message = __('Sales team updated successfully!');
        } else {
            $this->validate($request, [
                'name' => 'required|string|max:100',
            ], $messages);

            $model = new SalesTeam();
            $input['team_lead_id'] = $team_lead_id;
            $model->fill($input)->save();
            $message = __('Sales team added successfully!');
        }
        // Add record in pivot table sales team members
        if ($input['member_list_ids'] <> "") {
            foreach ($member_ids as $m_id) {
                $member = new SalesTeamsMembers();
                $member->sales_team_id = $model->id;
                $member->member_id = Hashids::decode($m_id)[0];
                $member->save();
            }
        }
        // Remove Member from pivot table
           if($input['remove_member_ids'] <> "") {
                foreach ($remove_member_ids as $row) {
                    SalesTeamsMembers::where('member_id', Hashids::decode($row)[0])->where('sales_team_id', $model->id)->delete();
                }
            }
        return response()->json([
            'data' => ['redirect' => url('admin/sales-management/configuration/sales-team')],
            'status' => 1,
            'message' => $message,
            'messagetype' => 'success',
        ], 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = [];
        $id = Hashids::decode($id)[0];
        $data['action'] = 'View';
        $data['model'] = SalesTeam::find($id);
        $data['team_leader'] = Admin::where('id',$data['model']->team_lead_id)->where('is_active', 1)->first();
        $data['members'] = SalesTeamsMembers::with('team_members')->where('sales_team_id', $id)->get();
        // Code For Activities Section
        $log_uid = Auth::user()->id;
        $partner = Auth::user()->firstname .' '. Auth::user()->lastname;
        $start_date = $data['model']->start_date;
        $end_date = $data['model']->end_date;
        $logged_in_follower_ids = Followers::where('contact_id', $log_uid)->where('sales_team_id', $id)->where('module_type', 7)->where('follower_type', 2)->pluck('follower_id')->toArray();
        if (Contact::whereIn('id', $logged_in_follower_ids)->where('admin_id', $log_uid)->exists()) {

           $data['is_following'] = 1;
        }
        else {

            $data['is_following'] = 0;
        }
        $data['followers'] = $this->salesTeamRepository->follower_list($id,$log_uid, $module_type=7);
        $data['send_messages'] = ActivityMessages::with('activity_message_users','activity_attachments')->where('sales_team_id',$id)->orderBy('id','desc')->get();
        $attachments = ActivityAttachments::where('sales_team_id', $id)->orderBy('send_msg_id','desc')->orderBy('log_note_id', 'desc')->orderBy('schedule_activity_id', 'desc')->get();
        $data['log_notes'] = ActivityLogNotes::with('log_note_users','activity_attachments')->where('sales_team_id',$id)->orderBy('id','desc')->get();
        $recipients = Contact::where('admin_id','<>', Auth::user()->id)->orWhere('admin_id', null)->where('status', 1)->get();
        $schedule_users = Admin::where('is_active',1)->where('is_archive','<>', 1)->get();
        $schedule_activity_types = ActivityTypes::where('status',1)->get();
        $schedule_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('sales_team_id', $id)->where('status', 0)->orderBy('due_date','asc')->get();
        $scheduled_done_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users','activity_attachments')->where('sales_team_id', $id)->where('status', 1)->orderBy('id','desc')->get();
        $data['diffInDays'] = Carbon::parse($start_date)->diffInDays(Carbon::parse($end_date));
        $data['diffInMonths'] = Carbon::parse($start_date)->floatdiffInMonths(Carbon::parse($end_date));
        $data['send_messages_view'] = $this->salesTeamRepository->sendMsgs($id, $log_uid, $module ='saleTeams', $partner, $recipients, $module_type = 7,$log_uid);
        $data['log_notes_view'] = $this->salesTeamRepository->logNotes($id, $log_uid, $module ='saleTeams', $partner);
        $data['schedual_activities_view'] = $this->salesTeamRepository->schedualActivities($id, $log_uid, $module ='saleTeams', $schedule_users, $schedule_activity_types, $log_uid, $module_type=7);
        $data['notes_tab_partial_view'] = $this->salesTeamRepository->notesTabPartialView($data['log_notes']);
        $data['send_message_tab_partial_view'] = $this->salesTeamRepository->sendMsgTabPartialView($data['send_messages']);
        $data['schedual_activity_tab_partial_view'] = $this->salesTeamRepository->schedualActivityTabPartialView($schedule_activities, $scheduled_done_activities, $module ='saleTeams');
        $data['attachments_partial_view'] = $this->salesTeamRepository->attachmentsPartialView($attachments);
        return view('admin.sales.sales-team.sales_team_details')->with($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!auth()->user()->can('Edit Sales Team'))
        access_denied();

        $data = [];
        $id = Hashids::decode($id)[0];
        $data['action'] = 'Edit';
        $data['model'] = SalesTeam::find($id);
        $data['team_leads'] = Admin::where('is_active', 1)->get();
        $data['members'] = SalesTeamsMembers::with('team_members')->where('sales_team_id', $id)->get();
         // Code For Activities Section
        $log_uid = Auth::user()->id;
        $partner = Auth::user()->firstname .' '. Auth::user()->lastname;
        $start_date = $data['model']->start_date;
        $end_date = $data['model']->end_date;
        $logged_in_follower_ids = Followers::where('contact_id', $log_uid)->where('sales_team_id', $id)->where('module_type', 7)->where('follower_type', 2)->pluck('follower_id')->toArray();
        if (Contact::whereIn('id', $logged_in_follower_ids)->where('admin_id', $log_uid)->exists()) {

           $data['is_following'] = 1;
        }
        else {

            $data['is_following'] = 0;
        }
        $data['followers'] = $this->salesTeamRepository->follower_list($id,$log_uid, $module_type=7);
        $data['send_messages'] = ActivityMessages::with('activity_message_users','activity_attachments')->where('sales_team_id',$id)->orderBy('id','desc')->get();
        $attachments = ActivityAttachments::where('sales_team_id', $id)->orderBy('send_msg_id','desc')->orderBy('log_note_id', 'desc')->orderBy('schedule_activity_id', 'desc')->get();
        $data['log_notes'] = ActivityLogNotes::with('log_note_users','activity_attachments')->where('sales_team_id',$id)->orderBy('id','desc')->get();
        $recipients = Contact::where('admin_id','<>', Auth::user()->id)->orWhere('admin_id', null)->where('status', 1)->get();
        $schedule_users = Admin::where('is_active',1)->where('is_archive','<>', 1)->get();
        $schedule_activity_types = ActivityTypes::where('status',1)->get();
        $schedule_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('sales_team_id', $id)->where('status', 0)->orderBy('due_date','asc')->get();
        $scheduled_done_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('sales_team_id', $id)->where('status', 1)->orderBy('id','desc')->get();
        $data['diffInDays'] = Carbon::parse($start_date)->diffInDays(Carbon::parse($end_date));
        $data['diffInMonths'] = Carbon::parse($start_date)->floatdiffInMonths(Carbon::parse($end_date));
        $data['send_messages_view'] = $this->salesTeamRepository->sendMsgs($id, $log_uid, $module ='saleTeams', $partner, $recipients, $module_type = 7,$log_uid);
        $data['log_notes_view'] = $this->salesTeamRepository->logNotes($id, $log_uid, $module ='saleTeams', $partner);
        $data['schedual_activities_view'] = $this->salesTeamRepository->schedualActivities($id, $log_uid, $module ='saleTeams', $schedule_users, $schedule_activity_types, $log_uid, $module_type=7);
        $data['notes_tab_partial_view'] = $this->salesTeamRepository->notesTabPartialView($data['log_notes']);
        $data['send_message_tab_partial_view'] = $this->salesTeamRepository->sendMsgTabPartialView($data['send_messages']);
        $data['schedual_activity_tab_partial_view'] = $this->salesTeamRepository->schedualActivityTabPartialView($schedule_activities, $scheduled_done_activities, $module ='saleTeams');
        $data['attachments_partial_view'] = $this->salesTeamRepository->attachmentsPartialView($attachments);
        return view('admin.sales.sales-team.sales_team_form')->with($data);
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
        if(!auth()->user()->can('Delete Sales Team'))
        access_denied();

        $id = Hashids::decode($id)[0];
        SalesTeamsMembers::where('sales_team_id', $id)->delete();
        SalesTeam::where('id', $id)->delete();
        Alert::success(__('Success'), __('Sale team has been deleted successfully.'))->persistent('Close')->autoclose(5000);
        return redirect('admin/sales-management/configuration/sales-team');
    }

    /**
     * Sales Team Analytics
     *
     */
    public function analytics()
    {
        // All Sales Teams
        if(!auth()->user()->can('View Sales Analytics'))
        access_denied();

        $data['sales_teams'] = SalesTeam::all();
        foreach ($data['sales_teams'] as $s_team) {
            $quotations_query = Quotation::join('quotation_other_info', 'quotation_other_info.quotation_id', 'quotations.id');
            $quotations_query->join('sales_teams', 'sales_teams.id', 'quotation_other_info.sales_team_id');
            $quotations_query->select('quotations.id',DB::raw("DATE_FORMAT(quotations.created_at, '%Y-%m-%d') new_date"));
            $quotations_query->whereDate('quotations.created_at', '>', Carbon::now()->subDays(30));
            $quotations_query->where('sales_teams.id', $s_team->id);
            $quotations_query->orderBy('new_date','asc');
            $quotations = $quotations_query->get();

            $chart_data = array();
            foreach($quotations as $quotation)
            {
                $q_total = Quotation::where('id', $quotation->id)->first()->total;
                if( !isset($chart_data[$quotation->new_date]) ){
                    $chart_data[$quotation->new_date] = floatval(str_replace(",","",$q_total));
                }else{
                    $chart_data[$quotation->new_date] += floatval(str_replace(",","",$q_total));
                }
            }
            $sales_data = array();
            $period = CarbonPeriod::create(Carbon::now()->subDays(30)->format('Y-m-d'), Carbon::now()->format('Y-m-d'));
            $dates = array();
            foreach ($period as $date) {
                array_push($dates, $date->format('Y-m-d'));
            }
            foreach($dates as $date )
            {
                $item = (object)array();
                if(isset($chart_data[$date]))
                {
                    $item->date = $date;
                   $item->sales = currency_format($chart_data[$date],'','',1);
                }else{
                    $item->date = $date;
                    $item->sales = 0;
                }
                array_push($sales_data, $item);
            }
            $s_team->graph_data = $sales_data;
        }

        return view('admin.sales.sales-team.analytics', $data);
    }
    /**
     * Sales Team Analysis
     *
     */
    public function analysis(Request $request)
    {
        if(!auth()->user()->can('View Sale Analysis'))
        access_denied();

        if ($request->ajax()) {
            $quotations_query = Quotation::join('quotation_other_info', 'quotation_other_info.quotation_id', 'quotations.id');
            $quotations_query->leftjoin('sales_teams', 'sales_teams.id', 'quotation_other_info.sales_team_id');
            $quotations_query->select('quotations.id',DB::raw("DATE_FORMAT(quotations.created_at, '%Y-%m-%d') new_date"));

            $period = null;
            $dates = array();
            if(isset($request->country_id) && $request->country_id != '' && $request->country_id != null){
                $quotations_query->whereHas('customer', function ($query) use($request){
                    $query->where('country_id',$request->country_id);
                });
            }
            if(isset($request->start_date) && $request->start_date != '' ){
                $quotations_query->whereBetween('quotations.created_at', [Carbon::parse($request->start_date), Carbon::parse($request->end_date)->addDay()]);
                $period = CarbonPeriod::create(Carbon::parse($request->start_date)->format('Y-m-d'), Carbon::parse($request->end_date)->format('Y-m-d'));
                foreach ($period as $date) {
                    array_push($dates, $date->format('Y-m-d'));
                }
            }else{
                $quotations_query->whereDate('quotations.created_at', '>', Carbon::now()->subDays(30));
                $period = CarbonPeriod::create(Carbon::now()->subDays(30)->format('Y-m-d'), Carbon::now()->format('Y-m-d'));
                foreach ($period as $date) {
                    array_push($dates, $date->format('Y-m-d'));
                }
            }
            if(isset($request->customer_id) && $request->customer_id != ''){
                $quotations_query->where('quotations.customer_id', $request->customer_id);
            }
            if(isset($request->sales_person_id) && $request->sales_person_id != ''){
                $quotations_query->where('quotation_other_info.salesperson_id', $request->sales_person_id);
            }
            if(isset($request->sales_team_id) && $request->sales_team_id != ''){
                $quotations_query->where('quotation_other_info.sales_team_id', $request->sales_team_id);
            }
            if(isset($request->currency) && $request->currency != ''){
                $quotations_query->where('currency', $request->currency);
            }
            if(isset($request->product_id) && $request->product_id != null && $request->product_id != ''){
                $quotations_query->whereHas('order_lines', function($query) use($request){
                    $query->where('product_id', $request->product_id);
                    if(isset($request->variation_id) && $request->variation_id != null && $request->variation_id != ''){
                        $query->where('variation_id', $request->variation_id);
                    }
                });
            }

            $quotations_query->orderBy('new_date','asc');
            $quotations_query->groupBy('quotations.id');
            $quotations = $quotations_query->get();
            foreach($quotations as $ind => $d){
                // dd($data);
                if(isset($request->invoice_status) && $request->invoice_status != ''){
                    switch ($request->invoice_status) {
                        case 0:
                            if(count($d->invoices) > 0)
                            {
                                $quotations->forget($ind);
                            }
                            break;
                        case 1:
                            # Partially Paid
                            if(number_format($d->total,2) == number_format($d->invoicedamount,2)){
                                $quotations->forget($ind);
                            }
                            elseif($d->invoicedamount != 0 && number_format($d->total,2) > number_format($d->invoicedamount,2)){

                            }else{
                                $quotations->forget($ind);
                            }
                            break;
                        case 2:
                            # Fully Paid
                            if(number_format($d->total,2) == number_format($d->invoicedamount,2)){
                            }
                            elseif($d->invoicedamount != 0 && number_format($d->total,2) > number_format($d->invoicedamount,2)){
                                $quotations->forget($ind);
                            }else{
                                $quotations->forget($ind);
                            }
                            break;
                        case 3:
                            # Un-Paid
                            if(count($d->invoices) == 0){

                                $quotations->forget($ind);
                            }
                            if(number_format($d->total,2) == number_format($d->invoicedamount,2)){
                                $quotations->forget($ind);
                            }
                            elseif($d->invoicedamount != 0 && number_format($d->total,2) > number_format($d->invoicedamount,2)){
                                $quotations ->forget($ind);
                            }
                            break;
                        default:
                            # code...
                            break;
                    }
                }
            }
            // Chart Data
            $chart_data = array();
            //Total Sales
            $data['total_sales'] = 0;
            //Total Tax
            $data['total_tax'] = 0;
            //Orders
            $data['no_of_orders'] = count($quotations);
            //Customers
            $data['customer_count'] = 0;
            $customer_arr = array();
            //# Lines
            $data['no_of_lines'] = 0;

            foreach($quotations as $quotation)
            {
                $q = Quotation::where('id', $quotation->id)->withCount('order_lines')->first();
                $q_total = floatval(str_replace(",","",$q->total)*$q->exchange_rate);
                $q_total_tax = 0;
                foreach($q->order_lines as $o){
                    $subtotal = $o->qty * $o->unit_price *$q->exchange_rate;
                    $taxes = QuotationOrderLineTax::with('tax')->where('quotation_order_line_id',$o->id)->get();
                    foreach($taxes as $o_tax)
                    {
                        if($o_tax->tax != null){
                            switch($o_tax->tax->computation)
                            {
                                case 0:
                                    $q_total_tax += $o_tax->tax->amount;
                                    break;
                                case 1:
                                    $q_total_tax += $subtotal * $o_tax->tax->amount / 100;
                                    break;
                            }
                        }
                    }
                    $q_total_tax += $subtotal * $q->vat_percentage / 100;
                }
                if( !isset($chart_data[$quotation->new_date]) ){
                    $chart_data[$quotation->new_date] = $q_total;
                }else{
                    $chart_data[$quotation->new_date] += $q_total;
                }
                $data['total_sales'] += $q_total;
                $data['total_tax'] += $q_total_tax;
                $data['no_of_lines'] += $q->order_lines_count;
                if(!in_array($q->customer_id, $customer_arr)){
                    $data['customer_count'] += 1;
                }
                array_push($customer_arr,$q->customer_id);

            }
            $data['sales_data'] = array();


            foreach($dates as $date )
            {
                $item = (object)array();
                if(isset($chart_data[$date]))
                {
                    $item->date = $date;
                   $item->sales = currency_format($chart_data[$date],'','',1);
                }else{
                    $item->date = $date;
                    $item->sales = 0;
                }
                array_push($data['sales_data'], $item);
            }
            $data['untaxed_total'] = currency_format($data['total_sales']-$data['total_tax'],'','',1);
            $data['total_sales'] = currency_format($data['total_sales'],'','',1);
            //Total Tax
            $data['total_tax'] = currency_format($data['total_tax'],'','',1);
            return $data;
        }

        $data['customers'] = Contact::where('status',1)->whereIn('type',[2,3,4])->get();
        $data['salespersons'] = Admin::where('is_active',1)->get();
        $data['salesteams'] = SalesTeam::all();

        $period = CarbonPeriod::create(Carbon::now()->subDays(30)->format('Y-m-d'), Carbon::now()->format('Y-m-d'));
        $dates = array();
        foreach ($period as $date) {
            array_push($dates, $date->format('Y-m-d'));
        }

        $quotations_query = Quotation::join('quotation_other_info', 'quotation_other_info.quotation_id', 'quotations.id');
        $quotations_query->leftjoin('sales_teams', 'sales_teams.id', 'quotation_other_info.sales_team_id');
        $quotations_query->select('quotations.customer_id','quotations.id',DB::raw("DATE_FORMAT(quotations.created_at, '%Y-%m-%d') new_date"));
        $quotations_query->whereDate('quotations.created_at', '>', Carbon::now()->subDays(30));
        $quotations_query->orderBy('new_date','asc');
        $quotations_query->groupBy('quotations.id');
        $quotations = $quotations_query->get();

        // Chart Data
        $chart_data = array();
        //Total Sales
        $data['total_sales'] = 0;
        //Total Tax
        $data['total_tax'] = 0;
        //Orders
        $data['no_of_orders'] = count($quotations);
        //Customers
        $data['customer_count'] = 0;
        $customer_arr = array();
        //# Lines
        $data['no_of_lines'] = 0;
        foreach($quotations as $quotation)
        {
            $q = Quotation::where('id', $quotation->id)->withCount('order_lines')->first();
            $q_total = floatval(str_replace(",","",$q->total));
            $q_total_tax = floatval(str_replace(",","",$q->totaltaxcurrency));
            if( !isset($chart_data[$quotation->new_date]) ){
                $chart_data[$quotation->new_date] = $q_total;
            }else{
                $chart_data[$quotation->new_date] += $q_total;
            }
            $data['total_sales'] += $q_total;
            $data['total_tax'] += floatval(str_replace(",","",$q_total_tax));
            $data['no_of_lines'] += $q->order_lines_count;
            if(!in_array($q->customer_id, $customer_arr)){
                $data['customer_count'] += 1;
            }
            array_push($customer_arr,$q->customer_id);

        }
        $data['sales_data'] = array();

        foreach($dates as $date )
        {
            $item = (object)array();
            if(isset($chart_data[$date]))
            {
                $item->date = $date;
               $item->sales = currency_format($chart_data[$date],'','',1);
            }else{
                $item->date = $date;
                $item->sales = 0;
            }
            array_push($data['sales_data'], $item);
        }
        $data['currencies'] = Quotation::groupBy('currency')->pluck('currency','currency_symbol')->toArray();
        if( empty($data['currencies']) || !isset($data['currencies']['€']) )
        {
            $data['currencies']['€'] = 'EUR';
        }
        $temp_currency = array();
        $temp_currency['€'] = 'EUR';
        foreach($data['currencies'] as $ind => $d_c)
        {
            if($d_c != 'EUR')
            {
                $temp_currency[$ind] = $d_c;
            }
        }
        $data['currencies'] = $temp_currency;
        $data['countries'] = ContactCountry::all();
        $productList = Products::with( 'generalInformation', 'customer_taxes', 'variations', 'variations.variation_details' )->get();
        $data['products'] = [];
        foreach ($productList as $prod) {
            // If the product is simple product
            if( count($prod->variations) == 0 ){
                $store['product_id'] = $prod->id;
                $store['variation_id'] = '';
                $store['name'] = $prod->product_name;
                $store['price'] = isset($prod->generalInformation->sales_price) ? $prod->generalInformation->sales_price : 0;
                $taxes = isset($prod->customer_taxes[0]) ? $prod->customer_taxes : [];
                $store['taxes'] = [];
                foreach( $taxes as $t )
                {
                    $store['taxes'][] = $t->tax_id;
                }

                $store['taxes'] = json_encode( $store['taxes'] );

                $data['products'][] = $store;
            }
            // If the product is variable product
            else
            {
                foreach( $prod->variations as $prod_variation )
                {
                    $variation_price = isset($prod->generalInformation->sales_price) ? $prod->generalInformation->sales_price : 0;

                    $store['product_id'] = $prod->id;
                    $store['variation_id'] = $prod_variation->id;
                    $store['name'] = $prod->product_name.' '.$prod_variation->variation_name;
                    $store['price'] = $variation_price;
                    $taxes = isset($prod->customer_taxes[0]) ? $prod->customer_taxes : [];
                    $store['taxes'] = [];
                    foreach( $taxes as $t )
                    {
                        $store['taxes'][] = $t->id;
                    }

                    $store['taxes'] = json_encode( $store['taxes'] );

                    $data['products'][] = $store;
                }
            }
        }
        return view('admin.sales.sales-team.analysis',$data);
    }
    public function sales_analysis_quotations(Request $request)
    {
        if ($request->ajax()) {
            $data_query = Quotation::with(
                'customer',
                'order_lines',
                'order_lines.product',
                'order_lines.variation',
                'order_lines.quotation_taxes',
                'optional_products',
                'optional_products.product',
                'optional_products.variation',
                'other_info',
                'other_info.sales_person',
                'other_info.sales_team'
            )->orderBy('id','desc');
            if(isset($request->start_date) && $request->start_date != '' && $request->start_date != null ){
                $data_query->whereBetween('created_at', [Carbon::parse($request->start_date), Carbon::parse($request->end_date)->addDay()]);
            }else{
                $data_query->whereDate('created_at', '>', Carbon::now()->subDays(30));
            }

            if(isset($request->customer_id) && $request->customer_id != '' && $request->customer_id != null){
                $data_query->where('customer_id', $request->customer_id);
            }
            if(isset($request->country_id) && $request->country_id != '' && $request->country_id != null){
                $data_query->whereHas('customer', function ($query) use($request){
                    $query->where('country_id',$request->country_id);
                });
            }
            if(isset($request->currency) && $request->currency != '' && $request->currency != null){
                $data_query->where('currency', $request->currency);
            }
            if(isset($request->sales_person_id) && $request->sales_person_id != '' && $request->sales_person_id != null){
                $data_query->whereHas('other_info', function($query) use($request){
                    $query->where('salesperson_id', $request->sales_person_id);
                });
            }
            if(isset($request->sales_team_id) && $request->sales_team_id != '' && $request->sales_team_id != null){
                $data_query->whereHas('other_info', function($query) use($request){
                    $query->where('sales_team_id', $request->sales_team_id);
                });
            }
            if(isset($request->product_id) && $request->product_id != null && $request->product_id != ''){
                $data_query->whereHas('order_lines', function($query) use($request){
                    $query->where('product_id', $request->product_id);
                    if(isset($request->variation_id) && $request->variation_id != null && $request->variation_id != ''){
                        $query->where('variation_id', $request->variation_id);
                    }
                });
            }
            $data = $data_query->get();
            foreach($data as $ind => $d){
                // dd($data);
                if(isset($request->invoice_status) && $request->invoice_status != ''){
                    switch ($request->invoice_status) {
                        case 0:
                            if(count($d->invoices) > 0)
                            {
                                $data->forget($ind);
                            }
                            break;
                        case 1:
                            # Partially Paid
                            if(number_format($d->total,2) == number_format($d->invoicedamount,2)){
                                $data->forget($ind);
                            }
                            elseif($d->invoicedamount != 0 && number_format($d->total,2) > number_format($d->invoicedamount,2)){

                            }else{
                                $data->forget($ind);
                            }
                            break;
                        case 2:
                            # Fully Paid
                            if(number_format($d->total,2) == number_format($d->invoicedamount,2)){
                            }
                            elseif($d->invoicedamount != 0 && number_format($d->total,2) > number_format($d->invoicedamount,2)){
                                $data->forget($ind);
                            }else{
                                $data->forget($ind);
                            }
                            break;
                        case 3:
                            # Un-Paid
                            if(count($d->invoices) == 0){

                                $data->forget($ind);
                            }
                            if(number_format($d->total,2) == number_format($d->invoicedamount,2)){
                                $data->forget($ind);
                            }
                            elseif($d->invoicedamount != 0 && number_format($d->total,2) > number_format($d->invoicedamount,2)){
                                $data->forget($ind);
                            }
                            break;
                        default:
                            # code...
                            break;
                    }
                }
                if(isset($request->amount)&& $request->amount != ''){
                    if(isset($request->currency) && $request->currency != '')
                    {
                        if( number_format($d->total * $d->exchange_rate,2) != $request->amount )
                        {
                            $data->forget($ind);
                        }
                    }
                    else
                    {

                        if( $d->total != $request->amount )
                        {
                            $data->forget($ind);
                        }
                    }
                }
            }
            $datatable = Datatables::of($data);
            $datatable->editColumn('ordernumber', function ($row) {


                // return 'S'.str_pad($row->id, 5, '0', STR_PAD_LEFT);
                return '<a target="_blank" href="' .route('admin.quotations.show',Hashids::encode($row->id)). '">S'.str_pad($row->id, 5, '0', STR_PAD_LEFT).'</a>';
            });
            $datatable->editColumn('link', function ($row) {
                return route('admin.quotations.show',Hashids::encode($row->id));
            });
            $datatable->addColumn('customer', function ($row) {
                return $row->customer->name;
            });
            $datatable->addColumn('salesperson', function ($row) {
                return @$row->other_info->sales_person->firstname.' '.@$row->other_info->sales_person->lastname;
            });
            $datatable->addColumn('total', function ($row) {
                return currency_format($row->total*$row->exchange_rate,$row->currency_symbol,$row->currency);
            });
            $datatable->addColumn('status', function ($row) {
                switch($row->status){
                    case 0:
                        return '<span class="tagged quote">'.__('Quotation').'</span>';
                        break;
                    case 1:
                        return '<span class="tagged success">'.__('Sales Order').'</span>';
                        break;
                    case 2:
                        return '<span class="tagged warning">'.__('Locked').'</span>';
                        break;
                    case 3:
                        return '<span class="tagged quote">'.__('Quotation Sent').'</span>';
                        break;
                    case 4:
                        return '<span class="tagged danger">'.__('Cancelled').'</span>';
                        break;
                    default;
                }
            });
            $datatable->addColumn('invoicestatus', function ($row) {
                if(count($row->invoices) == 0){
                    return '<span class="tagged warning">'.__('Not Created').'</span>';
                }
                if(count($row->invoices) > 0){
                    if($row->is_refunded){
                        return '<span class="tagged danger">'.__('Refunded').'</span>';
                    
                    }elseif(currency_format($row->total * $row->exchange_rate,'','',1) == currency_format($row->invoicedamount,'','',1)){
                        return '<span class="tagged success">'.__('Fully Invoiced').'</span>';
                    }
                    elseif($row->invoicedamount != 0 && currency_format($row->total * $row->exchange_rate,'','',1) > currency_format($row->invoicedamount,'','',1)){
                        return '<span class="tagged quote">'.__('Partially Invoiced').'</span>';
                    }else{
                        return '<span class="tagged danger">'.__('Not Paid').'</span>';
                    }
                }
            });
            $datatable = $datatable->rawColumns(['ordernumber','status','invoicestatus','total']);
            return $datatable->make(true);
        }
    }
    // Method For Bulk Delete
    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        $idsArr = explode(",", $ids);
        SalesTeamsMembers::whereIn('sales_team_id', $idsArr)->delete();
        SalesTeam::whereIn('id', $idsArr)->delete();
        $response = response()->json(['success' => __('Sales teams has been deleted successfully.')]);
        return $response;
    }
     public function duplicateSaleTeam ($id)
    {
        $data = [];
        $id = Hashids::decode($id)[0];
        $data['action'] = 'Duplicate';
        $existTeam = SalesTeam::find($id);
        $newTeam = $existTeam->replicate();
        $newTeam->save();
        $data['team_leads'] = Admin::where('is_active', 1)->get();
        $data['members'] = SalesTeamsMembers::with('team_members')->where('sales_team_id', $newTeam->id)->get();
        $data['model'] = SalesTeam::find($newTeam->id);
        return redirect()->route('admin.sales-team.edit', Hashids::encode($newTeam->id));
    }

    public function isArchiveSaleTeam(Request $request)
    {
        $input = $request->all();
        $id = Hashids::decode($input['id']);
            $archive = $input['is_archive'];
            if ($archive == 1)
                $archiveMSg = __('Archived');
            else
                $archiveMSg = __('Unarchived');
            SalesTeam::where('id', $id)->update(['is_archive' => $archive]);
            $response = response()->json(['success' => __('Sale team').' '. $archiveMSg .' '.__('successfully.')]);
        return $response;
    }

    public function userList(Request $request)
    {
        $data = [];
        if ($request->ajax()) {
            $archive = $request->get('is_archive');
            $existingIds = $request->get('existingIds');
            $memberIds = array();
            if(isset($existingIds)) {
                foreach($existingIds as $row) {
                      $memberIds[] = Hashids::decode($row)[0];
                }
            }
            $data = Admin::with('languages')->latest()
                ->where(function ($data) use ($archive) {
                    if (isset($archive) && $archive != "") {
                        $data->where('is_archive', $archive);
                    }
                })
            ->whereNotIn('id', $memberIds)
            ->get();
            $datatable = Datatables::of($data);
            $datatable->setRowId(function ($row) {
                return 'tr_' . $row->id;
            });
            $datatable->addColumn('delete_check', function (Admin $row) {
                $indv_check = '';
                $indv_check = '<input type="checkbox" name="deleteCheck[]" class="sub_chk checkbox-input countChecks" onclick="checkBoxActions(this)" data-id="' . Hashids::encode($row->id) . '">';
                return $indv_check;
            });
            $datatable->addColumn('name', function ($row) {
                return $row->firstname . ' ' . $row->lastname;
            });
            $datatable->addColumn('latest_authentication', function ($row) {
                return isset($row->email_verified_at) ? date('m/d/Y  h:i:s A', strtotime($row->email_verified_at)) : '';
            });
            $datatable->addColumn('login', function ($row) {
                return $row->email;
            });
            $datatable->addColumn('language', function ($row) {
                return $row->languages->name;
            });
            $datatable = $datatable->rawColumns(['delete_check', 'name', 'login', 'language', 'latest_authentication']);
            return $datatable->make(true);
        }

    }
    // Method For Bulk Member Selection
    public function memberSelection(Request $request)
    {
        $ids = $request->ids;
        $idsArr = explode(",", $ids);
        $deocdedIds = array();
        foreach($idsArr as $id) {
           $deocdedIds[] = Hashids::decode($id)[0];
        }
        $members = Admin::whereIn('id', $deocdedIds)->where('is_active', 1)->get();
        $idsArr = [];
        $memberListArray = [];
        foreach($members as $member) {
        $_html = '<div class="col-sm-6 col-md-3 member-parent" data-member-id="'.Hashids::encode($member->id).'"><a href="javascript:void(0)" onclick = "updateMember(this)" data-id ="'.Hashids::encode($member->id).'"><div class="customer-box"><div class="customer-img"><img src="' . checkImage(asset("storage/uploads/admin/" . Hashids::encode($member->id) . '/' . $member->image),'avatar5.png') . '" alt="User Image" width="100%" height="100%"></div><div class="customer-content col-md-6"><h3 class="customer-heading">'.$member->firstname .' '. $member->lastname .'</h3></div></div></a></div>';
           array_push($memberListArray, $_html);
           array_push($idsArr, Hashids::encode($member->id));
        }
        $member_list = implode(" ", $memberListArray);
        $memberIds  = implode("," , $idsArr);
        return response()->json([
                'status'=> 1,
                'member_list'=> $member_list,
                'ids' => $memberIds
            ]);
    }

    public function generateSalesReportExcel(Request $request)
    {
        $data = $this->salesData($request);

        $sheet_array[] = array();
        $sheet_array[] = [
            'Sales Orders Report',
        ];
        $sheet_array[] = ['Metrics', 'Value'];
        $sheet_array[] = [
            'Metrics' => 'Quotations' ,
            'Value' => $data['quotation_count']
        ];
        $sheet_array[] = [
            'Metrics' => 'Sales Orders' ,
            'Value' => $data['salesorders_count']
        ];
        $sheet_array[] = [
            'Metrics' => 'Orders to Invoice' ,
            'Value' => $data['order_to_invoice']
        ];
        $sheet_array[] = [
            'Metrics' => 'Resellers' ,
            'Value' => $data['reseller_count']
        ];
        $sheet_array[] = [
            'Metrics' => 'Guests' ,
            'Value' => $data['guest_count']
        ];
        $sheet_array[] = [
            'Metrics' => 'Customers' ,
            'Value' => $data['customer_count']
        ];
        $sheet_array[] = [
            'Metrics' => 'Taxes' ,
            'Value' => $data['total_taxes'].' '.$request->currency
        ];
        $sheet_array[] = [
            'Metrics' => 'Total Amount' ,
            'Value' => $data['all_quotation_total'].' '.$request->currency
        ];
        $sheet_array[] = [
            'Metrics' => 'Untaxed Total Amount' ,
            'Value' => $data['all_quotation_untaxed_total'].' '.$request->currency
        ];
        $sheet_array[] = [
            'Metrics' => 'Total Amount Invoiced' ,
            'Value' => $data['total_invoiced_amount'].' '.$request->currency
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        for ($i = 0; $i < count($sheet_array); $i++) {
            //set value for indi cell
            $row = $sheet_array[$i];
            //writing cell index start at 1 not 0
            $j = 1;
            foreach ($row as $x => $x_value) {
                $sheet->setCellValueByColumnAndRow($j, $i + 1, $x_value);
                $j = $j + 1;
            }
        }
        $old_file = public_path().'/storage/sales/Sales Resport.xlsx';
        if (file_exists($old_file)) {
            unlink($old_file);
        }
        ob_clean();
        $writer = new Xlsx($spreadsheet);
        // header('Content-Type: application/vnd.ms-excel');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Sales Report.xlsx"');
        // $writer->save(public_path().'/storage/sales/Sales Resport.xlsx');
        return $writer->save('php://output');
        return public_path('storage/sales/Sales Resport.xlsx');
        // Excel::store(new VouchersExport($voucher_order_id), 'vouchers/vouchers-'.Hashids::encode($voucher_order_id).'.xlsx', 'public');
    }
}

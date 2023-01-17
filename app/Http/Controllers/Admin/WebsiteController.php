<?php

namespace App\Http\Controllers\Admin;

use DB;
use PDF;
use File;
use Hashids;
use DataTables;
use ZipArchive;
use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Contact;
use App\Models\Project;
use App\Models\Voucher;
use Carbon\CarbonPeriod;
use App\Models\Quotation;
use App\Models\Invoice;
use App\Models\LogVisitor;
use App\Models\LogActivity;
use App\Models\VoucherOrder;
use Illuminate\Http\Request;
use App\Models\PaymentGateway;
use App\Models\VoucherPayment;
use App\Http\Controllers\Controller;
use App\Models\ResellerRedeemedPage;
use App\Models\QuotationOrderLineTax;
use Alert;
use Image;
use App\Models\ResellerRedeemedPageNavigation;
use App\Models\User;

class WebsiteController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }
    /**
     * Dashboard
     */
    public  function dashboard(Request $request)
    {
        if (!auth()->user()->can('Website Dashboard'))
            access_denied();

        if ($request->ajax()) {
            $quotations_query = Quotation::join('quotation_other_info', 'quotation_other_info.quotation_id', 'quotations.id');
            $quotations_query->leftjoin('sales_teams', 'sales_teams.id', 'quotation_other_info.sales_team_id');
            $quotations_query->select('quotations.id', DB::raw("DATE_FORMAT(quotations.created_at, '%Y-%m-%d') new_date"));
            $quotations_query->where('quotation_other_info.sales_team_id', 1)->orderBy('id', 'desc');

            $period = null;
            $dates = array();

            if (isset($request->start_date) && $request->start_date != '') {
                $quotations_query->whereBetween('quotations.created_at', [Carbon::parse($request->start_date), Carbon::parse($request->end_date)->addDay()]);
                $period = CarbonPeriod::create(Carbon::parse($request->start_date)->format('Y-m-d'), Carbon::parse($request->end_date)->format('Y-m-d'));
                foreach ($period as $date) {
                    array_push($dates, $date->format('Y-m-d'));
                }
            } else {
                $quotations_query->whereDate('quotations.created_at', '>', Carbon::now()->subDays(30));
                $period = CarbonPeriod::create(Carbon::now()->subDays(30)->format('Y-m-d'), Carbon::now()->format('Y-m-d'));
                foreach ($period as $date) {
                    array_push($dates, $date->format('Y-m-d'));
                }
            }
            if (isset($request->currency) && $request->currency != '') {
                $quotations_query->where('quotations.currency', $request->currency);
            }
            if (isset($request->customer_id) && $request->customer_id != '') {
                $quotations_query->where('quotations.customer_id', $request->customer_id);
            }
            if (isset($request->sales_person_id) && $request->sales_person_id != '') {
                $quotations_query->where('quotation_other_info.salesperson_id', $request->sales_person_id);
            }
            if (isset($request->sales_team_id) && $request->sales_team_id != '') {
                $quotations_query->where('quotation_other_info.sales_team_id', $request->sales_team_id);
            }
            if (isset($request->country_id) && $request->country_id != '' && $request->country_id != null) {
                $quotations_query->whereHas('customer', function ($query) use ($request) {
                    $query->where('country_id', $request->country_id);
                });
            }
            if (isset($request->product_id) && $request->product_id != null && $request->product_id != '') {
                $quotations_query->whereHas('order_lines', function ($query) use ($request) {
                    $query->where('product_id', $request->product_id);
                    if (isset($request->variation_id) && $request->variation_id != null && $request->variation_id != '') {
                        $query->where('variation_id', $request->variation_id);
                    }
                });
            }

            $quotations_query->orderBy('new_date', 'asc');
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

            foreach ($quotations as $quotation) {
                $q = Quotation::where('id', $quotation->id)->withCount('order_lines')->first();

                $q_total = floatval(str_replace(",", "", $q->total) * $q->exchange_rate);
                $q_total_tax = 0;
                foreach ($q->order_lines as $o) {
                    $subtotal = $o->qty * $o->unit_price * $q->exchange_rate;
                    $taxes = QuotationOrderLineTax::with('tax')->where('quotation_order_line_id', $o->id)->get();
                    foreach ($taxes as $o_tax) {
                        if ($o_tax->tax != null) {
                            switch ($o_tax->tax->computation) {
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
                $q_total += $q_total_tax;
                if (!isset($chart_data[$quotation->new_date])) {
                    $chart_data[$quotation->new_date] = $q_total;
                } else {
                    $chart_data[$quotation->new_date] += $q_total;
                }
                $data['total_sales'] += floatval(str_replace(",", "", $q_total));
                $data['total_tax'] += floatval(str_replace(",", "", $q_total_tax));

                $data['no_of_lines'] += $q->order_lines_count;
                if (!in_array($q->customer_id, $customer_arr)) {
                    $data['customer_count'] += 1;
                }
                array_push($customer_arr, $q->customer_id);
            }
            $data['sales_data'] = array();

            $data['untaxed_total'] = currency_format($data['total_sales'] - $data['total_tax'], '', '', 1);
            $data['total_sales'] = currency_format($data['total_sales'], '', '', 1);
            $data['total_tax'] = currency_format($data['total_tax'], '', '', 1);
            foreach ($dates as $date) {
                $item = (object)array();
                if (isset($chart_data[$date])) {
                    $item->date = $date;
                    $item->sales = currency_format($chart_data[$date], '', '', 1);
                } else {
                    $item->date = $date;
                    $item->sales = 0;
                }
                array_push($data['sales_data'], $item);
            }
            return $data;
        }
        $data['customers'] = Contact::where('status', 1)->whereIn('type', [2, 3])->get();

        $period = CarbonPeriod::create(Carbon::now()->subDays(30)->format('Y-m-d'), Carbon::now()->format('Y-m-d'));
        $dates = array();
        foreach ($period as $date) {
            array_push($dates, $date->format('Y-m-d'));
        }

        // Chart Data
        $chart_data = array();
        //Total Sales
        $data['total_sales'] = 0;
        //Total Tax
        $data['total_tax'] = 0;
        //Orders
        $data['no_of_orders'] = 0;
        //Customers
        $data['customer_count'] = 0;
        $customer_arr = array();
        //# Lines
        $data['no_of_lines'] = 0;
        $data['sales_data'] = array();

        $data['currencies'] = Quotation::groupBy('currency')->pluck('currency', 'currency_symbol')->toArray();
        if (empty($data['currencies']) || !isset($data['currencies']['€'])) {
            $data['currencies']['€'] = 'EUR';
        }
        $temp_currency = array();
        $temp_currency['€'] = 'EUR';
        foreach ($data['currencies'] as $ind => $d_c) {
            if ($d_c != 'EUR') {
                $temp_currency[$ind] = $d_c;
            }
        }
        $data['currencies'] = $temp_currency;
        return view('admin.website.dashboard', $data);
    }
    /**
     * Add new item to cart
     *
     */
    public function getAbandonedCart(Request $request)
    {
        if (!auth()->user()->can('Website Abandoned Cart Listing'))
            access_denied();

        if ($request->ajax()) {
            $data = Cart::whereHas('cart_items')->whereHas('user')->with('user')->where('is_checkout', 0)->orderBy('id', 'desc');

            if (isset($request->start_date) && $request->start_date != '') {
                $data->whereBetween('updated_at', [Carbon::parse($request->start_date), Carbon::parse($request->end_date)->addDay()]);
            }
            if (isset($request->customer_name_email) && $request->customer_name_email != '') {
                // $data->where('customer_id', $request->customer_id);
                $data->whereHas('user', function ($query) use ($request) {
                    $query->where('name', 'LIKE', '%' . $request->customer_name_email . '%');
                    $query->orWhere('email', 'LIKE', '%' . $request->customer_name_email . '%');
                });
            }
            $data = $data->get();
            $datatable = Datatables::of($data)->addIndexColumn();

            $datatable->editColumn('user', function ($row) {
                return ucfirst($row->user->name) . '<br>' . $row->user->email;
            });
            $datatable->addColumn('abandoned_at', function ($row) {
                return $row->abandoned_at;
            });
            $datatable->addColumn('cart_items', function ($row) {
                $html = '';
                if ($row->cart_items != null) {
                    foreach ($row->cart_items as $index => $cart_item) {
                        $html .= @$cart_item->product->product_name;
                        if ($cart_item->variation_id != null) {
                            $html .= ' ' . @$cart_item->variation->variation_name;
                        }
                        $html .= ' ( Qty: ' . $cart_item->qty . ' )';
                        $html .= $index < count($row->cart_items) - 1 ? '<br>'  : '';
                    }
                }
                return $html;
            });
            $datatable = $datatable->rawColumns(['cart_items', 'user']);
            return $datatable->make(true);
        }
        return view('admin.website.abandoned-carts');
    }
    /**
     * Visitors and Visits Functions Start
     *
     */
    public function getVisitors(Request $request)
    {
        if (!auth()->user()->can('Visitors Listing'))
            access_denied();
        if ($request->ajax()) {
            $search = $request->all()['search']['value'];
            $data = null;
            if ($search != null) {
                if (str_contains('Website User', $search) || str_contains('website user', $search)) {
                    $data = LogVisitor::where(function ($q) use ($search) {
                        $q->whereNull('user_id');
                        $q->orWhere('user_id', 0);
                    });
                } else {
                    $data = LogVisitor::where(function ($q) use ($search) {
                        $q->whereHas('user', function ($query) use ($search) {
                            $query->where('name', 'LIKE', '%' . $search . '%');
                        })->orderBy('id', 'desc');
                    });
                }
            } else {
                $data = LogVisitor::orderBy('id', 'desc');
            }
            $datatable = Datatables::of($data)->addIndexColumn();
            $datatable->addColumn('user_name', function ($row) {

                return auth()->user()->can('Visitors Detail') ? '<a href="' . route('admin.website.visitor.detail', Hashids::encode($row->id)) . '"> ' . ucfirst(@$row->user_name) . ' </a>' : '';
            });
            $datatable->addColumn('last_visit_date', function ($row) {

                return \Carbon\Carbon::parse($row->last_visit->created_at)->diffForHumans();
            });
            $datatable->addColumn('last_visit_page', function ($row) {
                $url = $row->last_visit->url;
                if ($url == '/') {
                    return 'Home';
                } else {
                    $url = explode('/', $url);
                    return ucfirst(end($url));
                }
            });
            $datatable->addColumn('total_pages_visited', function ($row) {
                return count($row->activities);
            });
            $datatable = $datatable->rawColumns(['user_name']);
            return $datatable->make(true);
        }
        return view('admin.website.visitors.index');
    }
    public function getVisitorDetails($id)
    {
        if (!auth()->user()->can('Visitors Detail'))
            access_denied();
        try {
            //code...
            $id = Hashids::decode($id)[0];
        } catch (\Throwable $th) {
            return redirect()->route('admin.dashboard');
        }
        $data['visitor'] = LogVisitor::where('id', $id)->first();
        // foreach($data['visitor']->activities as $d){
        //     echo($d->url.'<br>');
        // }
        // dd($data['visitor']->activities()->groupBy('url')->get());
        return view('admin.website.visitors.detail', $data);
    }
    public function getViewVisits(Request $request)
    {
        if (!auth()->user()->can('Views Listing'))
            access_denied();
        if ($request->ajax()) {
            $search = isset($request->all()['search']['value']) ? $request->all()['search']['value'] : null;
            $data = null;

            if ($search != null) {
                $data = LogActivity::where('url', 'LIKE', '%'.$search.'%');
                if (str_contains('Website User', $search) || str_contains('website user', $search)) {
                    $data->whereHas('visitor', function ($q) {
                        $q->whereNull('user_id');
                        $q->orWhere('user_id', 0);
                    });
                } else {
                    $data->whereHas('visitor.user', function ($query) use ($search) {
                        $query->where('name', 'LIKE', '%'.$search.'%');
                    });
                }
                $data->orderBy('id', 'desc');
            } else {
                $data = LogActivity::orderBy('id', 'desc');
            }


            $datatable = Datatables::of($data)->addIndexColumn();
            $datatable->addColumn('user_name', function ($row) {
                return ucfirst(@$row->visitor->user_name);
            });
            $datatable->addColumn('visit_date_time', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('d M Y h:i:s');
            });
            $datatable->addColumn('page', function ($row) {
                $array = explode('/', $row->url);
                return end($array) == "" ? 'Home' : end($array);
            });
            $datatable->editColumn('url', function ($row) {
                return url($row->url);
            });
            $datatable->addColumn('product', function ($row) {
                $array = explode('/', $row->url);
                if (strpos($row->url, 'product-details') !== false) {
                    return end($array);
                } else {
                    return '';
                }
            });
            // $draw_val = $request->draw;
            // $return_data = array(
            // "draw"            => intval($draw_val),
            // "recordsTotal"    => intval($totalDataRecord),
            // "recordsFiltered" => intval($totalFilteredRecord),
            // "data"            => $data
            // );
            // return $return_data;
            // dd($datatable->make(true));
            return $datatable->make(true);
        }
        return view('admin.website.view.index');
    }
    /*** Visitors and Visits Functions End **/
    /**
     * Reseller Listing
     *
     */
    public function resellerListing(Request $request)
    {

        if (!auth()->user()->can('Reseller Listing'))
            access_denied();
        if ($request->ajax()) {
            $req = $request->all();
            $data = Contact::with('contact_countries')->orderBy('id', 'desc');
            $data = $data->where('type', 3);        // Type is Reseller
            if (isset($request->s) &&  !empty($request->s)) {
                $data = $data->where(function ($query) use ($request) {
                    $query->where('name', 'LIKE', '%' . $request->s . '%')
                        ->orWhere('email', 'LIKE', '%' . $request->s . '%')
                        ->orWhere('mobile', 'LIKE', '%' . $request->s . '%')
                        ->orWhere('phone', 'LIKE', '%' . $request->s . '%')
                        ->orWhere('zipcode', 'LIKE', '%' . $request->s . '%');
                });
            }
            if (isset($request->filter) && $request->filter != 3) {

                $data = $contacts->where('company_type', $request->filter);
            }

            if (isset($request->active_status) && $request->active_status != '') {
                $data = $data->where(function ($query) use ($request) {
                    $query->whereHas('user', function ($q) use ($request) {
                        $q->where('is_active', $request->active_status);
                    });
                });
            }


            $data = $data->get();
            $datatable = Datatables::of($data)->addIndexColumn();

            $datatable->addColumn('actions', function ($row) {

                $actions = '<div style="display:inline-flex">';
                $actions .= '&nbsp;<a class="btn btn-primary btn-icon" target="_blank" href="' . route("admin.voucher.orders", ['reseller_email' => $row->email]) . '" title="' . __('View Voucher Orders') . '"><i class="fa fa-gift"></i></a>';
                $actions .= '&nbsp;<a class="btn btn-warning btn-icon" href="' . route('admin.contacts.edit', Hashids::encode($row->id)) . '" title=' . __('View') . '><i class="fa fa-eye"></i></a>';
                $actions .= '&nbsp;<a class="btn btn-info btn-icon" href="' . route('admin.reseller.redeemed', Hashids::encode($row->user_id)) .  '" title=' . __('Visit-Redeemed-Page') . '>Visit Redeemed Page</a>';

                $actions .= '</div>';
                return $actions;
            });
            $datatable->addColumn('status', function ($row) {
                return (@$row->user->is_active) ? '<span class="badge badge-success">' . __('Active') . '</span>' : '<span class="badge badge-danger">' . __('Inactive') . '</span>';
            });
            $datatable->addColumn('package_name', function ($row) {
                $model = '';
                $style = '';
                if ($row->reseller_package) {
                    $model = $row->reseller_package->model == 0 ? 'increase' : 'discount';
                    $style = $row->reseller_package->model == 0 ? 'style="color:green"' : 'style="color:red"';
                }
                return ($row->reseller_package) ? $row->reseller_package->package_name . ' <strong ' . $style . '>(' . $row->reseller_package->percentage . '% ' . $model . ')</strong>' : 'Default Price Package <strong>(No Change)</strong>';
            });
            $datatable = $datatable->rawColumns(['status', 'actions', 'package_name']);
            return $datatable->make(true);
        }
        return view('admin.website.reseller.index');
    }
    /**
     * Lawful Interception
     */
    public function lawfulInterception(Request $request)
    {
        if (!auth()->user()->can('Lawful Interception Listing'))
            access_denied();

        if ($request->ajax()) {
            $req = $request->all();
            $type = $request->get('contact_type');
            $data = Contact::with('contact_countries')->orderBy('id', 'desc');
            $data = $data->whereIn('type', [2, 3]);        // Type is Customer / Reseller
            if (isset($request->s) &&  !empty($request->s)) {
                $data = $data->where(function ($query) use ($request) {
                    $query->where('name', 'LIKE', '%' . $request->s . '%')
                        ->orWhere('email', 'LIKE', '%' . $request->s . '%');
                });
            }
            if (isset($type) &&  !empty($type)) {
                $data = $data->where(function ($query) use ($type) {
                    $query->where('type', $type);
                });
            }
            $data = $data->get();
            $datatable = Datatables::of($data)->addIndexColumn();
            $datatable->editColumn('status', function ($row) {
                return ($row->status) ? '<span class="badge badge-success">' . __('Active') . '</span>' : '<span class="badge badge-danger">' . __('Inactive') . '</span>';
            });
            $datatable->editColumn('type', function ($row) {
                return ($row->type == 3) ? '<span>' . __('Reseller') . '</span>' : '<span>' . __('Customer') . '</span>';
            });
            $datatable->addColumn('actions', function ($row) {
                $actions = '<div style="display:inline-flex">';
                if ($row->type == 3) {
                    $actions .= auth()->user()->can('Reseller Details PDF') ? '&nbsp;<a target="_blank" class="btn btn-primary btn-icon" target="_blank" href="' . route('admin.website.lawfulinterception.resellerpdf', Hashids::encode($row->id)) . '" title="' . __('Reseller Detail PDF') . '"><i class="fa fa-user"></i></a>' : '';
                    $actions .= auth()->user()->can('Reseller Orders PDF') ? '&nbsp;<a target="_blank" class="btn btn-danger btn-icon" target="_blank" href="' . route('admin.website.lawfulinterception.orderpdf', Hashids::encode($row->id)) . '" title="' . __('Reseller Orders PDF') . '"><i class="fa fa-tags"></i></a>' : '';
                } else {
                    $actions .= auth()->user()->can('Customer Details PDF') ? '&nbsp;<a target="_blank" class="btn btn-primary btn-icon" target="_blank" href="' . route('admin.website.lawfulinterception.customerpdf', Hashids::encode($row->id)) . '" title="' . __('Customer Detail PDF') . '"><i class="fa fa-user"></i></a>' : '';
                    $actions .= auth()->user()->can('Customer Orders PDF') ? '&nbsp;<a target="_blank" class="btn btn-danger btn-icon" target="_blank" href="' . route('admin.website.lawfulinterception.customerorderpdf', Hashids::encode($row->id)) . '" title="' . __('Customer Orders PDF') . '"><i class="fa fa-tags"></i></a>' : '';
                    $actions .= auth()->user()->can('Customer Carts PDF') ? '&nbsp;<a target="_blank" class="btn btn btn-default btn-icon" target="_blank" href="' . route('admin.website.lawfulinterception.customercarts', Hashids::encode($row->user_id)) . '" title="' . __('Customer Carts PDF') . '"><i class="fa fa-cart-plus"></i></a>' : '';
                    $actions .= auth()->user()->can('Customer Order Invoices PDF') ? '&nbsp;<a target="_blank" class="btn btn-primary btn-icon" target="_blank" href="' . route('admin.website.lawfulinterception.customerinvoices', Hashids::encode($row->id)) . '" title="' . __('Customer Order Invoices PDF') . '"><i class="fa fa-credit-card"></i></a>' : '';
                    $actions .= auth()->user()->can('Download Customer All Data') ? '&nbsp;<a class="btn btn-warning btn-icon export-all-customer" data-id="' . Hashids::encode($row->id) . '" title="' . __('Download Data') . '"><i class="fa fa-download"></i></a>' : '';
                }
                if ($row->type == 3) {
                    $actions .= auth()->user()->can('Reseller Vouchers PDF') ? '&nbsp;<a target="_blank" class="btn btn-default btn-icon" target="_blank" href="' . route('admin.website.lawfulinterception.voucherpdf', Hashids::encode($row->id)) . '" title="' . __('Reseller Vouchers PDF') . '"><i class="fa fa-gift"></i></a>' : '';
                    $actions .= auth()->user()->can('Reseller Vouchers Payment PDF') ? '&nbsp;<a target="_blank" class="btn btn-primary btn-icon" target="_blank" href="' . route('admin.website.lawfulinterception.voucherpaymentpdf', Hashids::encode($row->id)) . '" title="' . __('Reseller Payments PDF') . '"><i class="fa fa-credit-card"></i></a>' : '';
                    $actions .= '&nbsp;<a class="btn btn-warning btn-icon export-all" data-id="' . Hashids::encode($row->id) . '" title="' . __('Download Data') . '"><i class="fa fa-download"></i></a>';
                }

                $actions .= '</div>';
                return $actions;
            });
            $datatable = $datatable->rawColumns(['status', 'type', 'actions']);
            return $datatable->make(true);
        }
        return view('admin.website.lawfulinterception.index');
    }
    // Reseller Details PDF
    public function lawfulInterceptionResellerPdf($contact_id)
    {

        try {
            $contact_id = Hashids::decode($contact_id)[0];
        } catch (\Throwable $th) {
            return 'Incorrect params';
        }
        $reseller_detail = Contact::where('id', $contact_id)->first();
        $pdf = PDF::loadView(
            'admin.website.lawfulinterception.resellerpdf',
            ['reseller_detail' => $reseller_detail],
            [],
            [
                'title' => "Reseller Information--" . $reseller_detail->name
            ]
        );

        return $pdf->stream("Reseller Information--" . $reseller_detail->name . ".pdf", array("Attachment" => false));
    }
    // Reseller Voucher Orders PDF
    public function lawfulInterceptionOrderPdf($contact_id, Request $request)
    {
        try {
            $contact_id = Hashids::decode($contact_id)[0];
        } catch (\Throwable $th) {
            return 'Incorrect params';
        }
        $data['reseller_detail'] = Contact::where('id', $contact_id)->first();
        $data_query = VoucherOrder::whereHas('reseller.contact', function ($query) use ($contact_id) {
            $query->where('id', $contact_id);
        })->with('reseller', 'vouchers', 'voucher_taxes', 'product', 'variation', 'product.generalInformation', 'product.customer_taxes', 'product.customer_taxes.tax');
        $data['orders'] = $data_query->get();

        $data_query = VoucherOrder::whereHas('reseller.contact', function ($query) use ($contact_id) {
            $query->where('id', $contact_id);
        });
        $data['total'] = $data_query->count();
        $data_query = VoucherOrder::whereHas('reseller.contact', function ($query) use ($contact_id) {
            $query->where('id', $contact_id);
        })->where('status', 0);
        $data['pending'] = $data_query->count();
        $data_query = VoucherOrder::whereHas('reseller.contact', function ($query) use ($contact_id) {
            $query->where('id', $contact_id);
        })->where('status', 1);
        $data['approved'] = $data_query->count();
        $data_query = VoucherOrder::whereHas('reseller.contact', function ($query) use ($contact_id) {
            $query->where('id', $contact_id);
        })->where('status', 2);
        $data['rejected'] = $data_query->count();

        // return view('admin.website.lawfulinterception.resellerorder')->with('data',$data);
        $pdf = PDF::loadView(
            'admin.website.lawfulinterception.resellerorder',
            ['data' => $data],
            [],
            [
                'title' => "Reseller Orders--" . $data['reseller_detail']->name
            ]
        );
        return $pdf->stream("Reseller Orders--" . $data['reseller_detail']->name . ".pdf", array("Attachment" => false));
    }
    // Reseller Vouchers PDF
    public function lawfulInterceptionVoucherPdf($contact_id, Request $request)
    {

        try {
            $contact_id = Hashids::decode($contact_id)[0];
        } catch (\Throwable $th) {
            return 'Incorrect params';
        }
        $data['reseller_detail'] = Contact::where('id', $contact_id)->first();
        $data_query = Voucher::whereHas('voucherOrder.reseller.contact', function ($query) use ($contact_id) {
            $query->where('id', $contact_id);
        });
        $data['vouchers'] = $data_query->get();
        $data['total'] = $data_query->count();
        $data['used'] = $data_query->where('status', 0)->count();


        $pdf = PDF::loadView(
            'admin.website.lawfulinterception.resellervoucher',
            ['data' => $data],
            [],
            [
                'title' => "Reseller Vouchers--" . $data['reseller_detail']->name
            ]
        );
        return $pdf->stream("Reseller Vouchers--" . $data['reseller_detail']->name . ".pdf", array("Attachment" => false));
        return view('admin.website.lawfulinterception.resellervoucher')->with('data', $data);
    }
    // Reseller Voucher Order Payment  PDF
    public function lawfulInterceptionVoucherPaymentPdf($contact_id, Request $request)
    {

        try {
            $contact_id = Hashids::decode($contact_id)[0];
        } catch (\Throwable $th) {
            return 'Incorrect params';
        }
        $data['reseller_detail'] = Contact::where('id', $contact_id)->first();
        $data_query = VoucherPayment::whereHas('details.voucher_order.reseller.contact', function ($query) use ($contact_id) {
            $query->where('id', $contact_id);
        });
        $data['voucher_payments'] = $data_query->get();

        // return view('admin.website.lawfulinterception.resellervoucherorderpayment',['voucher_payments' => $data['voucher_payments']]);
        $pdf = PDF::loadView(
            'admin.website.lawfulinterception.resellervoucherorderpayment',
            ['voucher_payments' => $data['voucher_payments'], 'data' => $data],
            [],
            [
                'title' => "Reseller Payments--" . $data['reseller_detail']->name
            ]
        );
        return $pdf->stream("Reseller Payment--" . $data['reseller_detail']->name . ".pdf", array("Attachment" => false));
    }
    // Export All Reseller Lawful Interception
    public function lawfulInterceptionExportAllZip($contact_id)
    {
        try {
            $contact_id = Hashids::decode($contact_id)[0];
        } catch (\Throwable $th) {
            return 'Incorrect params';
        }
        // Reseller Details
        $reseller_detail = Contact::where('id', $contact_id)->first();
        if (!File::exists(public_path() . '/storage/exportData/' . Hashids::encode($reseller_detail->id))) {

            File::makeDirectory(public_path() . '/storage/exportData/' . Hashids::encode($reseller_detail->id), 0777, true);
        }
        $pdf = PDF::loadView('admin.website.lawfulinterception.resellerpdf', ['reseller_detail' => $reseller_detail])->setOptions(['defaultFont' => 'sans-serif']);
        $output = $pdf->output();

        $file_path_1 = public_path() . '/storage/exportData/' . Hashids::encode($reseller_detail->id) . '/' . $reseller_detail->name . '-details.pdf';
        if (file_exists($file_path_1)) {
            //delete previous file
            unlink($file_path_1);
        }
        file_put_contents($file_path_1, $output);


        // Reseller Orders
        $data_query = VoucherOrder::whereHas('reseller.contact', function ($query) use ($contact_id) {
            $query->where('id', $contact_id);
        })->with('reseller', 'vouchers', 'voucher_taxes', 'product', 'variation', 'product.generalInformation', 'product.customer_taxes', 'product.customer_taxes.tax');
        $data['orders'] = $data_query->get();

        $data_query = VoucherOrder::whereHas('reseller.contact', function ($query) use ($contact_id) {
            $query->where('id', $contact_id);
        });
        $data['total'] = $data_query->count();
        $data_query = VoucherOrder::whereHas('reseller.contact', function ($query) use ($contact_id) {
            $query->where('id', $contact_id);
        })->where('status', 0);;
        $data['pending'] = $data_query->count();
        $data_query = VoucherOrder::whereHas('reseller.contact', function ($query) use ($contact_id) {
            $query->where('id', $contact_id);
        })->where('status', 1);;
        $data['approved'] = $data_query->count();
        $data_query = VoucherOrder::whereHas('reseller.contact', function ($query) use ($contact_id) {
            $query->where('id', $contact_id);
        })->where('status', 2);;
        $data['rejected'] = $data_query->count();

        $pdf = PDF::loadView('admin.website.lawfulinterception.resellerorder', ['data' => $data]);
        $output = $pdf->output();

        $file_path = public_path() . '/storage/exportData/' . Hashids::encode($reseller_detail->id) . '/' . $reseller_detail->name . '-orders-details.pdf';
        if (file_exists($file_path)) {
            //delete previous file
            unlink($file_path);
        }
        file_put_contents($file_path, $output);

        // Reseller Vouchers
        $data_query = Voucher::whereHas('voucherOrder.reseller.contact', function ($query) use ($contact_id) {
            $query->where('id', $contact_id);
        });
        $data['vouchers'] = $data_query->get();
        $data['total'] = $data_query->count();
        $data['used'] = $data_query->where('status', 0)->count();
        $pdf = PDF::loadView('admin.website.lawfulinterception.resellervoucher', ['data' => $data])->setOptions(['defaultFont' => 'sans-serif']);
        $output = $pdf->output();

        $file_path = public_path() . '/storage/exportData/' . Hashids::encode($reseller_detail->id) . '/' . $reseller_detail->name . '-vouchers-details.pdf';
        if (file_exists($file_path)) {
            //delete previous file
            unlink($file_path);
        }
        file_put_contents($file_path, $output);

        // Reseller Voucher Payments
        $data_query = VoucherPayment::whereHas('voucher_order.reseller.contact', function ($query) use ($contact_id) {
            $query->where('id', $contact_id);
        })->with(
            'voucher',
            'voucher.customer',
            'voucher_order',
            'voucher_order.product',
            'voucher_order.variation',
            'voucher_order.voucher_taxes',
        );
        $data['voucher_payments'] = $data_query->get();
        $pdf = PDF::loadView('admin.website.lawfulinterception.resellervoucherorderpayment', ['voucher_payments' => $data['voucher_payments']])->setOptions(['defaultFont' => 'sans-serif']);
        $output = $pdf->output();

        $file_path = public_path() . '/storage/exportData/' . Hashids::encode($reseller_detail->id) . '/' . $reseller_detail->name . 'voucher-payments-details.pdf';
        if (file_exists($file_path)) {
            //delete previous file
            unlink($file_path);
        }
        file_put_contents($file_path, $output);


        // Make Zip of all files
        if (!File::exists(public_path() . '/storage/exportDataZIP/')) {

            File::makeDirectory(public_path() . '/storage/exportDataZIP/', 0777, true);
        }
        $zip_file_name = $reseller_detail->name . '.zip';
        $zip_path = public_path() . '/storage/exportDataZIP/';
        $data_path = public_path() . '/storage/exportData/' . Hashids::encode($reseller_detail->id);

        $zip = new ZipArchive();
        $zip->open($zip_path . '/' . $zip_file_name, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($data_path));

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath     = $file->getRealPath();

                // extracting filename with substr/strlen
                $relativePath = substr($filePath, strlen($data_path) + 1);

                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
        $headers = array(
            'Content-Type' => 'application/octet-stream',
        );
        $filetopath = $zip_path . '/' . $zip_file_name;
        // Create Download Response
        if (file_exists($filetopath)) {
            // return response()->download($filetopath,$zip_file_name,$headers);
            return asset('/storage/exportDataZIP/' . $zip_file_name);
        }
    }
    // Customer Details PDF
    public function lawfulInterceptionCustomerPdf($contact_id)
    {


        try {
            $contact_id = Hashids::decode($contact_id)[0];
        } catch (\Throwable $th) {
            return 'Incorrect params';
        }
        $customer_detail = Contact::where('id', $contact_id)->first();
        $pdf = PDF::loadView(
            'admin.website.lawfulinterception.customerpdf',
            ['customer_detail' => $customer_detail],
            [],
            [
                'title' => "Customer Information--" . $customer_detail->name
            ]
        );
        $pdf->stream("Customer Information--" . $customer_detail->name . ".pdf", array("Attachment" => true));
    }
    // Customer Voucher Orders PDF
    public function lawfulInterceptionCustomerOrderPdf($contact_id, Request $request)
    {

        try {
            $contact_id = Hashids::decode($contact_id)[0];
        } catch (\Throwable $th) {
            return 'Incorrect params';
        }
        $data['customer_detail'] = Contact::where('id', $contact_id)->first();
        $data['total_orders_count'] = Quotation::where('customer_id', $contact_id)->count();
        $data['quotation_count'] = Quotation::where('customer_id', $contact_id)->where(function ($query) {
            $query->where('status', '!=', 1);
            $query->where('status', '!=', 2);
        })->count();
        $data['sales_order_count'] = Quotation::where('customer_id', $contact_id)->where(function ($query) {
            $query->where('status', 1);
            $query->orWhere('status', 2);
        })->count();
        $data['quotation_details'] = Quotation::with(
            'pricelist',
            'order_lines',
            'order_lines.product',
            'order_lines.variation',
            'order_lines.quotation_taxes',
            'order_lines.quotation_taxes.tax',
            'payment_term_detail',
            'invoice_address_detail',
            'other_info',
        )->where('customer_id', $contact_id)->get();
        $pdf = PDF::loadView(
            'admin.website.lawfulinterception.customerorder',
            ['data' => $data],
            [],
            [
                'title' => "Customer Order--" . $data['customer_detail']->name
            ]
        );
        return $pdf->stream("Customer Order--" . $data['customer_detail']->name . ".pdf", array("Attachment" => false));
        return view('admin.website.lawfulinterception.customerorder')->with('data', $data);
    }
    // Customer invoices  PDF
    public function lawfulInterceptionCustomerInvoicesPdf($contact_id, Request $request)
    {

        try {
            $contact_id = Hashids::decode($contact_id)[0];
        } catch (\Throwable $th) {
            return 'Incorrect params';
        }
        $data['customer_detail'] = Contact::where('id', $contact_id)->first();
        $data['invoices'] = Invoice::with(
            'quotation',
            'invoice_order_lines'
        )->whereHas('quotation', function ($query) use ($contact_id) {
            $query->where('customer_id', $contact_id);
        })
            ->get();
        $data['invoice_count'] = Invoice::join('quotations', 'quotations.id', 'invoices.quotation_id')->where('quotations.customer_id', $contact_id)->count();
        $data['inovice_paid_count'] = Invoice::join('quotations', 'quotations.id', 'invoices.quotation_id')->where('quotations.customer_id', $contact_id)->where('is_paid', 1)->count();
        $data['inovice_unpaid_count'] = Invoice::join('quotations', 'quotations.id', 'invoices.quotation_id')->where('quotations.customer_id', $contact_id)->where('is_paid', 0)->count();
        $data['inovice_partially_paid_count'] = Invoice::join('quotations', 'quotations.id', 'invoices.quotation_id')->where('quotations.customer_id', $contact_id)->where('is_partially_paid', 1)->count();
        // return view('admin.website.lawfulinterception.customerinvoices')->with('data',$data);
        $pdf = PDF::loadView(
            'admin.website.lawfulinterception.customerinvoices',
            ['data' => $data],
            [],
            [
                'title' => "Customer Invoices--" . $data['customer_detail']->name
            ]
        );
        return $pdf->stream("Customer Invoices--" . $data['customer_detail']->name . ".pdf", array("Attachment" => false));
    }
    // Customer Carts
    public function lawfulInterceptionCartsPdf($contact_id, Request $request)
    {

        try {
            $contact_id = Hashids::decode($contact_id)[0];
        } catch (\Throwable $th) {
            return 'Incorrect params';
        }
        $data['customer_detail'] = User::where('id', $contact_id)->first();
        $data['total_carts'] = Cart::with('user')->where('customer_id', $contact_id)->count();
        $data['total_completed_carts'] = Cart::with('user')->where('customer_id', $contact_id)->where('is_checkout', 1)->count();
        $data['total_abandoned_carts'] = Cart::with('user')->where('customer_id', $contact_id)->where('is_checkout', 0)->count();
        $data['carts'] = Cart::where('customer_id', $contact_id)->get();
        $pdf = PDF::loadView(
            'admin.website.lawfulinterception.customercarts',
            ['data' => $data],
            [],
            [
                'title' => "Customer Cart--" . $data['customer_detail']->name
            ]
        );
        return $pdf->stream("Customer Cart--" . $data['customer_detail']->name . ".pdf", array("Attachment" => false));
    }
    // Export All
    public function lawfulInterceptionCustomerExportAllZip($contact_id)
    {
        try {
            $contact_id = Hashids::decode($contact_id)[0];
        } catch (\Throwable $th) {
            return 'Incorrect params';
        }
        // Customer Details
        $customer_detail = Contact::where('id', $contact_id)->first();
        if (!File::exists(public_path() . '/storage/exportData/' . Hashids::encode($customer_detail->id))) {

            File::makeDirectory(public_path() . '/storage/exportData/' . Hashids::encode($customer_detail->id), 0777, true);
        }
        $pdf = PDF::loadView('admin.website.lawfulinterception.customerpdf', ['customer_detail' => $customer_detail]);
        $output = $pdf->output();

        $file_path_1 = public_path() . '/storage/exportData/' . Hashids::encode($customer_detail->id) . '/' . $customer_detail->name . '-details.pdf';
        if (file_exists($file_path_1)) {
            //delete previous file
            unlink($file_path_1);
        }
        file_put_contents($file_path_1, $output);


        // Customer Orders
        $data['total_orders_count'] = Quotation::where('customer_id', $contact_id)->count();
        $data['quotation_count'] = Quotation::where('customer_id', $contact_id)->where(function ($query) {
            $query->where('status', '!=', 1);
            $query->where('status', '!=', 2);
        })->count();
        $data['sales_order_count'] = Quotation::where('customer_id', $contact_id)->where(function ($query) {
            $query->where('status', 1);
            $query->orWhere('status', 2);
        })->count();
        $data['quotation_details'] = Quotation::with(
            'pricelist',
            'order_lines',
            'order_lines.product',
            'order_lines.variation',
            'order_lines.quotation_taxes',
            'order_lines.quotation_taxes.tax',
            'payment_term_detail',
            'invoice_address_detail',
            'other_info',
        )->where('customer_id', $contact_id)->get();

        $pdf = PDF::loadView('admin.website.lawfulinterception.customerorder', ['data' => $data]);
        $output = $pdf->output();

        $file_path = public_path() . '/storage/exportData/' . Hashids::encode($customer_detail->id) . '/' . $customer_detail->name . '-orders-details.pdf';
        if (file_exists($file_path)) {
            //delete previous file
            unlink($file_path);
        }
        file_put_contents($file_path, $output);

        //  Customer Invoices
        $data['invoices'] = Invoice::with(
            'quotation',
            'invoice_order_lines'
        )->whereHas('quotation', function ($query) use ($contact_id) {
            $query->where('customer_id', $contact_id);
        })
            ->get();
        $data['invoice_count'] = Invoice::join('quotations', 'quotations.id', 'invoices.quotation_id')->where('quotations.customer_id', $contact_id)->count();
        $data['inovice_paid_count'] = Invoice::join('quotations', 'quotations.id', 'invoices.quotation_id')->where('quotations.customer_id', $contact_id)->where('is_paid', 1)->count();
        $data['inovice_unpaid_count'] = Invoice::join('quotations', 'quotations.id', 'invoices.quotation_id')->where('quotations.customer_id', $contact_id)->where('is_paid', 0)->count();
        $data['inovice_partially_paid_count'] = Invoice::join('quotations', 'quotations.id', 'invoices.quotation_id')->where('quotations.customer_id', $contact_id)->where('is_partially_paid', 1)->count();
        $pdf = PDF::loadView('admin.website.lawfulinterception.customerinvoices', ['data' => $data]);
        $output = $pdf->output();

        $file_path = public_path() . '/storage/exportData/' . Hashids::encode($customer_detail->id) . '/' . $customer_detail->name . '-invoices-details.pdf';
        if (file_exists($file_path)) {
            //delete previous file
            unlink($file_path);
        }
        file_put_contents($file_path, $output);
        // Customer Carts
        $data['total_carts'] = Cart::with('user')->where('customer_id', $customer_detail->user_id)->count();
        $data['total_completed_carts'] = Cart::with('user')->where('customer_id', $customer_detail->user_id)->where('is_checkout', 1)->count();
        $data['total_abandoned_carts'] = Cart::with('user')->where('customer_id', $customer_detail->user_id)->where('is_checkout', 0)->count();
        $data['carts'] = Cart::with('user', 'cart_items')->where('customer_id', $customer_detail->user_id)->get();
        $pdf = PDF::loadView('admin.website.lawfulinterception.customercarts', ['data' => $data]);
        $output = $pdf->output();

        $file_path = public_path() . '/storage/exportData/' . Hashids::encode($customer_detail->id) . '/' . $customer_detail->name . '-cart-details.pdf';
        if (file_exists($file_path)) {
            //delete previous file
            unlink($file_path);
        }
        file_put_contents($file_path, $output);
        // Make Zip of all files
        if (!File::exists(public_path() . '/storage/exportDataZIP/')) {

            File::makeDirectory(public_path() . '/storage/exportDataZIP/', 0777, true);
        }
        $zip_file_name = $customer_detail->name . '.zip';
        $zip_path = public_path() . '/storage/exportDataZIP/';
        $data_path = public_path() . '/storage/exportData/' . Hashids::encode($customer_detail->id);

        $zip = new ZipArchive();
        $zip->open($zip_path . '/' . $zip_file_name, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($data_path));

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath     = $file->getRealPath();

                // extracting filename with substr/strlen
                $relativePath = substr($filePath, strlen($data_path) + 1);

                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
        $headers = array(
            'Content-Type' => 'application/octet-stream',
        );
        $filetopath = $zip_path . '/' . $zip_file_name;
        // Create Download Response
        if (file_exists($filetopath)) {
            // return response()->download($filetopath,$zip_file_name,$headers);
            return asset('/storage/exportDataZIP/' . $zip_file_name);
        }
    }
    /*** Lawful Interception Functions End **/
    /**
     * Projects Listing
     *
     */
    public function getProjectsList()
    {
        if (!auth()->user()->can('Projects Listing'))
            access_denied();
        $data['projects'] = Project::all();

        return view('admin.website.projects.index', $data);
    }

    /**
     * Payment Gateways
     *
     */
    public function paymentGateways()
    {
        if (!auth()->user()->can('Payment Gateway Settings'))
            access_denied();
        $data['gateways'] = PaymentGateway::all();
        return view('admin.website.paymentgateways.form', $data);
    }
    public function updatePaymentGateways(Request $request)
    {
        $input = $request->all();
        // Mollie Accounts
        $mollie = PaymentGateway::where('id', 1)->first();
        $mollie->sandbox_api_key = $input['mollie_sandbox_key'];
        $mollie->live_api_key = $input['mollie_live_key'];
        $mollie->mode = $input['mollie_mode'];
        $mollie->status = $input['mollie_status'];
        $mollie->save();
        return redirect()->route('admin.website.payment.gateways');
    }

    public function CreateResellerRedeemedPage($id)
    {


        $data = [];
        $data['id'] = $id;
        $id = Hashids::decode($id)[0];
        // $data['action'] = 'Add';
        $data['reseller_id'] = $id;
        $data['model'] = ResellerRedeemedPage::where('reseller_id', $id)->first();
        return view('admin.website.reseller.form')->with($data);
    }
    public function AddResellerRedeemedPage(Request $request)
    {
        $input = [];
        $input = $request->all();

        $voucher_form = str_contains($input['description'], '{{voucher_form}}');

        if ($voucher_form == 1) {

            $id = Hashids::decode($input['reseller_id'])[0];
            $this->validate(
                $request,
                [
                    'title' => "required|string|max:100",
                ]
            );

            $input['reseller_id'] = $id;
            if ($input['id'] != null) {
                // Exception case for domain presense
                if ($request->domain != '') {
                    $input['domain'] = $input['domain'] . '.' . env('reseller_domain');
                    $check_domain_exist = ResellerRedeemedPage::where('domain', $request->domain . '.' . env('reseller_domain'))
                        ->where('reseller_id', '!=', $id)->first();
                    if (!empty($check_domain_exist)) {
                        return redirect()->back()->with(session()->flash('alert-error', 'Domain already exist.Please use another domain!'));
                    }
                    $input['domain'] = 'https://' . $input['domain'];
                }
                $model = ResellerRedeemedPage::where('reseller_id', $id)->first();
                $model->is_reseller_changed = 1;
                $column_array = array();
                if( $model->description != $input['description'] ){
                    $column_array[] = 'description';
                }
                if( $model->terms_of_use != $input['terms_of_use'] ){
                    $column_array[] = 'terms_of_use';
                }
                if( $model->privacy_policy != $input['privacy_policy'] ){
                    $column_array[] = 'privacy_policy';
                }
                if( $model->imprint != $input['imprint'] ){
                    $column_array[] = 'imprint';
                }
                $model->update($input);
                if( count($column_array) > 0 )
                {
                    dispatch(new \App\Jobs\TranslateRedeemPageDataJob($model->id, $column_array));
                }
                $model->update($input);

                if ($request->domain != '' && $model->domain != $request->domain) {
                    $reseller_domain = $input['domain'];

                    // // check if the domain is pointed to the server
                    $dns_lookup_response = dns_get_record($reseller_domain, DNS_A);
                    if (array_search(env('server_ip'), array_column($dns_lookup_response, 'ip')) !== false) {
                        // reseller's current url
                        $url = isset($model->url) ? $model->url : '';
                        $url_exploded = explode("/", parse_url($url, PHP_URL_PATH));
                        $last_two_segments_sliced = array_slice($url_exploded, -2, count($url_exploded), true);
                        $last_two_segments = implode('/', $last_two_segments_sliced);
                        $new_url = $reseller_domain . '/' . $last_two_segments;
                        $new_url = str_replace(' ', '', $new_url);

                        $model->is_domain_verified = 1;
                        $model->url = $new_url;
                        $model->save();
                    } else {
                        $model->is_domain_verified = 0;
                        $model->save();
                    }
                }

                if ($request->image) {

                    $file   = $request->image;

                    $file_name = $file->getClientOriginalName();
                    $type = $file->getClientOriginalExtension();
                    $file_temp_name = 'redeem-' . time() . '.' . $type;
                    $path = public_path('storage/uploads/redeem-page') . '/' . $file_temp_name;
                    $img = Image::make($file)->save($path);
                    $model->logo = $file_temp_name;
                    $model->save();
                }
                if ($request->nav_title != null) {
                    if (!empty(array_filter($request->nav_title))) {

                        ResellerRedeemedPageNavigation::where('reseller_redeem_page_id', $model->id)->delete();
                        foreach ($request->nav_title as  $ind => $title) {
                            $nav_title = $title;
                            $reseller_redeemed_navigation = new ResellerRedeemedPageNavigation;
                            $reseller_redeemed_navigation->reseller_redeem_page_id = $model->id;
                            $reseller_redeemed_navigation->title  = $request->nav_title[$ind];
                            $reseller_redeemed_navigation->url  = $request->nav_url[$ind];
                            $reseller_redeemed_navigation->save();
                        }
                    }
                }
                Alert::success(__('Success'), __('Reseller Redeemed Page Updated successfully!'))->persistent('Close')->autoclose(5000);
            } else {
                $model = new ResellerRedeemedPage();
                $model->fill($input)->save();
                Alert::success(__('Success'), __('Reseller Redeemed Page Added successfully!'))->persistent('Close')->autoclose(5000);
            }
        } else {

            Alert::error(__('Failure'), __('Please enter {{voucher_form}} in description!!'))->persistent('Close')->autoclose(5000);
            return redirect()->back();
        }
        return redirect('admin/website/resellers');
    }
}

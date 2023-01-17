<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Interfaces\PartialViewsRepositoryInterface;
use App\Http\Traits\FSecureTrait;
use App\Http\Controllers\Controller;
use App\Models\ActivityAttachments;
use App\Models\ScheduleActivities;
use App\Models\ActivityMessages;
use App\Models\ActivityLogNotes;
use Yajra\DataTables\DataTables;
use App\Models\ProductVariation;
use App\Models\ActivityTypes;
use Illuminate\Http\Request;
use App\Models\FsecureLog;
use App\Models\Followers;
use App\Models\Products;
use App\Models\License;
use App\Models\Contact;
use App\Models\Admin;
use Carbon\Carbon;
use DateTime;
use Hashids;
use Alert;
use View;
use Auth;

class FsecureController extends Controller
{
    use FSecureTrait;
     /**
     * @var PartialViewsRepositories.
     */
    protected $kasperskyRepository;
    /**
     * PartialViewsRepositories Constructor.
     *
     * @param PartialViewsRepositories $kasperskyRepository
     */
    public function __construct(PartialViewsRepositoryInterface $kasperskyRepository)
    {
        $this->kasperskyRepository = $kasperskyRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $not_data = array(
            '67DWeddfv5fxlaPLinnsa', '3bKq2pdvaeqWG6kuxZLRF', 'QnJAddmSzUWX05LVUWBLU', 'tjQFydDoJqnJPZ9ZRvouc', 'JDendUXNOrSsTyz1iiURk', 'CVEqdid2An22sFuAiZprD' , 'YN31d5bmv4lZAw12yKjDJ',
            'qwertydvrok', 'DDBoddud1GnrYVlBto7is', 'EBddvybjUNY95a3CxnKL2', 'odicCjdElVzPOYBff6kmL', 'LGWwdc0yqsTh9XlpVWe58', 'ZOK8deb4V5cyy4bfxN5Lk' , '94ORdvLxtAciHode5YVJX',
            'SVjn7dwlZnwQQ2Mm9tGAr', 'd10vdItglNZtm8rhdQULs', 'nRAdObfiYgdaUa1lhuqUD', '10JdrBPVOhpq7zTwT5bLq', '7jIdHkNnUaGgQ4lApt3OY', 'NI5dK3Cf23Ub9Sz1VYWtA' , 'PMdG0rOJX4UiVZC0m4VyF',
            'gZdAVjvQjMsxMxDwnyCcx', 'o0r5ZsntyopuXqsOQCqT', 'LIBSn5wfeW9PGft5QFyA', '6gC4D2MLyoBtVWf8J5nc', 'jqJXf59e0hrwtQYL0Yav', 'qW1tDAIt6rOnigkgtEqa' , '6iOBrlenvfYJr4aclluk',
            'mOIkMCneV0lFUPp0CXCu', 'BxZUf2bvIZbK7IzXI9Dh', 'ndmdmv9i05sBohJ2Brar', 'UoBxzuVlPMLIdDC40FfC', '1iCpOGeDXc39hOvWYu5s', 'oImniuAeENHAIRWpaEN5' , '2OfVdFteDI4zmyxrAWAl',
            'KkV8bo461jCLEKsW5Low', 'Maqut6wmQq1EcfNu0Src', 'ZbZipO77cRgvcvLJceoE', 'dAr0Zc4pkrtgF9yYcfmE', 'fTXTE42mUlWlZqJJUpY2', 'AEGqmuTtOaSSPCxmufWw' , 'DJDRQ1yN69QOL405Yfwq',
            'DfnxiuHMpZnO9En8Emm8', 'krSlcVHLVOBAbZguZjBq', '3drwe700pBTHuDfQGs42', 'DDBodud1GnrYVlBto7is', 'Ur20DpVy6ilE22WIusqB', '417fFuIrtyoEvB39qdul' , 'uc6hXYEdJR7GcIJ3ZX4x',
            '2Q9DmX9UdYifvaszF6q9', '7jIdHkNnUaGgQ4lApt3OY', 'NI5dK3Cf23Ub9Sz1VYWtA', 'PMdG0rOJX4UiVZC0m4VyF', 'gZdAVjvQjMsxMxDwnyCcx', 'o0r5ZsntyopuXqsOQCqT' , 'LIBSn5wfeW9PGft5QFyA',
            '6gC4D2MLyoBtVWf8J5nc', 'jqJXf59e0hrwtQYL0Yav', 'qW1tDAIt6rOnigkgtEqa', '6iOBrlenvfYJr4aclluk', 'mOIkMCneV0lFUPp0CXCu', '3bKq2pdvaeqWG6kuxZLRF' , 'QnJAddmSzUWX05LVUWBLU'
        );
        $data = [];
        $data['licenses'] = License::whereHas('product',function($query){
            $query->where('product_type', 2);
        })->whereNotIn('license_key',$not_data)->orderBy('id','desc')->get();
        // dd($data['licenses']);
        return view('admin.fsecure.index', $data);
    }

    public function cancelLicense(Request $request)
    {
        $response = $this->cancelLicenseHelper($request->id, $request->licenseKey);
        $customerReference = $response->items[0]->customerReference;
        if($customerReference){
            $data =  array(
                'product_id' =>  $request->id,
                'license_key'   => $request->licenseKey
            );
            License::where($data)->update(['status'=> 3]);
            return response()->json(['success'=>"The License has been permanantly cancelled."]);
        }else{
            return response()->json(['error'=>"Something went wrong. Please try again."]);
        }

    }

    public function fescureLog(Request $request){
        if ($request->ajax()) {
            $data = FsecureLog::orderBy('id','desc');
            if(isset($request->start_date) && $request->start_date != '' ){
                $data->whereBetween('created_at', [Carbon::parse($request->start_date), Carbon::parse($request->end_date)->addDay()]);
            }
            $datatable = Datatables::of($data);
            $datatable->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->format('d-M-Y H:i:s');
            });
            return $datatable->make(true);
        }
        return view('admin.fsecure.logs');
    }
}

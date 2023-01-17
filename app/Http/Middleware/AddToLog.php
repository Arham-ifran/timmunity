<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\LogActivity;
use App\Models\LogVisitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AddToLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // dd(auth()->user());

        $pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';
        $ipaddress = $this->getIP();
        $response =  $next($request);

        $url  = request()->path();
        $url_exploded = explode('/', $url);
        $visitor = null;
        
        if(!$pageWasRefreshed){
            if($url_exploded[0] != 'admin' && $url_exploded[0] != 'backend' &&  $url_exploded[0] != 'frontend'){
                if(auth()->user())
                {
                    $visitor = LogVisitor::where('user_id',auth()->id())->first();
                    if(!$visitor)
                    {
                        $visitor = new LogVisitor;
                        $visitor->ip = $ipaddress;
                        $visitor->user_id = auth()->id();
                        $visitor->is_logged_user =  1;
                        $visitor->save();
                    }
                    else
                    {
                        $visitor->ip = $ipaddress;
                        $visitor->save();
                    }
                }
                else
                {
                    $visitor = LogVisitor::where('ip',$ipaddress)->where('is_logged_user',0)->first();
                    if(!$visitor)
                    {
                        $visitor = new LogVisitor;
                        $visitor->ip = $ipaddress;
                        $visitor->user_id = 0;
                        $visitor->is_logged_user =  0;
                        $visitor->save();
                    }
                }
                $log = new LogActivity;
                $log->url = request()->path();
                $log->log_visitor_id = $visitor->id;
                $log->save();
            }
        }

        return $response;
    }
    public function getIP(){
        if (getenv('HTTP_CLIENT_IP'))
            return getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            return getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            return getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            return getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            return getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            return getenv('REMOTE_ADDR');
        else
            return 'UNKNOWN';
    }
}

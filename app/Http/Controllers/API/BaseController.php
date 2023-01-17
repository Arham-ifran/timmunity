<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Distributor;

class BaseController extends Controller
{

    public function verifyRequest(Request $request)
    {
        $auth_key = $request->header('auth-key');
        $auth_email = $request->header('auth-email');
        // $auth_key = "OUthTgzvTy8qmgn";
        // $auth_email = "faizan@distributor123.com";
        $distributor = Distributor::where('email', $auth_email)->where('auth_key', $auth_key)->first();
        if(!$distributor)
        {
            return array(
                'success' => 'false',
                'products' => [],
                'message' => __('Authentication Failure')
            );
        }
        return $distributor;
    }
}

<?php

namespace App\Http\Controllers;

use App\Model\Lan;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function adjustRequestForLan(Request $request): Request
    {
        if (is_null($request->input('lan_id'))) {
            $request['lan_id'] = Lan::getCurrentLan()->id;
        }
        return $request;
    }
}

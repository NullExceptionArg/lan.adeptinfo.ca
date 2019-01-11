<?php

namespace App\Http\Controllers;

use App\Model\Lan;
use Illuminate\{Contracts\Validation\Validator, Http\Request, Support\Facades\Auth};
use Laravel\Lumen\Routing\Controller as BaseController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Controller extends BaseController
{
    public function adjustRequestForLan(Request $request): Request
    {
        if (is_null($request->input('lan_id'))) {
            $lan = Lan::getCurrent();
            if (is_null($lan)) {
                return $request;
            } else {
                $request['lan_id'] = $lan->id;
            }
        }
        return $request;
    }

    public function adjustRequestForEmail(Request $request): Request
    {
        if (is_null($request->input('email'))) {
            $request['email'] = Auth::user()->email;
        }
        return $request;
    }

    public function checkValidation(Validator $validator)
    {
        if ($validator->fails()) {
            throw new BadRequestHttpException($validator->errors());
        }
    }
}

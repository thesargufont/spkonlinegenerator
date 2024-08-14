<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Basecamp;
use App\Models\JsonResponseAPI;

class BasecampAPIController extends Controller {
    public function getBasecamp() {
        try {
            $findBaseCamp = Basecamp::get()->toArray();

            if($findBaseCamp) {
                return JsonResponseAPI::jsonResponseSuccess('Get Basecamp Successfully', $findBaseCamp);
            }

            return JsonResponseAPI::jsonResponseError('Basecamp not found');

        } catch (\Exception $e) {
            return JsonResponseAPI::jsonResponseError($e->getMessage(), $e->getCode());
        }
    }
}
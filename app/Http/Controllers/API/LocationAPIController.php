<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JsonResponseAPI;
use App\Models\Location;


class LocationAPIController extends Controller {
    public function getLocation(Request $request) {
        try {
            $findLocation = Location::get()->toArray();

            if($findLocation) {
                return JsonResponseAPI::jsonResponseSuccess('Get Location Successfully', $findLocation);
            }

            return JsonResponseAPI::jsonResponseError('Get Location Successfully');

        } catch (\Exception $e) {
            return JsonResponseAPI::jsonResponseError($e->getMessage(), $e->getCode());
        }
    }
}

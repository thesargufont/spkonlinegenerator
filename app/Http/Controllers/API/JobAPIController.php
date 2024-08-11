<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JsonResponseAPI;

class JobAPIController extends Controller {
    public function getJob() {
        try {
            $findBaseCamp = Job::get()->toArray();

            if($findBaseCamp) {
                return JsonResponseAPI::jsonResponseSuccess('Get Job Successfully', $findBaseCamp);
            }

            return JsonResponseAPI::jsonResponseError('Job not found');

        } catch (\Exception $e) {
            return JsonResponseAPI::jsonResponseError($e->getMessage(), $e->getCode());
        }
    }
}
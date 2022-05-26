<?php

namespace App\Http\Controllers;

use App\Enums\ResponseCode;
use App\Enums\ResponseType;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Create a commonly formatted json response
     *
     * @param array<string, mixed> $response_data Response data returned, depending on the response type the data will
     *          be included into the `data` or the `errors` field
     * @param ResponseType $response_type The response type, can be success or error, defaults to success, defines:
     *          - the status code `success`/`error`
     *          - where the `$response_data` will be placed (into the `errors` or the `data` field)
     * @param ResponseCode $response_code HTTP status code of the response by default it is HTTP_OK
     *          NOTE: in case of `$response_type` is ERROR this value MUST be manually changed
     * @return JsonResponse
     */
    protected function jsonResponse(
        array $response_data = [],
        ResponseType $response_type = ResponseType::SUCCESS,
        ResponseCode $response_code = ResponseCode::HTTP_OK
    ): JsonResponse
    {
        $return_pack = [
            "status" => $response_type === ResponseType::SUCCESS ? "success" : "error",
            "errors" => $response_type === ResponseType::ERROR ? $response_data : [],
            "data" => $response_type === ResponseType::SUCCESS ? $response_data : []
        ];

        return response()->json($return_pack, $response_code->value);
    }
}

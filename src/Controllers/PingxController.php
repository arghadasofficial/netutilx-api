<?php

namespace Argha\NetutilxApi\Controllers;

use Argha\NetutilxApi\Controllers\BaseApiController;
use Argha\NetutilxApi\Helpers\Response;
use Argha\NetutilxApi\Helpers\PingHelper;
use Argha\NetutilxApi\Config\Constants;
use Argha\NetutilxApi\Helpers\ServiceHelper;

class PingxController extends BaseApiController
{

    private $serviceHelper;
    private $pingHelper;

    public function __construct()
    {
        parent::__construct();
        Constants::init();
        $this->serviceHelper = new ServiceHelper(Constants::$SERVICE_URL);
        $this->pingHelper = new PingHelper();
    }

    public function queryPing()
    {
        $params = [
            "query" => "crudoimage.com",
        ];
        $response = $this->serviceHelper->sendGetRequest('pingx', $params);

        $parsedResponse = $this->pingHelper->parsePingResponse($response['decoded_response']['data']['output']);
        
        return [
            "success" => $response['success'],
            "data" => $parsedResponse
        ];
    }
}

<?php 
namespace Argha\NetutilxApi\Controllers;

use Argha\NetutilxApi\Config\Constants;
use Argha\NetutilxApi\Controllers\BaseApiController;
use Argha\NetutilxApi\Helpers\ServiceHelper;
use Argha\NetutilxApi\Helpers\TraceRouteHelper;

class TracexController extends BaseApiController {

    private $serviceHelper;
    private $traceRouteHelper;

    public function __construct() {
        parent::__construct();
        Constants::init();
        $this->serviceHelper = new ServiceHelper(Constants::$SERVICE_URL);
        $this->traceRouteHelper = new TraceRouteHelper();
    }

    public function traceRoute() {
        $params = [
            "query" => "google.com",
        ];
        $response = $this->serviceHelper->sendGetRequest('tracex', $params);

        return [
            "success" => $response['success'],
            "data" => $response['decoded_response']['data']['output']
        ];
    }

}

?>
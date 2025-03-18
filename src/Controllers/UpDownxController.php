<?php 
namespace Argha\NetutilxApi\Controllers;

use Argha\NetutilxApi\Config\Constants;
use Argha\NetutilxApi\Helpers\ServiceHelper;
use Argha\NetutilxApi\Helpers\UpDownHelper;
use Argha\NetutilxApi\Helpers\ToolHistoryHelper;

class UpDownxController extends BaseApiController {
    
    private $serviceHelper;
    private $updownHelper;
    private $toolHistoryHelper;

    public function __construct() {
        parent::__construct();
        Constants::init();
        $this->updownHelper = new UpDownHelper();
        $this->toolHistoryHelper = new ToolHistoryHelper();
        $this->serviceHelper = new ServiceHelper(Constants::$SERVICE_URL);
    }

    public function fetchUpDown($query) {
        $params = ["url" => $query];
        $response = $this->serviceHelper->sendGetRequest("updownx", $params);
        return $response;
    }

}


?>
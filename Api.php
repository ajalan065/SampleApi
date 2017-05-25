<?php

/**
 * File: Api.php
 */
require_once("Rest.php");
class Api extends Rest {

    /**
     * Create an Api object.
     */
    public function __construct(){
      parent::__construct();              // Init parent contructor
    }
    
   /*
    * Public method for access api.
    * This method dynmically call the method once the inputs are provided
    */
    public function processApi(){
      // Cross validation if the request method is GET
      if($_SERVER['REQUEST_METHOD'] != "GET"){
        $this->response('',406);
      }
      // Cross validation if the required credentials are provided.
      else if (!isset($_GET['quote']) || !isset($_GET['oauth_access_token']) || !isset($_GET['oauth_access_token_secret']) || !isset($_GET['consumer_key']) || !isset($_GET['consumer_secret'])) {
        $this->response('', 206);
      }

      $param=$this->_request['quote'];
      // If success everythig is good send header as "OK" return param
      $this->response($param, 200);
    }
}
   // Create instance automatically and call the process.
   $api = new Api;
   $api->processApi();
?>
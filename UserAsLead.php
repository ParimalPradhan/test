<?php


$site_url = "http://172.31.1.4/ceylincolifeinsurance";

$url = $site_url . "/service/v4_1/rest.php";
$username = "simpleworksadmin";
$password = "simpleworksadmin";
     
  // $request_data = json_decode(file_get_contents('php://input'));

//$prefix = 'webhookLog_';
//$file = '.log';
//$date = new DateTime();
//error_log($date->format('Y-m-d H:i:s') . ' ' . $_REQUEST . "\n\n", 3, $prefix . $file);
//file_put_contents('filename.txt', print_r($_REQUEST, true));
  
     $name = $_REQUEST['chatname'];
     $phone = $_REQUEST['chatmobile'];
     $email = $_REQUEST['chatemail'];
     $message = $_REQUEST['chatmsg'];
     $lead_source = $_REQUEST['chatleadsource'];
   // $name = $request_data->chatname;
    //$phone = $request_data->chatmobile;
    //$email = $request_data->chatemail;
    //$lead_source = $request_data->chatleadsource;
    //$msg = $request_data->chatmsg;
    //function to make cURL request
    function call($method, $parameters, $url)
    {
        ob_start();
        $curl_request = curl_init();

        curl_setopt($curl_request, CURLOPT_URL, $url);
        curl_setopt($curl_request, CURLOPT_POST, 1);
        curl_setopt($curl_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($curl_request, CURLOPT_HEADER, 1);
        curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);

        $jsonEncodedData = json_encode($parameters);

        $post = array(
             "method" => $method,
             "input_type" => "JSON",
             "response_type" => "JSON",
             "rest_data" => $jsonEncodedData
        );

        curl_setopt($curl_request, CURLOPT_POSTFIELDS, $post);
        $result = curl_exec($curl_request);
        curl_close($curl_request);

        $result = explode("\r\n\r\n", $result, 2);
        $response = json_decode($result[1]);
        ob_end_flush();

        return $response;
    }

    //login ---------------------------------------------
    $login_parameters = array(
         "user_auth" => array(
              "user_name" => $username,
              "password" => md5($password),
              "version" => "1"
         ),
         "application_name" => "RestTest",
         "name_value_list" => array(),
    );

    $login_result = call("login", $login_parameters, $url);

    $session_id = $login_result->id;

    //search_by_module -------------------------------------------------
    $search_by_module_parameters = array(
        "session" => $session_id,
        'search_string' => $email,
        'modules' => array(
            'Leads',
        ),
        'offset' => 0,
        'max_results' => 1,
        'assigned_user_id' => '',
        'select_fields' => array('id'),
        'unified_search_only' => false,
        'favorites' => false
    );

    $search_by_module_results = call('search_by_module', $search_by_module_parameters, $url);

    $response = array();
    
//     if (isset($search_by_module_results->entry_list[0]->records[0]) && isset($search_by_module_results->entry_list[0]->records[0]->id) && isset($search_by_module_results->entry_list[0]->records[0]->id->value) && !is_null($search_by_module_results->entry_list[0]->records[0]->id->value) && $search_by_module_results->entry_list[0]->records[0]->id->value != '') {
//         $_SESSION['crmchatbot']['userID'] = $search_by_module_results->entry_list[0]->records[0]->id->value;
//         $response['name'] = $name;
//         $response['userID'] = $search_by_module_results->entry_list[0]->records[0]->id->value;
//         $response['alreadyUser'] = true;
//         echo json_encode($response);
//  return;
// //        exit;
//     }

    //create account -------------------------------------
    $set_entry_parameters = array(
         //session id
         "session" => $session_id,

         //The name of the module from which to retrieve records.
         "module_name" => "Leads",

         //Record attributes
         "name_value_list" => array(
              //to update a record, you will nee to pass in a record id as commented below
              array("name" => "last_name", "value" => $name),
              array("name" => "phone_mobile", "value" => $phone),
              array("name" => "email1", "value" => $email),
              array("name" => "lead_source", "value" => $lead_source),
              array("name" => "description", "value" => $message),
         ),
    );

    $set_entry_result = call("set_entry", $set_entry_parameters, $url);

    // $_SESSION['crmchatbot']['userID'] = $set_entry_result->id;
    $response['name'] = $name;
    $response['alreadyUser'] = false;
    $response['userID'] = $set_entry_result->id;

    echo json_encode($response);

?>

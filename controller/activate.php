<?php
$data_array = array();
$data_array['memberID'] = $route->getParameter(2);    
$data_array['active'] = $route->getParameter(3);    

$gump = new GUMP();
$data_array = $gump->sanitize($data_array); 
$validation_rules_array = array(
    'memberID'    => 'required|integer',
    'active'    => 'required|exact_len,32'
);
$gump->validation_rules($validation_rules_array);

$filter_rules_array = array(
    'memberID' => 'trim|sanitize_string',
    'active' => 'trim',
);
$gump->filter_rules($filter_rules_array);
$validated_data = $gump->run($data_array);

if($validated_data === false) {
    $error = $gump->get_readable_errors(false);
    exit;
} 
else {
    foreach($validation_rules_array as $key => $val) {
        ${$key} = $data_array[$key];
    }
    $userVeridator = new UserVeridator();
    if($userVeridator->isReady2Active($id, $active)){
        $data_array['active'] = "Yes";
        Database::get()->update("member", $data_array, "memberID", $data_array['memberID']); 
        header('Location: '.Config::BASE_URL.'login');
        exit;
    }
    else{
        $msg->error("Your account could not be activated.");
        header('Location: '.Config::BASE_URL.'login'); 
    exit;
    }
}
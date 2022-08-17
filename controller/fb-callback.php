<?php
$helper = $fb->getRedirectLoginHelper();

try {
        $accessToken = $helper->getAccessToken();
    } 
    catch(Facebook\Exceptions\FacebookResponseException $e) {
        // When Graph returns an error
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } 
    catch(Facebook\Exceptions\FacebookSDKException $e) {
        // When validation fails or other local issues
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
}

if (!isset($accessToken)) {
    if ($helper->getError()) {
        header('HTTP/1.0 401 Unauthorized');
        echo "Error: " . $helper->getError() . "\n";
        echo "Error Code: " . $helper->getErrorCode() . "\n";
        echo "Error Reason: " . $helper->getErrorReason() . "\n";
        echo "Error Description: " . $helper->getErrorDescription() . "\n";
    } 
    else {
        header('HTTP/1.0 400 Bad Request');
        echo 'Bad request';
    }
    exit;
}

// Logged in
echo '<h3>Access Token</h3>';

// The OAuth 2.0 client handler helps us manage access tokens
$oAuth2Client = $fb->getOAuth2Client();

// Get the access token metadata from /debug_token
$tokenMetadata = $oAuth2Client->debugToken($accessToken);
echo '<h3>Metadata</h3>';
var_dump($tokenMetadata);

// Validation (these will throw FacebookSDKException's when they fail)
$tokenMetadata->validateAppId(Config::FB_APP_ID); // Replace {app-id} with your app id
// If you know the user ID this access token belongs to, you can validate it here
//$tokenMetadata->validateUserId('123');
$tokenMetadata->validateExpiration();

if (!$accessToken->isLongLived()) {
    // Exchanges a short-lived access token for a long-lived one
    try {
        $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
    } 
    catch (Facebook\Exceptions\FacebookSDKException $e) {
        echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
        exit;
    }
    echo '<h3>Long-lived</h3>';
    var_dump($accessToken->getValue());
}

$_SESSION['fb_access_token'] = (string) $accessToken;  
// User is logged in with a long-lived access token.
// You can redirect them to a members-only page.
//header('Location: https://example.com/members.php');
$fb->setDefaultAccessToken($accessToken);
$response = $fb->get('/me?locale=en_US&fields=id,name,email');
$userNode = $response->getGraphUser();
$email = $userNode->getField('email');
$fb_user_id = $userNode->getField('id');

$userVeridator = new UserVeridator();
$userVeridator->isEmailDuplicate($email);
$error = $userVeridator->getErrorArray();

if(!$error){
    // 沒有註冊
    $table = 'member';
    $data_array = array(
        'username' => $fb_user_id,
        'password' => $passwordObject->password_hash(date().rand(), PASSWORD_BCRYPT),
        'email' => $email,
        'active' => 'Yes',
        'fb_user_id' => $fb_user_id
    );
    Database::get()->insert($table, $data_array);

    $_SESSION['memberID'] = Database::get()->getLastId();
    $_SESSION['username'] = $fb_user_id;
}
else{
    // 有註冊了
    if($userVeridator->fbLoginVerification($email, $fb_user_id)){
        $_SESSION['memberID'] = $userVeridator->getMemberIdByFb($fb_user_id);
        $_SESSION['username'] = $fb_user_id;
    }
}
header('Location: '.Config::BASE_URL."upsell");
exit;
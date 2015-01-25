<html>
<head>
  <title>uCloudCall.com - PHP SMS Broadcast Example Code</title>
</head>
<body>
    <?php echo '<p>Send an SMS Broadcast</p>'; ?> 

    <?php if (!empty($_POST)): 
        $broadcastTitle = $_POST["broadcast_title"];
        $phoneNumbers = $_POST["phoneNumbers"]; 
        $message = $_POST["message"]; 
        $phoneNumberArr = str_getcsv($phoneNumbers, ",");
        
        send_sms_broadcsat($broadcastTitle, $phoneNumberArr, $message);
        ?>
        Your brodcast: <?php echo htmlspecialchars($broadcastTitle); ?>!<br>
        Is being sent to phone numbers: <?php echo htmlspecialchars($phoneNumbers); ?>.<br>
        
    <?php else: ?>
        <form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> method="post">
            Broadcast Title: <input type="text" name="broadcast_title" /><br>
            Phone Numbers (example: 4167775555,4162225555,6473333333): <input type="text" name="phoneNumbers" /><br>
            Message: <br/>
            <input type="text" name="message" /><br/>
            <input type="submit">
        </form>
    <?php endif; ?>

</body>
</html>


<?php

function send_sms_broadcsat($title, $phoneNumberArr, $smsMessage) {

    $user="fake_user@fake_email.com";
    $pass="fake_password";

    $telArray = array();
    $objJson = new stdClass();
    $objJson->contactList = new stdClass();
    $objJson->contactList->title=$title;
    $objJson->sms = new stdClass();
    $objJson->sms->textBody=$smsMessage;
    $objJson->broadcastType="Text";
    
    foreach ($phoneNumberArr as &$phoneNumber) {
        $contactObj = new stdClass();
        $contactObj->tel = $phoneNumber;
        array_push($telArray, $contactObj);

    }
    $objJson->contactList->contacts=$telArray;

    $theurl = "https://app.ucloudcall.com/api/users/" . urlencode($user) . "/broadcasts";
    $thedata = json_encode($objJson);
    $theheader = "Authorization: ".$user." ".$pass."\r\n"
                    . "Content-Type: application/json\r\n"
                    . "Content-Length: " . strlen($thedata) . "\r\n";
    
    echo "URL: " . $theurl . "<br/>"; 
    echo "Message Header: " . $theheader . "<br/>"; 
    echo "Message Body: " . $thedata . "<br/>";
    
    do_post_request($theurl, $thedata, $theheader);

}

function do_post_request($url, $data, $optional_headers = null)
{
  $params = array('http' => array(
  'method' => 'POST',
  'content' => $data
  ));
  if ($optional_headers !== null) {
  $params['http']['header'] = $optional_headers;
}

$ctx = stream_context_create($params);
$fp = @fopen($url, 'r', false, $ctx);
if (!$fp) {
throw new Exception("Problem with $url, $php_errormsg");
}
$response = @stream_get_contents($fp);
if ($response === false) {
throw new Exception("Problem reading data from $url, $php_errormsg");
}
return $response;
}

?> 
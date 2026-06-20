<?php

include "$_SERVER[DOCUMENT_ROOT]/lenzpogi/CloudFlare/Api.php";
include "$_SERVER[DOCUMENT_ROOT]/lenzpogi/CloudFlare/Zone/Dns.php";

$result = "";

$key = "5d4b5f36e83e90745aa0f4323fd04f16"; 
// Above Cloudflare Zone ID, find it in Domain Overview --> Domain Summary --> Zone ID
$id = new \Cloudflare\Api("harulenz@gmail.com", "20f989db3c168115b85e51e03b07b9c7eb85b");
// Above Cloudflare Email + Cloudflare Global API Key (https://www.cloudflare.com/a/profile) --> Global API Key
$dns = new \Cloudflare\Zone\Dns($id);

if(!empty($_POST["name"]) and !empty($_POST["value"]) and !empty($_POST["record"])) {
    $response = $dns->create($key, $_POST["record"], $_POST["name"] . ".octaviavpn.net", $_POST["value"], 1);
    // Make sure to enter your domain name above (.yourdomain.name), or else the script won't work
    if ($response->success) {
        $result = '<div class="toast toast-success" style="margin: 0 auto; width: auto;text-align: center;"><b>Success!</b> Your hostname <b>' . $_POST['name'] . '.octaviavpn.net</b> successfully pointed to <b>' . $_POST['value'] . '</b>!</div>';
    } else {
        $result = '<div class="toast toast-error" style="margin: 0 auto; width: auto;text-align: center;"><b>Sorry!</b> Your hostname <b>' . $_POST['name'] . '.octaviavpn.net</b> could not be pointed to <b>' . $_POST['value'] . '</b>!</div>';
    }
    
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Octavia VPN</title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <link href="/lenzpogi/assets/spectre.min.css" rel="stylesheet">
  <link href="/lenzpogi/assets/spectre-exp.min.css" rel="stylesheet">
  <link href="/lenzpogi/assets/favicon.ico?" rel="shortcut icon">
  <link href="/lenzpogi/assets/style.css" rel="stylesheet" type="text/css">
</head>
<body>
  <div class="outer">
    <div class="middle">
      <div>
        <?php echo $result; ?>
      </div><br>
      <center>
      <a href="#" onClick="history.go(-1)" class="btn btn-primary">Done</a>
      </center>
    </div>
  </div>
</body>
</html>
<!-- Script downloaded from https://github.com/reckr/cloudflare-dns-creator (c) 2018 -->

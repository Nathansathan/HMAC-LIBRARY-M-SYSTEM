<?php
$secret = 'base64:GYzKZ7QIQn03NQ8x9mxm+4iP/W6WKX+oLdq5aPextY8=';
$body1 = '{"test":true}';
$body2 = "{\"test\":true}\n";
$body3 = "{\"test\":true}\r\n";

echo "No newline: " . hash_hmac('sha256', $body1, $secret) . PHP_EOL;
echo "LF newline: " . hash_hmac('sha256', $body2, $secret) . PHP_EOL;
echo "CRLF newline: " . hash_hmac('sha256', $body3, $secret) . PHP_EOL;

 
<?php
header("content-type:text/html;charset=utf-8");
$smtp = array(
    "url" => "mail.aikq.de",
    "port" => "465", // 1 As for the 25
    "username" => "info@timmunity.com",
    "password" => "9OnDA4da%WkG34zw",
    "from" => "info@timmunity.com",
    "to" => "fzahid001@gmail.com",
    "subject" => " test 3 Under the title ",
    "body" => " test 1 The content of "
);
$CRLF = "\r\n";
$test = "";
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $smtp['url']);
curl_setopt($curl, CURLOPT_PORT, $smtp['port']);
curl_setopt($curl, CURLOPT_TIMEOUT, 10);
function inlineCode($str)
{
    $str = trim($str);
    return $str ? '=?UTF-8?B?' . base64_encode($str) . '?= ' : '';
}
function buildHeader($headers)
{
    $ret = '';
    foreach ($headers as $k => $v) {
        $ret .= $k . ': ' . $v . "\n";
    }
    return $ret;
}
// 
$header = array(
    'Return-path' => '<' . $smtp['from'] . '>',
    'Date' => date('r'),
    'From' => '<' . $smtp['from'] . '>',
    'MIME-Version' => '1.0',
    'Subject' => inlineCode($smtp['subject']),
    'To' => $smtp['to'],
    'Content-Type' => 'text/html; charset=UTF-8; format=flowed',
    'Content-Transfer-Encoding' => 'base64'
);
$data = buildHeader($header) . $CRLF . chunk_split(base64_encode($smtp['body']));
$content = "HELO " . $smtp["url"] . $CRLF; //  First get hello1 Under the  
$content .= "<br>AUTH LOGIN" . $CRLF . base64_encode($smtp["username"]) . $CRLF . base64_encode($smtp["password"]) . $CRLF; //  Verify landing  
$content .= "<br>MAIL FROM:" . $smtp["from"] . $CRLF; //  Send an address  
$content .= "<br>RCPT TO:" . $smtp["to"] . $CRLF; //  Receives an address  
$content .= "<br>DATA" . $CRLF . $data . $CRLF . "." . $CRLF; //  Send content  
$content .= "<br>QUIT" . $CRLF; //  exit  
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // curl Receive returned data  
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $content);
$test = curl_exec($curl);
$err = curl_error($curl);
var_dump($test);
echo "<br/>\r\n";
var_dump($err);
echo "<br/>\r\n";
var_dump($content);
//  The end of the  
curl_close($curl);


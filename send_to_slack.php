<?php
namespace slackbot;

function SendToSlack($message, $channel, $token)
{
    if ($message == "" || $channel == "" || $token == "") {
        return;
    }

    $ch = curl_init("https://slack.com/api/chat.postMessage");
    $data = http_build_query([
        "token" => $token,
    	"channel" => $channel,
    	"text" => $message,
    	"username" => "SlackBot",
    ]);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);
    
    return $result;
}
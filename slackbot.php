<?php
namespace slackbot;

include 'send_to_slack.php';
include 'slackbot_model.php';

$slackbot_model = new SlackBotModel();
if ($slackbot_model->errno) {
  http_response_code(500);
  die("error");
}
$sent_news = array();

do {
  if (FROM_URL) {
    $xml = file_get_contents(RSS_URL);
    if ($xml) {
      $news = new \SimpleXMLElement($xml);
    }
  } else {
    $news = new \SimpleXMLElement("full.xml",null,true);
  }

  $sent_count = 0;
  if (isset($news)) {
    $items = $news->channel->item;
    if ($items) {
      for ($i = count($items) - 1; $i >= 0; $i--) {
        $value = $items[$i];

        if (!isset($sent_news[$value->guid]))
        {
          if (!$slackbot_model->ItemExists($value->guid)) {
            $link = trim(preg_replace('/\s+/', ' ', $value->link));
            $title = trim(preg_replace('/\s+/', ' ', $value->title));
            $message = sprintf("<%s| *%s* >\n%s", $link, $title, $value->description);
            SendToSlack($message, SLACK_CHANNEL, SLACK_TOKEN);
            $slackbot_model->AddItem($value->guid);
            $sent_count++;
            sleep(1);
          }

          $sent_news[$value->guid] = 1;
        }
      }
    }
  }

  $interval = SEND_INTERVAL - $sent_count;
  if (IN_WHILE && $interval > 0) {
    sleep($interval);
  }
} while (IN_WHILE);
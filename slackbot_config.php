<?php
namespace slackbot;

const DB_HOST = "localhost";
const DB_USER = "root";
const DB_PASSWORD = "root";
const DB_NAME = "testSlackBotDB";
const DB_TABLE = "news";

const RSS_URL = "http://www.tatar-inform.ru/rss/full/";
const FROM_URL = false;
const IN_WHILE = false;
const SEND_INTERVAL = 3 * 60;
const SLACK_CHANNEL = "#random";

// Токен можно получить по ссылке https://api.slack.com/custom-integrations/legacy-tokens
const SLACK_TOKEN = "";
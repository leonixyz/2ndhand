<?php

// load database config
include(__DIR__ . '/../../postgresql-www-data.php');

// load database interface
include(__DIR__ . '/db.php');

// load 3rd party library PHPMailer
include(__DIR__ . '/PHPMailer/PHPMailerAutoload.php');

// configure PHPMailer
include(__DIR__ . '/mailer.conf.php');

// load utils
include(__DIR__ . '/util.php');
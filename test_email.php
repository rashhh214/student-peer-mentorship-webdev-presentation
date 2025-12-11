<?php
require __DIR__ . '/email_config.php';
$res = sendMail('your-email@example.com', 'Test', '<p>Hello — test email</p>');
var_dump($res);

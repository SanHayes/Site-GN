<?php

return array (
  'dbHost' => '127.0.0.1',
  'dbPort' => '3306',
  'dbUser' => 'root',
  'dbPassword' => 'db7135f3492a8c16',
  'dbName' => 'chat',
  'superUser' => 'admin',
  'superPass' => '123456',
  'services' => 
  array (
    'mailer' => 
    array (
      'smtp' => 'true',
      'smtpSecure' => 'ssl',
      'smtpHost' => 'smtp.gmail.com',
      'smtpPort' => '465',
      'smtpUser' => 'admin@gmail.com',
      'smtpPass' => 'admin@gmail.com',
    ),
  ),
  'appSettings' => 
  array (
    'contactMail' => 'admin@gmail.com',
  ),
);

?>
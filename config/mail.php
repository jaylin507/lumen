<?php

return [
    'driver' => env('MAIL_DRIVER', 'smtp'),
    'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
    'port' => env('MAIL_PORT', 587),
    'from' => ['address' => env('MAIL_USERNAME'), 'name' => env('MAIL_USERNAME'),],
    'title' => env('MAIL_TITLE','【异常】服务器异常邮件'),
    'encryption' => env('MAIL_ENCRYPTION', 'tls'),
    'username' => env('MAIL_USERNAME'),
    'password' => env('MAIL_PASSWORD'),
    'sendmail' => '/usr/sbin/sendmail -bs',
    'pretend' => false,
    //接收邮件的开发者,为空不发送邮件
    'develops' => [
        'jialin507@foxmail.com'
    ],
];
<?php
/**
 * Created by PhpStorm.
 * User: songjialin
 * Date: 2018/8/22
 * Time: 下午6:18
 */

namespace App\Libraries\Logger;

use Monolog\Formatter\HtmlFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SwiftMailerHandler;
use Monolog\Logger as MonologLogger;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class Logger
{
    /**
     * 渠道类型：公共
     */
    const CHANNEL_COMMON = 'Common';

    /**
     * 渠道类型：控制器
     */
    const CHANNEL_CONTROLLER = 'Controllers';

    /**
     * 渠道类型：模型
     */
    const CHANNEL_MODEL = 'Model';

    /**
     * 渠道类型：服务
     */
    const CHANNEL_SERVICE = 'Service';

    /**
     * 渠道类型：登录
     */
    const CHANNEL_LOGIN = 'Login';

    /**
     * 渠道类型：异常
     */
    const CHANNEL_EXCEPTION = 'Exception';

    /**
     * 渠道类型：扩展包
     */
    const CHANNEL_LIBRARIES = 'Libraries';

    /**
     * @param $channel
     * @return MonologLogger
     * @throws \Exception
     */
    public function init($channel)
    {
        //初始化Logger
        $logger = new MonologLogger($channel);

        $tos = self::getTos();
        if (count($tos) > 0) {
            $mailHandler = $this->initMail($tos, $channel);
            $logger->pushHandler($mailHandler);
        }

        $path = __DIR__ . '/../storage/logs/' . date('Y-m') . '/' . $channel . '/';
        $this->mkDirs($path);

        $fileName = $channel . '-' . date('m-d') . '.log';
        $streamHandler = new StreamHandler($path . $fileName);
        $logger->pushHandler($streamHandler);
        return $logger;
    }

    /**
     * 返回异常邮件接收的处理人员，为空则不发送邮件
     * @return array
     */
    private static function getTos()
    {
        $develops = config('mail.develops');
        $tos = [];
        if (count($develops) > 0) {
            foreach ($develops as $develop) {
                if ($develop) {
                    $tos[] = $develop;
                }
            }
        }
        return $tos;
    }

    private function initMail($tos, $channel)
    {
        $host = config('mail.host');
        $port = config('mail.port');
        $username = config('mail.username');
        $password = config('mail.password');
        $encryption = config('mail.encryption');
        $from = config('mail.from');

        //初始化SwiftMailer
        $transport = (new Swift_SmtpTransport($host, $port))
            ->setUsername($username)
            ->setPassword($password)
            ->setEncryption($encryption);
        $mailer = new Swift_Mailer($transport);
        //初始化发送消息
        $message = (new Swift_Message('【异常】' . $channel . '异常邮件'))
            ->setFrom([$from['address'] => $from['name']])
            ->setTo($tos);

        $message->setContentType('text/html');

        $mailHandler = new SwiftMailerHandler($mailer, $message);//第三个参数可以设置发送级别，只有大于设定的级别才会发送邮件，默认为ERROR（400）
        $formatter = new HtmlFormatter();

        $mailHandler->setFormatter($formatter);

        return $mailHandler;
    }

    private function mkDirs($dir, $mode = 0777)
    {
        if (!is_dir($dir)) {
            if (!$this->mkDirs(dirname($dir))) {
                return false;
            }
            if (!mkdir($dir, $mode)) {
                return false;
            }
            chmod($dir, $mode);
        }
        return true;
    }
}
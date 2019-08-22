<?php
/**
 * Created by PhpStorm.
 * User: jaylin
 * Date: 2019-08-22
 * Time: 15:18
 */

namespace App\Libraries\Logger;


use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\MailHandler;
use Monolog\Logger;
use Swift;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class SwiftMailerHandler extends MailHandler
{
    /**
     * SwiftMailerHandler constructor.
     * @param int $level
     * @param bool $bubble
     */
    public function __construct(int $level = Logger::DEBUG, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
        $tos = $this->getTos();
        if (count($tos) > 0) {
            $this->initMail($tos);
        }
    }

    /**
     * 返回异常邮件接收的处理人员，为空则不发送邮件
     * @return array
     */
    protected function getTos()
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

    /**
     * @param $tos
     * @return void
     */
    protected function initMail($tos)
    {
        $host = config('mail.host');
        $port = config('mail.port');
        $username = config('mail.username');
        $password = config('mail.password');
        $encryption = config('mail.encryption');
        $from = config('mail.from');
        $title = config('mail.title');

        //初始化SwiftMailer
        $transport = (new Swift_SmtpTransport($host, $port))
            ->setUsername($username)
            ->setPassword($password)
            ->setEncryption($encryption);
        $this->mailer = new Swift_Mailer($transport);
        //初始化发送消息
        $message = (new Swift_Message($title))
            ->setFrom([$from['address'] => $from['name']])
            ->setTo($tos);
        $message->setContentType('text/html');

        $this->messageTemplate = $message;
    }

    /**
     * Send a mail with the given content
     *
     * @param string $content formatted email body to be sent
     * @param array $records the array of log records that formed this content
     * @throws \Exception
     */
    protected function send($content, array $records)
    {
        if (empty($this->mailer)) {
            return;
        }
        $this->mailer->send($this->buildMessage($content, $records));
    }

    /**
     * Creates instance of Swift_Message to be sent
     *
     * @param  string $content formatted email body to be sent
     * @param  array $records Log records that formed the content
     * @return \Swift_Message
     * @throws \Exception
     */
    protected function buildMessage($content, array $records)
    {
        $message = null;
        if ($this->messageTemplate instanceof \Swift_Message) {
            $message = clone $this->messageTemplate;
            $message->generateId();
        } elseif (is_callable($this->messageTemplate)) {
            $message = call_user_func($this->messageTemplate, $content, $records);
        }

        if (!$message instanceof \Swift_Message) {
            throw new \InvalidArgumentException('Could not resolve message as instance of Swift_Message or a callable returning it');
        }

        if ($records) {
            $subjectFormatter = $this->getSubjectFormatter($message->getSubject());
            $message->setSubject($subjectFormatter->format($this->getHighestRecord($records)));
        }

        $message->setBody($content);
        if (version_compare(Swift::VERSION, '6.0.0', '>=')) {
            $message->setDate(new \DateTimeImmutable());
        } else {
            $message->setDate(time());
        }

        return $message;
    }

    /**
     * BC getter, to be removed in 2.0
     * @throws \Exception
     */
    public function __get($name)
    {
        if ($name === 'message') {
            trigger_error('SwiftMailerHandler->message is deprecated, use ->buildMessage() instead to retrieve the message', E_USER_DEPRECATED);

            return $this->buildMessage(null, array());
        }

        throw new \InvalidArgumentException('Invalid property ' . $name);
    }

    /**
     * Gets the formatter for the Swift_Message subject.
     *
     * @param  string $format The format of the subject
     * @return FormatterInterface
     */
    protected function getSubjectFormatter($format)
    {
        return new LineFormatter($format);
    }

}
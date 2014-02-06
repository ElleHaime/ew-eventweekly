<?php
/**
 * Class Emailer
 *
 * Example of usage:
 * <code>
 *   $message = $this->di->get('mailMessage');
 *   $message->setSubject('test subject')
 *   ->setFrom(array('basko.slava@gmail.com' => 'Slava Basko'))
 *   ->setTo(array('feitosa_slava@mail.ru'))
 *   ->setBody('Here is the message itself');
 *
 *   $mailer = $this->di->get('mailEmailer');
 *   $res = $mailer->send($message);
 * </code>
 *
 * @author Slava Basko <basko.slava@gmail.com>
 */

namespace Core\Mail;


class Emailer {

    const SMTP_TRANSPORT = 1;

    const SENDMAIL_TRANSPORT = 2;

    const MAIL_TRANSPORT = 3;

    /**
     * @var \Swift_MailTransport
     */
    private $transport;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @param $config
     */
    public function __construct($config)
    {
        $transport = $config->transport;

        switch($transport) {
            case 1: $this->transport = \Swift_SmtpTransport::newInstance($config->smtp->host, $config->smtp->port, $config->smtp->security)
                ->setUsername($config->smtp->user)
                ->setPassword($config->smtp->pass);
                break;
            case 2: $this->transport = \Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');
                break;
            case 3: $this->transport = \Swift_MailTransport::newInstance();
                break;
        }

        $this->mailer = \Swift_Mailer::newInstance($this->transport);

        return $this;
    }

    /**
     * Send email to user
     *
     * @param $Message
     * @return int
     */
    public function send($Message)
    {
        $result = $this->mailer->send($Message);

        return $result;
    }

} 
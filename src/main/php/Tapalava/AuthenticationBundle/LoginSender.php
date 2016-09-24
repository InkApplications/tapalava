<?php

namespace Tapalava\AuthenticationBundle;

use InkApplications\Knock\MessageSender;
use Swift_Mailer;
use Swift_Message;
use Twig_Environment;

/**
 * Sends out login authentication emails.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class LoginSender implements MessageSender
{
    private $mailer;
    private $twig;

    public function __construct(Swift_Mailer $mailer, Twig_Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function send($email, $code, $emailId)
    {
        $params = ['email' => $email, 'code' => $code, 'emailId' => $emailId];
        $body = $this->twig->render('AuthenticationBundle:Login:email.html.twig', $params);
        $title = $this->twig->render('AuthenticationBundle:Login:emailTitle.txt.twig', $params);

        $message = Swift_Message::newInstance()
            ->setSubject($title)
            ->setFrom('support@tapalava.com')
            ->setTo($email)
            ->setBody($body, 'text/html');
        $this->mailer->send($message);
    }
}

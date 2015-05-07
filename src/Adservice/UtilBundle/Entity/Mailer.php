<?php
namespace Adservice\UtilBundle\Entity;

use Swift_Mailer;
use Swift_Transport_EsmtpTransport;
use Symfony\Bundle\TwigBundle\TwigEngine;

class Mailer
{
    private $subject;
    private $from;
    private $to;
    private $body = array();
    private $cc;
    private $attach;
    private $replyTo;


    private $mailer;
    private $transport;
    private $templating;



    public function __construct(Swift_Mailer $mailer, Swift_Transport_EsmtpTransport $transport, TwigEngine $templating)
    {
        $this->mailer = $mailer;
        $this->transport = $transport;
        $this->templating = $templating;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    public function setFrom($from)
    {
        $this->from = $from;
        return $this;
    }

    public function setTo($to)
    {
        $this->to = $to;
        return $this;
    }

    public function setBody($view)
    {
        $this->body['body'] = $view;
        return $this;
    }

    public function setCc($cc)
    {
        $this->cc = $cc;
        return $this;
    }

    public function setAttachment($filePath)
    {
        $this->attach = $filePath;
        return $this;
    }

    public function setReplyTo($replyTo)
    {
        $this->replyTo = $replyTo;
        return $this;
    }

    public function sendMailToSpool($message = null)
    {
        //Si no se le pasa ninguna estancia de \Swift_Message, la creo
        if(is_null($message)) {
            $message = \Swift_Message::newInstance();
        }

        if($this->subject)
            $message->setSubject($this->subject);

        if($this->from)
            $message->setFrom($this->from);

        if($this->to)
            $message->setTo($this->to);
            $message->setBody($this->body['body'],'text/html', 'utf-8');

        if($this->attach){
            $attachment = \Swift_Attachment::fromPath($this->attach);
            $message->attach($attachment);
        }

        if($this->cc)
            $message->setCc($this->cc);

        if($this->replyTo)
            $message->setReplyTo($this->replyTo);

        $this->mailer->send($message);
    }

    public function sendSpool()
    {
        $spool = $this->mailer->getTransport()->getSpool();
        $spool->flushQueue($this->transport);
    }
}

?>

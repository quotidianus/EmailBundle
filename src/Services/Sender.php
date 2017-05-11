<?php

namespace Librinfo\EmailBundle\Services;

use Doctrine\ORM\EntityManager;
use Librinfo\EmailBundle\Services\InlineAttachments;
use Librinfo\EmailBundle\Services\Tracking;

class Sender
{
    /**
     *
     * @var EntityManager
     */
    protected $manager;
    
    /**
     *
     * @var Tracking
     */
    protected $tracker;
    
    /**
     *
     * @var InlineAttachments $inlineAttachmentsHandler
     */
    protected $inlineAttachmentsHandler;
    
    /**
     *
     * @var Swift_Mailer $directMailer
     */
    protected $directMailer;
    
    /**
     *
     * @var Swift_Mailer $spoolMailer
     */
    protected $spoolMailer;

    /**
     *
     * @var Email $email
     */
    protected $email;

    /**
     *
     * @var array $attachments
     */
    protected $attachments;

    /**
     *
     * @var boolean $needsSpool Wheter the email has one or more recipients
     */
    protected $needsSpool;
    
    /**
     * @param EntityManager $manager
     * @param Tracking $tracker
     * @param InlineAttachments $inlineAttachmentsHandler
     * @param Swift_Mailer $directMailer
     * @param Swift_Mailer $spoolMailer
     */
    public function __construct(EntityManager $manager, $tracker, $inlineAttachmentsHandler, $directMailer, $spoolMailer)
    {
        $this->manager = $manager;
        $this->tracker = $tracker;
        $this->inlineAttachmentsHandler = $inlineAttachmentsHandler;
        $this->directMailer = $directMailer;
        $this->spoolMailer = $spoolMailer;
    }
    
    /**
     * Sends an email
     * 
     * @param Email $email  The email to send
     * @return int          Number of successfully sent emails
     */
    public function send($email)
    {
        $this->email = $email;
        $this->attachments = $email->getAttachments();
        $addresses = explode(';', $this->email->getFieldTo());
        
        $this->needsSpool = count($addresses) > 1;
        
        if( $this->email->getIsTest() )
            return $this->directSend($this->email->getTestAdress());
        
        if( $this->needsSpool )
            return $this->spoolSend($addresses);
        else
            return $this->directSend($addresses);
    }

    /**
     * Sends the mail directly
     * 
     * @param array $to                The To addresses
     * @param array $cc                The Cc addresses (optional)
     * @param array $bcc               The Bcc addresses (optional) 
     * @param array $failedRecipients  An array of failures by-reference (optional)
     *
     * @return int The number of successful recipients. Can be 0 which indicates failure
     */
    protected function directSend($to, &$failedRecipients = null, $message = null)
    {
        $message = $this->setupSwiftMessage($to, $message);

        $sent = $this->directMailer->send($message, $failedRecipients);
        $this->updateEmailEntity($message);

        return $sent;
    }

    /**
     * Spools the email
     * 
     * @param array $addresses
     */
    protected function spoolSend($addresses)
    {
        $message = $this->setupSwiftMessage($addresses);

        $this->updateEmailEntity($message);

        $sent = $this->spoolMailer->send($message);
        
        return $sent;
    }

    /**
     * Creates Swift_Message from Email
     * 
     * @param array $to   The To addresses
     * @param string $message
     * @return Swift_Message
     */
    protected function setupSwiftMessage($to, $message = null)
    {
        $content = $this->email->getContent();

        if( $message == null )
            $message = \Swift_Message::newInstance();
        
        foreach ( $to as $key => $address )
            $to[$key] = trim($address);
        
        // do not modify yet email content if it goes to spool
        if ( !$this->needsSpool )
        {
            $content = $this->inlineAttachmentsHandler->handle($content, $message);
            
            if( $this->email->getTracking())
                try{
                    $content = $this->tracker->addTracking($content, $to[0], $this->email->getId());
                 }catch(\Exception $e){
                    die($e);
                }
        }
        
        $message->setSubject($this->email->getFieldSubject())
                ->setFrom(trim($this->email->getFieldFrom()))
                ->setTo($to)
                ->setBody($content, 'text/html')
                ->addPart($this->email->getTextContent(), 'text/plain')
        ;
        
        
        if( !empty($cc = $this->email->getFieldCc()) )
            $message->setCc($cc);
        
        if( !empty($bcc = $this->email->getFieldBcc()) )
            $message->setBcc($bcc);

        $this->addAttachments($message);

        return $message;
    }

    /**
     * Adds attachments to the Swift_Message
     * @param Swift_Message $message
     */
    protected function addAttachments($message)
    {
        if ( count($this->attachments ) > 0 )
        {
            foreach ( $this->attachments as $file )
            {
                $attachment = \Swift_Attachment::newInstance()
                        ->setFilename($file->getName())
                        ->setContentType($file->getMimeType())
                        ->setBody($file->getFile())
                ;
                $message->attach($attachment);
            }
        }
    }

    /**
     *
     * @param Swift_Message $message
     */
    protected function updateEmailEntity($message)
    {
        if ( $this->needsSpool )
            //set the id of the swift message so it can be retrieved from spool flushQueue()
            $this->email->setMessageId($message->getId());
        else if ( !$this->email->getIsTest() )
            $this->email->setSent(true);

        $this->manager->persist($this->email);
        $this->manager->flush();
    }

}

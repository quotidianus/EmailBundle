<?php

namespace Librinfo\EmailBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Librinfo\EmailBundle\Entity\EmailReceipt;
use Librinfo\EmailBundle\Entity\EmailLink;


class TrackingController extends Controller
{   
    private $manager;
    
    public function testAction()
    {
        $e = $this->getDoctrine()->getManager()->getRepository('LibrinfoEmailBundle:EmailAttachment')->findAll();
        
        dump($e);
        file_put_contents('zeiu.png', $e[28]->getFile());
        return new Response("ok", 200);
    }
    
    public function trackOpensAction($emailId, $recipient)
    {
        $this->trackOpens($emailId, $recipient);
        
        return new Response("ok", 200);
    }
    
    public function trackLinksAction($emailId, $recipient, $destination){
        
        $dest = base64_decode($destination);
        
        $this->trackOpens($emailId, $recipient);
        
        $this->trackLinks($emailId, $recipient, $dest);
        
        return new RedirectResponse($dest, 302);
    }
    
    public function trackOpens($emailId, $recipient)
    {
        $count = 0;
        
        $this->initManager();
        
        $email = $this->manager->getRepository("LibrinfoEmailBundle:Email")->find($emailId);
       
        if(!$email){
            return;
        }
        
        $receipts = $email->getReceipts();
          
        if($receipts->count() > 0){
            foreach($receipts->getSnapshot() as $receipt){
      
                if($receipt->getAddress() == $recipient){
                    
                    $count++;
                }
            }
        }
        if($count == 0)
            $this->addReceipt($email, $recipient);
    }
    
    private function trackLinks($emailId, $recipient, $destination)
    {
        $count = 0;
        
        $this->initManager();
        
        $email = $this->manager->getRepository("LibrinfoEmailBundle:Email")->find($emailId);
       
        if(!$email){
            return;
        }
        
        $links = $email->getLinks();
          
        if($links->count() > 0){
            foreach($links->getSnapshot() as $link){
      
                if($link->getAddress() == $recipient && $link->getDestination() == $destination){
                    
                    $count++;
                }
            }
        }
        if($count == 0)
            $this->addLink($email, $recipient, $destination);
    }
    
    private function addReceipt($email, $recipient)
    {
        $newReceipt = new EmailReceipt();
        $newReceipt->setEmail($email);
        $newReceipt->setAddress($recipient);
        $newReceipt->setDate(new \DateTime());

        $email->addReceipt($newReceipt);
        
        $this->manager->persist($email);
        $this->manager->flush();
    }
    
    private function addLink($email, $recipient, $destination)
    {
        $newLink = new EmailLink();
        $newLink->setEmail($email);
        $newLink->setAddress($recipient);
        $newLink->setDate(new \DateTime());
        $newLink->setDestination($destination);
        $email->addLink($newLink);
     
        $this->manager->persist($email);
        $this->manager->flush();
    }
    
    private function initManager()
    {
         if(!$this->manager){
            
            $this->manager = $this->getDoctrine()->getManager();
        }
    }
    
}

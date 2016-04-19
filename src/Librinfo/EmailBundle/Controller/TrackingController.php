<?php

namespace Librinfo\EmailBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Librinfo\EmailBundle\Entity\EmailReceipt;


class TrackingController extends Controller
{   
    private $manager;
    
    public function trackOpensAction($emailId, $recipient)
    {
        $count = 0;
        
        if(!$this->manager){
            
            $this->manager = $this->getDoctrine()->getManager();
        }
        
        $email = $this->manager->getRepository("LibrinfoEmailBundle:Email")->find($emailId);
       
        $receipts = $email->getReceipts();
          
        if($receipts->count() > 0){
            foreach($receipts->getSnapshot() as $receipt){
      
                if($receipt->getAddress() == $recipient){
                    
                    $count++;
                }
            }
        }
        if($count == 0){
            $this->addReceipt($email, $recipient);
            
        }
        return new Response("ok", 200);
    }
    
    public function addReceipt($email, $recipient){
        $newReceipt = new EmailReceipt();
        $newReceipt->setEmail($email);
        $newReceipt->setAddress($recipient);
        $newReceipt->setDate(new \DateTime());

        $email->addReceipt($newReceipt);
        
        $this->manager->persist($email);
        $this->manager->flush();
    }
}

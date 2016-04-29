<?php

namespace Librinfo\EmailBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Librinfo\EmailBundle\Entity\EmailAttachment;

class AjaxController extends Controller
{

    public function getEmailTemplateAction($templateId)
    {
        $repo = $this->getDoctrine()->getRepository('LibrinfoEmailBundle:EmailTemplate');
        $template = $repo->find($templateId);

        return new Response($template->getContent(), 200);
    }

    public function uploadAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        
        $tempId = $request->get('temp_id');
        $file = $request->files->get('file');
       
       $attachment = new EmailAttachment();
       
       $attachment->setTempId($tempId);
       $attachment->setFile($file);
       $attachment->setMimeType($file->getMimeType());
       $attachment->setName($file->getClientOriginalName());
       $attachment->setSize($file->getClientSize());
       
       $manager->persist($attachment);
       $manager->flush();
        
        return new Response("Ok", 200);
    }
    
    public function removeUploadAction($fileName, $fileSize){
        
        $manager = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository('LibrinfoEmailBundle:EmailAttachment');
        
        $attachment = $repo->findOneBy(array(
            
            'name' => $fileName,
            'size' => $fileSize
        ));
        
        $manager->remove($attachment);
        $manager->flush();
        
        return new Response($fileName." removed successfully", 200);
    }

}

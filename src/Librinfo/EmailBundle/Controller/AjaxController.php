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

    public function removeUploadAction($fileName, $fileSize)
    {

        $manager = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository('LibrinfoEmailBundle:EmailAttachment');

        $attachment = $repo->findOneBy(array(
            'name' => $fileName,
            'size' => $fileSize
        ));

        $manager->remove($attachment);
        $manager->flush();

        return new Response($fileName . " removed successfully", 200);
    }

    public function addToContentAction($fileName, $fileSize)
    {

        $repo = $this->getDoctrine()->getRepository('LibrinfoEmailBundle:EmailAttachment');

        $attachment = $repo->findOneBy(array(
            'name' => $fileName,
            'size' => $fileSize
        ));

        if ( $this->isImage($attachment) )
        {
           return new Response( $this->generateImgTag($attachment) , 200);
        }

        return new Response($fileName . " is not an image", 300);
    }

    private function isImage($attachment)
    {
        if ($attachment && preg_match('!^image\/!', $attachment->getMimeType()) === 1 )
        {
            return true;
        }
        return false;
    }
    
    private function generateImgTag($attachment)
    {
        $alt = explode('.', $attachment->getName())[0];
        
        
        $tag = '<img src="data:'.$attachment->getMimeType().';base64,'.$attachment->getBase64File().'" alt="'.$alt.'" />';
        
        return $tag;
    }
}

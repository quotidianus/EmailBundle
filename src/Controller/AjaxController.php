<?php

namespace Librinfo\EmailBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AjaxController extends Controller
{
    /**
     * 
     * @param String $templateId
     * @return Response The template content that will be inserted into the main content
     */
    public function getEmailTemplateAction($templateId)
    {
        $repo = $this->getDoctrine()->getRepository('LibrinfoEmailBundle:EmailTemplate');
        $template = $repo->find($templateId);

        return new Response($template->getContent(), 200);
    }

    /**
     * 
     * @param String $fileName
     * @param String $fileSize
     * @param String $tempId
     * @return Response img tag that will be embedded into the main content
     */
    public function addToContentAction($fileId)
    {
        $repo = $this->getDoctrine()->getRepository('LibrinfoMediaBundle:File');
        
        $file = $repo->find($fileId);

        return new Response($this->renderView('LibrinfoMediaBundle:Generator:img_tag.html.twig', ['img' => $file]));
    }

}

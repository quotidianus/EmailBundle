<?php

namespace Librinfo\EmailBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AjaxController extends Controller
{

    public function getEmailTemplateAction($templateId)
    {
        $repo = $this->getDoctrine()->getRepository('LibrinfoEmailBundle:EmailTemplate');
        $template = $repo->find($templateId);

        return new Response($template->getContent(), 200);
    }

    public function upload()
    {
        return new Response("Ok", 200);
    }

}

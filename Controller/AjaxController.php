<?php

namespace Librinfo\EmailBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Librinfo\EmailBundle\Entity\EmailAttachment;

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
     * Upload of attachments
     * 
     * @param Request $request
     * @return Response
     */
    public function uploadAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        //tempId used to link an attachment to an email that has yet to be persisted
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

    /**
     * Removal of uploaded attachments
     * 
     * @param String $fileName
     * @param String $fileSize
     * @param String $tempId
     * @return Response
     */
    public function removeUploadAction($fileName, $fileSize, $tempId)
    {

        $manager = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository('LibrinfoEmailBundle:EmailAttachment');

        $attachment = $repo->findOneBy(array(
            'name' => $fileName,
            'size' => $fileSize,
            'tempId' => $tempId
        ));

        $manager->remove($attachment);
        $manager->flush();

        return new Response($fileName . " removed successfully", 200);
    }

    /**
     * 
     * @param String $fileName
     * @param String $fileSize
     * @param String $tempId
     * @return Response img tag that will be embedded into the main content
     */
    public function addToContentAction($fileName, $fileSize, $tempId)
    {

        $repo = $this->getDoctrine()->getRepository('LibrinfoEmailBundle:EmailAttachment');

        $attachment = $repo->findOneBy(array(
            'name' => $fileName,
            'size' => $fileSize,
            'tempId' => $tempId
        ));

        if ($this->isImage($attachment))
        {
            return new Response($this->generateImgTag($attachment), 200);
        }

        return new Response($fileName . " is not an image", 300);
    }

    /**
     * Retrieves the attachments of an email for edit aciton
     * 
     * @param String $emailId
     * @return Response attachments converted to json array
     */
    public function loadAttachmentsAction($emailId)
    {
        $repo = $this->getDoctrine()->getRepository('LibrinfoEmailBundle:EmailAttachment');

        $attachments = $repo->findBy(array('email' => $emailId));

        return new Response($this->attachmentsToJson($attachments), 200);
    }

    /**
     * Converts attachment collection to json array
     * 
     * @param Array $attachments
     * @return String
     */
    private function attachmentsToJson($attachments)
    {
        $jsonAttachments = '[';
        $keySet = array_keys($attachments);
        $lastKey = end($keySet);

        foreach ($attachments as $key => $attachment)
        {
            if ($key == $lastKey)
            {
                $end = '}';
            } else
            {
                $end = '},';
            }
            $jsonAttachments .= '{"name": "' . $attachment->getName() .
                    '", "size": ' . $attachment->getSize() .
                    // ', "file":"' .$attachment->getBase64File().'"'.
                    ', "tempId":"' . $attachment->getTempId() . '"' .
                    $end;
        }

        return $jsonAttachments . "]";
    }

    /**
     * Checks if the file is an image with its mimetype
     * 
     * @param EmailAttachment $attachment
     * @return boolean
     */
    private function isImage($attachment)
    {
        if ($attachment && preg_match('!^image\/!', $attachment->getMimeType()) === 1)
        {
            return true;
        }
        return false;
    }

    /**
     * Generates the img tag from attachment file
     * 
     * @param EmailAttachment $attachment
     * @return string
     */
    private function generateImgTag($attachment)
    {
        $alt = explode('.', $attachment->getName())[0];

        $tag = '<img src="data:' . $attachment->getMimeType() . ';base64,' . $attachment->getBase64File() . '" alt="' . $alt . '" />';

        return $tag;
    }

}

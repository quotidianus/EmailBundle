<?php

namespace Librinfo\EmailBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as SonataCRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class CRUDController extends SonataCRUDController
{
    private $mailer;
    private $manager;
    private $email;
    private $attachments;

    public function sendAction(Request $request)
    {
        $this->manager = $this->getDoctrine()->getManager();
        $id = $request->get($this->admin->getIdParameter());
        $this->email = $this->admin->getObject($id);
        $this->attachments = $this->email->getAttachments();
        $addresses = explode(';', $this->email->getFieldTo());

        if(count($addresses) > 1) {

            $this->newsLetterSend($addresses);
        }else{
            $this->directSend($this->email->getFieldTo());
        }

        $this->addFlash('sonata_flash_success', "Message ".$id." envoyé");

        return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }



    private function directSend($address)
    {
        $this->setDirectMailer();

        $message = $this->setupSwiftMessage($address);

        $this->updateEmailEntity($message);

        $replacements = $this->container->get('swiftdecorator.replacements');
        $decorator = new \Swift_Plugins_DecoratorPlugin($replacements);
        $this->mailer->registerPlugin($decorator);

        $this->mailer->send($message);
    }

    private function newsLetterSend($addresses)
    {
        $this->setSpoolMailer();

        $message = $this->setupSwiftMessage($addresses);

        $this->updateEmailEntity($message);

        $this->mailer->send($message);
    }

    public function setDirectMailer()
    {
        $this->mailer = $this->get('swiftmailer.mailer.direct_mailer');
    }

    public function setSpoolMailer()
    {
        $this->mailer = $this->get('swiftmailer.mailer.spool_mailer');
    }

    public function setupSwiftMessage($to){

        $message = \Swift_Message::newInstance()
            ->setSubject($this->email->getFieldSubject())
            ->setFrom($this->email->getFieldFrom())
            ->setTo($to)
            ->setBody($this->email->getContent(), 'text/html')
            ->addPart($this->email->getTextContent(), 'text/plain')
        ;
        //$this->addAttachments($message);

        return $message;
    }

    private function addAttachments($message)
    {
         if(count($this->attachments) > 0){
            foreach ($this->$attachments as $file) {

                $attachment = \Swift_Attachment::newInstance()
                    ->setFilename($file->getName())
                    ->setContentType($file->getMimeType())
                    ->setBody($file)
                ;
                $message->attach($attachment);
            }
        }
    }

    private function updateEmailEntity($message)
    {
        $this->email->setMessageId($message->getId());
        $this->manager->persist($this->email);
        $this->manager->flush();
        $this->email->setSent(true);
    }

//     public function createAction()
//     {
//         $request = $this->getRequest();
//         // the key used to lookup the template
//         $templateKey = 'edit';
//
//         $this->admin->checkAccess('create');
//
//         $class = new \ReflectionClass($this->admin->hasActiveSubClass() ? $this->admin->getActiveSubClass() : $this->admin->getClass());
//
//         if ($class->isAbstract()) {
//             return $this->render(
//                 'SonataAdminBundle:CRUD:select_subclass.html.twig',
//                 array(
//                     'base_template' => $this->getBaseTemplate(),
//                     'admin'         => $this->admin,
//                     'action'        => 'create',
//                 ),
//                 null,
//                 $request
//             );
//         }
//
//         $this->email = $this->admin->getNewInstance();
//
//         $preResponse = $this->preCreate($request, $this->email);
//         if ($preResponse !== null) {
//             return $preResponse;
//         }
//
//         $this->admin->setSubject($this->email);
//
//         /** @var $form \Symfony\Component\Form\Form */
//         $form = $this->admin->getForm();
//         $form->setData($this->email);
//         $form->handleRequest($request);
//
//         if ($form->isSubmitted()) {
//             //TODO: remove this check for 3.0
//             if (method_exists($this->admin, 'preValidate')) {
//                 $this->admin->preValidate($this->email);
//             }
//             $isFormValid = $form->isValid();
//
//             // persist if the form was valid and if in preview mode the preview was approved
//             if ($isFormValid && (!$this->isInPreviewMode($request) || $this->isPreviewApproved($request))) {
//                 $this->admin->checkAccess('create', $this->email);
//
//                 try {
//                     $this->email = $this->admin->create($this->email);
//
//                     if ($this->isXmlHttpRequest()) {
//                         return $this->renderJson(array(
//                             'result'   => 'ok',
//                             'objectId' => $this->admin->getNormalizedIdentifier($this->email),
//                         ), 200, array());
//                     }
//
//                     $this->addFlash(
//                         'sonata_flash_success',
//                         $this->admin->trans(
//                             'flash_create_success',
//                             array('%name%' => $this->escapeHtml($this->admin->toString($this->email))),
//                             'SonataAdminBundle'
//                         )
//                     );
//
//                     // redirect to edit mode
//                     return $this->redirectTo($this->email);
//                 } catch (ModelManagerException $e) {
//                     $this->handleModelManagerException($e);
//
//                     $isFormValid = false;
//                 }
//             }
//
//             // show an error message if the form failed validation
//             if (!$isFormValid) {
//                 if (!$this->isXmlHttpRequest()) {
//                     $this->addFlash(
//                         'sonata_flash_error',
//                         $this->admin->trans(
//                             'flash_create_error',
//                             array('%name%' => $this->escapeHtml($this->admin->toString($this->email))),
//                             'SonataAdminBundle'
//                         )
//                     );
//                 }
//             } elseif ($this->isPreviewRequested()) {
//                 // pick the preview template if the form was valid and preview was requested
//                 $templateKey = 'preview';
//                 $this->admin->getShow();
//             }
//         }
//
//         $view = $form->createView();
//
//         // set the theme for the current Admin Form
//         $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());
//
//         return $this->render($this->admin->getTemplate($templateKey), array(
//             'action' => 'create',
//             'form'   => $view,
//             'object' => $this->email,
//         ), null);
//     }
}

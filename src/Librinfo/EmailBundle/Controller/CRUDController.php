<?php

namespace Librinfo\EmailBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as SonataCRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class CRUDController extends SonataCRUDController
{
    public function sendAction(Request $request)
    {
        $id = $request->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        $message = \Swift_Message::newInstance()
            ->setSubject($object->getFieldSubject())
            ->setFrom($object->getFieldFrom())
            ->setTo($object->getFieldTo())
            ->setBody($object->getContent(), 'text/html')
            ->addPart($object->getTextContent(), 'text/plain')
        ;

        $attachments = $object->getAttachments();

        if($attachments->count() > 0){
            foreach ($attachments as $file) {
                $attachment = \Swift_Attachment::newInstance()
                    ->setFilename($file->getName())
                    ->setContentType($file->getMimeType())
                    ->setBody($file)
                ;
                $message->attach($attachment);

            }
        }

        $object->setMessageId($message->getId());

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($object);
        $manager->flush();

        $this->get('swiftmailer.mailer.spool_mailer')->send($message);

        $this->addFlash('sonata_flash_success', "Message ".$id." envoyé");

        return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
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
//         $object = $this->admin->getNewInstance();
//
//         $preResponse = $this->preCreate($request, $object);
//         if ($preResponse !== null) {
//             return $preResponse;
//         }
//
//         $this->admin->setSubject($object);
//
//         /** @var $form \Symfony\Component\Form\Form */
//         $form = $this->admin->getForm();
//         $form->setData($object);
//         $form->handleRequest($request);
//
//         if ($form->isSubmitted()) {
//             //TODO: remove this check for 3.0
//             if (method_exists($this->admin, 'preValidate')) {
//                 $this->admin->preValidate($object);
//             }
//             $isFormValid = $form->isValid();
//
//             // persist if the form was valid and if in preview mode the preview was approved
//             if ($isFormValid && (!$this->isInPreviewMode($request) || $this->isPreviewApproved($request))) {
//                 $this->admin->checkAccess('create', $object);
//
//                 try {
//                     $object = $this->admin->create($object);
//
//                     if ($this->isXmlHttpRequest()) {
//                         return $this->renderJson(array(
//                             'result'   => 'ok',
//                             'objectId' => $this->admin->getNormalizedIdentifier($object),
//                         ), 200, array());
//                     }
//
//                     $this->addFlash(
//                         'sonata_flash_success',
//                         $this->admin->trans(
//                             'flash_create_success',
//                             array('%name%' => $this->escapeHtml($this->admin->toString($object))),
//                             'SonataAdminBundle'
//                         )
//                     );
//
//                     // redirect to edit mode
//                     return $this->redirectTo($object);
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
//                             array('%name%' => $this->escapeHtml($this->admin->toString($object))),
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
//             'object' => $object,
//         ), null);
//     }
}

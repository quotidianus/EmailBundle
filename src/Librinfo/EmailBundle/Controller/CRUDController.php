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
        
        if (count($addresses) > 1)
        {

            $this->newsLetterSend($addresses);
        } else
        {
            $this->directSend($this->email->getFieldTo());
        }

        $this->addFlash('sonata_flash_success', "Message " . $id . " envoyé");

        return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }

    private function directSend($address)
    {
        $this->setDirectMailer();

        if ($this->email->getTracking())
        {

            $content = $this->email->getContent();
            $tracker = '<img src="http:localhost:8000/app_dev.php/librinfo/email/tracking/' . 
                $this->email->getID() . '/' . $this->email->getFieldTo() .'.png" alt="" width="1" height="1"/>';

            $this->email->setContent($content.$tracker);
            
            $this->processLinks($this->email->getContent(), $address);
            //$this->processLinks($this->email->getTextContent(), $address);
        }

        $message = $this->setupSwiftMessage($address);

        $replacements = $this->container->get('swiftdecorator.replacements');
        $decorator = new \Swift_Plugins_DecoratorPlugin($replacements);
        $this->mailer->registerPlugin($decorator);

        $this->mailer->send($message);

        $this->updateEmailEntity($message, false);
    }

    private function newsLetterSend($addresses)
    {
        $this->setSpoolMailer();

        $message = $this->setupSwiftMessage($addresses);

        $this->updateEmailEntity($message, true);

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

    public function setupSwiftMessage($to)
    {

        $message = \Swift_Message::newInstance()
                ->setSubject($this->email->getFieldSubject())
                ->setFrom($this->email->getFieldFrom())
                ->setTo($to)
                ->setBody($this->email->getContent(), 'text/html')
                ->addPart($this->email->getTextContent(), 'text/plain')
        ;
        $this->addAttachments($message);

        return $message;
    }

    private function addAttachments($message)
    {
        if (count($this->attachments) > 0)
        {
            foreach ($this->attachments as $file)
            {

                $attachment = \Swift_Attachment::newInstance()
                        ->setFilename($file->getName())
                        ->setContentType($file->getMimeType())
                        ->setBody($file)
                ;
                $message->attach($attachment);
            }
        }
    }

    private function updateEmailEntity($message, $isNewsLetter)
    {
        if ($isNewsLetter)
        {

            $this->email->setMessageId($message->getId());
        } else if (!$this->email->getIsTest())
        {

            $this->email->setSent(true);
        }
        $this->manager->persist($this->email);
        $this->manager->flush();
    }
    
    private function processLinks($content, $address)
    {
        $links = array();
   
        preg_match_all('!<a\s(.*)href="(http.*)"(.*)>(.*)</a>!U', $content, $links, PREG_SET_ORDER);
        
        foreach($links as $link){
            $content = str_replace(
              $link[0],
              '<a '.$link[1].'href="http:localhost:8000/app_dev.php/librinfo/email/tracking/'.$this->email->getId().'/'.$address.'/'.base64_encode($link[2]).'" '.$link[3].'>'.$link[4].'</a>',
              $content
            );
        }
        $this->email->setContent($content);
    }
/********************************************************************************************************************************/
/*****************               OVERRIDED  CRUD  ACTIONS                         *********************************************/
/********************************************************************************************************************************/    
    public function createAction()
    {
        $request = $this->getRequest();
        // the key used to lookup the template
        $templateKey = 'edit';

        $this->admin->checkAccess('create');

        $class = new \ReflectionClass($this->admin->hasActiveSubClass() ? $this->admin->getActiveSubClass() : $this->admin->getClass());

        if ($class->isAbstract())
        {
            return $this->render(
                            'SonataAdminBundle:CRUD:select_subclass.html.twig', array(
                        'base_template' => $this->getBaseTemplate(),
                        'admin' => $this->admin,
                        'action' => 'create',
                            ), null, $request
            );
        }

        $object = $this->admin->getNewInstance();

        $preResponse = $this->preCreate($request, $object);
        if ($preResponse !== null)
        {
            return $preResponse;
        }

        $this->admin->setSubject($object);

        /** @var $form \Symfony\Component\Form\Form */
        $form = $this->admin->getForm();
        $form->setData($object);
        $form->handleRequest($request);

        if ($form->isSubmitted())
        {
            //TODO: remove this check for 3.0
            if (method_exists($this->admin, 'preValidate'))
            {
                $this->admin->preValidate($object);
            }
            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode($request) || $this->isPreviewApproved($request)))
            {
                $this->admin->checkAccess('create', $object);

                try {
                    $object = $this->admin->create($object);
                    /** ************************************************************************************ */
                    if ($object->getIsTest())
                    {
                        $this->manager = $this->getDoctrine()->getManager();
                        $this->email = $object;
                        $this->directSend($object->getTestAdress());
                    }

                    if ($object->getIsTemplate())
                    {
                        $this->manager = $this->getDoctrine()->getManager();
                        $template = new \Librinfo\EmailBundle\Entity\EmailTemplate();
                        $template->setContent($object->getContent());
                        $template->setName($object->getNewTemplateName());
                        $this->manager->persist($template);
                        $this->manager->flush();
                    }
                    /** ************************************************************************************ */

                    if ($this->isXmlHttpRequest())
                    {
                        return $this->renderJson(array(
                                    'result' => 'ok',
                                    'objectId' => $this->admin->getNormalizedIdentifier($object),
                                        ), 200, array());
                    }

                    $this->addFlash(
                            'sonata_flash_success', $this->admin->trans(
                                    'flash_create_success', array('%name%' => $this->escapeHtml($this->admin->toString($object))), 'SonataAdminBundle'
                            )
                    );

                    // redirect to edit mode
                    return $this->redirectTo($object);
                } catch (ModelManagerException $e) {
                    $this->handleModelManagerException($e);

                    $isFormValid = false;
                }
            }

            // show an error message if the form failed validation
            if (!$isFormValid)
            {
                if (!$this->isXmlHttpRequest())
                {
                    $this->addFlash(
                            'sonata_flash_error', $this->admin->trans(
                                    'flash_create_error', array('%name%' => $this->escapeHtml($this->admin->toString($object))), 'SonataAdminBundle'
                            )
                    );
                }
            } elseif ($this->isPreviewRequested())
            {
                // pick the preview template if the form was valid and preview was requested
                $templateKey = 'preview';
                $this->admin->getShow();
            }
        }

        $view = $form->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());

        return $this->render($this->admin->getTemplate($templateKey), array(
                    'action' => 'create',
                    'form' => $view,
                    'object' => $object,
                        ), null);
    }

    public function editAction($id = null)
    {
        $request = $this->getRequest();
        // the key used to lookup the template
        $templateKey = 'edit';

        $id = $request->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object)
        {
            throw $this->createNotFoundException(sprintf('unable to find the object with id : %s', $id));
        }

        $this->admin->checkAccess('edit', $object);

        $preResponse = $this->preEdit($request, $object);
        if ($preResponse !== null)
        {
            return $preResponse;
        }

        $this->admin->setSubject($object);

        /** @var $form Form */
        $form = $this->admin->getForm();
        $form->setData($object);
        $form->handleRequest($request);

        if ($form->isSubmitted())
        {
            //TODO: remove this check for 3.0
            if (method_exists($this->admin, 'preValidate'))
            {
                $this->admin->preValidate($object);
            }
            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved()))
            {
                try {
                    $object = $this->admin->update($object);
                    /************************************************************************************************ */
                    if ($object->getIsTest())
                    {
                        $this->manager = $this->getDoctrine()->getManager();
                        $this->email = $object;
                        $this->directSend($object->getTestAdress());
                    }

                    if ($object->getIsTemplate())
                    {
                        $this->manager = $this->getDoctrine()->getManager();
                        $template = new \Librinfo\EmailBundle\Entity\EmailTemplate();
                        $template->setContent($object->getContent());
                        $template->setName($object->getNewTemplateName());
                        $this->manager->persist($template);
                        $this->manager->flush();
                    }
                    /****************************************************************************************** */
                    if ($this->isXmlHttpRequest())
                    {
                        return $this->renderJson(array(
                                    'result' => 'ok',
                                    'objectId' => $this->admin->getNormalizedIdentifier($object),
                                    'objectName' => $this->escapeHtml($this->admin->toString($object)),
                                        ), 200, array());
                    }

                    $this->addFlash(
                            'sonata_flash_success', $this->admin->trans(
                                    'flash_edit_success', array('%name%' => $this->escapeHtml($this->admin->toString($object))), 'SonataAdminBundle'
                            )
                    );

                    // redirect to edit mode
                    return $this->redirectTo($object);
                } catch (ModelManagerException $e) {
                    $this->handleModelManagerException($e);

                    $isFormValid = false;
                } catch (LockException $e) {
                    $this->addFlash('sonata_flash_error', $this->admin->trans('flash_lock_error', array(
                                '%name%' => $this->escapeHtml($this->admin->toString($object)),
                                '%link_start%' => '<a href="' . $this->admin->generateObjectUrl('edit', $object) . '">',
                                '%link_end%' => '</a>',
                                    ), 'SonataAdminBundle'));
                }
            }

            // show an error message if the form failed validation
            if (!$isFormValid)
            {
                if (!$this->isXmlHttpRequest())
                {
                    $this->addFlash(
                            'sonata_flash_error', $this->admin->trans(
                                    'flash_edit_error', array('%name%' => $this->escapeHtml($this->admin->toString($object))), 'SonataAdminBundle'
                            )
                    );
                }
            } elseif ($this->isPreviewRequested())
            {
                // enable the preview template if the form was valid and preview was requested
                $templateKey = 'preview';
                $this->admin->getShow();
            }
        }

        $view = $form->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());

        return $this->render($this->admin->getTemplate($templateKey), array(
                    'action' => 'edit',
                    'form' => $view,
                    'object' => $object,
                        ), null);
    }

}

<?php

namespace Librinfo\EmailBundle\Controller;

use Librinfo\MediaBundle\Controller\CRUDController as BaseCRUDController;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CRUDController extends BaseCRUDController
{
    /**
     *
     * @var Swift_Mailer $mailer
     */
    private $mailer;

    /**
     *
     * @var Email $email
     */
    private $email;

    /**
     *
     * @var Array $attachments
     */
    private $attachments;

    /**
     *
     * @var Boolean $isNewsLetter Wheter the email has one or more recipients
     */
    private $isNewsLetter;

    /**
     * Clones the email excluding the id and passes it to the create action wich returns the response
     *
     * @return Response
     */
    public function duplicateAction()
    {
        $id = $this->getRequest()->get($this->admin->getIdParameter());
        $email = $this->admin->getObject($id);

        $cloner = $this->container->get('librinfo_email.cloning');

        $object = $cloner->cloneEmail($email);

        return $this->createAction($object);
    }

    /**
     * Sends the email and redirects to list view keeping filter parameters
     *
     * @return RedirectResponse
     */
    public function sendAction()
    {
        $this->manager = $this->getDoctrine()->getManager();
        $id = $this->getRequest()->get($this->admin->getIdParameter());
        $this->email = $this->admin->getObject($id);
        $this->attachments = $this->email->getAttachments();
        $addresses = explode(';', $this->email->getFieldTo());
        
        // TODO: change this. An email with multiple recipients is not necessarily a newsletter
        //$this->isNewsLetter = count($addresses) > 1;
        $this->isNewsLetter = false;

        //prevent resending of an email
        if ($this->email->getSent())
        {
            $this->addFlash('sonata_flash_error', "Message " . $id . " déjà envoyé");

            return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
        }

        if ($this->isNewsLetter)
        {
            $this->newsLetterSend($addresses);
        } else
        {
            $to = explode(';', $this->email->getFieldTo());
            $cc = $this->email->getFieldCc();
            $bcc = $this->email->getFieldBcc();
            $failedRecipients = [];

            // avoid SwiftRfcComplianceException on cc and bcc
            $cc = null == $cc ? $cc : explode(';', $this->email->getFieldCc());
            $bcc = null == $bcc ? $bcc : explode(';', $this->email->getFieldBcc());
        
            $nbSent = $this->directSend($to, $cc, $bcc, $failedRecipients);
        }

        // TODO: handle $nbSent and $failedRecipients

        $this->addFlash('sonata_flash_success', "Message " . $id . " envoyé");

        return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }

    /**
     * Send an email directly (ajax call)
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AccessDeniedException, NotFoundHttpException
     */
    public function sendAjaxAction(Request $request)
    {
        $id = $request->get('id');
        $this->email = $this->admin->getObject($id);

        if (!$this->email) {
            throw $this->createNotFoundException(sprintf('unable to find the email with id : %s', $id));
        }

        // TODO: set the admin class accessMapping send property, then uncomment this:
        //$this->admin->checkAccess('send', $email);

        $this->attachments = $this->email->getAttachments();

        //prevent resending of an email
        if ($this->email->getSent())
        {
            $this->addFlash('sonata_flash_error', "Message " . $id . " déjà envoyé");

            return new JsonResponse(array(
                'status' => 'NOK',
                'sent' => true,
                'error' => 'librinfo.error.email_already_sent',
            ));
        }
        
        $to = explode(';', $this->email->getFieldTo());
        $cc = $this->email->getFieldCc();
        $bcc = $this->email->getFieldBcc();
        $failedRecipients = [];
        
        // avoid SwiftRfcComplianceException on cc and bcc
        $cc = null == $cc ? $cc : explode(';', $this->email->getFieldCc());
        $bcc = null == $bcc ? $bcc : explode(';', $this->email->getFieldBcc());

        try {
            $nbSent = $this->directSend($to, $cc, $bcc, $failedRecipients);
        } catch (\Exception $exc) {
            return new JsonResponse(array(
                'status' => 'NOK',
                'sent' => false,
                'error' => $exc->getMessage(),
            ));
        }

        return new JsonResponse(array(
            'status' => 'OK',
            'sent' => true,
            'error' => '',
            'failed_recipients' => implode(';', $failedRecipients),
        ));
    }

    /**
     * Sends the mail directly
     * @param array $to                The To addresses
     * @param array $cc                The Cc addresses (optional)
     * @param array $bcc               The Bcc addresses (optional)
     * @param array $failedRecipients  An array of failures by-reference (optional)
     *
     * @return int The number of successful recipients. Can be 0 which indicates failure
     */
    private function directSend($to, $cc = null, $bcc = null, &$failedRecipients = null)
    {
        $this->setDirectMailer();

        $message = $this->setupSwiftMessage($to, $cc, $bcc);

        $replacements = $this->container->get('librinfo_email.replacements');
        $decorator = new \Swift_Plugins_DecoratorPlugin($replacements);
        $this->mailer->registerPlugin($decorator);

        $sent = $this->mailer->send($message, $failedRecipients);
        $this->updateEmailEntity($message, false);

        return $sent;
    }

    /**
     * Spools the email
     * @param Array $addresses
     */
    private function newsLetterSend($addresses)
    {
        $this->setSpoolMailer();

        $message = $this->setupSwiftMessage($addresses);

        $this->updateEmailEntity($message, true);

        $this->mailer->send($message);
    }

    private function setDirectMailer()
    {
        $this->mailer = $this->get('swiftmailer.mailer.direct_mailer');
    }

    private function setSpoolMailer()
    {
        $this->mailer = $this->get('swiftmailer.mailer.spool_mailer');
    }

    /**
     * @param array $to   The To addresses
     * @param array $cc   The Cc addresses (optional)
     * @param array $bcc  The Bcc addresses (optional)
     * @return Swift_Message
     */
    private function setupSwiftMessage($to, $cc = null, $bcc = null)
    {
        $content = $this->email->getContent();
        $inlineAttachmentsHandler = $this->container->get('librinfo_email.inline_attachments');
        
        if (!$this->isNewsLetter && $this->email->getTracking())
        {
            $tracker = $this->container->get('librinfo_email.tracking');
            $content = $tracker->addTracking($content, $to, $this->email->getId());
        }

        $message = \Swift_Message::newInstance();
        
        $content = $inlineAttachmentsHandler->handle($content, $message);
        
        $message->setSubject(trim($this->email->getFieldSubject()))
                ->setFrom(trim($this->email->getFieldFrom()))
                ->setTo($to)
                ->setBody($content, 'text/html')
                ->addPart($this->email->getTextContent(), 'text/plain')
        ;
        
        if( !empty($cc) )
            $message->setCc($cc);
        
        if( !empty($bcc) )
            $message->setBcc($bcc);

        $this->addAttachments($message);

        return $message;
    }

    /**
     * Adds attachments to the Swift_Message
     * @param Swift_Message $message
     */
    private function addAttachments($message)
    {
        if (count($this->attachments) > 0)
        {
            foreach ($this->attachments as $file)
            {

                $attachment = \Swift_Attachment::newInstance()
                        ->setFilename($file->getName())
                        ->setContentType($file->getMimeType())
                        ->setBody($file->getFile())
                ;
                $message->attach($attachment);
            }
        }
    }

    /**
     *
     * @param Swift_Message $message
     * @param Boolean $isNewsLetter
     */
    private function updateEmailEntity($message, $isNewsLetter)
    {
        if ($isNewsLetter)
        {
            //set the id of the swift message so it can be retrieve from spool fulshQueue()
            $this->email->setMessageId($message->getId());
        } else if (!$this->email->getIsTest())
        {
            $this->email->setSent(true);
        }
        $this->manager->persist($this->email);
        $this->manager->flush();
    }

    /**
     * Adds tracking data to show view
     *
     * @param Request $request
     * @param Email $object
     * @return Response
     */
    protected function preShow(Request $request, $object)
    {
        $twigArray = array(
            'action' => 'show',
            'object' => $object,
            'elements' => $this->admin->getShow()
                )
        ;

        if ($object->getTracking())
        {
            $statHelper = $this->get('librinfo_email.stats');

            $this->admin->setSubject($object);

            $twigArray['stats'] = $statHelper->getStats($object);
        }
        return $this->render($this->admin->getTemplate('show'), $twigArray, null);
    }

    /**
     * Overrides SonataAdminBundle CRUDController
     *
     * @param Email $object
     * @return Response
     */
    public function createAction($object = Null)
    {
        $request = $this->getRequest();
        $this->manager = $this->getDoctrine()->getManager();
        // the key used to lookup the template
        $templateKey = 'edit';

        $tempId = $request->get('temp_id');

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

        $object = $object ? $object : $this->admin->getNewInstance();

        $this->handleFiles($object, $tempId);

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

                    //set email isTest to true as the checkbox is disabled in create action
                    $object->setIsTest(true);

                    $this->handleTest($object);

                    $this->handleTemplate($object);

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

    /**
     * Overrides SonataAdminBundle CRUDController
     *
     * @param type $id
     * @return type
     * @throws type
     */
    public function editAction($id = null)
    {
        $request = $this->getRequest();
        $tempId = $request->get('temp_id');
        $this->manager = $this->getDoctrine()->getManager();
        // the key used to lookup the template
        $templateKey = 'edit';

        $id = $request->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        $this->handleFiles($object, $tempId);

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
                    /*                     * ********************************************************************************************** */
                    $this->handleTest($object);

                    $this->handleTemplate($object);
                    /*                     * **************************************************************************************** */
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

    /**
     * Handles sending of the test Email
     *
     * @param Email $email
     */
    protected function handleTest($email)
    {

        if ($email->getIsTest() && $email->getTestAddress())
        {
            $this->email = $email;
            $this->directSend($email->getTestAddress());
        }
    }

    /**
     * Handle creation of the template from email content
     *
     * @param Email $email
     */
    protected function handleTemplate($email)
    {

        if ($email->getIsTemplate() && $email->getNewTemplateName())
        {
            $template = new \Librinfo\EmailBundle\Entity\EmailTemplate();
            $template->setContent($email->getContent());
            $template->setName($email->getNewTemplateName());
            $this->manager->persist($template);
            $this->manager->flush();
        }
    }

    public function listAction()
    {
        $request = $this->getRequest();

        $this->admin->checkAccess('list');

        $preResponse = $this->preList($request);
        if ($preResponse !== null) {
            return $preResponse;
        }

        if ($listMode = $request->get('_list_mode')) {
            $this->admin->setListMode($listMode);
        }

        $datagrid = $this->admin->getDatagrid();
        $formView = $datagrid->getForm()->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());


        return $this->render($this->admin->getTemplate('list'), array(
            'action'     => 'list',
            'form'       => $formView,
            'datagrid'   => $datagrid,
            'csrf_token' => $this->getCsrfToken('sonata.batch'),
        ), null, $request);
    }

    /**
     * Redirect the user depend on this choice.
     *
     * @param object $object
     *
     * @return RedirectResponse
     */
    protected function redirectTo($object)
    {
        $request = $this->getRequest();
        $url = false;
        $from_admin = $request->get('from_admin'); // admin code
        $from_id = $request->get('from_id');
        if ($from_admin !== null && $from_id !== null) {
            $admin = $this->get($from_admin);
            $from_object = $admin->getObject($from_id);
            if ($admin->isGranted('SHOW', $from_object))
                $url = $admin->generateObjectUrl('show', $from_object);
        }

        if ($url)
            return new RedirectResponse($url);

        return parent::redirectTo($object);
    }

}

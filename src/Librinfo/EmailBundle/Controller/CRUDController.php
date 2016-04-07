<?php

namespace Librinfo\EmailBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as SonataCRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class CRUDController extends SonataCRUDController
{
    public function sendAction(Request $request)
    {
        $id      = $request->get($this->admin->getIdParameter());

       $this->addFlash('sonata_flash_success', $id);

        return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }
}

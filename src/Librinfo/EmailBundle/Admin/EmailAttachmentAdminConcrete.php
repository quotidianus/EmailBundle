<?php

namespace Librinfo\EmailBundle\Admin;

use Librinfo\CoreBundle\Admin\Traits\EmbeddedAdmin;

class EmailAttachmentAdminConcrete extends EmailAttachmentAdmin
{

    use EmbeddedAdmin;

    private $container;
    private $rootDir;
    private $uploadDir;

    public function prePersist($attachment)
    {
        $this->upload($attachment);
    }

    public function preUpdate($attachment)
    {
        $this->upload($attachment);
    }

    public function preRemove($attachment)
    {
        $this->remove($attachment);
    }

    protected function getAbsolutePath()
    {
        return $this->rootDir . "/../web/uploads/" . $this->uploadDir;
    }

    protected function upload($attachment)
    {
//        $file = $attachment->getFile();
//        if (null != $file)
//        {
//            $attachment->setSize($file->getClientSize());
//            $attachment->setName($file->getClientOriginalName());
//            $attachment->setMimeType($file->getMimeType());
//            $attachment->setPath(sha1(uniqid(mt_rand(), true)) . '.' . $file->guessClientExtension());
//            // if there is an error when moving the file, an exception will
//            // be automatically thrown by move(). This will properly prevent
//            // the entity from being persisted on error
//            $file->move($this->getAbsolutePath(), $attachment->getPath());
//            $attachment->setFile(null);
//        }
    }

    protected function remove($attachment)
    {
        $file = $this->getAbsolutePath() . '/' . $attachment->getPath();
        if ($file)
        {
            unlink($file);
        }
    }

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->container = $container;
        $this->setUpPathInfo($container);
    }

    protected function setUpPathInfo($container)
    {
        $this->rootDir = $container->getParameter('kernel.root_dir');
        $this->uploadDir = $container->getParameter('librinfo_email.upload_path');
    }

}

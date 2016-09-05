<?php

namespace Librinfo\EmailBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Monolog\Logger;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class CRMBundleListener implements LoggerAwareInterface, EventSubscriber
{
    /**
     * @var array
     */
    private $bundles;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'loadClassMetadata',
            //'prePersist',
            //'preUpdate',
        ];
    }

    /**
     * define mapping with Organism, Postion and Contact at runtime if LibrinfoCRMBundle exists
     *
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        if ( !array_key_exists('LibrinfoCRMBundle', $this->bundles) )
            return;

        /** @var ClassMetadata $metadata */
        $metadata = $eventArgs->getClassMetadata();
        if ($metadata->getName() != 'Librinfo\EmailBundle\Entity\Email')
            return;
        $this->logger->debug("[CRMBundleListener] Entering CRMBundleListener for « loadClassMetadata » event");

        // mapping with Organism entity (many-to-many owning side)
        $metadata->mapManyToMany([
            'targetEntity' => 'Librinfo\CRMBundle\Entity\Organism',
            'fieldName'    => 'organisms',
            'inversedBy'   => 'emailMessages',
            'joinTable'    => [
                'name'               => 'librinfo_email_email__organism',
                'joinColumns'        => ['email_id' => ['referencedColumnName' => 'id']],
                'inverseJoinColumns' => ['organism_id'    => ['referencedColumnName' => 'id']],
            ]
        ]);

        // mapping with Position entity (many-to-many owning side)
        $metadata->mapManyToMany([
            'targetEntity' => 'Librinfo\CRMBundle\Entity\Position',
            'fieldName'    => 'positions',
            'inversedBy'   => 'emailMessages',
            'joinTable'    => [
                'name'               => 'librinfo_email_email__position',
                'joinColumns'        => ['email_id' => ['referencedColumnName' => 'id']],
                'inverseJoinColumns' => ['position_id'    => ['referencedColumnName' => 'id']],
            ]
        ]);

        // mapping with Contact entity (many-to-many owning side)
        $metadata->mapManyToMany([
            'targetEntity' => 'Librinfo\CRMBundle\Entity\Contact',
            'fieldName'    => 'contacts',
            'inversedBy'   => 'emailMessages',
            'joinTable'    => [
                'name'               => 'librinfo_email_email__contact',
                'joinColumns'        => ['email_id' => ['referencedColumnName' => 'id']],
                'inverseJoinColumns' => ['contact_id'    => ['referencedColumnName' => 'id']],
            ]
        ]);

        $this->logger->debug("[CRMBundleListener] Added CRM mapping metadata to Entity", ['class' => $metadata->getName()]);
    }

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     * @return null
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function setBundles($kernelBundles)
    {
        $this->bundles = $kernelBundles;
    }

}

<?php

namespace Librinfo\EmailBundle\Block;

use Doctrine\ORM\EntityManager;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\TextBlockService;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\Response;

class EmailsListBlock extends TextBlockService
{
    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @param EntityManager $manager
     */
    public function setManager(EntityManager $manager) {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();
        $targetEntity = $settings['target_entity'];
        $maxResults = $settings['max_results'];
        $emails = $this->getEmails($targetEntity, $maxResults);


        return $this->renderResponse($blockContext->getTemplate(), array(
            'block' => $blockContext->getBlock(),
            'settings' => $settings,
            'emails' => $emails,
        ), $response);
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'content' => 'Insert your custom content here',
            'template' => 'LibrinfoEmailBundle:Block:block_emails_list.html.twig',
            'target_entity' => null,
            'max_results' => 20,
        ));
    }

    /**
     * @param object $targetEntity
     * @param int $maxResults
     * @return array
     * @throws \Exception
     */
    private function getEmails($targetEntity, $maxResults)
    {
        if (!$targetEntity || !is_object($targetEntity))
            return [];

        if (!method_exists($targetEntity, 'getEmailRecipients'))
            throw new \Exception(sprintf('Class %s does not provide a "getEmailRecipients" method.', get_class($targetEntity)));

        $recipients = $targetEntity->getEmailRecipients();

        $repo = $this->manager->getRepository('Librinfo\EmailBundle\Entity\Email');
        $qb = $repo->createQueryBuilder('e')
            ->where('e.field_to IN (:recipients)')
            ->orWhere('e.field_cc IN (:recipients)')
            ->orWhere('e.field_bcc IN (:recipients)')
            ->orderBy('e.updatedAt', 'desc')
            ->setMaxResults($maxResults)
            ->setParameter('recipients', $recipients)
        ;
        return $qb->getQuery()->getResult();
    }


}


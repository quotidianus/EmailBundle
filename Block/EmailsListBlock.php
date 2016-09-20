<?php

namespace Librinfo\EmailBundle\Block;

use Doctrine\ORM\EntityManager;
use Librinfo\EmailBundle\Entity\Email;
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
        $rc = new \ReflectionClass($targetEntity);
        $email = new Email;
        if (!in_array($rc->getName(), $email->getExternallyLinkedClasses()))
            return [];
        $targets = strtolower($rc->getShortName()) . 's'; // ex. contacts

        $repo = $this->manager->getRepository('Librinfo\EmailBundle\Entity\Email');
        $qb = $repo->createQueryBuilder('e')
            ->orderBy('e.updatedAt', 'desc')
            ->setMaxResults($maxResults)
        ;

        // TODO: remove this
        if (false && get_class($targetEntity) == 'Librinfo\CRMBundle\Entity\Organism')
            $qb->leftJoin ('e.organisms', 'org')
                ->leftJoin ('e.positions', 'pos')
                ->where('org.id = :targetid')
                ->orWhere($qb->expr()->andX(
                    $qb->expr()->eq('pos.organism', ':targetid'),
                    $qb->expr()->isNotNull('pos.id')))
                ->setParameter('targetid', $targetEntity->getId())
            ;
        else
            $qb->leftJoin('e.'.$targets , 't')
                ->where('t.id = :targetid')
                ->setParameter('targetid', $targetEntity->getId())
            ;
        return $qb->getQuery()->getResult();
    }


}


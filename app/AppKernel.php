<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),

            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),

            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Knp\DoctrineBehaviors\Bundle\DoctrineBehaviorsBundle(),

            new JMS\AopBundle\JMSAopBundle(),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),

            new FOS\UserBundle\FOSUserBundle(),

            new JeroenDesloovere\Bundle\VCardBundle\JeroenDesloovereVCardBundle(),

            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Sonata\UserBundle\SonataUserBundle('FOSUserBundle'),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new Sonata\IntlBundle\SonataIntlBundle(),

            new Librinfo\CoreBundle\LibrinfoCoreBundle(),
            new Librinfo\DoctrineBundle\LibrinfoDoctrineBundle(),
            new Librinfo\DecoratorBundle\LibrinfoDecoratorBundle(),
            new Librinfo\UIBundle\LibrinfoUIBundle(),
            new Librinfo\BaseEntitiesBundle\LibrinfoBaseEntitiesBundle(),
            new Librinfo\SecurityBundle\LibrinfoSecurityBundle(),
            new Librinfo\CRMBundle\LibrinfoCRMBundle(),
            new Librinfo\UserBundle\LibrinfoUserBundle(),
            new Librinfo\DoctrinePgsqlBundle\LibrinfoDoctrinePgsqlBundle(),
            new Librinfo\EmailBundle\LibrinfoEmailBundle(),

            new Stfalcon\Bundle\TinymceBundle\StfalconTinymceBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test')))
        {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir() . '/config/config_' . $this->getEnvironment() . '.yml');
    }
}

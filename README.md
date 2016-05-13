# SymfonyLibrinfoEmailBundle


## About

 The Libre Informatique *EmailBundle* leverages Swiftmailer and the Libre Informatique *CoreBundle* to provide seemless email and newsletter functionalities.
 Features include database spooling, configurable spool flush command, email openings and link clicks tracking along with stats display, inline attachments, test send, duplication, ... 

## Installation

``` $ composer require libre-informatique/email-bundle ```

```php
// app/AppKernel.php
// ...
public function registerBundles()
{
    $bundles = array(
        // ...
            
        // The libre-informatique bundles
        new Librinfo\EmailBundle\LibrinfoEmailBundle(),
            
        // your personal bundles
    );
}
```
Don't forget to *install the assets* as file upload and other features rely heavily on javascript

## Configuration

First, make sure to configure the bundles LibrinfoEmailBundle depends on properly.

The Sonata bundles
------------------

Do not forget to configure the SonataAdminBundle. e.g.:

```php
    // app/AppKernel.php
    // ...
    public function registerBundles()
    {
        $bundles = array(
            // ...
            
            // Sonata
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),
            
            // your personal bundles
        );
    }
    // ...
```

```
# app/config/routing.yml
admin:
    resource: '@SonataAdminBundle/Resources/config/routing/sonata_admin.xml'
    prefix: /
  
_sonata_admin:
    resource: .
    type: sonata_admin
    prefix: /
```

```
# app/config/config.yml
sonata_block:
    default_contexts: [cms]
    blocks:
        # Enable the SonataAdminBundle block
        sonata.admin.block.admin_list:
            contexts:   [admin]
        # Your other blocks
```

But please, refer to the source doc to get up-to-date :
https://sonata-project.org/bundles/admin/2-3/doc/reference/installation.html

Just notice that the ```prefix``` value is ```/``` instead of ```/admin``` as advised by the Sonata Project... By the way, it means that this access is universal, and not a specific "backend" interface. That's a specificity of a software package that intends to be focused on professional workflows.

Add the custom form field template to your conifg.yml

# app/config/config.yml
twig:
    debug:            "%kernel.debug%"
    strict_variables: "%kernel.debug%"
    form:
        resources:
            - 'LibrinfoEmailBundle:form:fields.html.twig'
```
You have to configure two mailers as follows in order to use the database spooling feature

```
# app/config/config.yml

swiftmailer:
    default_mailer: direct_mailer
    mailers:
        direct_mailer:
            transport: "%mailer_transport%"
            host:      "%mailer_host%"
            username:  "%mailer_user%"
            password:  "%mailer_password%"
        spool_mailer:
            transport: "%mailer_transport%"
            host:      "%mailer_host%"
            username:  "%mailer_user%"
            password:  "%mailer_password%"
            spool: { type: db }
```



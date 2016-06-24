# README

## Introduction

CogitowebUserBundle is a plug and play SonataUserBundle extension with the following setup:

* Intended for being used with PostgreSQL, but config can be edited easily
* Extends FOSUserBundle
* FOSUserBundle routes are overridden with SonataUserBundle routes
* Handles SonataAdminBundle related routes only
* Firewall comes with *switch_user* and *remember_me* features
* User is redirected to Sonata Admin dashboard upon successful login
* User's profile dashboard is not configured (yet)
* PdoSessionHandler

Please note that the following options are **disabled**:

* Security ACL
* Two-step validation
* FOSUser APIs

For further informations, please refer to [SonataUserBundle documentation][1].

The pourpose of this bundle is to reduce the effort to configure SonataUserBundle from scratch everytime a new project is started.
The aim is reached by gathering common settings in fewer (2) configuration files, so that the developer just have to import such files
in main configs to have the system ready.

## Installation

Add the repository in `repositories` section of your project's `composer.json`,
so that composer becomes aware of CogitowebUserBundle existance

```json
    ...
    "repositories": [
        ...
        {
            "type": "vcs",
            "url": "https://github.com/cogitoweb/UserBundle"
        }
    ],
    ...
```

And install the package

    $ composer require cogitoweb/user-bundle

## Enable bundle

Like all other bundles, to enable CogitowebUserBundle add it in `app/AppKernel.php`, along with its dependencies

```php
            ...
            new FOS\UserBundle\FOSUserBundle(),
            new Sonata\UserBundle\SonataUserBundle('FOSUserBundle'),
	        new Cogitoweb\UserBundle\CogitowebUserBundle(),
            ...
```

## Configuration

As stated in the introduction of this document, CogitowebUserBundle `config.yml` must be imported in `app/config/config.yml` main configuration

```yaml
imports:
    ...
    - { resource: "@CogitowebUserBundle/Resources/config/config.yml" }
```

Unfortunately, due to adverse parameters merging, it is necessary to comment the session handler ID parameter in the same file

```yaml
#        handler_id:  ~
```

Or set it to

```yaml
        handler_id: session.handler.pdo
```

If you do not follow the previous step, the PDO session handling feature enabled by CogitowebUserBundle will not be available
because the parameter imported from the bundle is overridden with ~ (null).

Furthermore, it is necessary to add a placeholder in `app/config/security.yml` for CogitowebUserBundle *admin* firewall
because the exception

> InvalidConfigurationException in PrototypedArrayNode.php line 311:
> You are not allowed to define new elements for path "security.firewalls". Please define all elements for this path in one config file.

is thrown otherwise. For further informations, please refer to [this][2] GitHub page.

```yaml
    firewalls:
        dev:
            ...

        # Cogitoweb: placeholder for CogitowebUserBundle admin firewall
        admin:

        main:
            ...
```

## Routing

Like configuration, also routing `routing.yml` file must be imported in `app/config/routing.yml`

```yaml
...
cogitoweb_user:
    resource: "@CogitowebUserBundle/Resources/config/routing.yml"
```

## Clear cache and update database

System is almost ready. Just perform a clear cache and update database to match the entities

```
    $ php app/console cache:clear
    $ php app/console doctrine:schema:update --force
```

Beware that PdoSessionHandler does not use Doctrine ORM, therefore the *sessions* table it needs was not created with the previous command.
You can find ready-to-run SQL scripts in `vendor/cogitoweb/user-bundle/data/sql/PdoSessionHandler/` suitable for most common DBMS distributions.
Just execute the one that fits your database to have the *sessions* table available to PdoSessionHandler.

## Conclusions

At this point you should be prompted for username and password when visiting your [project's Admin][3].

Need to configure an account? Please, visit [FOSUserBundle command line tools][4] for further informations.

## Customization

All the parameters set by CogitowebUserBundle can be easily overridden (i.e. to be project-specific) in `app/config/config.yml`.
For example, if you want to use MySQL you can apply the following override

<pre>
	services.session.handler.pdo.arguments: [ "<b>mysql</b>:host=%database_host%;dbname=%database_name%", { db_username: "%database_user%", db_password: "%database_password%" } ]
</pre>

Please, refer to [SonataAdminBundle documentation][5] to edit front-end elements (images, stylesheets and javascripts) of login screen.

[1]: https://sonata-project.org/bundles/user/master/doc/index.html
[2]: https://github.com/symfony/symfony/issues/16517
[3]: http://localhost:8000/admin
[4]: https://symfony.com/doc/master/bundles/FOSUserBundle/command_line_tools.html
[5]: https://sonata-project.org/bundles/admin/master/doc/index.html
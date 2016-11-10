# README

## Introduction

CogitowebUserBundle is a plug and play SonataUserBundle extension with the
following setup:

* Intended for being used with PostgreSQL, but config can be edited easily
* Extends FOSUserBundle
* FOSUserBundle routes are overridden with SonataUserBundle routes
* Handles SonataAdminBundle related routes only
* Firewall comes with *switch_user* and *remember_me* features
* User is redirected to Sonata Admin dashboard upon successful login
* User's profile dashboard is not configured (yet)
* PdoSessionHandler

Notice that the following options are **disabled**:

* Security ACL
* Two-step validation
* FOSUser APIs

For further informations, please refer to [SonataUserBundle documentation][1].

The pourpose of this bundle is to reduce the effort to configure
SonataUserBundle from scratch everytime a new project is started.
The aim is reached by gathering common settings in fewer (2) configuration
files, so that the developer just have to import such files in main configs to
have the system ready.

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

```
$ composer require cogitoweb/user-bundle
```

## Enable bundle

Like all other bundles, to enable CogitowebUserBundle add it in
`app/AppKernel.php`, along with its dependencies

```php
            ...
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
			new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Sonata\UserBundle\SonataUserBundle('FOSUserBundle'),
	        new Cogitoweb\UserBundle\CogitowebUserBundle(),
            ...
```

## Configuration

As stated in the introduction of this document, CogitowebUserBundle `config.yml`
must be imported in `app/config/config.yml` main configuration

```yaml
imports:
    ...
    - { resource: "@CogitowebUserBundle/Resources/config/config.yml" }
```

Unfortunately, due to adverse parameters merging,
it is necessary to comment the session handler ID parameter in the same file

```yaml
#        handler_id:  ~
```

Or set it to

```yaml
        handler_id: session.handler.pdo
```

If you do not follow the previous step, the PDO session handling feature enabled
by CogitowebUserBundle will not be available because the parameter imported from
the bundle is overridden with ~ (null).

Furthermore, it is mandatory to add a placeholder in `app/config/security.yml`
for CogitowebUserBundle *admin* firewall because the exception

> InvalidConfigurationException in PrototypedArrayNode.php line 311:
> You are not allowed to define new elements for path "security.firewalls". Please define all elements for this path in one config file.

is thrown otherwise. For further informations, refer to [this][2] GitHub page.

```yaml
    firewalls:
        dev:
            ...

        # Cogitoweb: placeholder for CogitowebUserBundle admin firewall
        admin:

        main:
            ...
```

Still in `app/config/security.yml`, it is necessary to configure the basic
access control paths. This setup cannot be done in CogitowebUserBundle because
it would prevent developers from adding custom paths.
In fact, if **access_control** is declared twice, the exception 

> ForbiddenOverwriteException in BaseNode.php line 223:
> Configuration path "security.access_control" cannot be overwritten. You have to define all options for this path, and any of its sub-paths in one configuration section.

is thrown.

```yaml
    access_control:
        # Admin login page needs to be accessed without credential
        - { path: ^/admin/login$,       role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/admin/logout$,      role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/admin/login_check$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/admin/resetting,    role: IS_AUTHENTICATED_ANONYMOUSLY }

        # Secured part of the site
        # This config requires being logged for the whole site and having the admin role for the admin part.
        # Change these rules to adapt them to your needs
        - { path: ^/admin/, role: [ROLE_ADMIN, ROLE_SONATA_ADMIN] }
```

## Routing

Like configuration, also routing `routing.yml` file must be imported in
`app/config/routing.yml`

```yaml
...
cogitoweb_user:
    resource: "@CogitowebUserBundle/Resources/config/routing.yml"
```

## Generate entities

At this point the bundle is working, but `User` and `Group` entities must be generated in order to use it.
This task can be easily performed with EasyExtends. The following command will automatically create the entities mentioned above
in `src/Application/Cogitoweb/UserBundle/` directory

```
$ php app/console sonata:easy-extends:generate CogitowebUserBundle -d src/
```

Mapping informations of generated entities are stored in XML format in `src/Application/Cogitoweb/UserBundle/Resources/config/doctrine/`.
These mappings can be converted to another notation (annotations or YAML, i.e.)
and `src/Application/Cogitoweb/UserBundle/Resources/config/doctrine/` folder deleted.

Update FOSUserBundle configuration to use the new entities. Import the configuration file made available by CogitowebUserBundle
for this purpose in `app/config/config.yml`

```yaml
imports:
    ...
    - { resource: "@CogitowebUserBundle/Resources/config/config_extended_entities.yml" }
```

Register the generated bundle in `app/AppKernel.php`

```php
            ...
	        new Application\Cogitoweb\UserBundle\ApplicationCogitowebUserBundle(),
            ...
```


## Clear cache and update database

System is almost ready. Just perform a clear cache and update database to match
the entities

```
$ php app/console cache:clear
$ php app/console doctrine:schema:update --force
```

Beware that PdoSessionHandler does not use Doctrine ORM,
therefore the *sessions* table it needs was not created with the previous
command.
You can find ready-to-run SQL scripts in
`vendor/cogitoweb/user-bundle/data/sql/PdoSessionHandler/`
suitable for most common DBMS distributions.
Just execute the one that fits your database to have the *sessions* table
available to PdoSessionHandler.

## Credentials expiry

CogitowebUserBundle extends this feature provided by FOSUserBundle:
it ignores the CredentialsExpiredException by overriding the [UserChecker][3]
and allowing the user to authenticate, then the [CredentialsExpired subscriber][4]
redirects (and locks) the user to change password page if its credentials are expired.
After the credentials are updated, the overridden [ChangePasswordFormHandler][5]
updates the `credentialsExpired` and `credentialsExpireAt` User properties.

By default, User's `credentialsExpireAt` property is set to `null` so that, once updated,
credentials will never expire. To change this behaviour, developer can change the
`credentials_expire_at` parameter in `app/config/config.yml`

```yaml
...
cogitoweb_user:
    change_password:
        credentials_expire_at: +1 month # Format suitable for PHP DateTime() class.
```

## Conclusions

At this point you should be prompted for username and password when visiting
your [project's Admin][6].

Need to configure an account? Visit [FOSUserBundle command line tools][7] for
further informations.

## Customization

All the parameters set by CogitowebUserBundle can be easily overridden
(i.e. to be project-specific) in `app/config/config.yml`.
For example, if you want to use MySQL you can apply the following override

<pre>
	services.session.handler.pdo.arguments: [ "<b>mysql</b>:host=%database_host%;dbname=%database_name%", { db_username: "%database_user%", db_password: "%database_password%" } ]
</pre>

Refer to [SonataAdminBundle documentation][8] to edit front-end elements
(images, stylesheets and javascripts) of login screen.

[1]: https://sonata-project.org/bundles/user/master/doc/index.html
[2]: https://github.com/symfony/symfony/issues/16517
[3]: https://github.com/cogitoweb/UserBundle/tree/master/Security/UserChecker.php
[4]: https://github.com/cogitoweb/UserBundle/tree/master/EventSubscriber/CredentialsExpiredSubscriber.php
[5]: https://github.com/cogitoweb/UserBundle/tree/master/Form/Handler/ChangePasswordFormHandler.php
[6]: http://localhost:8000/admin
[7]: https://symfony.com/doc/master/bundles/FOSUserBundle/command_line_tools.html
[8]: https://sonata-project.org/bundles/admin/master/doc/index.html
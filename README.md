MisdRavenBundle
===============

[![Build Status](https://secure.travis-ci.org/misd-service-development/raven-bundle.png)](http://travis-ci.org/misd-service-development/raven-bundle)

This bundle contains a Symfony2 authentication provider so that users can log in to a Symfony2 application through [Raven](http://raven.cam.ac.uk/), the University of Cambridge's central authentication service.

It also contains a user provider, which can allow any user authenticating through Raven access to your application.

Authors
-------

* Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>

The bundle uses code based on the [UcamWebauth PHP class](https://wiki.cam.ac.uk/raven/PHP_library).

Requirements
------------

* [Symfony 2.1](http://symfony.com/)
* [PHP OpenSSL library](http://www.php.net/manual/en/book.openssl.php)

Installation
------------

 1. Add the bundle to your dependencies:

        // composer.json

        {
           // ...
           "require": {
               // ...
               "misd/raven-bundle": "dev-master"
           }
        }

 2. Use Composer to download and install the bundle:

        $ php composer.phar update misd/raven-bundle

 3. Register the bundle in your application:

        // app/AppKernel.php

        class AppKernel extends Kernel
        {
            // ...
            public function registerBundles()
            {
                $bundles = array(
                    // ...
                    new Misd\RavenBundle\MisdRavenBundle(),
                    // ...
                );
            }
            // ...
        }

Configuration
-------------

### Firewall

To enable Raven authentication, add `raven: true` to a firewall configuration:

    // app/config/security.yml

    security:
        firewalls:
            raven_secured:
                pattern: ^/secure/.*
                raven: true

### User provision

Normal Symfony2 user providers can be used, as long as the username is the user's CRSid.

If you would like any user who has successfully authenticated with Raven to access your application, you can use the bundle's Raven user provider:

    // app/config/security.yml

    security:
        providers:
            raven:
                id: raven_user_provider

The user provider returns an instance of `Misd\RavenBundle\Security\User\RavenUser` with the role `ROLE_USER`.

This can be chained with other providers to grant certain users extra roles. For example:

    // app/config/security.yml

    security:
        providers:
            chain_provider:
                chain:
                    providers: [in_memory, raven]
            in_memory:
                memory:
                    users:
                        abc123: { roles: [ 'ROLE_ADMIN' ] }
            raven:
                id: raven_user_provider

### Resource description

You can add the name of your application to the Raven log in page:

    // app/config/config.yml

    misd_raven:
        description: "My application"

The text on the page will now include something like "This resource calls itself 'My application' and is ...".

### Test Raven service

During development, especially when not on the University network, it is sometimes necessary to use the [test Raven Service](http://raven.cam.ac.uk/project/test-demo/). You can use this instead of the live service:

    // app/config/config_dev.yml

    misd_raven:
        use_test_service: true

The test Raven service **must not** be used in production: it might compromise your application. Keep it to `config_dev.yml`!

Exceptions
----------

The bundle can throw various exceptions. To catch them, set up [event listeners](http://symfony.com/doc/2.1/cookbook/service_container/event_listener.html) and implement your logic (display a message, redirect to another page etc).

### `Misd\RavenBundle\Exception\AuthenticationCancelledException`

This is thrown if the user clicks 'cancel' on the Raven log in screen.

### `Symfony\Component\Security\Core\Exception\UsernameNotFoundException`

This is thrown if the user is not provisioned. If you're using the Raven user provider, this will never been seen.

### `Misd\RavenBundle\Exception\RavenException`

This is thrown if something has gone wrong with either the bundle or Raven itself. As this is an exceptional state, you probably won't need to catch it (and let the `500 Internal Server Error` be returned). It has the following sub-types:

* `Misd\RavenBundle\Exception\LoginTimedOutException`: If the Raven response is older than 30 seconds.

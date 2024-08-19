<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Knp\Bundle\MenuBundle\KnpMenuBundle;
use Presta\MailReceiverBundle\PrestaMailReceiverBundle;
use Psr\Log\NullLogger;
use Sonata\AdminBundle\SonataAdminBundle;
use Sonata\BlockBundle\SonataBlockBundle;
use Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle;
use Sonata\Twig\Bridge\Symfony\SonataTwigBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\HttpKernel\Log\Logger;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new TwigBundle();
        yield new SecurityBundle();
        yield new DoctrineBundle();
        yield new KnpMenuBundle();
        yield new SonataAdminBundle();
        yield new SonataTwigBundle();
        yield new SonataBlockBundle();
        yield new SonataDoctrineORMAdminBundle();
        yield new PrestaMailReceiverBundle();
    }

    public function getProjectDir(): string
    {
        return \dirname(__DIR__);
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->services()->set('logger', Logger::class)
            ->args(['$output' => '%kernel.logs_dir%/test.log'])
            ->public(true);

        $container->extension('framework', [
            'test' => true,
            'secret' => '$ecretf0rt3st',
            'session' => [
                'handler_id' => null,
                'cookie_secure' => 'auto',
                'cookie_samesite' => 'lax',
                'storage_factory_id' => 'session.storage.factory.mock_file',
            ],
            'mailer' => [
                'dsn' => 'null://null',
            ],
        ]);
        $container->extension('security', [
            'firewalls' => [
                'main' => [
                    'pattern' => '^/',
                    'security' => false,
                ],
            ],
        ]);
        $container->extension('doctrine', [
            'dbal' => [
                'url' => 'sqlite:///%kernel.project_dir%/var/database.sqlite',
                'logging' => false,
            ],
            'orm' => [
                'auto_generate_proxy_classes' => true,
                'naming_strategy' => 'doctrine.orm.naming_strategy.underscore',
                'mappings' => [
                    'PrestaMailReceiverBundle' => [
                        'is_bundle' => false,
                        'type' => 'attribute',
                        'dir' => '%kernel.project_dir%/src/Entity',
                        'prefix' => 'Presta\\MailReceiverBundle\\Entity',
                        'alias' => 'PrestaMailReceiverBundle',
                    ],
                ],
            ],
        ]);
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('@SonataAdminBundle/Resources/config/routing/sonata_admin.xml');
        $routes->import('.', 'sonata_admin');
    }
}

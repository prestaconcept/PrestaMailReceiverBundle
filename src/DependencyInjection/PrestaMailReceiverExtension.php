<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\DependencyInjection;

use Presta\MailReceiverBundle\Rule\Action\RuleActionInterface;
use Presta\MailReceiverBundle\Rule\Condition\RuleConditionInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class PrestaMailReceiverExtension extends Extension
{
    /**
     * @param array<array<string, mixed>> $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('presta.mail_receiver.archive', $config['archive']);

        //todo before going OS : convert YML to XML
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $container->registerForAutoconfiguration(RuleConditionInterface::class)
            ->addTag('presta.mail_receiver.rule_condition');
        $container->registerForAutoconfiguration(RuleActionInterface::class)
            ->addTag('presta.mail_receiver.rule_action');

        $this->registerFormTheme($container);
    }

    private function registerFormTheme(ContainerBuilder $container): void
    {
        $resources = $container->hasParameter('twig.form.resources') ?
            $container->getParameter('twig.form.resources') : [];

        \array_unshift($resources, '@PrestaMailReceiver/form/template.html.twig');
        $container->setParameter('twig.form.resources', $resources);
    }
}

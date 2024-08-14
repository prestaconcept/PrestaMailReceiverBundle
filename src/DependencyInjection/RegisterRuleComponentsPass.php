<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\DependencyInjection;

use Presta\MailReceiverBundle\Rule\ActionRegistry;
use Presta\MailReceiverBundle\Rule\ConditionRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterRuleComponentsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $this->register($container, 'presta.mail_receiver.rule_condition', ConditionRegistry::class);
        $this->register($container, 'presta.mail_receiver.rule_action', ActionRegistry::class);
    }

    private function register(ContainerBuilder $container, string $tag, string $registryId): void
    {
        $indexed = [];
        foreach ($container->findTaggedServiceIds($tag) as $serviceId => $tags) {
            foreach ($tags as $attributes) {
                $code = $attributes['code'] ?? $this->codeFromServiceId($serviceId);
                $indexed[$code] = new Reference($serviceId);
            }
        }

        $container->getDefinition($registryId)->setArgument(0, $indexed);
    }

    private function codeFromServiceId(string $serviceId): string
    {
        if (strpos($serviceId, '\\') && class_exists($serviceId)) {
            return (new \ReflectionClass($serviceId))->getShortName();
        }

        //todo
        return $serviceId;
    }
}

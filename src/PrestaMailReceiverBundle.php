<?php

namespace Presta\MailReceiverBundle;

use Presta\MailReceiverBundle\DependencyInjection\RegisterRuleComponentsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class PrestaMailReceiverBundle extends Bundle
{
    /**
     * @inheritdoc
     */
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterRuleComponentsPass());
    }
}

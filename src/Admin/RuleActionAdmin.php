<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Admin;

use Presta\MailReceiverBundle\Entity\RuleAction;
use Presta\MailReceiverBundle\Form\Type\RuleActionVirtualType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * @extends AbstractAdmin<RuleAction>
 */
final class RuleActionAdmin extends AbstractAdmin
{
    /**
     * @inheritdoc
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->clear(); //this admin has no route, it's mean to be embedded in RuleAdmin
    }

    /**
     * @inheritdoc
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $action = $this->getSubject();

        $form
            ->add('sortOrder', HiddenType::class, ['attr' => ['hidden' => true]])
            ->add('element', RuleActionVirtualType::class, ['entity' => $action])
            ->add('breakpoint', null, ['label' => 'rule.form.label.breakpoint']);
        ;
    }
}

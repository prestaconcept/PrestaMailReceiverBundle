<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Admin;

use Presta\MailReceiverBundle\Entity\RuleCondition;
use Presta\MailReceiverBundle\Form\Type\RuleConditionVirtualType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * @extends AbstractAdmin<RuleCondition>
 */
final class RuleConditionAdmin extends AbstractAdmin
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
        $condition = $this->getSubject();

        $form
            ->add('sortOrder', HiddenType::class, ['attr' => ['hidden' => true]])
            ->add('element', RuleConditionVirtualType::class, ['entity' => $condition])
        ;
    }
}

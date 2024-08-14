<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Admin;

use Presta\MailReceiverBundle\Entity\RuleGroup;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\Form\Type\CollectionType;

/**
 * @extends AbstractAdmin<RuleGroup>
 */
class RuleGroupAdmin extends AbstractAdmin
{
    protected function configureRoutes(RouteCollectionInterface $routes): void
    {
        $routes->remove('export')
            ->remove('show');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('name', null, [
            'label' => 'rule_group.list.label.name',
        ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form->add(
            'name',
            null,
            [
                'label' => 'rule_group.form.label.name',
            ]
        )
            ->add(
                'rulesElements',
                CollectionType::class,
                [
                    'label' => 'rule_group.form.label.rules_elements',
                    'by_reference' => false,
                ],
                [
                    'delete' => true,
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable' => 'sortOrder',
                ]
            );
    }
}

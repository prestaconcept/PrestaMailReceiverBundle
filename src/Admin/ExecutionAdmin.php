<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Admin;

use Presta\MailReceiverBundle\Entity\Execution;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * @extends AbstractAdmin<Execution>
 */
final class ExecutionAdmin extends AbstractAdmin
{
    /**
     * @inheritdoc
     */
    public function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues[DatagridInterface::SORT_BY] = 'date';
        $sortValues[DatagridInterface::SORT_ORDER] = 'DESC';
    }

    /**
     * @inheritdoc
     */
    public function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->clearExcept(['list', 'show'])
            ->add('downloadEvaluationError', $this->getRouterIdParameter() . '/evaluation/{evaluationId}/error/download')
            ->add('downloadResultError', $this->getRouterIdParameter() . '/result/{resultId}/error/download');
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('email', null, [
                'label' => 'execution.filter.label.email',
            ])
            ->add('rule', null, [
                'label' => 'execution.filter.label.rule',
            ])
            ->add('satisfied', null, [
                'label' => 'execution.filter.label.satisfied',
            ])
            ->add('performed', null, [
                'label' => 'execution.filter.label.performed',
            ])
            ->add('date', null, [
                'label' => 'execution.filter.label.date',
            ]);
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('email', null, [
                'label' => 'execution.list.label.email',
            ])
            ->add('rule', null, [
                'label' => 'execution.list.label.rule',
            ])
            ->add('satisfied', null, [
                'label' => 'execution.list.label.satisfied',
            ])
            ->add('performed', null, [
                'label' => 'execution.list.label.performed',
            ])
            ->add('date', null, [
                'label' => 'execution.list.label.date',
            ]);
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->with('execution.show.fieldset.general')
                ->add('email', null, [
                    'label' => 'execution.show.label.email',
                ])
                ->add('rule', null, [
                    'label' => 'execution.show.label.rule',
                ])
                ->add('satisfied', null, [
                    'label' => 'execution.show.label.satisfied',
                ])
                ->add('performed', null, [
                    'label' => 'execution.show.label.performed',
                ])
                ->add('date', null, [
                    'label' => 'execution.show.label.date',
                ])
            ->end()
            ->with('execution.show.fieldset.evaluations', ['class' => 'col-md-6'])
                ->add('evaluations', null, [
                    'label' => false,
                    'template' => '@PrestaMailReceiver/Execution/show-evaluations.html.twig',
                ])
            ->end()
            ->with('execution.show.fieldset.results', ['class' => 'col-md-6'])
                ->add('results', null, [
                    'label' => false,
                    'template' => '@PrestaMailReceiver/Execution/show-results.html.twig',
                ])
            ->end()
        ;
    }
}

<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Admin;

use Knp\Menu\ItemInterface as MenuItemInterface;
use Presta\MailReceiverBundle\Entity\Email;
use Presta\MailReceiverBundle\Entity\Execution;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * @extends AbstractAdmin<Email>
 */
final class EmailAdmin extends AbstractAdmin
{
    /**
     * @inheritdoc
     */
    public function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues[DatagridInterface::SORT_BY] = 'sentAt';
        $sortValues[DatagridInterface::SORT_ORDER] = 'DESC';
        $sortValues['setStatus'] = 'EDIT';
    }

    /**
     * @inheritdoc
     */
    public function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->clearExcept(['list', 'batch', 'show', 'delete']);
        $collection->add('downloadAttachment', 'download/{id}/{filename}');
        $collection->add('testDispatchEmail', 'test-dispatch/{id}');
        $collection->add('dispatchEmail', 'dispatch/{id}');
    }

    /**
     * @inheritdoc
     */
    protected function configureTabMenu(MenuItemInterface $menu, string $action, AdminInterface $childAdmin = null): void
    {
        if (in_array($action, ['show', 'edit'], true)) {
            $menu->addChild(
                'email_received.menu.executions',
                [
                    'uri' => $this->getConfigurationPool()->getAdminByClass(Execution::class)
                        ->generateUrl('list', ['filter' => ['email' => ['value' => $this->getSubject()->getId()]]]),
                ]
            );
        }
    }

    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('subject', null, [
                'label' => 'email_received.filter.label.subject',
            ])
            ->add('sender', null, [
                'label' => 'email_received.filter.label.sender',
            ])
            ->add('recipient', null, [
                'label' => 'email_received.filter.label.recipient',
            ])
            ->add('body', null, [
                'label' => 'email_received.filter.label.body',
            ])
            ->add('status', null, [
                'label' => 'email_received.filter.label.status',
            ])
        ;
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('subject', null, [
                'label' => 'email_received.list.label.subject',
            ])
            ->add('sender', null, [
                'label' => 'email_received.list.label.sender',
            ])
            ->add('recipient', null, [
                'label' => 'email_received.list.label.recipient',
            ])
            ->add('body', null, [
                'label' => 'email_received.list.label.body',
            ])
            ->add('status', null, [
                'label' => 'email_received.list.label.status',
                'template' => '@PrestaMailReceiver/Email/status_list_field.html.twig'
            ])
            ->add('sentAt', 'date', [
                'label' => 'email_received.list.label.sent_at',
                'format' => 'd-m-Y'
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'dispatchEmail' => [
                        'template' => '@PrestaMailReceiver/CRUD/list__action_dispatch_email.html.twig',
                    ],
                    'testDispatchEmail' => [
                        'template' => '@PrestaMailReceiver/CRUD/list__action_test_dispatch_email.html.twig'
                    ]
                ]
            ])
        ;
    }

    /**
     * @inheritdoc
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('subject', null, [
                'label' => 'email_received.form.label.subject',
            ])
            ->add('sender', null, [
                'label' => 'email_received.form.label.sender',
            ])
            ->add('recipient', null, [
                'label' => 'email_received.form.label.recipient',
            ])
            ->add('body', null, [
                'label' => 'email_received.form.label.body',
            ])
            ->add('raw', null, [
                'label' => 'email_received.form.label.raw',
            ])
        ;
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->tab('email_received.show.label.general')
                ->with('email_received.show.label.general')
                    ->add('subject', null, [
                        'label' => 'email_received.show.label.subject',
                    ])
                    ->add('sender', null, [
                        'label' => 'email_received.show.label.sender',
                    ])
                    ->add('recipient', null, [
                        'label' => 'email_received.show.label.recipient',
                    ])
                    ->add('body', null, [
                        'label' => 'email_received.show.label.body',
                    ])
                    ->add(
                        'attachmentsName',
                        null,
                        [
                            'template' => '@PrestaMailReceiver/Email/attachments_list.html.twig',
                            'label' => 'email_received.show.label.attachments',
                        ]
                    )
                ->end()
            ->end()
            ->tab('email_received.show.label.headers')
                ->with('email_received.show.label.headers')
                    ->add(
                        'headers',
                        null,
                        ['template' => '@PrestaMailReceiver/Email/display_headers.html.twig', 'label' => false]
                    )
                ->end()
            ->end()
        ;
    }

    /**
     * @inheritdoc
     */
    protected function configureBatchActions($actions): array
    {
        $actions = parent::configureBatchActions($actions);
        if ($this->hasAccess('setStatus')) {
            $actions['setStatus'] = [
                'label' => 'email_received.batch.set_status',
                'template' => '@PrestaMailReceiver/Email/batch-set-status.html.twig',
            ];
        }

        return $actions;
    }
}

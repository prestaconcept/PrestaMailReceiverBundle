<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Admin;

use Knp\Menu\ItemInterface as MenuItemInterface;
use Presta\MailReceiverBundle\Entity\Execution;
use Presta\MailReceiverBundle\Entity\Rule;
use Presta\MailReceiverBundle\Rule\ActionRegistry;
use Presta\MailReceiverBundle\Rule\ComponentWithHelpInterface;
use Presta\MailReceiverBundle\Rule\ComponentWithLabelInterface;
use Presta\MailReceiverBundle\Rule\ConditionRegistry;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * @extends AbstractAdmin<Rule>
 */
final class RuleAdmin extends AbstractAdmin
{
    public function __construct(
        string $code,
        string $class,
        string $baseControllerName,
        private ConditionRegistry $conditions,
        private ActionRegistry $actions,
    ) {
        parent::__construct($code, $class, $baseControllerName);
    }
    /**
     * @inheritdoc
     */
    public function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues[DatagridInterface::SORT_BY] = 'name';
        $sortValues[DatagridInterface::SORT_ORDER] = 'ASC';
    }

    /**
     * @inheritDoc
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->add('conditionAdd', $this->getRouterIdParameter() . '/conditions/add/{type}')
            ->add('actionAdd', $this->getRouterIdParameter() . '/actions/add/{type}')
            ->remove('show')
        ;
    }

    /**
     * @inheritdoc
     */
    protected function configureTabMenu(MenuItemInterface $menu, string $action, AdminInterface $childAdmin = null): void
    {
        if (in_array($action, ['edit'], true)) {
            $menu->addChild(
                'rule.menu.executions',
                [
                    'uri' => $this->getConfigurationPool()->getAdminByClass(Execution::class)
                        ->generateUrl('list', ['filter' => ['rule' => ['value' => $this->getSubject()->getId()]]]),
                ]
            );
        }

        if ($action !== 'edit') {
            return;
        }

        $conditionMenu = $menu->addChild(
            $this->getLabelTranslatorStrategy()->getLabel('conditions', 'rule', 'label'),
            ['attributes' => ['dropdown' => true]]
        );
        $conditionMenu->setExtra('translation_domain', $this->getTranslationDomain());

        foreach ($this->conditions->list() as $code) {
            $condition = $this->conditions->get($code);

            $uri = $this->generateObjectUrl('conditionAdd', $this->getSubject(), ['type' => $code]);

            $label = $this->getLabelTranslatorStrategy()->getLabel($code, 'rule.condition', 'label');

            if ($condition instanceof ComponentWithLabelInterface) {
                $label = $condition->label();
            }
            $title = null;
            if ($condition instanceof ComponentWithHelpInterface) {
                $title = $condition->help();
            }

            $conditionMenu->addChild($label, ['uri' => $uri, 'attributes' => ['title' => $title]]);
        }

        $actionMenu = $menu->addChild(
            $this->getLabelTranslatorStrategy()->getLabel('actions', 'rule', 'label'),
            ['attributes' => ['dropdown' => true]]
        );
        $actionMenu->setExtra('translation_domain', $this->getTranslationDomain());

        foreach ($this->actions->list() as $code) {
            $action = $this->actions->get($code);

            $uri = $this->generateObjectUrl('actionAdd', $this->getSubject(), ['type' => $code]);
            $label = $this->getLabelTranslatorStrategy()->getLabel($code, 'rule.action', 'label');
            if ($action instanceof ComponentWithLabelInterface) {
                $label = $action->label();
            }
            $title = null;
            if ($action instanceof ComponentWithHelpInterface) {
                $title = $action->help();
            }

            $actionMenu->addChild($label, ['uri' => $uri, 'attributes' => ['title' => $title]]);
        }
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('name', null, [
                'label' => 'rule.list.label.name',
            ])
            ->add(
                '_conditions',
                null,
                [
                    'template' => '@PrestaMailReceiver/ruleAdmin/ruleConditionDescription.html.twig',
                    'label' => 'rule.list.label.conditions',
                ]
            )
            ->add(
                '_actions',
                'actions',
                [
                    'template' => '@PrestaMailReceiver/ruleAdmin/ruleActionDescription.html.twig',
                    'label' => 'rule.list.label.actions',
                ]
            )
            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    /**
     * @inheritdoc
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $rule = $this->getSubject();

        $form
            ->tab('rule.form.fieldset.general')
                ->with('rule.form.fieldset.general')
                    ->add('name', null, [
                        'label' => 'rule.form.label.name',
                        'help' => 'rule.form.help.name',
                        'translation_domain' => 'PrestaMailReceiverBundle',
                    ])
                ->end()
            ->end();

        if ($rule->getId() !== null) {
            $form
                ->tab('rule.form.fieldset.conditions')
                    ->with('rule.form.fieldset.conditions')
                        ->add(
                            'conditionOperator',
                            ChoiceType::class,
                            [
                                'choices' => Rule::OPERATORS,
                                'label' => 'rule.form.label.condition_operator',
                                'choice_label' => function (string $choice): string {
                                    return "rule.form.choice.condition_operator.{$choice}";
                                },
                                'choice_translation_domain' => $this->getTranslationDomain(),
                            ]
                        )
                        ->add(
                            'conditions',
                            CollectionType::class,
                            [
                                'by_reference' => false,
                                'label' => false,
                            ],
                            [
                                'delete' => true,
                                'edit' => 'inline',
                                'inline' => 'table',
                                'sortable' => 'sortOrder',
                            ]
                        )
                    ->end()
                ->end()
                ->tab('rule.form.fieldset.actions')
                    ->with('rule.form.fieldset.actions')
                        ->add(
                            'actions',
                            CollectionType::class,
                            [
                                'by_reference' => false,
                                'label' => false,
                                'help' => 'rule.form.help.breakpoint',
                                'translation_domain' => 'PrestaMailReceiverBundle',
                            ],
                            [
                                'delete' => true,
                                'edit' => 'inline',
                                'inline' => 'table',
                                'sortable' => 'sortOrder',
                            ]
                        )
                    ->end()
                ->end();
        }
    }
}

<?php

namespace Presta\MailReceiverBundle\Twig;

use InvalidArgumentException;
use Presta\MailReceiverBundle\Entity\RuleAction;
use Presta\MailReceiverBundle\Entity\RuleCondition;
use Presta\MailReceiverBundle\Rule\ActionNotRegisteredException;
use Presta\MailReceiverBundle\Rule\ActionRegistry;
use Presta\MailReceiverBundle\Rule\ComponentWithDescriptionInterface;
use Presta\MailReceiverBundle\Rule\ConditionNotRegisteredException;
use Presta\MailReceiverBundle\Rule\ConditionRegistry;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class DescribeExtension extends AbstractExtension
{
    public function __construct(private ActionRegistry $actions, private ConditionRegistry $conditions)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('presta_describe_component', function (object $component): string {
                if ($component instanceof RuleAction) {
                    return $this->describeAction($component->getType(), $component->getSettings());
                } elseif ($component instanceof RuleCondition) {
                    return $this->describeCondition($component->getType(), $component->getSettings());
                }

                throw new InvalidArgumentException('Unknown component');
            }),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('presta_describe_action', [$this, 'describeAction']),
            new TwigFunction('presta_describe_condition', [$this, 'describeCondition']),
        ];
    }

    /**
     * @param array<string, mixed> $settings
     */
    public function describeAction(string $type, array $settings): string
    {
        try {
            $action = $this->actions->get($type);
        } catch (ActionNotRegisteredException $exception) {
            return "[$type]";
        }

        return $this->describe($action, $type, $settings);
    }

    /**
     * @param array<string, mixed> $settings
     */
    public function describeCondition(string $type, array $settings): string
    {
        try {
            $condition = $this->conditions->get($type);
        } catch (ConditionNotRegisteredException $exception) {
            return "[$type]";
        }

        return $this->describe($condition, $type, $settings);
    }

    /**
     * @param array<string, mixed> $settings
     */
    private function describe(object $component, string $type, array $settings): string
    {
        if ($component instanceof ComponentWithDescriptionInterface) {
            return $component->describe($settings);
        }

        return sprintf('[%s] (%s)', $type, json_encode($settings));
    }
}

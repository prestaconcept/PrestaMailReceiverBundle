<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Controller;

use Presta\MailReceiverBundle\Entity\Rule;
use Presta\MailReceiverBundle\Entity\RuleAction;
use Presta\MailReceiverBundle\Entity\RuleCondition;
use Presta\MailReceiverBundle\Rule\ActionNotRegisteredException;
use Presta\MailReceiverBundle\Rule\ActionRegistry;
use Presta\MailReceiverBundle\Rule\ComponentWithSettingsInterface;
use Presta\MailReceiverBundle\Rule\ConditionNotRegisteredException;
use Presta\MailReceiverBundle\Rule\ConditionRegistry;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @extends CRUDController<Rule>
 */
final class RuleController extends CRUDController
{
    public function __construct(private ConditionRegistry $conditions, private ActionRegistry $actions)
    {
    }

    public function conditionAddAction(string $type, Request $request): Response
    {
        $id = $request->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);
        if (!$object instanceof Rule) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        try {
            $condition = $this->conditions->get($type);
        } catch (ConditionNotRegisteredException $exception) {
            throw $this->createNotFoundException('Not found', $exception);
        }

        $settings = [];
        if ($condition instanceof ComponentWithSettingsInterface) {
            $settings = $condition->defaults();
        }

        $condition = new RuleCondition();
        $condition->setType($type);
        $condition->setSettings($settings);
        $object->addCondition($condition);

        $this->admin->update($object);

        return $this->redirectTo($request, $this->admin->getSubject());
    }

    public function actionAddAction(string $type, Request $request): Response
    {
        $id = $request->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);
        if (!$object instanceof Rule) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        try {
            $action = $this->actions->get($type);
        } catch (ActionNotRegisteredException $exception) {
            throw $this->createNotFoundException('Not found', $exception);
        }

        $settings = [];
        if ($action instanceof ComponentWithSettingsInterface) {
            $settings = $action->defaults();
        }

        $action = new RuleAction();
        $action->setType($type);
        $action->setSettings($settings);
        $object->addAction($action);

        $this->admin->update($object);

        return $this->redirectTo($request, $this->admin->getSubject());
    }
}

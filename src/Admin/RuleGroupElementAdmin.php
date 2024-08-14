<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Admin;

use Presta\MailReceiverBundle\Entity\Rule;
use Presta\MailReceiverBundle\Entity\RuleGroupElement;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * @extends AbstractAdmin<RuleGroupElement>
 */
class RuleGroupElementAdmin extends AbstractAdmin
{
    /**
     * @inheritdoc
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form->add('sortOrder', HiddenType::class, ['attr' => ['hidden' => true]])
            ->add(
                'rule',
                EntityType::class,
                ['class' => Rule::class, 'choice_label' => 'name', 'label' => 'rule_group.elements.form.label.rules']
            )
            ->add('breakpoint', null, ['label' => 'rule_group.elements.form.label.breakpoint']);
    }
}

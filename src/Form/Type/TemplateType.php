<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Form\Type;

use Presta\MailReceiverBundle\Form\Type\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TemplateType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['template'] = $options['template'];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['template']);
    }
}

<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Form\Type;

use Symfony\Component\Form\AbstractType as BaseAbstractType;

abstract class AbstractType extends BaseAbstractType
{
    public function getBlockPrefix(): string
    {
        return 'presta_mail_receiver_' . parent::getBlockPrefix();
    }
}

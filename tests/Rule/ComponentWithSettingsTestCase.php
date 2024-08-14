<?php

namespace Presta\MailReceiverBundle\Tests\Rule;

use Presta\MailReceiverBundle\Form\Type\SettingsType;
use Presta\MailReceiverBundle\Rule\ComponentWithSettingsInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\Extension\Validator\EventListener\ValidationListener;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Extension\Validator\ViolationMapper\ViolationMapper;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

abstract class ComponentWithSettingsTestCase extends TypeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->dispatcher = new EventDispatcher();
        $this->dispatcher->addSubscriber(
            new ValidationListener(Validation::createValidator(), new ViolationMapper())
        );
    }

    protected function getExtensions()
    {
        return [new ValidatorExtension(Validation::createValidator())];
    }

    protected function form(ComponentWithSettingsInterface $component): FormInterface
    {
        return $this->factory->create(SettingsType::class, [], ['configurator' => $component->configurator()]);
    }

    public static function assertValidSettings(FormInterface $form, array $settings): void
    {
        $form->submit($settings);
        self::assertTrue($form->isValid(), 'Settings form is valid');
        self::assertCount(0, $form->getErrors(true, true), 'Settings form has no errors');
    }

    public static function assertInvalidSettings(FormInterface $form, array $settings): void
    {
        $form->submit($settings);
        self::assertFalse($form->isValid(), 'Settings form is not valid');
        self::assertNotCount(0, $form->getErrors(true, true), 'Settings form has errors');
    }
}

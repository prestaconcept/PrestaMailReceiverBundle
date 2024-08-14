<?php

namespace Presta\MailReceiverBundle\Rule\Action;

use Presta\MailReceiverBundle\Entity\Email;
use Presta\MailReceiverBundle\Rule\Action\InvalidSettingsException;
use Presta\MailReceiverBundle\Rule\Action\RuleActionInterface;
use Presta\MailReceiverBundle\Rule\ComponentWithDescriptionInterface;
use Presta\MailReceiverBundle\Rule\ComponentWithSettingsInterface;
use Presta\MailReceiverBundle\Rule\SettingsConfiguratorInterface;
use RuntimeException;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as MimeEmail;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

final class Notify implements RuleActionInterface, ComponentWithSettingsInterface, ComponentWithDescriptionInterface
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(MailerInterface $mailer, TranslatorInterface $translator)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
    }

    public function handle(Email $email, array $settings = []): void
    {
        $to = $settings['to'] ?? null;
        if ($to === null) {
            throw InvalidSettingsException::missing('to', $settings);
        }

        $from = $settings['from'] ?? null;
        if ($from === null) {
            throw InvalidSettingsException::missing('from', $settings);
        }

        $message = (new MimeEmail())
            ->subject('Vous avez recu un nouveau mail')
            ->html($email->getBody())
            ->from($from)
            ->to($to);

        try {
            $this->mailer->send($message);
        } catch (Throwable $exception) {
            //todo use library exception
            throw new RuntimeException('The notification cannot be sent', 0, $exception);
        }
    }

    public function defaults(): array
    {
        return ['to' => null, 'from' => 'recrutement@prestaconcept.net']; //todo random translated email
    }

    public function configurator(): SettingsConfiguratorInterface
    {
        return new class() implements SettingsConfiguratorInterface {
            public function configure(FormBuilderInterface $builder): void
            {
                $builder->add(
                    'to',
                    EmailType::class,
                    [
                        'required' => true,
                        'translation_domain' => 'PrestaMailReceiverBundle',
                        'label' => 'rule.form.label.to',
                        'help' => 'rule.form.help.to',
                        'constraints' => [
                            new Assert\NotNull(),
                            new Assert\Email(['mode' => Assert\Email::VALIDATION_MODE_STRICT]),
                        ],
                    ]
                )
                    ->add(
                        'from',
                        EmailType::class,
                        [
                            'required' => true,
                            'translation_domain' => 'PrestaMailReceiverBundle',
                            'label' => 'rule.form.label.from',
                            'constraints' => [
                                new Assert\NotNull(),
                                new Assert\Email(['mode' => Assert\Email::VALIDATION_MODE_STRICT]),
                            ],
                        ]
                    );
            }
        };
    }

    public function describe(array $settings = []): string
    {
        return $this->translator->trans('rule.description.actions.notify', ['%from%' => $settings['from'], '%to%' => $settings['to']], 'PrestaMailReceiverBundle');
    }
}

<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use PhpMimeMailParser\Attachment;
use PhpMimeMailParser\Parser;
use Presta\MailReceiverBundle\Repository\EmailRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EmailRepository::class)]
#[ORM\Table(name: 'presta_mail_receiver_email')]
class Email
{
    public const STATUS_WAITING = 'waiting';
    public const STATUS_TREATED = 'treated';
    public const STATUS_UNMATCHED = 'unmatched';
    public const STATUS_ERRORED = 'errored';

    private const STATUS_PRIORITY = [self::STATUS_WAITING, self::STATUS_UNMATCHED, self::STATUS_ERRORED, self::STATUS_TREATED];

    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'subject', type: 'string', length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private $subject;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'sender', type: 'string', length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private $sender;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'recipient', type: 'string', length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private $recipient;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'body', type: 'text', nullable: false)]
    private $body;

    /**
     * @var string
     */
    #[ORM\Column(name: 'raw', type: 'text', nullable: false)]
    private $raw;

    /**
     * @var DateTime
     */
    #[ORM\Column(name: 'sent_at', type: 'datetime', nullable: false)]
    private $sentAt;

    /**
     * @var string
     */
    #[ORM\Column(name: 'status', type: 'string', length: 255, nullable: false)]
    protected $status = self::STATUS_WAITING;

    private function __construct(
        ?string $subject,
        ?string $sender,
        ?string $recipient,
        ?string $body,
        ?DateTime $sentAt,
        string $raw
    ) {
        $this->subject = $subject;
        $this->sender = $sender;
        $this->body = $body;
        $this->recipient = $recipient;
        $this->raw = $raw;
        $this->sentAt = $sentAt ?: new DateTime();
    }

    public static function fromRaw(string $raw): self
    {
        $parser = (new Parser())->setText($raw);

        return new self(
            $parser->getHeader('subject') ?: null,
            $parser->getAddresses('from')[0]['address'] ?? null,
            $parser->getAddresses('to')[0]['address'] ?? null,
            $parser->getMessageBody(),
            $parser->getHeader('date') ? new DateTime($parser->getHeader('date')) : null,
            $parser->getData()
        );
    }

    public function __toString(): string
    {
        return (string) $this->subject;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function getSender(): ?string
    {
        return $this->sender;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function getRecipient(): ?string
    {
        return $this->recipient;
    }

    public function getRaw(): string
    {
        return $this->raw;
    }

    public function getSentAt(): DateTime
    {
        return $this->sentAt;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return array<string, mixed>
     */
    public function getHeaders(): array
    {
        $parser = $this->getParser();

        return $parser->getHeaders();
    }

    /**
     * @return array<string>
     */
    public function getAttachmentsName(): array
    {
        $parser = $this->getParser();

        $filename = function (Attachment $attachment): string {
            return $attachment->getFilename();
        };

        $attachments = $parser->getAttachments();

        return array_map($filename, $attachments);
    }

    public function getAttachmentByName(string $name): ?Attachment
    {
        $parser = $this->getParser();
        $attachments = $parser->getAttachments();
        foreach ($attachments as $attachment) {
            if ($attachment->getFilename() === $name) {
                return $attachment;
            }
        }

        return null;
    }

    /**
     * During the processing of the email status cannot regress if it has, at least,
     * match a condition the email cannot be marked as 'unmatched'.
     * if at least one action is applied to the email it cannot be marked as 'errored' anymore
     */
    public function updateStatus(string $status): void
    {
        if (!in_array($status, self::STATUS_PRIORITY, true)) {
            throw new \RuntimeException('Status must be one of the Email status constant');
        }

        $priority = array_flip(self::STATUS_PRIORITY);

        if ($priority[$status] > $priority[$this->status]) {
            $this->status = $status;
        }
    }

    private function getParser(): Parser
    {
        return (new Parser())->setText($this->raw);
    }
}

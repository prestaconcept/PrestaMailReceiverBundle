<?php

namespace Presta\MailReceiverBundle\Tests\Fixtures;

use Presta\MailReceiverBundle\Entity\Email;

final class Emails
{
    public static function cheerz(): Email
    {
        return self::fromFile(__DIR__ . '/cheerz.eml');
    }

    public static function cuteCatPicture(): Email
    {
        return self::fromFile(__DIR__ . '/cute-cat-attachment.eml');
    }

    private static function fromFile(string $file): Email
    {
        return Email::fromRaw(file_get_contents($file));
    }
}

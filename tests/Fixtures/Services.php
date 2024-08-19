<?php

namespace Presta\MailReceiverBundle\Tests\Fixtures;

use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

final class Services
{
    private const TRANSLATION_CATALOG = __DIR__ . '/../../translations/PrestaMailReceiverBundle.fr.yaml';

    private static $services = [];

    public static function translator(): TranslatorInterface
    {
        if (!isset(self::$services[__FUNCTION__])) {
            self::$services[__FUNCTION__] = $translator = new Translator('fr_FR');
            $translator->addLoader('yaml', new YamlFileLoader());
            $translator->addResource('yaml', self::TRANSLATION_CATALOG, 'fr_FR', 'PrestaMailReceiverBundle');
        }

        return self::$services[__FUNCTION__];
    }
}

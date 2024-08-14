<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Tests\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Presta\MailReceiverBundle\Tests\DatabaseTestHelper;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Field\InputFormField;

abstract class AdminTestCase extends WebTestCase
{
    protected static ?KernelBrowser $client = null;
    protected static ?EntityManagerInterface $doctrine = null;

    protected function setUp(): void
    {
        parent::setUp();

        self::$client = self::createClient();
        self::$doctrine = self::getContainer()->get('doctrine.orm.default_entity_manager');
        DatabaseTestHelper::rebuild(self::getContainer());
    }

    protected function assertPageContainsCountElement(int $count, Crawler $page): void
    {
        self::assertCount($count, $page->filter('.sonata-ba-list > tbody > tr'));
    }

    protected static function submitSonataMainForm(Crawler $page, array $values): Crawler
    {
        $form = self::getSonataMainForm($page);
        $formName = self::getSonataFormName($form);
        $namedValues = [];
        foreach ($values as $name => $value) {
            $namedValues[$formName . '[' . $name . ']'] = $value;
        }

        $form = $form->form();

        // Le champ CSRF n'est pas un enfant direct du formulaire dans Sonata
        // mais le champ existe, alors on va le chercher pour l'ajouter manuellement
        $csrfTokenName = $formName . '[_token]';
        $csrfToken = $page->filter('input[name="' . $csrfTokenName . '"]');
        if ($csrfToken) {
            $form->set(new InputFormField($csrfToken->getNode(0)));
        }

        return self::$client->submit($form, $namedValues);
    }

    protected static function getSonataMainForm(Crawler $page): Crawler
    {
        return $page->filter('form[action^="' . \parse_url($page->getUri())['path'] . '"]');
    }

    protected static function getSonataFormName(Crawler $form): string
    {
        \parse_str(\parse_url($form->attr('action'))['query'], $output);

        return $output['uniqid'] ?? throw new \LogicException('Sonata form is missing "uniqid" in action URI');
    }
}

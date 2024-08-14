<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\DependencyInjection;

use DateTimeImmutable;
use Presta\MailReceiverBundle\Entity\Email;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    private const STATUSES_ARCHIVE_DEFAULTS = [
        Email::STATUS_WAITING => '1 month',
        Email::STATUS_TREATED => '3 days',
        Email::STATUS_UNMATCHED => '4 month',
        Email::STATUS_ERRORED => '2 weeks',
    ];

    public function getConfigTreeBuilder(): TreeBuilder
    {
        /** @var ArrayNodeDefinition $root */
        $root = ($tree = new TreeBuilder('presta_mail_receiver'))->getRootNode();

        $root
            ->addDefaultsIfNotSet()
            ->children()
            ->append($this->archive())
            ->end();

        return $tree;
    }

    private function archive(): ArrayNodeDefinition
    {
        /** @var ArrayNodeDefinition $archive */
        $archive = (new TreeBuilder('archive'))->getRootNode();
        $archive->addDefaultsIfNotSet();

        $invalidDateModifier = function ($value): bool {
            if (!is_string($value)) {
                return true;
            }
            /** @var DateTimeImmutable|false $dateTime */
            $dateTime = (new DateTimeImmutable())->modify($value);

            return $dateTime === false;
        };

        foreach (self::STATUSES_ARCHIVE_DEFAULTS as $status => $keep) {
            $archive->children()->scalarNode($status)
                ->defaultValue($keep)
                ->info("A date modifier that indicates how many time emails with \"{$status}\" status will be kept.")
                ->validate()
                ->ifTrue($invalidDateModifier)
                ->thenInvalid('Invalid date modifier.');
        }

        return $archive;
    }
}

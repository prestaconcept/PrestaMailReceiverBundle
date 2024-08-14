<?php

namespace Presta\MailReceiverBundle\Rule\Condition\Match;

use Presta\MailReceiverBundle\Rule\Condition\Match\MatchSettings;
use Presta\MailReceiverBundle\Rule\Condition\Match\MatchSettingsConfigurator;
use Presta\MailReceiverBundle\Rule\Condition\Match\MatchSettingsDescriptor;

trait MatchDependenciesTrait
{
    /**
     * @var MatchSettings
     */
    private $match;

    /**
     * @var MatchSettingsConfigurator
     */
    private $settings;

    /**
     * @var MatchSettingsDescriptor
     */
    private $descriptor;

    public function __construct(
        MatchSettings $match,
        MatchSettingsConfigurator $settings,
        MatchSettingsDescriptor $descriptor
    ) {
        $this->match = $match;
        $this->settings = $settings;
        $this->descriptor = $descriptor;
    }
}

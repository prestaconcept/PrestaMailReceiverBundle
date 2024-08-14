<?php

namespace Presta\MailReceiverBundle\Rule;

interface ComponentWithDescriptionInterface
{
    /**
     * @param array<string, mixed> $settings
     *
     * @return string
     */
    public function describe(array $settings = []): string;
}

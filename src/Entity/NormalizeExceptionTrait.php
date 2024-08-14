<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Entity;

use Throwable;

trait NormalizeExceptionTrait
{
    /**
     * @param Throwable $exception
     *
     * @return array<string, mixed>
     */
    private function normalizeException(Throwable $exception): array
    {
        $normalized = [
            'class' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ];

        if ($exception->getPrevious()) {
            $normalized['previous'] = $this->normalizeException($exception->getPrevious());
        }

        return $normalized;
    }
}

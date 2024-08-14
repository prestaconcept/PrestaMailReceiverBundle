<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Controller;

use Presta\MailReceiverBundle\Entity\Execution;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @extends CRUDController<Execution>
 */
final class ExecutionController extends CRUDController
{
    public function downloadEvaluationErrorAction(int $evaluationId, Request $request): Response
    {
        $id = $request->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);
        if (!$object instanceof Execution) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        foreach ($object->getEvaluations() as $evaluation) {
            if ($evaluation->getId() !== $evaluationId) {
                continue;
            }

            return new JsonResponse($evaluation->getError());
        }

        throw $this->createNotFoundException();
    }

    public function downloadResultErrorAction(int $resultId, Request $request): Response
    {
        $id = $request->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);
        if (!$object instanceof Execution) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        foreach ($object->getResults() as $result) {
            if ($result->getId() !== $resultId) {
                continue;
            }

            return new JsonResponse($result->getError());
        }

        throw $this->createNotFoundException();
    }
}

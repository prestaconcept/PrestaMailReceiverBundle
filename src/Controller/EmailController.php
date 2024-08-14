<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Controller;

use Presta\MailReceiverBundle\Dispatcher\MailDispatcher;
use Presta\MailReceiverBundle\Dispatcher\MailTestConditionsDispatcher;
use Presta\MailReceiverBundle\Entity\Email;
use Presta\MailReceiverBundle\Storage\ExecutionStorage;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @extends CRUDController<Email>
 */
final class EmailController extends CRUDController
{
    public function __construct(
        private MailDispatcher $mailDispatcher,
        private ExecutionStorage $executionStorage,
        private MailTestConditionsDispatcher $testConditionsDispatcher,
    ) {
    }

    /**
     * @param ProxyQueryInterface<Email> $selectedModelQuery
     */
    public function batchActionSetStatus(ProxyQueryInterface $selectedModelQuery, Request $request): Response
    {
        $this->admin->checkAccess('setStatus');

        $status = $request->get('status');

        if ($status === null) {
            $this->addFlash('sonata_flash_info', $this->setStatusFlashMessage('no_status'));

            return $this->redirectToList();
        }

        /** @var Email[] $emails */
        $emails = $selectedModelQuery->execute();

        try {
            foreach ($emails as $email) {
                $email->setStatus($status);
                $this->admin->getModelManager()->update($email);
            }
        } catch (Throwable $exception) {
            $this->addFlash('sonata_flash_error', $this->setStatusFlashMessage('error'));

            return $this->redirectToList();
        }

        $this->addFlash('sonata_flash_success', $this->setStatusFlashMessage('success'));

        return $this->redirectToList();
    }

    public function downloadAttachmentAction(string $filename): BinaryFileResponse
    {
        /** @var Email $email */
        $email = $this->admin->getSubject();
        $attachment = $email->getAttachmentByName($filename);
        if ($attachment === null) {
            $this->createNotFoundException(sprintf('attachment with given name %s cannot be found', $filename));
        }

        $attachmentPath = $attachment->save(sys_get_temp_dir());

        $response = $this->file($attachmentPath);
        $response->deleteFileAfterSend(true);

        return $response;
    }

    public function dispatchEmailAction(): RedirectResponse
    {
        $this->admin->checkAccess('edit');
        /** @var Email $email */
        $email = $this->admin->getSubject();
        $email->setStatus(Email::STATUS_UNMATCHED);

        $execution = $this->mailDispatcher->dispatchEmail($email);
        $this->executionStorage->store($execution);

        $this->addFlash('sonata_flash_success', $this->trans($this->trans("email_received.flash.email_dispatched", [], $this->admin->getTranslationDomain())));

        return new RedirectResponse(
            $this->generateUrl(
                'admin_presta_mailreceiver_execution_list',
                ['filter' => ['email' => ['value' => $email->getId()]]]
            )
        );
    }

    public function testDispatchEmailAction(): Response
    {
        $this->admin->checkAccess('edit');
        /** @var Email $email */
        $email = $this->admin->getSubject();
        $execution = $this->testConditionsDispatcher->testConditionsDispatch($email);

        return $this->renderWithExtraParams('@PrestaMailReceiver/TestConditionsExecution/show_test_condition.html.twig', [
            'execution' => $execution
        ]);
    }

    private function setStatusFlashMessage(string $type): string
    {
        return $this->trans("email_received.flash.set_status.$type", [], $this->admin->getTranslationDomain());
    }
}

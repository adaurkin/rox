<?php

namespace Rox\Core\Listener;

use Rox\Core\Kernel\LegacyHttpKernel;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class LegacyDispatchListener
{
    /**
     * @var LegacyHttpKernel
     */
    protected $kernel;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    public function __construct(LegacyHttpKernel $kernel, SessionInterface $session, TokenStorage $tokenStorage)
    {
        $this->kernel = $kernel;
        $this->session = $session;
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        // Only act if the exception was a 404 error, we will then try to route
        // it with the legacy kernel.
        if (!$event->getException() instanceof NotFoundHttpException) {
            return;
        }

        // If the Symfony router matched the request but we have a 404, it means
        // the controller probably threw the 404 error, so don't try legacy
        // dispatch.
        if ($event->getRequest()->attributes->get('_route')) {
            return;
        }

        // Kick-start the Symfony session. This replaces session_start() in the
        // old code, which is now turned off.
        $this->session->start();
        if (!$this->session->has('IdMember')) {
            $rememberMeToken = unserialize($this->session->get('_security_default'));
            $user = $rememberMeToken->getUser();
            if ($user !== null) {
                $this->session->set('IdMember', $user->id);
            }
        }
        try {
            $response = $this->kernel->handle($event->getRequest(), $event->getRequestType());
        } catch (ResourceNotFoundException $e) {
            // If the legacy kernel also failed to route the request, let the
            // original error bubble back up to the new Symfony error handler.
            return;
        }

        $event->setResponse($response);
    }
}

<?php

namespace App\EventSubscriber;

use App\Repository\NotificationRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Security;

class RequestSubscriber implements EventSubscriberInterface
{

    private $security;
    /**
     * @var NotificationRepository
     */
    private $notifRepo;
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(Security $security, NotificationRepository $notificationRepository, SessionInterface $session)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
        $this->notifRepo = $notificationRepository;
        $this->session = $session;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $currentUser = $this->security->getUser();

        // Ignore AJAX request
        if ($event->getRequest()->isXmlHttpRequest()) {
            return;
        }

        // Récupérer les notifs non lues par le current User
        $userNotifs = $this->notifRepo->findBy([
            'receiver' => $currentUser,
            'isRead'=> 0
        ], ['created_at' => 'DESC'])
        ;

        $nbNotif = count($userNotifs);

        // Enregistrer en session
        $this->session->set('nbNotif', $nbNotif);

    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }
}

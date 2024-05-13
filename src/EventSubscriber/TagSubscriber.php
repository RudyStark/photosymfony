<?php

namespace App\EventSubscriber;

use App\Service\TagService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RequestStack;

class TagSubscriber implements EventSubscriberInterface
{
    private $tagService;
    private $session;

    public function __construct(TagService $tagService, RequestStack $requestStack)
    {
        $this->tagService = $tagService;
        $this->session = $requestStack->getSession();
    }

    /**
     * Récupère les tags les plus utilisés à partir du TagService et les stocke dans la session.
     *
     * @param ControllerEvent $event L'objet événement contenant les informations sur le contrôleur et la requête.
     */
    public function onKernelController(ControllerEvent $event)
    {
        // Récupère les tags les plus utilisés à partir du TagService
        $mostUsedTags = $this->tagService->getMostUsedTags();

        // Stocke les tags les plus utilisés dans la session
        $this->session->set('mostUsedTags', $mostUsedTags);
    }

    /**
     * Retourne un tableau associatif qui mappe les noms des événements aux méthodes qui doivent être appelées.
     * @return array Le tableau associatif des événements auxquels cette classe est abonnée.
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}

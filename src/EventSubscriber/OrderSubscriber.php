<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Order;
use App\Message\StageTransitionMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class OrderSubscriber implements EventSubscriberInterface
{
    protected EntityManagerInterface $entityManager;
    protected MessageBusInterface $messageBus;

    public function __construct(EntityManagerInterface $entityManager, MessageBusInterface $messageBus)
    {
        $this->entityManager = $entityManager;
        $this->messageBus = $messageBus;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                ['handleOrderStage', EventPriorities::PRE_WRITE],
            ]
        ];
    }

    public function handleOrderStage(ViewEvent $event)
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        if (!$entity instanceof Order || !in_array($method, [Request::METHOD_POST, Request::METHOD_PUT])) {
            // Not Order POST/PUT
            return;
        }

        /** @var Order $entity */
        $orgData = $this->entityManager->getUnitOfWork()->getOriginalEntityData($entity);
        $oldStageName = empty($orgData) ? '' : $orgData['stage']->getName();
        $newStage = $entity->getStage();
        $newStageName = $newStage->getName();
        if ($newStageName !== $oldStageName) {
            $id = $entity->getStage()->getId();
            $params = [];
            $message = new StageTransitionMessage($id, $newStageName, $params);
            $this->messageBus->dispatch(new Envelope($message, [new DelayStamp(5000)]));
        }
    }
}

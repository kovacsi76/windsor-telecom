<?php

namespace App\MessageHandler;

use App\Message\StageTransitionMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class StageTransitionMessageHandler implements MessageHandlerInterface
{
    public function __invoke(StageTransitionMessage $message)
    {
        $stageName = $message->getName();
        switch ($stageName) {
            case 'Created':
                // Dispatch EmailMessage to customer with order id
                break;
            case 'Approved':
                // If Contract type, dispatch EmailMessage to customer with contract link
                break;
            case 'Signed':
                // Call API using order id to retrieve PDF and store locally
                break;
            case 'Delivered':
                // Dispatch EmailMessage to sales team to follow up with customer
                break;
            case 'Expired':
                // If Free trial type, dispatch EmailMessage to customer that trial expired
                // AND dispatch EmailMessage to sales team to follow up with customer
                break;
        }
    }
}

<?php

namespace App\Validator;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class OrderStageTransitionValidator extends ConstraintValidator
{
    protected const NEXT_STAGES = [
        'Created' => ['Approved' => []],
        'Approved' => [
            'Signed' => ['Contract'],
            'Delivered' => ['Free trial']
        ],
        'Signed' => ['Delivered' => ['Contract']],
        'Delivered' => ['Completed' => []],
        'Completed' => ['Expired' => ['Free trial']],
        'Expired' => null,
    ];

    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\OrderStageTransition $constraint */

        if (!$value instanceof Order) {
            throw new LogicException('Only Order is supported');
        }

        $orgData = $this->entityManager->getUnitOfWork()->getOriginalEntityData($value);
        if (empty($orgData)) {
            $newStage = $value->getStage()->getName();
            if ($newStage !== 'Created') {
                $this->context
                    ->buildViolation('New order needs to be created with stage "Created"')
                    ->atPath('stage')
                    ->addViolation();
            }

            return;
        }

        $oldStage = $orgData['stage']->getName();
        $newStage = $value->getStage()->getName();
        $types = self::NEXT_STAGES[$oldStage][$newStage] ?? null;
        if ($types === null || !empty($types) && !in_array($value->getType()->getName(), $types)) {
            $this->context
                ->buildViolation('Transition from "' . $oldStage . '" to "' . $newStage . '" not allowed')
                ->atPath('stage')
                ->addViolation();
        }

        return;
    }
}

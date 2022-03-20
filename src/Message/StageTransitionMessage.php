<?php

namespace App\Message;

final class StageTransitionMessage
{
    private int $id;
    private string $name;
    private array $params;

    public function __construct(int $id, string $name, array $params = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->params = $params;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}

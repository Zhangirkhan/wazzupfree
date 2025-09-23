<?php

namespace App\Contracts;

interface SagaInterface
{
    public function addStep(string $name, callable $action, callable $compensation): void;
    public function execute(): void;
    public function compensate(): void;
    public function isCompleted(): bool;
    public function getCurrentStep(): ?string;
}

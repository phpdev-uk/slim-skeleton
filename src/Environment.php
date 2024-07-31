<?php

declare(strict_types=1);

namespace App;

class Environment
{
    public function __construct(private array $env)
    {
    }

    public function get(string $key): string
    {
        return $this->env[$key];
    }

    public function set(string $key, string $value): self
    {
        $this->env[$key] = $value;
        return $this;
    }
}

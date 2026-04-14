<?php

namespace App\ValueObjects;

use InvalidArgumentException;

final class RecipeTitle
{
    private string $value;

    private function __construct(string $value)
    {
        $v = trim($value);
        if ($v === '' || mb_strlen($v) < 3 || mb_strlen($v) > 200) {
            throw new InvalidArgumentException('Invalid recipe title');
        }
        $this->value = $v;
    }

    public static function from(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

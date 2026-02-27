<?php
declare(strict_types=1);

namespace FunTask\Domain;

final class Tag
{
    /** @var string */
    private $type;

    /** @var string|null */
    private $value;

    private function __construct(string $type, ?string $value)
    {
        $type = trim($type);
        if ($type === '') {
            throw new \InvalidArgumentException('Tag type must not be empty');
        }

        $this->type = $type;
        $this->value = $value !== null ? trim($value) : null;
    }

    public static function fromString(string $raw): self
    {
        $raw = trim($raw);
        if ($raw === '') {
            throw new \InvalidArgumentException('Tag string must not be empty');
        }

        $parts = explode(':', $raw, 2);
        $type = $parts[0];
        $value = $parts[1] ?? null;

        return new self($type, $value);
    }

    public function type(): string
    {
        return $this->type;
    }

    public function value(): ?string
    {
        return $this->value;
    }

    public function isType(string $type): bool
    {
        return $this->type === $type;
    }

    public function is(string $type, ?string $value = null): bool
    {
        if ($this->type !== $type) return false;
        if ($value === null) return true;
        return $this->value === $value;
    }

    public function __toString(): string
    {
        return $this->value === null ? $this->type : ($this->type . ':' . $this->value);
    }
}

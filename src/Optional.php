<?php

namespace Gcanal\FeedCreator;

/**
 * @template T
 */
final class Optional
{
    /**
     * @param T $value
     */
    private final function __construct(private readonly mixed $value = null)
    {
    }

    /**
     * @throws \Throwable When the value is empty
     * @return T
     */
    public function orElseThrow(\Throwable $e): mixed
    {
        if (!$this->value) {
            throw $e;
        }

        return $this->value;
    }

    public function map(callable $fn): self
    {
       if ($this->value) {
           return Optional::of($fn($this->value));
       }

       return Optional::empty();
    }

    /**
     * @template S
     * @return self<S>
     */
    public static function of(mixed $value): self
    {
        return new self($value);
    }

    public static function empty(): self
    {
        return new self(null);
    }

    /**
     * @return T
     */
    public function get()
    {
        if (!$this->value) {
            throw new \LogicException('No such element');
        }

        return $this->value;
    }
}
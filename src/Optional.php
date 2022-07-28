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
     * @template E of \Throwable
     * @param E $e
     *
     * @return T
     *
     * @throws E When the value is empty
     */
    public function orElseThrow(\Throwable $e): mixed
    {
        if (!$this->value) {
            throw $e;
        }

        return $this->value;
    }

    /**
     * @param callable(T): void $fn
     */
    public function ifPresent(callable $fn): void
    {
        if ($this->value !== null) {
            $fn($this->value);
        }
    }

    /**
     * @template S
     * @param callable(T):S $fn
     *
     * @return self<S>
     */
    public function map(callable $fn): self
    {
        if ($this->value) {
            return Optional::of($fn($this->value));
        }

        return Optional::empty();
    }

    /**
     * @template S
     * @param S $value
     *
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
     * @template S
     * @param S $other
     * @return T|S
     */
    public function orElse(mixed $other): mixed
    {
        if (!$this->value) {
            return $other;
        }

        return $this->value;
    }
}
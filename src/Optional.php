<?php

declare(strict_types=1);

namespace Gcanal\FeedCreator;

/**
 * @template-covariant T
 */
final class Optional
{
    /**
     * @var T|never
     */
    private $value;

    /**
     * @param T $value
     */
    private function __construct($value = null)
    {
        $this->value = $value;
    }

    /**
     * @template U
     * @param U $value
     * @phpstan-assert !null $value
     * @return Optional<U>
     */
    public static function of(mixed $value): Optional
    {
        if ($value === null) {
            throw new \LogicException("The value souldn't be null");
        }

        return new self($value);
    }

    /**
     * @template U
     * @param U $value
     * @return Optional<U>
     */
    public static function ofNullable($value): Optional
    {
        return $value !== null ? self::of($value) : self::empty();
    }

    /**
     * @return Optional<never>
     */
    public static function empty(): Optional
    {
        /** @var self<never> $never */
        $never = new self();

        return $never;
    }

    /**
     * Check if the Optional has a value.
     *
     * @return bool
     */
    public function isPresent(): bool
    {
        return $this->value !== null;
    }

    public function ifPresent(callable $fn): void
    {
        if ($this->value) {
            $fn($this->value);
        }
    }

    /**
     * @return T
     */
    public function get(): mixed
    {
        if ($this->value === null) {
            throw new \LogicException('value is empty');
        }

        return $this->value;
    }

    /**
     * @template U
     * @param U $defaultValue
     *
     * @return-phpstan (T is null ? U : T)
     * @return T|U
     */
    public function orElse($defaultValue): mixed
    {
        if ($defaultValue === null) {
            throw new \LogicException("The default value shouldn't be null");
        }

        return $this->value !== null ? $this->value : $defaultValue;
    }

    /**
     * @template E of \Throwable
     * @param E $exception
     * @throws E
     *
     * @return T
     */
    public function orElseThrow(\Throwable $exception): mixed
    {
        if ($this->value) {
            return $this->value;
        }

        throw $exception;
    }


    /**
     * @template U
     * @param callable(T): U $mapper
     * @return Optional<U>
     */
    public function map(callable $mapper): Optional
    {
        return $this->value !== null ? self::of($mapper($this->value)) : self::empty();
    }
}

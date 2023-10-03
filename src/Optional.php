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
     * @param T|never $value
     */
    private function __construct($value = null)
    {
        $this->value = $value;
    }

    /**
     * @template U
     * @param U $value
     *
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
     *
     * @return Optional<U>
     */
    public static function ofNullable($value): Optional
    {
        return $value !== null ? self::of($value) : self::empty();
    }

    /**
     * @phpstan-return $value is '' ? Optional<null> : Optional<non-empty-string>
     */
    public static function ofNonEmptyString(string $value): Optional
    {
        return $value !== '' ? self::of($value) : self::empty();
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
        if ($this->value !== null) {
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
     * @phpstan-return (T is null ? self<U> : self<T>)
     */
    public function orElse($defaultValue): Optional
    {
        if ($defaultValue === null) {
            throw new \LogicException("The default value shouldn't be null");
        }

        return $this->value !== null ? $this : new self($defaultValue);
    }

    /**
     * @template U
     * @param U $defaultValue
     *
     * @phpstan-return (T is null ? U : T)
     */
    public function orElseGet($defaultValue): mixed
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
        if ($this->value !== null) {
            return $this->value;
        }

        throw $exception;
    }

    /**
     * @template U
     * @param callable(T): U $mapper
     *
     * @return Optional<U>
     */
    public function map(callable $mapper): Optional
    {
        return $this->value !== null ? self::of($mapper($this->value)) : self::empty();
    }
}

<?php 

declare(strict_types = 1);

namespace Gcanal\FeedCreator;

/**
 * @template-covariant T
 */
final class Pipe
{
    /**
     * @param T $value
     */
    private function __construct(private mixed $value) {}

    /**
     * @template U
     * 
     * @param U $value
     * 
     * @return self<U>
     */
    public static function from($value): self
    {
        return new self($value);
    }

    /**
     * @template U
     * @template R
     * @param U $equal
     * @param R $return
     * 
     * @phpstan-return (T is U ? R : T)
     */
    public function orWhen(mixed $equal, mixed $return): mixed
    {
        return $this->value === $equal ? $return : $this->value;
    }

    /**
     * @template U
     * @param U $value
     * @param \Closure(U, T): T $callback
     *
	 * @return self<T>
     */
    public function if(mixed $value, \Closure $callback): self
    {
        $this->value = $value !== null ? $callback($value, $this->value) : $this->value;

        return $this;
    }

    /**
     * @return T
     */
    public function collect(): mixed
    {
        return $this->value;
    }
}
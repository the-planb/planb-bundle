<?php

declare(strict_types=1);

namespace PlanB\Domain\Model;

use Symfony\Component\Uid\Ulid;

abstract class EntityId
{
    protected Ulid $ulid;

    final public function __construct(string $ulid = null)
    {
        if (is_null($ulid)) {
            $this->ulid = new Ulid();

            return;
        }

        $this->ulid = Ulid::fromString($ulid);
    }

    public function equals(EntityId $otherId): bool
    {
        return $this->ulid()->equals($otherId->ulid());
    }

    public function ulid(): Ulid
    {
        return $this->ulid;
    }

    public function __toString(): string
    {
        return $this->ulid()->toRfc4122();
    }
}

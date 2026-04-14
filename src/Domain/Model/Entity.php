<?php

declare(strict_types=1);

namespace PlanB\Domain\Model;

interface Entity
{
    public EntityId $id {
        get;
    }
}

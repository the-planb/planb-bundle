<?php

namespace PlanB\Domain\Criteria;

enum OrderDir: string
{
    case DESC = 'desc';
    case ASC = 'asc';
}

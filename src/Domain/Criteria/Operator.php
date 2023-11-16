<?php

namespace PlanB\Domain\Criteria;

enum Operator: string
{
    case EQUALS = 'equals';
    case NOT_EQUALS = 'not_equals';
    case CONTAINS = 'contains';
    case NOT_CONTAINS = 'not_contains';
    case GREATER_THAN = 'gt';
    case LESS_THAN = 'lt';
    case GREATER_OR_EQUALS_THAN = 'gte';
    case LESS_OR_EQUALS_THAN = 'lte';
    case BETWEEN = 'between';
    case STARTS_WITH = 'starts';
    case ENDS_WITH = 'ends';
    case IDENTITY = 'identity';
    case NOT_IDENTITY = 'not_identity';
}

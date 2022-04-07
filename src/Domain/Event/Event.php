<?php

/**
 * This file is part of the planb project.
 *
 * (c) jmpantoja <jmpantoja@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PlanB\Domain\Event;

use Carbon\CarbonImmutable;
use Laminas\Filter\Word\CamelCaseToSeparator;

class Event
{
    private const NON_ALLOWED_WORDS
        = [
            'has',
            'been',
            'was',
            'event',
            'spec',
            'entity',
            'document',
            'model',
            'phpcr',
            'couchdocument',
            'domain',
            'doctrine',
            'orm',
            'mongodb',
            'couchdb',
        ];

    private EventId $id;

    private string $name;

    private string $event;

    private CarbonImmutable $date;

    public function __construct(string $name, string $event, CarbonImmutable $date)
    {
        $this->id = new EventId();

        $this->setName($name);
        $this->setEvent($event);
        $this->date = $date;
    }

    private function setName(string $name): self
    {
        $pieces = explode('\\', $name);
        $pieces = array_map(function ($piece) {
            return $this->normalize($piece);
        }, $pieces);

        $pieces    = array_filter($pieces);
        $eventName = implode('.', $pieces);

        $this->name = $eventName;

        return $this;
    }

    private function normalize(string $name): string
    {
        $filter = new CamelCaseToSeparator('_');
        $name   = strtolower($filter->filter($name));

        $pieces = explode('_', $name);
        $pieces = array_filter($pieces, function (string $item) {
            return ! in_array($item, self::NON_ALLOWED_WORDS);
        });

        return implode('_', $pieces);
    }

    private function setEvent(string $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getId(): EventId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEvent(): string
    {
        return $this->event;
    }

    public function getDate(): CarbonImmutable
    {
        return $this->date;
    }
}

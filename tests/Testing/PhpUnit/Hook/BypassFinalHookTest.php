<?php

declare(strict_types=1);

namespace PlanB\Tests\Testing\PhpUnit\Hook;

use PHPUnit\Framework\TestCase;
use PlanB\Testing\PhpUnit\Hook\BypassFinalHook;

final class BypassFinalHookTest extends TestCase
{
    public function test_its_possible_create_a_mock_from_a_final_class()
    {
        $hook = new BypassFinalHook();
        $hook->executeBeforeTest(__METHOD__);

        $this->getMockClass(FinalClass::class);
        $this->addToAssertionCount(1); //porque no hay un método assertNotException
    }
}

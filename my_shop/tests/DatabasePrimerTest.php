<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DatabasePrimerTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function database_primer_loads_correctly(): void
    {
        $this->assertTrue(true);
    }
}

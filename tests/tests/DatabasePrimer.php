<?php

namespace App\Tests;

use Doctrine\ORM\Tools\SchemaTool;
use LogicException;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Migrate database to memory (sqlite)
 */
class DatabasePrimer
{
    public static function prime(KernelInterface $kernel)
    {
        if ('test' !== $kernel->getEnvironment()) {
            throw new LogicException('Primer must be executed in the test enviroment.');
        }
        $entityMenager = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        $metadatas = $entityMenager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityMenager);
        $schemaTool->updateSchema($metadatas);
    }
}
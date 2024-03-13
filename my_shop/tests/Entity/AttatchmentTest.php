<?php

namespace App\Tests\Entity;

use App\Entity\Attatchment;
use App\Tests\KernelTestCaseWithDatabase;

class AttatchmentTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function attatchment_can_be_created_in_database()
    {
        // Given
        $attatchment = new Attatchment();
        $attatchment->setName('attatchment_name');
        $attatchment->setDescription('attatchemnt_description');
        $attatchment->setFileName('attatchment_file_name');
        $attatchment->setType('attatchemnt_type');

        /** @var AttatchmentRepository $attatchmentRepository */
        $attatchmentRepository = $this->entityManager->getRepository(Attatchment::class);

        // When
        $this->entityManager->persist($attatchment);
        $this->entityManager->flush();

        /** @var Attatchment $attatchmentRecord */
        $attatchmentRecord = $attatchmentRepository->findOneBy(['name' => 'attatchment_name']);

        // Then
        $this->assertNotEquals(null, $attatchmentRecord);
        $this->assertGreaterThan(0, $attatchmentRecord->getId());
        $this->assertEquals('attatchment_name', $attatchmentRecord->getName());
        $this->assertEquals('attatchemnt_description', $attatchmentRecord->getDescription());
        $this->assertEquals('attatchment_file_name', $attatchmentRecord->getFileName());
        $this->assertEquals('attatchemnt_type', $attatchmentRecord->getType());
    }

    /** @test */
    public function product_is_not_deleted_when_attatchment_is_deleted(): void
    {
        // todo
    }
}
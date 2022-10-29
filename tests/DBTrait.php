<?php

declare(strict_types=1);

namespace App\Tests;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * This trait is used to extend tests that interact with the database.
 *
 * @mixin KernelTestCase
 */
trait DBTrait
{
    /**
     * Get document manager instance.
     */
    protected function getDocumentManager(): DocumentManager
    {
        /** @var DocumentManager $dm */
        $dm = self::getContainer()->get('doctrine_mongodb.odm.document_manager');

        return $dm;
    }

    /**
     * Asserts that the given document exists in the database.
     */
    protected function assertExists(string $class, array $criteria): void
    {
        $repository = $this->getDocumentManager()->getRepository($class);
        $object = $repository->findOneBy($criteria);
        $this->assertNotNull($object);
    }

    /**
     * Deletes the given document from the database.
     */
    protected function deleteDocument(string|object $class, array $criteria = []): void
    {
        if (is_object($class)) {
            $this->getDocumentManager()->remove($class);
            $this->getDocumentManager()->flush();

            return;
        }

        $repository = $this->getDocumentManager()->getRepository($class);
        $object = $repository->findOneBy($criteria);

        $this->getDocumentManager()->remove($object);
        $this->getDocumentManager()->flush();
    }

    /**
     * Persists the given document to the database.
     */
    protected function persistDocument(object $document): void
    {
        $this->getDocumentManager()->persist($document);
        $this->getDocumentManager()->flush();
    }
}

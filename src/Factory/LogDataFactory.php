<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Factory;

use AssoConnect\LogBundle\Serializer\LogSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;

/** @phpstan-type LogData array{entity: object, entityColumn: string, entityOldValue: ?string} */
class LogDataFactory
{
    public function __construct(
        private readonly LogSerializer $formatter,
        private readonly array $includedEntities,
        private readonly array $excludedEntities,
    ) {
    }

    /** @return LogData[] */
    public function createFromEvent(OnFlushEventArgs $eventArgs): iterable
    {
        $em = $eventArgs->getEntityManager();
        $unitOfWork = $em->getUnitOfWork();

        // Creation
        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            if ($this->isLoggable($entity)) {
                yield [
                    'entity' => $entity,
                    'entityColumn' => 'action.create',
                    'entityOldValue' => $this->formatter->formatEntity($em, $entity),
                ];
            }
        }

        // Update
        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            if ($this->isLoggable($entity)) {
                yield from $this->getLogsForEntityFields($entity, $em);
            }
        }

        // Delete
        foreach ($unitOfWork->getScheduledEntityDeletions() as $entity) {
            if ($this->isLoggable($entity)) {
                yield [
                    'entity' => $entity,
                    'entityColumn' => 'action.delete',
                    'entityOldValue' => $this->formatter->formatEntity($em, $entity),
                ];
            }
        }
    }

    private function isLoggable($entity): bool
    {
        if ($this->isSubClassFromList($entity, $this->excludedEntities)) {
            return false;
        }

        return [] === $this->includedEntities || $this->isSubClassFromList($entity, $this->includedEntities);
    }

    private function isSubClassFromList($entity, array $classes): bool
    {
        foreach ($classes as $class) {
            if (is_a($entity, $class)) {
                return true;
            }
        }

        return false;
    }

    /** @return LogData[] */
    private function getLogsForEntityFields($entity, EntityManagerInterface $entityManager): iterable
    {
        $unitOfWork = $entityManager->getUnitOfWork();

        $metadata = $entityManager->getMetadataFactory()->getMetadataFor($entity::class);

        $fields = array_merge($metadata->getFieldNames(), $metadata->getAssociationNames());

        foreach ($unitOfWork->getEntityChangeSet($entity) as $field => $changeSet) {
            // We keep only mapped fields
            if (false === in_array($field, $fields, true)) {
                continue;
            }

            yield [
                'entity' => $entity,
                'entityColumn' => $field,
                'entityOldValue' => $this->formatter->formatValueAsString($changeSet[0]),
            ];
        }
    }
}

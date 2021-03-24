<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Subscriber;

use AssoConnect\LogBundle\Factory\LogFactoryInterface;
use AssoConnect\LogBundle\Serializer\LogSerializer;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;

/**
 * This Doctrine subscriber creates a Log entity every time a fully Doctrine-managed entity is persisted,
 * updated or removed.
 */
class LoggerSubscriber implements EventSubscriber
{
    private LogSerializer $formatter;
    private LogFactoryInterface $factory;
    private array $includedEntities;
    private array $excludedEntities;

    public function __construct(
        LogSerializer $formatter,
        LogFactoryInterface $factory,
        array $includedEntities,
        array $excludedEntities
    ) {
        $this->formatter = $formatter;
        $this->factory = $factory;
        $this->includedEntities = $includedEntities;
        $this->excludedEntities = $excludedEntities;
    }

    public function getSubscribedEvents()
    {
        return [Events::onFlush];
    }

    public function onFlush(OnFlushEventArgs $eventArgs): void
    {
        $entityManager = $eventArgs->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        $logs = [];

        //Creation
        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            if ($this->isLoggeable($entity)) {
                $logs[] = $this->factory->createLogFromEntity(
                    $entity,
                    'action.create',
                    json_encode(
                        $this->formatter->formatEntity(
                            $entityManager,
                            $entity
                        )
                    )
                );
            }
        }

        //Update
        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            if ($this->isLoggeable($entity)) {
                $logs = array_merge($logs, $this->getLogsForEntityFields($entity, $entityManager));
            }
        }

        //Delete
        foreach ($unitOfWork->getScheduledEntityDeletions() as $entity) {
            if ($this->isLoggeable($entity)) {
                $logs[] = $this->factory->createLogFromEntity(
                    $entity,
                    'action.delete',
                    json_encode(
                        $this->formatter->formatEntity(
                            $entityManager,
                            $entity
                        )
                    )
                );
            }
        }

        $cmf = $entityManager->getMetadataFactory();

        foreach ($logs as $log) {
            $entityManager->persist($log);
            $unitOfWork->computeChangeSet($cmf->getMetadataFor(get_class($log)), $log);
        }
    }

    private function isLoggeable($entity): bool
    {
        if ($this->isSubClassFromList($entity, $this->excludedEntities)) {
            return false;
        }

        return empty($this->includedEntities) || $this->isSubClassFromList($entity, $this->includedEntities);
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

    private function getLogsForEntityFields($entity, EntityManagerInterface $entityManager): array
    {
        $logs = [];

        $unitOfWork = $entityManager->getUnitOfWork();

        $fields = $entityManager->getClassMetadata(get_class($entity))->getFieldNames();
        foreach ($unitOfWork->getEntityChangeSet($entity) as $field => $changeSet) {
            // We keep only mapped fields
            if (false === in_array($field, $fields)) {
                continue;
            }

            $logs[] = $this->factory->createLogFromEntity(
                $entity,
                $field,
                json_encode($this->formatter->formatValue($changeSet[0]))
            );
        }

        return $logs;
    }
}

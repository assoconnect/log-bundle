<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Subscriber;

use AssoConnect\LogBundle\Factory\LogDataFactory;
use AssoConnect\LogBundle\Factory\LogFactoryInterface;
use AssoConnect\LogBundle\Factory\RequestContextAwareLogFactoryInterface;
use AssoConnect\LogBundle\Factory\SecurityContextAwareLogFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;

/**
 * This Doctrine subscriber creates a Log entity every time
 * a fully Doctrine-managed entity is persisted, updated, or removed.
 */
#[AsDoctrineListener(event: Events::onFlush)]
class LoggerSubscriber
{
    public function __construct(
        private readonly LogFactoryInterface $factory,
        private readonly LogDataFactory $logDataFactory,
        private readonly string $projectDir
    ) {
    }

    public function onFlush(OnFlushEventArgs $eventArgs): void
    {
        $em = $eventArgs->getEntityManager();
        $unitOfWork = $em->getUnitOfWork();
        $cmf = $em->getMetadataFactory();
        $requestTrace = $this->getRequestTrace();

        foreach ($this->logDataFactory->createFromEvent($eventArgs) as $logData) {
            $log = $this->factory->createLogFromEntity(
                $logData['entity'],
                $logData['entityColumn'],
                $logData['entityOldValue'],
                $requestTrace
            );
            if ($this->factory instanceof RequestContextAwareLogFactoryInterface) {
                $this->factory->setRequestContext($log);
            }
            if ($this->factory instanceof SecurityContextAwareLogFactoryInterface) {
                $this->factory->setSecurityUser($log);
            }
            $em->persist($log);
            $unitOfWork->computeChangeSet($cmf->getMetadataFor($log::class), $log);
        }
    }

    private function getRequestTrace(): string
    {
        // Request trace
        $traces = [];
        // adding Http refere to the trace
        if (isset($_SERVER['HTTP_REFERER'])) {
            $traces[] = 'HTTP Referer: ' . $_SERVER['HTTP_REFERER'] . PHP_EOL;
        }

        $projectDirLength = strlen($this->projectDir);
        /** @phpstan-ignore-next-line debug_backtrace() is banned */
        foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $trace) {
            $file = $trace['file'] ?? '';
            if (str_starts_with($file, $this->projectDir)) {
                $file = substr($file, $projectDirLength);
            }
            $line = $trace['line'] ?? 0;
            $traces[] = $file . '::' . $trace['function'] . ':' . $line;
        }

        return implode(PHP_EOL, $traces);
    }
}

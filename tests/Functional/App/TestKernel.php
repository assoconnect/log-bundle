<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Tests\Functional\App;

use AssoConnect\LogBundle\LogBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new LogBundle(),
            new DoctrineBundle(),
        ];
    }

    public function getCacheDir(): string
    {
        return $this->basePath() . 'cache/' . $this->environment;
    }

    public function getLogDir(): string
    {
        return $this->basePath() . 'logs';
    }

    public function getRootDir(): string
    {
        return __DIR__;
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/config/config.yml');
    }

    private function basePath(): string
    {
        return sys_get_temp_dir() . '/LogBundle/' . Kernel::VERSION . '/';
    }
}

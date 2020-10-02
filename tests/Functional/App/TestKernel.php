<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Tests\Functional\App;

use AssoConnect\LogBundle\LogBundle;
use AssoConnect\PHPDateBundle\PHPDateBundle;
use AssoConnect\PHPPercentBundle\PHPPercentBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new LogBundle(),
            new DoctrineBundle()
        ];
    }

    public function getCacheDir()
    {
        return $this->basePath() . 'cache/' . $this->environment;
    }

    public function getLogDir()
    {
        return $this->basePath() . 'logs';
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config.yml');
    }

    private function basePath()
    {
        return sys_get_temp_dir() . '/LogBundle/' . Kernel::VERSION . '/';
    }
}

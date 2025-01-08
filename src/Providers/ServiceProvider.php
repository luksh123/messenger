<?php declare(strict_types = 1);

namespace LDTech\Messenger\Providers;

use Nette;
use Symfony\Contracts\Service\ServiceProviderInterface;
use Contributte\Psr11\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ServiceProvider extends ContainerBuilder implements ServiceProviderInterface
{

    public function getProvidedServices(): array 
    {
        return [];
    }
}
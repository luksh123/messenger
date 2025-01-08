<?php declare(strict_types = 1);

namespace LDTech\Messenger\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Container;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\DI\ServiceCreationException;
use Nette\PhpGenerator\ClassType;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Psr\Container\ContainerInterface;
use stdClass;
use Symfony\Component\Messenger\Command;
use Symfony\Component\Messenger\RoutableMessageBus;
use Symfony\Component\Messenger\MessageBus;
use Tracy\Bar;
use LDTech\Messenger\Providers\ServiceProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Messenger\DependencyInjection\MessengerPass;

/**
 * @method stdClass getConfig()
 */
class MessengerExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'debug' => Expect::bool(false),
			'loggers' => Expect::arrayOf(Expect::type(Statement::class)),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig();

		$cacheFactory = $builder->getDefinitionByType('Contributte\Psr6\ICachePoolFactory');

		$cache = $builder->addDefinition($this->prefix('cache'))
		->setFactory('@'.$cacheFactory->getName().'::create', ['cleanrail.messages']);
		
		$container = $builder->addDefinition($this->prefix('container'))
		->setType(ServiceProvider::class)
		->setFactory(ServiceProvider::class);

		/*$container = $builder->addDefinition($this->prefix('DIContainer'))
		->setType(ContainerBuilder::class)
		->setFactory(ContainerBuilder::class, []);*/

		$messageBus = $builder->addDefinition($this->prefix('messageBus'))
		->setType(MessageBus::class)
		->setFactory(MessageBus::class, []);

		$routerMessageBus = $builder->addDefinition($this->prefix('routableMessageBus'))
		->setType(RoutableMessageBus::class)
		->setFactory(RoutableMessageBus::class, [$container, $messageBus])
		->setAutowired(false);
		
		$messenger = $builder->addDefinition($this->prefix('messenger'))
		->setType(MessengerPass::class)
		->setFactory(MessengerPass::class)
		->addSetup('process', ['@'.$this->prefix('container')])
		->setAutowired(true);

		//bdump($messenger);

		// Symfony commands
		$builder->addDefinition($this->prefix('command.consume'))
		->setFactory(Command\ConsumeMessagesCommand::class, [$routerMessageBus, $container])
		->setAutowired(false);

		$builder->addDefinition($this->prefix('command.failedMessagesRemove'))
		->setFactory(Command\FailedMessagesRemoveCommand::class,['cleanrail', $container])
		->setAutowired(false);

		$builder->addDefinition($this->prefix('command.failedMessagesRetry'))
		->setFactory(Command\FailedMessagesRetryCommand::class, ['cleanrail', $container])
		->setAutowired(false);

		$builder->addDefinition($this->prefix('command.failedMessagesShow'))
		->setFactory(Command\FailedMessagesShowCommand::class, ['cleanrail', $container])
		->setAutowired(false);

		$builder->addDefinition($this->prefix('command.setupTransports'))
		->setFactory(Command\SetupTransportsCommand::class, [$container])
		->setAutowired(false);

		$builder->addDefinition($this->prefix('command.statsCommand'))
		->setFactory(Command\StatsCommand::class, [$container])
		->setAutowired(false);

		$builder->addDefinition($this->prefix('command.stopWorkersCommand'))
		->setFactory(Command\StopWorkersCommand::class, [$cache])
		->setAutowired(false);

		$builder->addDefinition($this->prefix('command.DebugCommand'))
		->setFactory(Command\DebugCommand::class,[[]])
		->setAutowired(false);
	
	}

	public function beforeCompile(): void 
	{

	}

	public function afterCompile(ClassType $class): void
	{
/*		$config = $this->getConfig();
		$builder = $this->getContainerBuilder();
		$initialization = $this->getInitialization();

		if ($config->debug) {
			$initialization->addBody(
				// @phpstan-ignore-next-line
				$builder->formatPhp('?->addPanel(?);', [
					$builder->getDefinitionByType(Bar::class),
					new Statement(EventPanel::class, [$builder->getDefinition($this->prefix('dispatcher.tracy'))]),
				])
			);
		}*/
	}

}

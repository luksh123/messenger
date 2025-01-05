<?php declare(strict_types = 1);

namespace LDTech\Messenger\DI;

use Symfony\Component\Messenger\Command;
use Nette\DI\CompilerExtension;
use Nette\DI\Container;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\DI\ServiceCreationException;
use Nette\PhpGenerator\ClassType;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;
use Tracy\Bar;

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

		// Symfony commands
		$builder->addDefinition($this->prefix('symfony.messegner.consume_command'))
		->setFactory(ConsumeMessagesCommand::class, [])
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

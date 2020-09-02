<?php

declare(strict_types=1);

namespace Tables\Bridges;

use Nette\Application\Helpers;
use Nette\Routing\Route;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Pages\DB\PageRepository;
use Pages\DB\RedirectRepository;
use Pages\DB\SitemapRepository;
use Pages\Redirector;
use Pages\Router;
use StORM\Entity;

class TablesDI extends \Nette\DI\CompilerExtension
{
	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'userPath' => Expect::string(),
			'flagsPath' => Expect::string(),
			'flagsExt' => Expect::string(),
			'primaryMutation' => Expect::string(),
			'mutations' => Expect::arrayOf('string')->min(1),
		]);
	}
	
	public function loadConfiguration(): void
	{
		$config = $this->getConfig();
		
		/** @var \Nette\DI\ContainerBuilder $builder */
		$builder = $this->getContainerBuilder();
		
		$pages = $builder->addDefinition($this->prefix('componentFactory'))->setType(\Tables\ComponentFactory::class);
		$pages->addSetup('setDefaultMutations', [$config->mutations]);
		$pages->addSetup('setDefaultUserPath', [$config->userPath]);
		$pages->addSetup('setDefaultFlagsConfiguration', [$config->flagsPath, $config->flagsExt]);
		$pages->addSetup('setDefaultPrimaryMutation', [$config->primaryMutation ?: \current($config->mutations)]);
		
		return;
	}
}

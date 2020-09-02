<?php

declare(strict_types=1);

namespace Tables;

use CMS\Table\Datalist;
use Nette\Localization\ITranslator;
use StORM\Collection;
use StORM\ICollection;
use CMS\Form\Form;

class ComponentFactory
{
	/**
	 * @var string[]
	 */
	private array $defaultMutations = [];
	
	private string $primaryMutation;
	
	private string $userPath;
	
	private ?string $flagsPath = null;
	
	private ?string $flagsExt = null;
	
	private ITranslator $translator;
	
	public function __construct(ITranslator $translator)
	{
		$this->translator = $translator;
	}
	
	public function setDefaultUserPath(string $userPath): void
	{
		$this->userPath = $userPath;
	}
	
	public function getDefaultUserPath(): string
	{
		return $this->userPath;
	}
	
	public function setDefaultMutations(array $mutations): void
	{
		$this->defaultMutations = $mutations;
	}
	
	public function setDefaultPrimaryMutation(string $mutation): void
	{
		$this->primaryMutation = $mutation;
	}
	
	/**
	 * @return array|string[]
	 */
	public function getDefaultMutations(): array
	{
		return $this->defaultMutations;
	}
	
	public function getDefaultPrimaryMutation(): string
	{
		return $this->primaryMutation;
	}
	
	public function setDefaultFlagsConfiguration(?string $path, ?string $ext)
	{
		$this->flagsPath = $path;
		$this->flagsExt = $ext;
	}
	
	public function createForm(): Form
	{
		$form = new Form();
		$form->setTranslator($this->translator);
		$form->setMutations($this->getDefaultMutations());
		$form->setPrimaryMutation($this->getDefaultPrimaryMutation());
		$form->setUserPath($this->userPath);
		$form->setFlagsConfiguration($this->flagsPath, $this->flagsExt);
		
		return $form;
	}
	
	public function createDatalist(Collection $collection): Datalist
	{
		$datalist = new Datalist();
		$datalist->setSource($collection);
		
		return $datalist;
	}
	
	public function createDatagrid(): Datagrid
	{
		$datagrid = new Datalist();
		$datagrid->setTranslator($this->translator);
		$datagrid->setUserPath($this->userPath);
		$datagrid->setFlagsConfiguration($this->flagsPath, $this->flagsExt);
		$datagrid->setMutations($this->getDefaultMutations());
		$datagrid->setActiveMutation($this->getDefaultPrimaryMutation());
		
		return $datagrid;
	}
}

<?php

declare(strict_types=1);

namespace Tables\Form;

use Nette\Application\ApplicationException;
use Nette\Forms\IFormRenderer;
use Nette\Utils\Html;
use StORM\Meta\Table;

/**
 * Class Form
 * @property-read IFormRenderer $renderer
 */
class Form extends \Nette\Application\UI\Form
{
	const MUTATION_SELECTOR_NAME = '__MUTATION_SELECTOR';
	
	use LocaleComponentsTrait;
	use ComponentsTrait;
	
	protected ?string $flagsPath = null;
	
	protected ?string $flagsExt = null;
	
	protected string $userPath;
	
	protected ?string $primaryMutation = null;
	
	/**
	 * @var string[]
	 */
	protected array $mutations;
	
	public function __construct(Nette\ComponentModel\IContainer $parent = null, string $name = null)
	{
		parent::__construct($parent, $name);
		$this->setRenderer(new DefaultRenderer());
	}
	
	public function getPrimaryMutation(): ?string
	{
		return $this->primaryMutation;
	}
	
	public function setPrimaryMutation(string $mutation)
	{
		$this->primaryMutation = $mutation;
	}
	
	/**
	 * @param string[] $mutations
	 */
	public function setMutations(array $mutations)
	{
		if (isset($this[self::MUTATION_SELECTOR_NAME])) {
			throw new ApplicationException('Mutation selector already exists, please call ->removeMutationSelector() first.');
		}
		
		$this->mutations = $mutations;
	}
	
	/**
	 * @return string[]
	 */
	public function getMutations(): array
	{
		return $this->mutations;
	}
	
	public function setActiveMutation(string $mutation): void
	{
		if (isset($this[self::MUTATION_SELECTOR_NAME])) {
			$this[self::MUTATION_SELECTOR_NAME]->setDefaultValue($mutation);
		}
	}
	
	public function getActiveMutation(): ?string
	{
		if (!isset($this[self::MUTATION_SELECTOR_NAME])) {
			throw new ApplicationException('Mutation does not exists, please call ->addMutationSelector($label) first.');
		}
		
		return $this[self::MUTATION_SELECTOR_NAME]->getValue();
	}
	
	public function setUserPath(string $userPath): void
	{
		$this->userPath = $userPath;
	}
	
	public function getUserPath(): string
	{
		return $this->userPath;
	}
	
	public function setFlagsConfiguration(?string $flagsPath, ?string $flagsExt): void
	{
		if ($flagsPath) {
			$this->flagsPath = $flagsPath;
		}
		
		if ($flagsExt) {
			$this->flagsExt = $flagsExt;
		}
	}
	
	public function getFlagsPath(): string
	{
		return $this->flagsPath;
	}
	
	public function getFlagsExt(): string
	{
		return $this->flagsExt;
	}
	
	public function getFlagSrc(string $mutation): string
	{
		return $this->flagsPath . '/' . $mutation . '.' . $this->flagsExt;
	}
	
	public function addMutationSelector(string $label)
	{
		$items = [];
		
		foreach ($this->getMutations() as $mutation) {
			$items[$mutation] = Html::el("img alt=$mutation title=$mutation src=" . $this->getFlagSrc($mutation));
		}
		
		$this->addRadioList(self::MUTATION_SELECTOR_NAME, $label, $items)->setDefaultValue($this->getPrimaryMutation())->setOmitted();
	}
	
	public function removeMutationSelector(): void
	{
		unset($this[self::MUTATION_SELECTOR_NAME]);
	}
	
	/**
	 * Adds naming container to the form.
	 * @param string|int  $name
	 */
	public function addContainer($name): Container
	{
		$control = new Container();
		$control->currentGroup = $this->currentGroup;
		if ($this->currentGroup !== null) {
			$this->currentGroup->add($control);
		}
		
		return $this[$name] = $control;
	}
	
	protected function setReadonlyForDescendant(\Nette\Forms\Container $container, string $mutation)
	{
		// TODO implement
	}
	
	public function setReadonly(?array $mutations = null): void
	{
		foreach ($this->getComponents() as $component) {
			// if language selector
			
			// if container
			
			// if BaseControl
		}
	}
}

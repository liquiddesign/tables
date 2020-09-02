<?php

declare(strict_types=1);

namespace Tables\Form;

use Nette\Utils\Html;

/**
 * Trait LocaleComponentsTrait
 * @method \CMS\Form\LocaleContainer addLocaleText() addLocaleText(string $name, $label = null, int $cols = null, int $maxLength = null)
 * @method \CMS\Form\LocaleContainer addLocalePassword() addLocalePassword(string $name, $label = null, int $cols = null, int $maxLength = null)
 * @method \CMS\Form\LocaleContainer addLocaleTextArea() addLocaleTextArea(string $name, $label = null, int $cols = null, int $maxLength = null)
 * @method \CMS\Form\LocaleContainer addLocaleEmail() addLocaleEmail(string $name, $label = null)
 * @method \CMS\Form\LocaleContainer addLocaleInteger() addLocaleInteger(string $name, $label = null)
 * @method \CMS\Form\LocaleContainer addLocaleUpload() addLocaleUpload(string $name, $label = null)
 * @method \CMS\Form\LocaleContainer addLocaleMultiUpload() addLocaleMultiUpload(string $name, $label = null)
 * @method \CMS\Form\LocaleContainer addLocaleCheckbox() addLocaleCheckbox(string $name, $caption = null)
 * @method \CMS\Form\LocaleContainer addLocaleRadioList() addLocaleRadioList(string $name, $label = null, array $items = null)
 * @method \CMS\Form\LocaleContainer addLocaleCheckboxList() addLocaleCheckboxList(string $name, $label = null, array $items = null)
 * @method \CMS\Form\LocaleContainer addLocaleSelect() addLocaleSelect(string $name, $label = null, array $items = null, int $size = null)
 * @method \CMS\Form\LocaleContainer addLocaleMultiSelect() addLocaleMultiSelect(string $name, $label = null, array $items = null, int $size = null)
 * @method \CMS\Form\LocaleContainer addLocaleImage() addLocaleImage(string $name, string $src = null, string $alt = null)
 * @mixin \Nette\Forms\Container
 */
trait LocaleComponentsTrait
{
	public function getForm(bool $throw = true): Form
	{
		return $this instanceof Form ? $this : $this->lookup(Form::class, $throw);
	}
	
	protected function addLocaleContainer(string $name)
	{
		$control = new LocaleContainer();
		$control->currentGroup = $this->currentGroup;
		if ($this->currentGroup !== null) {
			$this->currentGroup->add($control);
		}
		
		return $this[$name] = $control;
	}
	
	public function __call($name, $arguments)
	{
		$prefix = 'addLocale';
		$controlName = (string) substr($name, strlen($prefix));
		
		if ($prefix === substr($name, 0, strlen($prefix)) && \method_exists($this, 'add' . $controlName)) {
			return $this->addLocaleControls($this->getForm()->getPrimaryMutation(), $this->getForm()->getMutations(), \strtolower($controlName), $arguments);
		}
		
		/** @noinspection PhpUndefinedClassInspection */
		return parent::__call($name, $arguments);
	}
	
	protected function addLocaleControls(string $primaryMutation, array $mutations, string $controlName, array $args)
	{
		$prefix = 'add';
		$name = array_shift($args);
		
		$container = $this->addLocaleContainer($name);
		$mutations = \array_unique([$primaryMutation] + $mutations);
		
		foreach ($mutations as $mutation) {
			$argMutation = \array_merge([$mutation], $args);
			
			$method = $prefix . \ucfirst($controlName);
			
			/** @var \Nette\Forms\Controls\BaseControl $control */
			$control = $container->$method(...$argMutation);
			
			$control->getLabelPrototype()->setAttribute('data-mutation', $mutation);
			$control->getControlPrototype()->setAttribute('data-mutation', $mutation);
		}
		
		return $container;
	}
}
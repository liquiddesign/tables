<?php

declare(strict_types=1);

namespace Tables\Form;

use CMS\Helpers;

class LocaleContainer extends \Nette\Forms\Container
{
	use LocaleComponentsTrait;
	use ComponentsTrait;
	
	public function forAll(callable $callback)
	{
		foreach ($this->getForm()->getMutations() as $mutation) {
			$params = Helpers::reflectionOf($callback)->getNumberOfParameters() === 1 ? [$this[$mutation]] : [$this[$mutation], $mutation];
			\call_user_func_array($callback, $params);
		}
		
		return $this;
	}
	
	public function forPrimary(callable $callback)
	{
		$mutation = $this->getForm()->getPrimaryMutation();
		$params = Helpers::reflectionOf($callback)->getNumberOfParameters() === 1 ? [$this[$mutation]] : [$this[$mutation], $mutation];
		\call_user_func_array($callback, $params);
		
		return $this;
	}
	
	public function forSecondary(callable $callback)
	{
		foreach ($this->getForm()->getMutations() as $mutation) {
			if ($mutation === $this->getForm()->getPrimaryMutation()) {
				continue;
			}
			
			$params = Helpers::reflectionOf($callback)->getNumberOfParameters() === 1 ? [$this[$mutation]] : [$this[$mutation], $mutation];
			\call_user_func_array($callback, $params);
		}
		
		return $this;
	}
}
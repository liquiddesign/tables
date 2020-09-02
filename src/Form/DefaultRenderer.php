<?php

declare(strict_types=1);

namespace Tables\Form;

use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Rendering\DefaultFormRenderer;
use Nette\Utils\Html;

class DefaultRenderer extends DefaultFormRenderer
{
	/**
	 * Renders single visual row.
	 */
	public function renderPair(\Nette\Forms\IControl $control): string
	{
		
		$pair = $this->getWrapper('pair container');
		
		$pair->addHtml($this->renderLabel($control));
		$pair->addHtml($this->renderControl($control));
		$pair->class($this->getValue($control->isRequired() ? 'pair .required' : 'pair .optional'), true);
		$pair->class($control->hasErrors() ? $this->getValue('pair .error') : null, true);
		$pair->class($control->getOption('class'), true);
		if (++$this->counter % 2) {
			$pair->class($this->getValue('pair .odd'), true);
		}
		$pair->id = $control->getOption('id');
		
		if ($control instanceof BaseControl && $form = $control->getForm()) {
			if ($form instanceof Form) {
				$controlMutation = $control->getControlPrototype()->getAttribute('data-mutation');
				$activeMutation = $form->getActiveMutation();
				
				if ($controlMutation) {
					$pair->setAttribute('data-mutation', $controlMutation);
					
					if ($controlMutation !== $activeMutation) {
						$pair->hidden(true);
					}
				}
			}
		}
		
		return $pair->render(0);
	}
	
	/**
	 * Renders 'label' part of visual row of controls.
	 */
	public function renderLabel(\Nette\Forms\IControl $control): Html
	{
		$html = parent::renderLabel($control);
		
		if ($control instanceof BaseControl && $form = $control->getForm()) {
			if ($form instanceof Form) {
				$controlMutation = $control->getControlPrototype()->getAttribute('data-mutation');
				
				if ($controlMutation) {
					$src = $form->getFlagSrc($controlMutation);
					
					$html->addHtml("&nbsp; <img src=$src alt=$controlMutation title=$controlMutation>");
				}
			}
		}
		
		return $html;
	}
}
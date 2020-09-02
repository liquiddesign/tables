<?php

declare(strict_types=1);

namespace Tables\Form;

use Nette\Forms\Controls\TextArea;
use Nette\Forms\Controls\TextInput;

/**
 * Trait ComponentsTrait
 * @mixin \Nette\Forms\Container
 */
trait ComponentsTrait
{
	/**
	 * @deprecated User addWysiwyg instead
	 */
	public function addRichEdit()
	{
		$this->addWysiwyg();
	}
	
	public function addWysiwyg(string $name, bool $perex = false): TextArea
	{
		return $this->addTextArea($name)->setHtmlAttribute('class', $perex ? 'richedit' : 'richperex');
	}
	
	public function addDate(string $name, ?string $format = null, ?string $minDate = null, ?string $maxDate = null): TextInput
	{
		$textbox = $this->addText($name)->setHtmlType('date');
		
		if ($format) {
			$textbox->setHtmlAttribute('data-date-format', $format);
		}
		
		if ($minDate) {
			$textbox->setHtmlAttribute('min', $minDate);
		}
		
		if ($maxDate) {
			$textbox->setHtmlAttribute('max', $maxDate);
		}
		
		return $textbox;
	}
	
	public function addDatetime(string $name, ?string $minDate = null, ?string $maxDate = null, ?int $step = null): TextInput
	{
		$textbox = $this->addText($name)->setHtmlType('datetime-local');
		
		if ($minDate) {
			$textbox->setHtmlAttribute('min', $minDate);
		}
		
		if ($maxDate) {
			$textbox->setHtmlAttribute('max', $maxDate);
		}
		
		if ($maxDate) {
			$textbox->setHtmlAttribute('step', $step);
		}
		
		return $textbox;
	}
	
	public function addTime(string $name, ?string $min = null, ?string $max = null): TextInput
	{
		$textbox = $this->addText($name)->setHtmlType('time');
		
		if ($min) {
			$textbox->setHtmlAttribute('min', $min);
		}
		
		if ($max) {
			$textbox->setHtmlAttribute('max', $max);
		}
		
		return $textbox;
	}
	
	public function addDateRange()
	{
		// TODO http://www.daterangepicker.com/
	}
	
	public function addDatetimeRange()
	{
		// TODO http://www.daterangepicker.com/
	}
	
	public function addColor($name): TextInput
	{
		return $this->addText($name)->setHtmlType('color');
	}
	
	public function addRange()
	{
		// TODO zobrazeni soupatka s od - do
	}
	
	public function addDataSelect()
	{
		// TODO moznost pridani callbacku s daty, moznost nechat data vyplivnout hned nebo ajaxem, link bude v data atributu, handluje ho parent form
	}
	
	public function addDataMultiSelect()
	{
		// TODO viz dataselect
	}
	
	public function addImagePicker(string $name, ?string $src = null, ?string $alt = null)
	{
		// TODO rozsireni image, ktery uz rovnou nahrava do slozky, umi udelat nekolik verzi obrazku pro ruzne adresare, v pripade existence, moznost mazani a preview
	}
	
	public function addDocumentPicker()
	{
		// TODO nahrani do slozku,  v pripade existence, moznost mazani a preview stahnuti
	}
}
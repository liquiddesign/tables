<?php

declare(strict_types=1);

namespace CMS\Table;


class Datagrid extends Datalist
{
	/**
	 * @var \CMS\Table\Column[]
	 */
	protected array $columns = [];
	
	/**
	 * @param string|\Nette\Utils\Html $th
	 * @param string|\Nette\Utils\Html $td
	 * @param callable $dataCallback
	 * @param string|null $orderExpression
	 * @param array $wrapperAttributes
	 * @return \CMS\Table\Column
	 */
	public function addColumn($th, $td, callable $dataCallback, ?string $orderExpression = null, array $wrapperAttributes = []): Column
	{
		$id = \count($this->columns);
		$column = new Column($this, $th, $td, $dataCallback, $orderExpression, $wrapperAttributes);
		$column->setId($id);
		$this->columns[$id] = $column;
		
		return $column;
	}
	
	public function addColumnSelector(): Column
	{
		//$onclick = "$('input[type=\"checkbox\"].row-selector').prop('checked', this.checked); $('input[type=\"checkbox\"].row-selector').change();";
		$selector = $this->getForm()->addCheckbox('__select_all')->setHtmlAttribute('onclick', $onclick);
		
		return $this->addColumnContainer($selector->getControlPart(), '__select', '%s', static function ($container, $item) {
			return [$container->addCheckbox(Table::encodeId($item->uuid))->setAttribute('class', 'row-selector')];
		}, false, null, false, ['class' => 'minimal']);
	}
	
	public function addColumnText(string $th, string $td, array $properties, ?string $orderExpression = null, bool $isOrderNumerical = false, array $wrapperAttributes = []): Column
	{
		return $this->addColumn($th, $td, static function ($table, $item) use ($properties) {
			$vars = [];
			
			foreach ($properties as $property) {
				$vars[] = $item->$property;
			}
			
			return $vars;
		}, $orderExpression, $isOrderNumerical, $wrapperAttributes);
	}
	
	/*
	public function addColumnPriority(): Column
	{
		return $this->addColumnContainer('Pořadí', 'priority', '%s', static function ($container, $item) {
			$textbox = $container->addText(Table::encodeId($item->uuid))->setDefaultValue($item->priority)->setAttribute('size', 1)->setAttribute('class', 'form-control input-sm');
			
			return [$textbox];
		}, true, 'this.priority', true, ['class' => 'minimal']);
	}
	
	public function addColumnHidden($label): Column
	{
		return $this->addColumnContainer('Skrytý', 'hidden', '%s', static function ($container, $item) {
			$textbox = $container->addCheckbox(Table::encodeId($item->uuid))->setDefaultValue($item->hidden)->setAttribute('value', 1);
			
			return [$textbox];
		}, true, 'this.hidden', true, ['class' => 'minimal']);
	}*/
	
	public function addColumnLink(string $label, string $icon, string $uuid, string $link, string $linkClass = 'btn btn-secondary btn-edit'): Column
	{
		return $this->addColumn(
			'',
			'<a href="%s" class="' . $linkClass . '"><i class="fas ' . $icon . '"></i> ' . $label . '</a>',
			static function ($table, $item) use ($link, $uuid) {
				return [$table->getPresenter()->link($link, $item->$uuid)];
			},
			null, ['class' => 'minimal']);
	}
	
	public function addColumnLinkEdit(string $label, string $link): Column
	{
		return $this->addColumn('', '<a href="%s" class="btn btn-primary btn-edit"><i class="fas fa-pencil-alt"></i> Upravit</a>', static function ($table, $item) use ($link, $uuid) {
			return [$table->getPresenter()->link($link, $item->$uuid)];
		}, null, false, ['class' => 'minimal']);
	}
	
	public function addColumnLinkDelete(string $uuid): Column
	{
		return $this->addColumn('', '<a href="%s" class="btn btn-danger btn-delete with-confirm"><i class="fas fa-trash"></i> Smazat</a>', static function ($table, $item) use ($uuid) {
			return [$table->link('deleteRow!', $item->$uuid)];
		}, null, false, ['class' => 'minimal']);
	}
}
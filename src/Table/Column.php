<?php

namespace Tables\Table;

use CMS\Table\Datagrid;
use Nette\Forms\Container;
use Nette\Forms\Controls\BaseControl;
use Nette\Utils\Html;

class Column
{
	private int $id;
	
	/**
	 * @var string|\Nette\Utils\Html
	 */
	private $th;
	
	/**
	 * @var string|\Nette\Utils\Html
	 */
	private $td;
	
	/**
	 * @var callable
	 */
	private $dataCallback;
	
	private string $wrapperTag;
	
	private \Nette\Utils\Html $wrapper;
	
	private ?\Nette\Forms\Container $container = null;
	
	private ?string $orderExpression;
	
	private Datagrid $table;
	
	/**
	 * @var string[]
	 */
	private array $wrapperAttributes;
	
	public function __construct(Datagrid $table, $th, $td, callable $dataCallback, ?string $orderName = null, array $wrapperAttributes = [])
	{
		$this->th = $th;
		$this->td = $td;
		$this->dataCallback = $dataCallback;
		$this->wrapperTag = 'th';
		$this->orderExpression = $orderName;
		$this->table = $table;
		$this->wrapperAttributes = $wrapperAttributes;
	}
	
	public function setContainer(Container $container): void
	{
		$this->container = $container;
	}
	
	public function getContainer(): ?Container
	{
		return $this->container;
	}
	
	public function setId(int $id): void
	{
		$this->id = $id;
	}
	
	public function getId(): int
	{
		return $this->id;
	}
	
	public function getWrapper(): Html
	{
		return $this->wrapper ?? $this->wrapper = Html::el($this->wrapperTag);
	}
	
	public function getTableHead(): string
	{
		return $this->th;
	}
	
	public function getTableData(object $row, bool $passWrapper = false): string
	{
		$tdWrapper = $passWrapper ? Html::el('td') : null;
		$parameters = \call_user_func_array($this->dataCallback, [$this->container ?: $this->table, $row, $tdWrapper]);
		$args = [];
		
		if ($parameters === null) {
			return '';
		}
		
		foreach ($parameters as $p) {
			$args[] = $p instanceof BaseControl ? (string) $p->getControlPart() : $p;
		}
		
		return \vsprintf($this->td, $args);
	}
	
	public function getTableDataExpression(): string
	{
		return $this->td;
	}
	
	public function getDataCallback(): callable
	{
		return $this->dataCallback;
	}
	
	public function isOrdable(): bool
	{
		return $this->orderExpression !== null;
	}
	
	public function isOrderNumerical(): ?bool
	{
		$column = $this->table->getSource(true)->getRepository()->getStructure()->getColumn($this->orderExpression);
		
		if ($column) {
			return $column->getPropertyType() === 'int' || $column->getPropertyType() === 'float';
		}
		
		return null;
	}
	
	public function getOrderExpression(): ?string
	{
		return $this->orderExpression;
	}
	
	public function getAttribute(string $name): string
	{
		return $this->wrapperAttributes[$name] ?? '';
	}
}

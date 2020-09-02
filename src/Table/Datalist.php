<?php

declare(strict_types=1);

namespace Tables\Table;

use Nette\Application\ApplicationException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Component;
use Nette\Utils\Paginator;
use StORM\Collection;

/**
 * @method onLoad(\StORM\Collection $source)
 */
class Datalist extends Component
{
	/** @var callable[]&(callable(Form): void)[]; Occurs before data is load */
	public $onLoad;
	
	/** @var callable[]&(callable(Form): void)[]; Occurs before state is loaded */
	public $onLoadState;
	
	/** @var callable[]&(callable(Form): void)[]; Occurs after state is save */
	public $onSaveState;
	
	/**
	 * @persistent
	 * @string[]
	 */
	public ?array $orderBy = null;
	
	/**
	 * @persistent
	 * @string[]
	 */
	public ?array $filters = null;
	
	/**
	 * @persistent
	 */
	public ?int $page = null;
	
	/**
	 * @persistent
	 */
	public ?int $onPage = null;
	
	/**
	 * @var string[]|callable[]
	 */
	protected array $availableOrderBy = [];
	
	/**
	 * @var string[]
	 */
	protected array $secondaryOrderBy = [];
	
	/**
	 * @var string[]|callable[]
	 */
	protected array $availableFilters = [];
	
	protected Paginator $paginator;
	
	protected ?Collection $source = null;
	
	/**
	 * @var string[]
	 */
	protected array $allowedRepositoryFilters = [];
	
	/**
	 * @var string[]
	 */
	protected array $mutations = [];
	
	protected ?string $primaryMutation = null;
	
	protected ?string $activeMutation = null;
	
	public function __construct()
	{
		$this->paginator = new Paginator();
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
		$this->activeMutation = $mutation;
	}
	
	public function getActiveMutation(): ?string
	{
		return $this->activeMutation;
	}
	
	public function setSource(Collection $source, ?int $onPage = null): void
	{
		$this->source = $source;
		
		foreach (\array_keys($source->getRepository()->getStructure()->getColumns(true)) as $property) {
			// TODO add current mutation of property
			$this->availableOrderBy[$property] = $property;
		}
		
		// TODO throw error if limit or offset is set
	}
	
	public function getSource(bool $needed  = false): ?Collection
	{
		// TODO needed implement
		return $this->source;
	}
	
	protected function cloneSource(): Collection
	{
		if (!$this->source) {
			throw new ApplicationException('Source is not set');
		}
		
		return clone $this->source;
	}
	
	public function getPaginator(): \Nette\Utils\Paginator
	{
		if ($this->page !== null) {
			$this->paginator->setPage($this->page);
		}
		
		if ($this->onPage !== null) {
			$this->paginator->setItemsPerPage($this->onPage);
		}
		
		if ($this->source) {
			$this->paginator->setItemCount($this->source->count());
		}
		
		return $this->paginator;
	}
	
	public function setPage(int $page): void
	{
		$this->page = $page;
	}
	
	public function getPage(): int
	{
		return $this->page ?: 1;
	}
	
	public function setOnPage(?int $onPage): void
	{
		$this->onPage = $onPage;
	}
	
	public function getOnPage(): ?int
	{
		return $this->onPage;
	}
	
	public function addFilterExpression($name, callable $callback): void
	{
		$this->availableFilters[$name] = $callback;
	}
	
	public function removeFilterExpressions(array $listToRemove): void
	{
		foreach ($listToRemove as $name) {
			unset($this->availableFilters[$name]);
		}
	}
	
	/**
	 * @param string[] $list
	 */
	public function allowRepositoryFilters(array $list)
	{
		$this->allowedRepositoryFilters = $list;
	}
	
	public function addOrderByExpression(string $name, callable $callback): void
	{
		$this->availableOrderBy[$name] = $callback;
	}
	
	/**
	 * @param string[] $listToRemove
	 */
	public function removeOrderByExpressions(array $listToRemove): void
	{
		foreach ($listToRemove as $name) {
			unset($this->availableOrderBy[$name]);
		}
	}
	
	/**
	 * @return string[]
	 */
	public function getOrderBy(): array
	{
		return $this->orderBy;
	}
	
	/**
	 * @param string[] $orderBy
	 */
	public function setOrderBy(array $orderBy): void
	{
		$this->orderBy[] = $orderBy;
	}
	
	/**
	 * @param string[] $array
	 */
	public function setSecondaryOrderBy(array $orderBy): void
	{
		$this->secondaryOrderBy = $orderBy;
	}
	
	/**
	 * @return string[]
	 */
	public function getSecondaryOrderBy(): array
	{
		return $this->secondaryOrderBy;
	}
	
	/**
	 * @return \StORM\Entity[]
	 */
	public function getItemsOnPage(): array
	{
		$source = $this->cloneSource();
		
		// FILTERS
		if ($this->filters && count($this->filters) > 0) {
			foreach ($this->filters as $name => $value) {
				if (!isset($this->availableFilters[$name]) || !\in_array($name, $this->allowedRepositoryFilters)) {
					throw new BadRequestException("Filter $name is not allowed");
				}
				
				if (\in_array($name, $this->allowedRepositoryFilters)) {
					$source->filter([$name => $value]);
				} else {
					\call_user_func_array($this->availableFilters[$name], [$source, $value]);
				}
			}
		}
		
		// ORDER BY
		if ($this->orderBy !== null && count($this->orderBy) > 0) {
			foreach (\array_keys($this->orderBy) as $name) {
				if (!isset($this->availableOrderBy)) {
					throw new BadRequestException("Order by $name is not allowed");
				}
			}
			
			if (\count($this->orderBy) == 1 && \is_callable(\current($this->orderBy))) {
				$callback = \current($this->orderBy);
				\call_user_func_array($callback, [$source, \key($this->orderBy)]);
			} else {
				$source->setOrderBy(\array_merge($this->orderBy, $this->secondaryOrderBy));
			}
		}
		
		// PAGE
		if ($this->onPage !== null) {
			$source->setPage($this->getPaginator()->getPage(), $this->getPaginator()->getItemsPerPage());
		}
		
		$this->onLoad($source);
		
		return $source->toArray();
	}
}

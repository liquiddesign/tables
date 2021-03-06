<?php

namespace DB;

use StORM\ICollection;

class StockRepository extends \StORM\Repository // @codingStandardsIgnoreLine
{
	public function filterId(string $value, ICollection $collection): void
	{
		$collection->setWhere('this.uuid', $value);
	}
}

<?php


namespace Kaliop\ImportDiscount\Cron;

use Kaliop\ImportDiscount\Helper\Import;

class ImportDiscount
{
	protected $import;

    public function __construct(Import $import)
    {
        $this->import = $import;
    }

    public function execute() {
    	$this->import->readCSV();
    }
}
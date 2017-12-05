<?php

namespace App;

class TransactionListImportHandler implements \Maatwebsite\Excel\Files\ImportHandler
{
    public function handle($import)
    {
        // get the results
        $results = $import->get();
    }

}
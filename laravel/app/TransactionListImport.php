<?php

namespace App;

use Illuminate\Support\Facades\Input;

class TransactionListImport extends \Maatwebsite\Excel\Files\ExcelFile
{
    protected $delimiter = ',';
    protected $enclosure = '"';
    protected $lineEnding = '\r\n';

    public function getFile()
    {
        // Import a user provided file
        $fileName = Input::post('filename');

        // Return it's location
        return public_path('transactions') . '/' . $fileName;
    }

    public function getFilters()
    {
        return [
            'chunk'
        ];
    }

}
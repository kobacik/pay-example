<?php

namespace App\Services;

use App\Enums\CSVEnum;
use App\Exceptions\FileNotFoundException;
use Illuminate\Support\Collection;

class CollectionService
{
    public function collectionFromCsv($csvPath): Collection
    {
        throw_if(! file_exists($csvPath), new FileNotFoundException('File not found!'));


        $linesFromCSV = array_map('str_getcsv', file($csvPath));
        $lines = collect($linesFromCSV);

        return $lines->map(function ($process) {
            return [
                'date' => $process[CSVEnum::OPERATION_DATE->value],
                'user_id' => $process[CSVEnum::USER_ID->value],
                'user_type' => $process[CSVEnum::USER_TYPE->value],
                'operation_type' => $process[CSVEnum::OPERATION_TYPE->value],
                'operation_amount' => $process[CSVEnum::OPERATION_AMOUNT->value],
                'operation_currency' => $process[CSVEnum::CURRENCY->value]
            ];
        });
    }
}

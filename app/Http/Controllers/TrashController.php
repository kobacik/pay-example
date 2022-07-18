<?php

namespace App\Http\Controllers;

use App\Services\CollectionService;
use App\Services\CommissionService;
use App\Services\CurrencyService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class TrashController extends Controller
{
    public function __construct(
        protected CommissionService $commissionService,
        protected CollectionService $collectionService,
        protected CurrencyService $currencyService
    ){}

    public function index3()
    {

        $csvFile =  public_path('input.csv');
        $collection = $this->collectionService->collectionFromCsv($csvFile);




        foreach ($collection as $key => $operation) {
            $date = $operation['date'];

            $startOfWeek = Carbon::parse($date)
                ->startOfWeek()
                ->format('Y-m-d');

            $endOfWeek = Carbon::parse($date)
                ->endOfWeek()
                ->format('Y-m-d');

            $weeklyOperations = $collection->where('user_id', $operation['user_id'])
                ->whereBetween('date', [$startOfWeek, $endOfWeek]);
            if($operation['operation_type'] == 'deposit') {
                echo '<pre>'. 'deposit'.'<br>';
            }else{
                if($operation['user_type'] == 'business') {
                    echo '<pre>'. 'business'. '<br>';
                }else{
                    if($operation['operation_currency'] != 'EUR' && $operation['operation_type'] == 'withdraw' && $operation['user_type'] == 'private') {

                        $totalEUR = 0;
                        $totalCount = 0;
                        foreach ($weeklyOperations as $weeklyOperation) {
                            if($weeklyOperation['operation_currency'] != 'EUR') {
                                $totalEUR += $this->currencyService->convertToBase($weeklyOperation['operation_currency'], $weeklyOperation['operation_amount']);
                            }else{
                                $totalEUR += $weeklyOperation['operation_amount'];
                            }
                            $totalCount++;
                        }
                        if($totalEUR <= 1000 & $totalCount <= 3) {
                            $totalEUR+= $weeklyOperation['operation_amount'];
                            $totalCount++;
                            echo '0'.'<br>';
                        }else{
                            $result = $totalEUR - 1000;
                            echo '-1000'.'<br>';
                            //return $totalEUR - 1000 * 0.3;
                        }

                        echo 'as'.'<br>';
                    }
                }
            }
        }
    }


    /*   public function getBaseAmount()
       {
           $currency = 'JPY';
           $amount = 3000000;

           $rates = Http::get(config('pay-example.exchange-api-url'));
           $exchange = $rates['rates'][$currency];
           return $amount / $exchange;

       }*/

    public function checkFreeCharge($row, Collection $collection)
    {
        $date = $row['date'];
        $startOfWeek    = Carbon::parse($date)
            ->startOfWeek()
            ->format('Y-m-d');
        $endOfWeek      = Carbon::parse($date)
            ->endOfWeek()
            ->format('Y-m-d');

        return $collection->where('user_id', $row['user_id'])->whereBetween($row['date'], [$startOfWeek, $endOfWeek]);

    }

    /**
     * @throws \Throwable
     */
    public function indexOld()
    {
        $csvFile =  public_path('input.csv');

        $collection = $this->collectionService->collectionFromCsv($csvFile);



        foreach ($collection as $key => $item) {
            $startOfWeek    = Carbon::parse($item['date'])
                ->startOfWeek()
                ->format('Y-m-d');
            $endOfWeek      = Carbon::parse($item['date'])
                ->endOfWeek()
                ->format('Y-m-d');

            /*$checkWeek = $collection->where('user_id', $item['user_id'])
                                    ->whereBetween('date', [$startOfWeek, $endOfWeek]);
            return $checkWeek->sum('operation_amount');*/


            return '<pre>'. $key. '-'. $collection
                    ->where('user_id', $item['user_id'])
                    ->whereBetween('date', [$startOfWeek, $endOfWeek])
                . '<br>';

            /*return $startOfWeek.' - '. $endOfWeek;

            if($item['date'])



            // Check free charge
            echo $this->checkFreeCharge($item, $collection).'<br>';*/




            //dd($time);
        }


        exit;




        foreach ($collection as $row) {
            echo match ($row['operation_type']) {
                'deposit' => $this->depositCommissionService->getFee($row, $collection) . '<br>',
                'withdraw' => $this->withdrawCommissionService->getFee($row, $collection) . '<br>',
                default => '-' . '<br>',
            };

        }
        exit;
    }

    public function index2()
    {
        $exchangeRates = Http::get(config('pay-example.exchange-api-url'));

        $value = 3000000;
        $currency = 'JPY';
        $freeChargeLimit = 1000;

        if($currency != $exchangeRates['base']) {
            $valueToBase = $value / $exchangeRates['rates'][$currency];
            if($valueToBase > $freeChargeLimit) {
                $newBase = $valueToBase - $freeChargeLimit;

                $newBaseEUR = $newBase * $exchangeRates['rates'][$currency];

                return number_format($newBaseEUR * 0.3 / 100, 2);

            }

            return number_format($valueToBase);
        }




        $csvFile = public_path('input.csv');
        $lines = array_map('str_getcsv', file($csvFile));
        //$resultArray = []

        /* foreach ($lines as $process) {
             return $this->commissionService->
         }*/


        //return $this->commissionService->get();

        /* $number = 200.00;
         $fee = 0.03;

         return ($number * $fee) / 100;*/


        foreach ($lines as $key => $line) {

            echo $key+1 . ' => '.$this->calculateFee($line).'<br>';


            /*if(in_array($line[CSVEnum::OPERATION_TYPE->value], config('pay-example.process-types')))
            {
                echo $this->detectProcessType($line[CSVEnum::OPERATION_TYPE->value]).'<br>';
            }else{
                echo 'withdraw'.'<br>';
            }*/


            //if($line[CSVEnum::OPERATION_TYPE->value] == 'deposit') {

            //return $this->getDepositFee($line[CSVEnum::OPERATION_TYPE->value]);
            /*$value = floatval($line[CSVEnum::OPERATION_AMOUNT->value]);
            return number_format(200, 3) * number_format(0.03, 3);*/
            //dd($value * number_format(0.03, 2));
            //}
        }

        exit;
        //return $lines[0][CSVEnum::CURRENCY->value];

        //return $exchangeRates;
        //return 3 * $exchangeRates['rates']['TRY'];
        //return $exchangeRates['base'];
    }
}

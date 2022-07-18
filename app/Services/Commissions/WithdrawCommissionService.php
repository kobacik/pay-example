<?php

namespace App\Services\Commissions;

use App\Exceptions\OperationNotFoundException;
use App\Exceptions\UserTypeNotFound;
use App\Services\CommissionService;
use App\Services\CurrencyService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class WithdrawCommissionService
{
    /**
     * @param CurrencyService $currencyService
     * @param $availableBalance
     */
    public function __construct(
        protected CurrencyService $currencyService,
        protected $availableBalance = 1000, // EUR,
    ){}


    public function getCommission($operation, $key, $collection)
    {
        // Calculate fee by user type
        switch ($operation['user_type'])
        {
            case 'business':
                return $this->calculateBusiness($operation);
                break;
            case 'private':
                return $this->calculatePrivate($operation, $collection);
                break;
            default:
                return throw new UserTypeNotFound('User type not found!');
        }
    }

    private function calculateBusiness($operation)
    {
        $businessWithdrawFee = config('pay-example.withdrawn.business');

        return number_format(($operation['operation_amount'] * $businessWithdrawFee) / 100, 2);
    }

    private function calculatePrivate($operation, $collection)
    {
        $arr = $this->weeklyOperations($operation, $collection);

        // @TODO Check Currency
        if($operation['operation_currency'] != 'EUR') {
            // Calculate currency to base and check for available balance
            return $operation['operation_currency'];
        }

        if(
            $arr['operationCount'] <= config('pay-example.free-charge-time-per-week')
            &&
            $arr['totalAmount'] <= config('pay-example.free-charge-amount')
        ) {

            return '0.00'; // Free
        }

        if($arr['operationCount'] >= 3 || $arr['totalAmount'] >= config('pay-example.free-charge-amount')) {
            if(($operation['operation_amount'] - $this->availableBalance) == 0) {
                // 7th line can't show correct result because 6th line isn't EUR
                return number_format(($operation['operation_amount'] * config('pay-example.withdrawn.private')) / 100, 2);
            }
            if(($operation['operation_amount'] - $this->availableBalance) < 0) {
                return number_format(($operation['operation_amount'] * config('pay-example.withdrawn.private')) / 100, 2);
            }

            return number_format((($operation['operation_amount'] - $this->availableBalance) * config('pay-example.withdrawn.private')) / 100, 2);
        }


        return number_format(($operation['operation_amount'] * config('pay-example.withdrawn.private')) / 100, 2);
    }


    private function weeklyOperations($operation, $collection)
    {
        // Total Amount
        $startOfWeek = Carbon::parse($operation['date'])
            ->startOfWeek()
            ->format('Y-m-d');

        $endOfWeek = Carbon::parse($operation['date'])
            ->endOfWeek()
            ->format('Y-m-d');

        $totalAmount = 0;
        $operationCount = 0;


        $weeklyOperations = $collection->where('user_id', $operation['user_id'])
            ->where('operation_type', 'withdraw')
            ->where('user_type', 'private')
            ->whereBetween('date', [$startOfWeek, $endOfWeek]);

        foreach ($weeklyOperations as $operation1) {
            $totalAmount += $operation1['operation_amount'];
            $operationCount++;
        }

        return [
            'weeklyOperations' => $weeklyOperations,
            'totalAmount' => $totalAmount,
            'operationCount' => $operationCount,
            'isFree' => $totalAmount <= 1000 && $operationCount <= 3 ? true : false
        ];

    }

    private function isFree($operation, $collection)
    {
        $totalOperation = 0;
        $totalPrice = 0;
        $priceLimit = 1000;
        $operationLimit = 0;
        $weeklyOperations = $this->weeklyOperations($operation, $collection);

        if($operation['operation_amount'] != 'EUR') {
            $operationAmount = $this->currencyService->convertToBase($operation['operation_currency'], $operation['operation_amount']);
        }else{
            $operationAmount = $operation['operation_amount'];
        }

        $this->totalAmount += 100;




        if($operationAmount <= $priceLimit && $totalOperation < 3) {
            $totalOperation ++;
            $totalPrice += $operationAmount;

        }else{
            $result = ($totalPrice - $priceLimit) * 0.3 / 100;
        }
        return $operationAmount;

    }




}

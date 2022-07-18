<?php

namespace App\Services\Commissions;

use App\Exceptions\OperationNotFoundException;

class DepositCommissionService
{
    /**
     * @throws \Throwable
     */
    public function getCommission(array $operation)
    {
        return number_format(($operation['operation_amount'] * config('pay-example.deposit-fee')) / 100, 2);
    }
}

<?php

namespace App\Services;

use App\Exceptions\OperationNotFoundException;
use App\Services\Commissions\DepositCommissionService;
use App\Services\Commissions\WithdrawCommissionService;
use Illuminate\Support\Collection;

class CommissionService
{
    public function __construct(
        protected DepositCommissionService $depositCommissionService,
        protected WithdrawCommissionService $withdrawCommissionService
    ){}

    /**
     * @throws \Throwable
     */
    public function calculateFee(array $operation, int $key, Collection $collection)
    {
        throw_if(! in_array($operation['operation_type'], config('pay-example.operation-types')), new OperationNotFoundException('Operation not found!'));

        return match ($operation['operation_type']) {
            'deposit' => $this->depositCommissionService->getCommission($operation),
            'withdraw' => $this->withdrawCommissionService->getCommission($operation, $key, $collection),
            default => '-' ,
        };
    }
}

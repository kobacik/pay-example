<?php

namespace App\Http\Controllers;

use App\Services\CollectionService;
use App\Services\CommissionService;

class HomeController extends Controller
{
    public function __construct(
        protected CommissionService $commissionService,
        protected CollectionService $collectionService,
    ){}


    /**
     * @throws \Throwable
     */
    public function index()
    {
        $csvFile =  public_path('input.csv');
        $collection = $this->collectionService->collectionFromCsv($csvFile);
        $fees = [];

        foreach ($collection as $key => $operation) {
            $fees[] = $this->commissionService->calculateFee($operation, $key, $collection);
        }

        return view('fees', compact('fees'));


    }


}

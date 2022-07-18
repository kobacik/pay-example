<?php
namespace App\Enums;


enum CSVEnum: int
{
    case OPERATION_DATE     = 0;
    case USER_ID            = 1;
    case USER_TYPE          = 2;
    case OPERATION_TYPE     = 3;
    case OPERATION_AMOUNT   = 4;
    case CURRENCY           = 5;
}

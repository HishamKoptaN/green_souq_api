<?php

namespace App\Features\Users\Domain\Enums;

enum UserType: string
{
    case Shop = 'shop';
    case SalesEngineer = 'salesEngineer';
    case Farmer = 'farmer';
    case AgriculturalToolsMerchant = 'agriculturalToolsMerchant';
    case JobSeeker = 'jobSeeker';
}

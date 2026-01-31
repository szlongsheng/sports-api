<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Common\BatchController;
use App\Models\Unit;

class BatchUnitController extends BatchController
{
    protected string $modelClass = Unit::class;
}

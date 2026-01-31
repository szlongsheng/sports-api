<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Common\BatchController;
use App\Models\Admin;

class BatchUserController extends BatchController
{
    protected string $modelClass = Admin::class;
}

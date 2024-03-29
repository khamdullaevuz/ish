<?php

use App\Http\Controllers\Api\V1\CompanyController;
use App\Http\Controllers\Api\V1\EmployeeController;
use App\Http\Controllers\Api\V1\UserController;

Route::prefix('auth')->group(base_path('routes/api/v1/auth.php'));

Route::prefix('companies')
    ->middleware('auth:sanctum')
    ->resource('companies', CompanyController::class);

Route::prefix('employees')
    ->middleware('auth:sanctum')
    ->resource('employees', EmployeeController::class);

Route::prefix('users')
    ->middleware(['auth:sanctum', 'can:manage-users'])
    ->resource('users', UserController::class);

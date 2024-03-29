<?php

namespace App\UseCases\Employee;

use App\Dtos\EmployeeDto;
use App\Models\Employee;
use Exception;
use Illuminate\Support\Facades\DB;

class UpdateUseCase
{
    /**
     * @throws Exception
     */
    public function perform(Employee $employee, EmployeeDto $employeeDto): Employee
    {
        DB::beginTransaction();

        try {
            $employee->passport = $employeeDto->passport;
            $employee->surname = $employeeDto->surname;
            $employee->name = $employeeDto->name;
            $employee->patronymic = $employeeDto->patronymic;
            $employee->position = $employeeDto->position;
            $employee->phone = $employeeDto->phone;
            $employee->address = $employeeDto->address;

            $employee->save();

            DB::commit();

            return $employee;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

<?php

namespace App\UseCases\Employee;

use App\Dtos\EmployeeDto;
use App\Models\Employee;
use Exception;
use Illuminate\Support\Facades\DB;

class CreateUseCase
{
    /**
     * @throws Exception
     */
    public function perform(EmployeeDto $employeeDto): Employee
    {
        DB::beginTransaction();

        try {
            $employee = new Employee();
            $employee->passport = $employeeDto->passport;
            $employee->surname = $employeeDto->surname;
            $employee->name = $employeeDto->name;
            $employee->patronymic = $employeeDto->patronymic;
            $employee->position = $employeeDto->position;
            $employee->phone = $employeeDto->phone;
            $employee->address = $employeeDto->address;
            $employee->company_id = user_company()->id;

            $employee->save();

            DB::commit();

            return $employee;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

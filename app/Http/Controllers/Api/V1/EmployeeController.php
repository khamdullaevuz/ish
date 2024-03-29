<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\Utils\PaginationDto;
use App\Filters\EmployeeFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\IndexRequest;
use App\Http\Requests\Employee\StoreRequest;
use App\Http\Requests\Employee\UpdateRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\UseCases\Employee\CreateUseCase;
use App\UseCases\Employee\UpdateUseCase;
use Exception;

class EmployeeController extends Controller
{
    public function index(IndexRequest $request)
    {
        $this->authorize('viewAny', Employee::class);

        $query = Employee::query()->company();

        if(!$request->has('company_id'))
        {
            $query->with('company');
        }

        $paginator = (new EmployeeFilter($query, $request))
            ->apply()
            ->paginate(perPage: request('per_page', 10), page: request('page', 1));

        $employees = $paginator->map(fn ($employee) => new EmployeeResource($employee));

        $paginator = $employees->isEmpty() ? null : $paginator;

        return [
            'employees' => $employees,
            'pagination' => PaginationDto::from(compact('paginator'))
        ];
    }

    /**
     * @throws Exception
     */
    public function store(StoreRequest $request, CreateUseCase $useCase)
    {
        $this->authorize('create', Employee::class);

        $employee = $useCase->perform($request->toDto());

        $employee = new EmployeeResource($employee);

        return compact('employee');
    }

    public function show($employee)
    {
        $employee = Employee::find($employee);
        error_if($employee === null, 'MODEL_NOT_FOUND');

        $this->authorize('view', $employee);

        $employee = new EmployeeResource($employee);

        return compact('employee');
    }

    /**
     * @throws Exception
     */
    public function update(UpdateRequest $request, Employee $employee, UpdateUseCase $useCase)
    {
        $this->authorize('update', $employee);

        $employee = $useCase->perform($employee, $request->toDto());

        $employee = new EmployeeResource($employee);

        return compact('employee');
    }

    public function destroy(Employee $employee)
    {
        $this->authorize('delete', $employee);

        $employee->delete();

        return 'Employee deleted successfully';
    }
}

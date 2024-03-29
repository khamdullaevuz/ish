<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\Utils\PaginationDto;
use App\Filters\CompanyFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Company\IndexRequest;
use App\Http\Requests\Company\StoreRequest;
use App\Http\Requests\Company\UpdateRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\UseCases\Company\CreateUseCase;
use App\UseCases\Company\UpdateUseCase;
use Exception;

class CompanyController extends Controller
{
    public function index(IndexRequest $request)
    {
        $this->authorize('viewAny', Company::class);

        $query = Company::query();

        $paginator = (new CompanyFilter($query, $request))
            ->apply()
            ->paginate(perPage: request('per_page', 10), page: request('page', 1));

        $companies = $paginator->map(fn ($company) => new CompanyResource($company));

        $paginator = $companies->isEmpty() ? null : $paginator;

        return [
            'companies' => $companies,
            'pagination' => PaginationDto::from(compact('paginator'))
        ];
    }

    /**
     * @throws Exception
     */
    public function store(StoreRequest $request, CreateUseCase $useCase)
    {
        $this->authorize('create', Company::class);

        $company = $useCase->perform($request->toDto());

        $company = new CompanyResource($company);

        return compact('company');
    }

    public function show($company)
    {
        $company = Company::find($company);
        error_if($company === null, 'MODEL_NOT_FOUND');

        $this->authorize('view', $company);

        $company = new CompanyResource($company);

        return compact('company');
    }

    /**
     * @throws Exception
     */
    public function update(UpdateRequest $request, Company $company, UpdateUseCase $useCase)
    {
        $this->authorize('update', $company);

        $company = $useCase->perform($company, $request->toDto());

        $company = new CompanyResource($company);

        return compact('company');
    }

    public function destroy(Company $company)
    {
        $this->authorize('delete', $company);

        $company->delete();

        return 'Company deleted successfully';
    }
}

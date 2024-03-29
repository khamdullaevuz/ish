<?php

namespace App\UseCases\Company;

use App\Dtos\CompanyDto;
use App\Models\Company;
use Exception;
use Illuminate\Support\Facades\DB;

class UpdateUseCase
{
    /**
     * @throws Exception
     */
    public function perform(Company $company, CompanyDto $companyDto): Company
    {
        DB::beginTransaction();

        try {
            $company->name = $companyDto->name;
            $company->owner_name = $companyDto->owner_name;
            $company->address = $companyDto->address;
            $company->email = $companyDto->email;
            $company->website = $companyDto->website;
            $company->phone = $companyDto->phone;
            $company->save();

            DB::commit();

            return $company;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->company_id === null;
    }

    public function view(User $user, Company $company)
    {
        return $user->company_id === $company->id || $user->company_id === null;
    }

    public function create(User $user)
    {
        return $user->company_id === null;
    }

    public function update(User $user, Company $company)
    {
        return $user->company_id === $company->id || $user->company_id === null;
    }

    public function delete(User $user, Company $company)
    {
        return $user->company_id === $company->id || $user->company_id === null;
    }

    public function restore(User $user, Company $company)
    {
        return $user->company_id === $company->id || $user->company_id === null;
    }

    public function forceDelete(User $user, Company $company)
    {
        return $user->company_id === $company->id || $user->company_id === null;
    }
}

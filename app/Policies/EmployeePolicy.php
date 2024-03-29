<?php

namespace App\Policies;

use App\Enums\UserRoles;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Employee $employee)
    {
        return $user->company_id === $employee->company_id && $user->role === UserRoles::USER;
    }

    public function create(User $user)
    {
        return $user->company_id !== null && $user->role === UserRoles::USER;
    }

    public function update(User $user, Employee $employee)
    {
        return $user->company_id === $employee->company_id && $user->role === UserRoles::USER;
    }

    public function delete(User $user, Employee $employee)
    {
        return $user->company_id === $employee->company_id && $user->role === UserRoles::USER;
    }

    public function restore(User $user, Employee $employee)
    {
        return $user->company_id === $employee->company_id && $user->role === UserRoles::USER;
    }

    public function forceDelete(User $user, Employee $employee)
    {
        return $user->company_id === $employee->company_id && $user->role === UserRoles::USER;
    }
}

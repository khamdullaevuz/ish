<?php

namespace App\UseCases\User;

use App\Dtos\UserDto;
use App\Enums\UserRoles;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateUseCase
{
    /**
     * @throws Exception
     */
    public function perform(UserDto $userDto): User
    {
        DB::beginTransaction();

        try {
            $user = new User();
            $user->name = $userDto->name;
            $user->email = $userDto->email;
            $user->password = bcrypt($userDto->password);
            $user->email_verified_at = now();
            $user->role = UserRoles::USER;
            $user->remember_token = Str::random(10);
            $user->company_id = $userDto->company_id;
            $user->save();

            DB::commit();

            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

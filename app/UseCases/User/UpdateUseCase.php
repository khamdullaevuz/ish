<?php

namespace App\UseCases\User;

use App\Dtos\UserDto;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class UpdateUseCase
{
    /**
     * @throws Exception
     */
    public function perform(User $user, UserDto $userDto): User
    {
        DB::beginTransaction();

        try {
            $user->name = $userDto->name;
            $user->email = $userDto->email;
            $user->password = bcrypt($userDto->password);
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

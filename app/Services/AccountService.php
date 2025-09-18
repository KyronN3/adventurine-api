<?php

namespace App\Services;

use App\Exceptions\AccountServiceException;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AccountService
{
    /**
     * @throws AccountServiceException
     */
    public function getAccount()
    {
        try {
            return User::with(['roles' => function ($query) {
                $query->select('name');
            }])->select(['id', DB::raw("REPLACE(Office, 'OFFICE OF THE CITY ', '') as office"), 'control_no'])->paginate(30);

        } catch (\Exception $e) {
            throw new AccountServiceException('Failed to retrieve all accounts events: ' . $e->getMessage(), '', $e->getCode(), $e->getPrevious());
        }
    }

    /**
     * @throws AccountServiceException
     */

    public function deleteAccount(): bool
    {
        try {
            $validated = request()->validate(['deleteControlId' => 'required|digits:6', 'passwordConfirmation' => 'required']);

            $user = User::query()->where('control_no', $validated['deleteControlId'])->firstOrFail();

            if (Hash::check($validated['passwordConfirmation'], $user->password)) {
                $user->delete();
                return true;
            }
            return false;
        } catch (ModelNotFoundException $e) {
            throw new AccountServiceException('Account not found ', '', 404, $e->getPrevious());
        } catch (QueryException $e) {
            throw new AccountServiceException('A database error occurred: ' . $e->getMessage(), '', 500, $e->getPrevious());
        }
    }
}

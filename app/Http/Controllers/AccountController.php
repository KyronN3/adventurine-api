<?php

namespace App\Http\Controllers;

use App\Components\ResponseFormat;
use App\Exceptions\AccountServiceException;
use App\Services\AccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    protected AccountService $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function getAccount(): JsonResponse
    {
        try {
            $account = $this->accountService->getAccount();
            return ResponseFormat::success('Account retrieved successfully', $account);
        } catch (\Exception $e) {
            return ResponseFormat::error('Error retrieving account: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @throws AccountServiceException
     */
    public function deleteAccount(): JsonResponse
    {
        if (Auth::user()->hasRole('hr')) {
            $account = $this->accountService->deleteAccount();
            if ($account) {
                return ResponseFormat::success('Account deleted successfully', ['success' => $account]);
            }

            return ResponseFormat::error('Unable to Delete Account, Wrong Password Confirmation.', 400);
        }
        return ResponseFormat::error('You do not have permission to delete account. Only HR can Delete Account.', 400);


    }

}

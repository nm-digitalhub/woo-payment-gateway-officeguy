<?php

namespace NmDigitalHub\SumitPayment\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use NmDigitalHub\SumitPayment\Services\TokenService;

class TokenController extends Controller
{
    protected TokenService $tokenService;

    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    /**
     * Create a new payment token
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'card_number' => 'required_without:single_use_token|string',
            'cvv' => 'required_without:single_use_token|string',
            'exp_month' => 'required_without:single_use_token|integer|min:1|max:12',
            'exp_year' => 'required_without:single_use_token|integer',
            'citizen_id' => 'nullable|string',
            'single_use_token' => 'required_without_all:card_number,cvv,exp_month,exp_year|string',
        ]);

        $userId = auth()->id();

        $result = $this->tokenService->createToken($validated, $userId);

        return response()->json($result);
    }

    /**
     * Get all tokens for authenticated user
     */
    public function index()
    {
        $userId = auth()->id();

        if (!$userId) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized',
            ], 401);
        }

        $tokens = $this->tokenService->getUserTokens($userId);

        return response()->json([
            'success' => true,
            'tokens' => $tokens,
        ]);
    }

    /**
     * Delete a payment token
     */
    public function destroy(Request $request, int $tokenId)
    {
        $userId = auth()->id();

        if (!$userId) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized',
            ], 401);
        }

        $deleted = $this->tokenService->deleteToken($tokenId, $userId);

        return response()->json([
            'success' => $deleted,
        ]);
    }

    /**
     * Set token as default
     */
    public function setDefault(Request $request, int $tokenId)
    {
        $userId = auth()->id();

        if (!$userId) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized',
            ], 401);
        }

        $success = $this->tokenService->setDefaultToken($tokenId, $userId);

        return response()->json([
            'success' => $success,
        ]);
    }
}

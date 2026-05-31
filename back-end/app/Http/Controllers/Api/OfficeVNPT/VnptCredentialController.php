<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\OfficeVNPT;

use App\Http\Controllers\Controller;
use App\Http\Requests\OfficeVNPT\SaveVnptCredentialRequest;
use App\Services\VnptCredentialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VnptCredentialController extends Controller
{
    public function __construct(
        private readonly VnptCredentialService $vnptCredentialService,
    ) {}

    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        Log::info('VnptCredentialController::show', ['user_id' => $user?->id, 'token' => substr($request->bearerToken() ?? '', 0, 20)]);

        if ($user === null) {
            Log::warning('VnptCredentialController::show - no user from request');
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $credential = $this->vnptCredentialService->getByUser($user->id);

        if ($credential === null) {
            return response()->json([
                'success' => true,
                'data' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $credential,
        ]);
    }

    public function store(SaveVnptCredentialRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $this->vnptCredentialService->save($user->id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => trans('messages.vnpt.credential_saved'),
            'data' => $data,
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();
        $this->vnptCredentialService->delete($user->id);

        return response()->json([
            'success' => true,
            'message' => trans('messages.vnpt.credential_deleted'),
        ]);
    }
}

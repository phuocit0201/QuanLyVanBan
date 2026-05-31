<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\OfficeVNPT;

use App\Http\Controllers\Controller;
use App\Http\Requests\OfficeVNPT\QuangNgaiLoginRequest;
use App\Services\OfficeVNPT\QuangNgaiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller for Quang Ngai Office external API integration.
 */
class QuangNgaiController extends Controller
{
    public function __construct(
        private readonly QuangNgaiService $quangNgaiService,
    ) {}

    public function login(QuangNgaiLoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $result = $this->quangNgaiService->login($credentials);

        if (isset($result['success']) && $result['success'] === false) {
            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? trans('messages.auth.login_failed'),
            ], $result['status_code'] ?? 401);
        }

        return response()->json([
            'success' => true,
            'message' => trans('messages.auth.login_success'),
            'data' => $result,
        ]);
    }

    /**
     * Get document list with auto-relogin on 401.
     */
    public function getDocumentList(Request $request): JsonResponse
    {
        $param = $request->input('param') ?? '';
        $pageNo = (int) ($request->input('pageNo') ?? 1);
        $pageRec = (int) ($request->input('pageRec') ?? 10);
        $kho = $request->input('kho') ?? 'Tra cứu văn bản';

        $credentials = [
            'username' => $request->input('credentials.username'),
            'password' => $request->input('credentials.password'),
        ];

        $result = $this->quangNgaiService->getDocumentList(
            param: (string) $param,
            pageNo: $pageNo,
            pageRec: $pageRec,
            kho: (string) $kho,
            credentials: $credentials,
        );

        if (isset($result['success']) && $result['success'] === false) {
            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? trans('messages.general.error'),
            ], $result['status_code'] ?? 400);
        }

        return response()->json([
            'success' => true,
            'message' => trans('messages.general.success'),
            'data' => $result,
        ]);
    }

    /**
     * Fetch documents from API and save to database.
     */
    public function syncDocuments(Request $request): JsonResponse
    {
        $param = $request->input('param') ?? '';
        $pageNo = (int) ($request->input('pageNo') ?? 1);
        $pageRec = (int) ($request->input('pageRec') ?? 10);
        $kho = $request->input('kho') ?? 'Tra cứu văn bản';
        $userId = $request->input('user_id');

        $credentials = [
            'username' => $request->input('credentials.username'),
            'password' => $request->input('credentials.password'),
        ];

        $result = $this->quangNgaiService->fetchAndSaveDocuments(
            param: (string) $param,
            pageNo: $pageNo,
            pageRec: $pageRec,
            kho: (string) $kho,
            credentials: $credentials,
            userId: $userId !== null ? (int) $userId : null,
        );

        if (isset($result['success']) && $result['success'] === false) {
            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? trans('messages.general.error'),
            ], $result['status_code'] ?? 400);
        }

        return response()->json([
            'success' => true,
            'message' => trans('messages.general.success'),
            'data' => $result['data'],
            'saved_count' => $result['saved_count'] ?? 0,
        ]);
    }
}

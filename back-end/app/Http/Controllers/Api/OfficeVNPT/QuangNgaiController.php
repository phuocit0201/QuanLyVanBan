<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\OfficeVNPT;

use App\Http\Controllers\Controller;
use App\Services\OfficeVNPT\QuangNgaiService;
use App\Services\VnptCredentialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller for Quang Ngai Office external API integration.
 * Credentials are stored in DB per user, no longer passed from client.
 */
class QuangNgaiController extends Controller
{
    public function __construct(
        private readonly QuangNgaiService $quangNgaiService,
        private readonly VnptCredentialService $vnptCredentialService,
    ) {}

    /**
     * Get document list. Credentials are fetched from DB.
     */
    public function getDocumentList(Request $request): JsonResponse
    {
        $user = $request->user();
        $credentials = $this->vnptCredentialService->getByUser($user->id);

        if ($credentials === null) {
            return response()->json([
                'success' => false,
                'message' => trans('messages.vnpt.not_configured'),
            ], 400);
        }

        $param = $request->input('param') ?? '';
        $pageNo = (int) ($request->input('pageNo') ?? 1);
        $pageRec = (int) ($request->input('pageRec') ?? 10);
        $kho = $request->input('kho') ?? 'Tra cứu văn bản';

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
     * Credentials are fetched from DB.
     */
    public function syncDocuments(Request $request): JsonResponse
    {
        $user = $request->user();
        $credentials = $this->vnptCredentialService->getByUser($user->id);

        if ($credentials === null) {
            return response()->json([
                'success' => false,
                'message' => trans('messages.vnpt.not_configured'),
            ], 400);
        }

        $param = $request->input('param') ?? '';
        $pageNo = (int) ($request->input('pageNo') ?? 1);
        $pageRec = (int) ($request->input('pageRec') ?? 10);
        $kho = $request->input('kho') ?? 'Tra cứu văn bản';

        $result = $this->quangNgaiService->fetchAndSaveDocuments(
            param: (string) $param,
            pageNo: $pageNo,
            pageRec: $pageRec,
            kho: (string) $kho,
            credentials: $credentials,
            userId: $user->id,
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

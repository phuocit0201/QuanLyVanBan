<?php

declare(strict_types=1);

namespace App\Services\OfficeVNPT;

use App\Repositories\OfficeDocumentRepositoryInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service for integrating with Quang Ngai Office external API.
 */
class QuangNgaiService
{
    private const BASE_URL = 'https://office.quangngai.gov.vn';
    private const LOGIN_ENDPOINT = '/qlvb_qni/api/login/v6/';
    private const DOCUMENT_LIST_ENDPOINT = '/qlvb_qni/api/document/getlistlookupbyparam/';
    private const USER_AGENT = 'VNPT iOffice Quang Ngai/1.1.5 (iPhone; iOS 17.1.1; Scale/3.00)';

    private ?string $authToken = null;

    public function __construct(
        private readonly OfficeDocumentRepositoryInterface $officeDocumentRepository,
    ) {}

    private function getBaseHeaders(): array
    {
        $headers = [
            'Host' => 'office.quangngai.gov.vn',
            'Connection' => 'keep-alive',
            'Accept' => 'application/json',
            'User-Agent' => self::USER_AGENT,
            'Accept-Language' => 'vi-VN;q=1',
            'Content-Type' => 'application/json',
        ];

        if ($this->authToken !== null) {
            $headers['X-Authentication-Token'] = $this->authToken;
        }

        return $headers;
    }

    public function setAuthToken(string $token): void
    {
        $this->authToken = $token;
    }

    public function getAuthToken(): ?string
    {
        return $this->authToken;
    }

    public function login(array $credentials): array
    {
        $payload = $this->buildLoginPayload($credentials);

        Log::info('QuangNgaiService: Attempting login', [
            'username' => $credentials['username'] ?? 'N/A',
        ]);

        try {
            $response = Http::withHeaders([
                'Host' => 'office.quangngai.gov.vn',
                'Connection' => 'keep-alive',
                'Accept' => 'application/json',
                'User-Agent' => self::USER_AGENT,
                'Accept-Language' => 'vi-VN;q=1',
                'Content-Type' => 'application/json',
            ])
                ->timeout(30)
                ->post(self::BASE_URL . self::LOGIN_ENDPOINT, $payload);

            if ($response->successful()) {
                Log::info('QuangNgaiService: Login successful');
                $result = $response->json();

                // Token is at $result['data']['token']
                $token = $result['data']['token'] ?? null;
                if ($token !== null) {
                    $this->authToken = $token;
                }

                return $result;
            }

            Log::warning('QuangNgaiService: Login failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => $this->parseErrorMessage($response),
                'status_code' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('QuangNgaiService: Exception during login', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => trans('messages.office.connection_error') . ': ' . $e->getMessage(),
                'status_code' => 500,
            ];
        }
    }

    /**
     * Get document list with auto-relogin on 401.
     *
     * @param string $param Filter parameter (default: empty string)
     * @param int $pageNo Page number (default: 1)
     * @param int $pageRec Records per page (default: 10)
     * @param string $kho Document category (default: 'Tra cứu văn bản')
     * @param array|null $credentials Optional credentials for auto-relogin (username, password)
     * @return array
     */
    public function getDocumentList(
        string $param = '',
        int $pageNo = 1,
        int $pageRec = 10,
        string $kho = 'Tra cứu văn bản',
        ?array $credentials = null
    ): array {
        $payload = [
            'param' => $param,
            'pageNo' => (string) $pageNo,
            'pageRec' => (string) $pageRec,
            'kho' => $kho,
        ];

        Log::info('QuangNgaiService: Getting document list', $payload);

        return $this->requestWithAuth(self::DOCUMENT_LIST_ENDPOINT, $payload, $credentials);
    }

    /**
     * Fetch documents from API and save to database.
     *
     * @param string $param Filter parameter
     * @param int $pageNo Page number
     * @param int $pageRec Records per page
     * @param string $kho Document category
     * @param array|null $credentials Credentials for API authentication
     * @param int|null $userId User ID to associate documents with
     * @return array
     */
    public function fetchAndSaveDocuments(
        string $param = '',
        int $pageNo = 1,
        int $pageRec = 10,
        string $kho = 'Tra cứu văn bản',
        ?array $credentials = null,
        ?int $userId = null
    ): array {
        $result = $this->getDocumentList($param, $pageNo, $pageRec, $kho, $credentials);

        if (isset($result['success']) && $result['success'] === false) {
            return $result;
        }

        $documents = $result['data'] ?? [];
        $savedCount = 0;

        if (!empty($documents) && $userId !== null) {
            $savedCount = $this->officeDocumentRepository->upsertMany($documents, $userId);
        }

        Log::info('QuangNgaiService: Documents fetched and saved', [
            'total' => count($documents),
            'saved' => $savedCount,
            'userId' => $userId,
        ]);

        return [
            'success' => true,
            'data' => $documents,
            'saved_count' => $savedCount,
        ];
    }

    /**
     * Make authenticated request with auto-relogin on 401.
     *
     * @param string $endpoint API endpoint to call
     * @param array $payload Request payload
     * @param array|null $credentials Optional credentials for auto-relogin (username, password)
     * @param string $method HTTP method (default: POST)
     * @param int $retryCount Internal retry counter
     * @return array
     */
    public function requestWithAuth(
        string $endpoint,
        array $payload,
        ?array $credentials = null,
        string $method = 'POST',
        int $retryCount = 0
    ): array {
        try {
            $response = Http::withHeaders($this->getBaseHeaders())
                ->timeout(30)
                ->{strtolower($method)}(self::BASE_URL . $endpoint, $payload);

            if ($response->status() === 401) {
                Log::warning('QuangNgaiService: Got 401, attempting relogin', [
                    'retryCount' => $retryCount,
                ]);

                if ($retryCount > 0 || $credentials === null) {
                    return [
                        'success' => false,
                        'error' => trans('messages.auth.unauthorized'),
                        'status_code' => 401,
                    ];
                }

                $loginResult = $this->login($credentials);

                if (isset($loginResult['success']) && $loginResult['success'] === false) {
                    return [
                        'success' => false,
                        'error' => $loginResult['error'] ?? trans('messages.auth.login_failed'),
                        'status_code' => 401,
                    ];
                }

                return $this->requestWithAuth($endpoint, $payload, null, $method, $retryCount + 1);
            }

            if ($response->successful()) {
                Log::info('QuangNgaiService: Request successful');
                return $response->json();
            }

            Log::warning('QuangNgaiService: Request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => $this->parseErrorMessage($response),
                'status_code' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('QuangNgaiService: Exception during request', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => trans('messages.office.connection_error') . ': ' . $e->getMessage(),
                'status_code' => 500,
            ];
        }
    }

    private function buildLoginPayload(array $credentials): array
    {
        return [
            'username' => $credentials['username'],
            'password' => $credentials['password'],
            'tokenFireBase' => $credentials['tokenFireBase'] ?? '',
            'language' => $credentials['language'] ?? 'VI',
            'type' => $credentials['type'] ?? 'IOS',
            'device' => $credentials['device'] ?? 'Unknown',
        ];
    }

    private function parseErrorMessage($response): string
    {
        $body = $response->json();

        if (isset($body['message'])) {
            return $body['message'];
        }

        if (isset($body['error'])) {
            return $body['error'];
        }

        return $response->status() === 401
            ? trans('messages.auth.invalid_credentials')
            : trans('messages.office.request_failed', ['status' => $response->status()]);
    }
}

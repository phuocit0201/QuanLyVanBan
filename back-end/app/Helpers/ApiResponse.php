<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Helper class for standardized API responses.
 * Supports i18n for messages.
 */
readonly class ApiResponse
{
    /**
     * Get current locale for messages.
     */
    private static function getLocale(): string
    {
        return app()->getLocale();
    }

    /**
     * Get localized message.
     */
    public static function message(string $key, array $replace = [], ?string $locale = null): string
    {
        $locale = $locale ?? self::getLocale();
        $message = trans("messages.{$key}", [], $locale);

        if (empty($replace)) {
            return $message;
        }

        foreach ($replace as $placeholder => $value) {
            $message = str_replace(':' . $placeholder, (string) $value, $message);
        }

        return $message;
    }

    /**
     * Success response.
     */
    public static function success(
        mixed $data = null,
        ?string $message = null,
        int $statusCode = 200,
    ): \Illuminate\Http\JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message ?? self::message('general.success'),
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Success response with created resource.
     */
    public static function created(
        mixed $data = null,
        ?string $message = null,
    ): \Illuminate\Http\JsonResponse {
        return self::success($data, $message, 201);
    }

    /**
     * Error response.
     */
    public static function error(
        ?string $message = null,
        int $statusCode = 400,
        mixed $errors = null,
    ): \Illuminate\Http\JsonResponse {
        $response = [
            'success' => false,
            'message' => $message ?? self::message('general.error'),
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Not found response.
     */
    public static function notFound(?string $message = null): \Illuminate\Http\JsonResponse
    {
        return self::error($message ?? self::message('general.not_found'), 404);
    }

    /**
     * Unauthorized response.
     */
    public static function unauthorized(?string $message = null): \Illuminate\Http\JsonResponse
    {
        return self::error($message ?? self::message('auth.unauthorized'), 401);
    }

    /**
     * Validation error response.
     */
    public static function validationError(
        mixed $errors,
        ?string $message = null,
    ): \Illuminate\Http\JsonResponse {
        return self::error($message ?? self::message('validation.name_required'), 422, $errors);
    }

    /**
     * Server error response.
     */
    public static function serverError(?string $message = null): \Illuminate\Http\JsonResponse
    {
        return self::error($message ?? self::message('general.server_error'), 500);
    }
}

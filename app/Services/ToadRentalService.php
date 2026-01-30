<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ToadRentalService
{
    private string $baseUrl;
    private ?string $token;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.toad.url'), '/');
        $this->token = config('services.toad.token');
    }

    /**
     * Récupère le token de l'utilisateur depuis la session
     */
    private function getUserToken(): ?string
    {
        $user = Session::get('user');
        return $user['token'] ?? $this->token;
    }

    /**
     * Récupère les locations effectuées sur une période donnée
     *
     * @param string $startDate Format: Y-m-d (ex: 2026-01-01)
     * @param string $endDate Format: Y-m-d (ex: 2026-01-31)
     * @return array|null Format: ['success' => bool, 'status' => int, 'data' => array|null, 'error' => string|null]
     */
    public function getRentalsByPeriod(string $startDate, string $endDate): ?array
    {
        $url = "{$this->baseUrl}/rentals";
        $headers = ['Accept' => 'application/json'];
        $token = $this->getUserToken();
        if ($token) {
            $headers['Authorization'] = "Bearer {$token}";
        }

        try {
            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->get($url, [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ]);

            Log::info("Rentals API Response", [
                'status' => $response->status(),
                'body' => $response->body(),
                'url' => $url,
                'params' => ['start_date' => $startDate, 'end_date' => $endDate]
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'status' => $response->status(),
                    'data' => $response->json(),
                    'error' => null,
                ];
            }

            $errorMessage = null;
            try {
                $json = $response->json();
                $errorMessage = $json['message'] ?? json_encode($json);
            } catch (\Throwable $e) {
                $errorMessage = $response->body();
            }

            return [
                'success' => false,
                'status' => $response->status(),
                'data' => null,
                'error' => $errorMessage,
            ];
        } catch (\Exception $e) {
            Log::error("Error fetching rentals: " . $e->getMessage());
            return [
                'success' => false,
                'status' => 500,
                'data' => null,
                'error' => $e->getMessage(),
            ];
        }
    }
}

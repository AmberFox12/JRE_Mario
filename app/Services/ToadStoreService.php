<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ToadStoreService
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
     * Récupère tous les stores
     *
     * @return array|null Format: ['success' => bool, 'status' => int, 'data' => array|null, 'error' => string|null]
     */
    public function getAllStores(): ?array
    {
        $url = "{$this->baseUrl}/stores";
        $headers = ['Accept' => 'application/json'];
        $token = $this->getUserToken();
        if ($token) {
            $headers['Authorization'] = "Bearer {$token}";
        }

        try {
            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->get($url);

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
            Log::error("Error fetching stores: " . $e->getMessage());
            return [
                'success' => false,
                'status' => 500,
                'data' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Récupère un store par son ID
     *
     * @param int $id
     * @return array|null
     */
    public function getStoreById($id): ?array
    {
        $url = "{$this->baseUrl}/stores/{$id}";
        $headers = ['Accept' => 'application/json'];
        $token = $this->getUserToken();
        if ($token) {
            $headers['Authorization'] = "Bearer {$token}";
        }

        try {
            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->get($url);

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
            Log::error("Error fetching store {$id}: " . $e->getMessage());
            return [
                'success' => false,
                'status' => 500,
                'data' => null,
                'error' => $e->getMessage(),
            ];
        }
    }
}

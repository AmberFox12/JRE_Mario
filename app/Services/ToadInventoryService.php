<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ToadInventoryService
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
     * Récupère tous les inventaires (DVD) groupés par film
     *
     * @return array|null Format: ['success' => bool, 'status' => int, 'data' => array|null, 'error' => string|null]
     */
    public function getAllInventory(): ?array
    {
        $url = "{$this->baseUrl}/inventories";
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
            Log::error("Error fetching inventory: " . $e->getMessage());
            return [
                'success' => false,
                'status' => 500,
                'data' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Récupère un inventaire (DVD) par son ID
     *
     * @param int $id
     * @return array|null
     */
    public function getInventoryById($id): ?array
    {
        $url = "{$this->baseUrl}/inventories/{$id}";
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
            Log::error("Error fetching inventory {$id}: " . $e->getMessage());
            return [
                'success' => false,
                'status' => 500,
                'data' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Crée un nouveau DVD dans l'inventaire
     *
     * @param array $payload
     * @return array|null
     */
    public function createInventory(array $payload): ?array
    {
        $url = "{$this->baseUrl}/inventories";
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
        $token = $this->getUserToken();
        if ($token) {
            $headers['Authorization'] = "Bearer {$token}";
        }

        try {
            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->post($url, $payload);

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
            Log::error("Error creating inventory: " . $e->getMessage());
            return [
                'success' => false,
                'status' => 500,
                'data' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Met à jour un DVD dans l'inventaire
     *
     * @param int $id
     * @param array $payload
     * @return array|null
     */
    public function updateInventory($id, array $payload): ?array
    {
        $url = "{$this->baseUrl}/inventories/{$id}";
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
        $token = $this->getUserToken();
        if ($token) {
            $headers['Authorization'] = "Bearer {$token}";
        }

        try {
            // Log pour debug
            Log::info("UPDATE Inventory Request", [
                'url' => $url,
                'id' => $id,
                'payload' => $payload
            ]);

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->put($url, $payload);

            // Log de la réponse
            Log::info("UPDATE Inventory Response", [
                'status' => $response->status(),
                'body' => $response->body()
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
            Log::error("Error updating inventory {$id}: " . $e->getMessage());
            return [
                'success' => false,
                'status' => 500,
                'data' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Supprime un DVD de l'inventaire
     *
     * @param int $id
     * @return array|null
     */
    public function deleteInventory($id): ?array
    {
        $url = "{$this->baseUrl}/inventories/{$id}";
        $headers = ['Accept' => 'application/json'];
        $token = $this->getUserToken();
        if ($token) {
            $headers['Authorization'] = "Bearer {$token}";
        }

        try {
            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->delete($url);

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
            Log::error("Error deleting inventory {$id}: " . $e->getMessage());
            return [
                'success' => false,
                'status' => 500,
                'data' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Vérifie si un DVD est disponible (non loué) en utilisant le nouvel endpoint
     *
     * @param int $id
     * @return bool
     */
    public function checkIfDVDIsAvailable($id): bool
    {
        $url = "{$this->baseUrl}/inventories/checkIfDVDIsAvailable/{$id}";
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
                return $response->json() === true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error("Error checking availability for inventory {$id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère tous les inventaires pour un film donné
     *
     * @param int $filmId
     * @return array|null
     */
    public function getInventoriesByFilmId($filmId): ?array
    {
        $url = "{$this->baseUrl}/inventories/film/{$filmId}";
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
                return $response->json();
            }

            return [];
        } catch (\Exception $e) {
            Log::error("Error fetching inventories for film {$filmId}: " . $e->getMessage());
            return [];
        }
    }
}

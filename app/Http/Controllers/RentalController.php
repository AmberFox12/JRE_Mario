<?php

namespace App\Http\Controllers;

use App\Services\ToadRentalService;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    private ToadRentalService $rentalService;

    public function __construct(ToadRentalService $rentalService)
    {
        $this->middleware('auth');
        $this->rentalService = $rentalService;
    }

    /**
     * Affiche la liste des locations effectuées sur une période donnée
     */
    public function index(Request $request)
    {
        // Récupérer les dates depuis le formulaire
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Si pas de dates fournies, afficher le mois en cours par défaut
        if (!$startDate || !$endDate) {
            $startDate = now()->startOfMonth()->format('Y-m-d');
            $endDate = now()->endOfMonth()->format('Y-m-d');
        }

        // Récupérer les locations depuis l'API
        $rentalsResult = $this->rentalService->getRentalsByPeriod($startDate, $endDate);

        $rentals = [];
        $error = null;

        if (is_array($rentalsResult) && ($rentalsResult['success'] ?? false)) {
            $rentals = $rentalsResult['data'] ?? [];
        } else {
            $error = !empty($rentalsResult['error']) ? $rentalsResult['error'] : 'Impossible de récupérer les locations';
        }

        return view('rentals.index', [
            'rentals' => $rentals,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'error' => $error,
        ]);
    }
}

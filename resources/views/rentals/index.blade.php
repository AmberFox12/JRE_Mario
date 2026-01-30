@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Gestion des Locations</h5>
                </div>

                <div class="card-body">
                    {{-- Formulaire de filtrage par période --}}
                    <div class="card mb-4">
                        <div class="card-body bg-light">
                            <form method="GET" action="{{ route('rentals.index') }}" class="row g-3">
                                <div class="col-md-4">
                                    <label for="start_date" class="form-label">Date de début</label>
                                    <input type="date"
                                           class="form-control"
                                           id="start_date"
                                           name="start_date"
                                           value="{{ $startDate }}"
                                           required>
                                </div>
                                <div class="col-md-4">
                                    <label for="end_date" class="form-label">Date de fin</label>
                                    <input type="date"
                                           class="form-control"
                                           id="end_date"
                                           name="end_date"
                                           value="{{ $endDate }}"
                                           required>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-search"></i> Filtrer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Messages d'erreur --}}
                    @if($error)
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i>
                            {{ $error }}
                        </div>
                    @endif

                    {{-- Tableau des locations --}}
                    @if (empty($rentals))
                        <div class="alert alert-warning">
                            <i class="bi bi-info-circle"></i>
                            Aucune location trouvée pour la période du <strong>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong> au <strong>{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</strong>.
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            Période affichée : du <strong>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong> au <strong>{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</strong>
                            <br>
                            <strong>{{ count($rentals) }}</strong> location(s) trouvée(s)
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 30%;">Film</th>
                                        <th style="width: 15%;">Date de location</th>
                                        <th style="width: 15%;">Date de retour</th>
                                        <th style="width: 15%;">Point de retrait</th>
                                        <th style="width: 15%;">Client</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rentals as $rental)
                                        @php
                                            $rentalDate = \Carbon\Carbon::parse($rental['rentalDate'] ?? null);
                                            $returnDate = isset($rental['returnDate']) ? \Carbon\Carbon::parse($rental['returnDate']) : null;
                                            $isReturned = $returnDate !== null;
                                            $storeAddress = $rental['address'] ?? 'N/A';
                                            $filmTitle = $rental['filmTitle'] ?? 'N/A';
                                            $customerName = $rental['customerName'] ?? 'N/A';
                                        @endphp
                                        <tr>
                                            <td><strong>{{ $filmTitle }}</strong></td>
                                            <td>{{ $rentalDate->format('d/m/Y') }}</td>
                                            <td>
                                                @if($isReturned)
                                                    {{ $returnDate->format('d/m/Y') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $storeAddress }}</td>
                                            <td>{{ $customerName }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

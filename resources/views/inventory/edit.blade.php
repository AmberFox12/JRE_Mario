@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Modifier un DVD</div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @php
                        $isRented = isset($inventory['rentalId']) && $inventory['rentalId'] !== null;
                        $film = $inventory['film'] ?? null;
                    @endphp

                    @if($isRented)
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Attention :</strong> Ce DVD est actuellement loué et ne peut pas être modifié.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('inventory.update', $inventory['inventoryId'] ?? $inventory['id']) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="filmId" value="{{ $inventory['filmId'] ?? ($inventory['film']['filmId'] ?? '') }}">

                        <div class="mb-3">
                            <label class="form-label">Film</label>
                            <input type="text" class="form-control" value="{{ $film['title'] ?? 'N/A' }}" disabled>
                            <small class="text-muted">Le film associé ne peut pas être modifié</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ID du DVD</label>
                            <input type="text" class="form-control" value="{{ $inventory['inventoryId'] ?? $inventory['id'] }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Statut actuel</label>
                            <div>
                                @if($isRented)
                                    <span class="badge bg-warning text-dark">Loué</span>
                                @else
                                    <span class="badge bg-success">En stock</span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="storeId" class="form-label">Store</label>
                            <select id="storeId" name="storeId" class="form-select" required {{ $isRented ? 'disabled' : '' }}>
                                <option value="">-- Sélectionnez un store --</option>
                                @foreach($stores as $store)
                                    @php
                                        $storeId = $store['storeId'] ?? $store['id'];
                                        $currentStoreId = old('storeId', $inventory['storeId'] ?? '');
                                    @endphp
                                    <option value="{{ $storeId }}"
                                            {{ $currentStoreId == $storeId ? 'selected' : '' }}>
                                        {{ $store['storeName'] ?? 'Store #' . $storeId }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Modifier le store où est stocké le DVD</small>
                            @error('storeId') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('inventory.index') }}" class="btn btn-secondary me-2">Annuler</a>
                            <button class="btn btn-primary" type="submit" {{ $isRented ? 'disabled' : '' }}>
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

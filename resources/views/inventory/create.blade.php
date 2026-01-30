@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Ajouter un DVD au stock</div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form method="POST" action="{{ route('inventory.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="filmId" class="form-label">Film</label>
                            @if($film)
                                <input type="hidden" name="filmId" value="{{ $film['filmId'] ?? $film['id'] }}">
                                <input type="text" class="form-control" value="{{ $film['title'] }}" disabled>
                                <small class="text-muted">Film présélectionné</small>
                            @else
                                <select id="filmId" name="filmId" class="form-select" required>
                                    <option value="">-- Sélectionnez un film --</option>
                                    @foreach($films as $filmOption)
                                        <option value="{{ $filmOption['filmId'] ?? $filmOption['id'] }}"
                                                {{ old('filmId') == ($filmOption['filmId'] ?? $filmOption['id']) ? 'selected' : '' }}>
                                            {{ $filmOption['title'] }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                            @error('filmId') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="storeId" class="form-label">Store</label>
                            <select id="storeId" name="storeId" class="form-select" required>
                                <option value="">-- Sélectionnez un store --</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store['storeId'] ?? $store['id'] }}"
                                            {{ old('storeId') == ($store['storeId'] ?? $store['id']) ? 'selected' : '' }}>
                                        {{ $store['storeName'] ?? 'Store #' . ($store['storeId'] ?? $store['id']) }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Sélectionnez le store où sera stocké le DVD</small>
                            @error('storeId') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Information :</strong> Le DVD sera ajouté au stock avec le statut "En stock" (non loué).
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('inventory.index') }}" class="btn btn-secondary me-2">Annuler</a>
                            <button class="btn btn-primary" type="submit">Ajouter le DVD</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

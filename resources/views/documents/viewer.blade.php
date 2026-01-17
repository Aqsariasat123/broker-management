@extends('layouts.app')
@section('content')

<style>
.viewer-container {
    background: #fff;
    min-height: 100vh;
    padding: 20px;
}
.viewer-header {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px 20px;
    margin-bottom: 20px;
}
.viewer-title-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}
.viewer-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
}
.client-name-header {
    font-size: 16px;
    color: #f3742a;
    font-weight: 500;
    margin-left: 5px;
}
.viewer-actions {
    display: flex;
    gap: 10px;
}
.btn-back {
    background: #4CAF50;
    color: #fff;
    border: none;
    padding: 8px 20px;
    border-radius: 2px;
    cursor: pointer;
    font-size: 14px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.btn-back:hover {
    background: #45a049;
    color: #fff;
    text-decoration: none;
}
.btn-download {
    background: #2196F3;
    color: #fff;
    border: none;
    padding: 8px 20px;
    border-radius: 2px;
    cursor: pointer;
    font-size: 14px;
    text-decoration: none;
}
.btn-download:hover {
    background: #0b7dda;
    color: #fff;
    text-decoration: none;
}
.client-info-banner {
    background: #f8f9fa;
    border-left: 4px solid #f3742a;
    padding: 10px 15px;
    margin-bottom: 15px;
    border-radius: 2px;
}
.client-info-banner .label {
    font-size: 12px;
    color: #666;
    font-weight: 600;
    display: inline-block;
    min-width: 80px;
}
.client-info-banner .value {
    font-size: 13px;
    color: #333;
    font-weight: 500;
}
.viewer-content {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
    text-align: center;
}
.document-image {
    max-width: 100%;
    max-height: 80vh;
    border: 1px solid #ddd;
    border-radius: 4px;
}
.document-iframe {
    width: 100%;
    height: 80vh;
    border: 1px solid #ddd;
    border-radius: 4px;
}
.document-info {
    background: #f5f5f5;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    margin-bottom: 20px;
    text-align: left;
}
.info-row {
    display: flex;
    padding: 5px 0;
    border-bottom: 1px solid #e0e0e0;
}
.info-row:last-child {
    border-bottom: none;
}
.info-label {
    font-weight: 600;
    color: #666;
    min-width: 150px;
}
.info-value {
    color: #333;
}
</style>

<div class="viewer-container">
    @php
        // Get client information for display
        $clientInfo = null;
        $clientName = null;
        $clientId = null;
        
        // Try to get client from various sources
        if (request()->has('client_id') && request('client_id')) {
            $clientInfo = \App\Models\Client::find(request('client_id'));
        } elseif (isset($document->client) && $document->client) {
            $clientInfo = $document->client;
        } elseif ($document->tied_to) {
            $clientInfo = \App\Models\Client::where('clid', $document->tied_to)->first();
        }
        
        if ($clientInfo) {
            $clientName = $clientInfo->client_name;
            $clientId = $clientInfo->clid;
        }
    @endphp

    <!-- Header with Client Name -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; padding:15px 20px; margin-bottom:10px;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h3 style="margin:0; font-size:18px; font-weight:600;">
                Documents
                @if($clientName)
                    <span style="color:#f3742a; font-size:16px; font-weight:500;"> - {{ $clientName }}</span>
                @endif
            </h3>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="viewer-header">
        <div class="viewer-title-section">
            <div class="viewer-title">
                📄 {{ $document->name }}
            </div>
            <div class="viewer-actions">
                @php
                    // SMART BACK BUTTON LOGIC
                    $backUrl = route('documents.index');
                    $backText = 'Back to Documents';
                    
                    // Priority 1: URL parameter
                    if (request()->has('client_id') && request('client_id')) {
                        $backUrl = route('clients.index') . '?client_id=' . request('client_id');
                        $backText = 'Back to Client';
                    }
                    // Priority 2: Document client_id
                    elseif (isset($document->client_id) && $document->client_id) {
                        $backUrl = route('clients.index') . '?client_id=' . $document->client_id;
                        $backText = 'Back to Client';
                    }
                    // Priority 3: Document client relationship
                    elseif (isset($document->client) && $document->client) {
                        $backUrl = route('clients.index') . '?client_id=' . $document->client->id;
                        $backText = 'Back to Client';
                    }
                    // Priority 4: Find via tied_to
                    elseif ($document->tied_to && $clientInfo) {
                        $backUrl = route('clients.index') . '?client_id=' . $clientInfo->id;
                        $backText = 'Back to Client';
                    }
                    // Priority 5: Session
                    elseif (session()->has('previous_url') && session('previous_url') != url()->current()) {
                        $backUrl = session('previous_url');
                        $backText = 'Back';
                    }
                @endphp
                
                <a href="{{ $backUrl }}" class="btn-back">
                    ← {{ $backText }}
                </a>
                <a href="{{ asset('storage/' . $document->file_path) }}" download="{{ $document->name }}" class="btn-download">
                    ⬇ Download
                </a>
            </div>
        </div>
        
        {{-- Client Info Banner --}}
        @if($clientInfo)
        <div class="client-info-banner">
            <span class="label">Client ID:</span>
            <span class="value">{{ $clientId }}</span>
            <span class="label" style="margin-left: 20px;">Client Name:</span>
            <span class="value">{{ $clientName }}</span>
        </div>
        @endif
    </div>

    <!-- Document Information -->
    <div class="document-info">
        <h4 style="margin-top: 0; margin-bottom: 10px;">Document Information</h4>
        <div class="info-row">
            <div class="info-label">Document ID:</div>
            <div class="info-value">{{ $document->doc_id }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Name:</div>
            <div class="info-value">{{ $document->name }}</div>
        </div>
        @if($document->type)
        <div class="info-row">
            <div class="info-label">Type:</div>
            <div class="info-value">{{ $document->type }}</div>
        </div>
        @endif
        @if($document->tied_to)
        <div class="info-row">
            <div class="info-label">Tied To:</div>
            <div class="info-value">
                {{ $document->tied_to }}
                @if($clientName)
                    ({{ $clientName }})
                @endif
            </div>
        </div>
        @endif
        <div class="info-row">
            <div class="info-label">Format:</div>
            <div class="info-value">{{ strtoupper($document->format) }}</div>
        </div>
        @if($document->date_added)
        <div class="info-row">
            <div class="info-label">Date Added:</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($document->date_added)->format('d M Y') }}</div>
        </div>
        @endif
    </div>

    <!-- Document Viewer -->
    <div class="viewer-content">
        @php
            $extension = strtolower($document->format ?? 'unknown');
            $imageMimes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
            $pdfMimes = ['pdf'];
        @endphp

        @if(in_array($extension, $imageMimes))
            <!-- Image Viewer -->
            <img src="{{ asset('storage/' . $document->file_path) }}" 
                 alt="{{ $document->name }}" 
                 class="document-image">
        @elseif(in_array($extension, $pdfMimes))
            <!-- PDF Viewer -->
            <iframe src="{{ asset('storage/' . $document->file_path) }}" 
                    class="document-iframe"
                    frameborder="0">
            </iframe>
        @else
            <!-- Unsupported Format -->
            <div style="padding: 40px;">
                <p style="font-size: 18px; color: #666; margin-bottom: 20px;">
                    Preview not available for this file type.
                </p>
                <a href="{{ asset('storage/' . $document->file_path) }}" 
                   download="{{ $document->name }}" 
                   class="btn-download">
                    ⬇ Download File
                </a>
            </div>
        @endif
    </div>
</div>

@endsection
@extends('layouts.dashboard')

@section('content')
    <div class="container mx-auto">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            @livewire('activity-logs-view')
        </div>
    </div>
@endsection
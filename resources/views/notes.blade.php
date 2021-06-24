@extends('layouts.main')

@section('content')
    <div class="notes-container">
        <div class="mb-10">
            <new-note-component owner="{{ auth()->user() }}">

            </new-note-component>
        </div>

        <div class="pinned">
            <p class="font-bold text-sm mb-2">PINNED</p>

            <notes-container-component :notes="{{ $notes->where('pinned', true) }}">

            </notes-container-component>
        </div>

        <div class="others">
            <p class="font-bold text-sm mt-20 mb-2">OTHERS</p>

            <notes-container-component :notes="{{ $notes->where('pinned', false) }}">

            </notes-container-component>
        </div>

    </div>
@endsection

@extends('layouts.main')

@section('content')
    <div class="notes-container">
        <div class="mb-10">
            <new-note-component owner="{{ auth()->user() }}"
                                :has-remainder="true">

            </new-note-component>
        </div>

{{--        @if( ! $notes->isEmpty() )--}}
            <notes-container-component :pinned_notes="{{ $pinned_notes }}"
                                       :other_notes="{{ $other_notes }}">>

            </notes-container-component>
{{--        @else--}}
{{--            <p class="text-center text-2xl mb-6 mt-14">--}}
{{--                <i class="bi bi-bell-fill icon-xl"></i>--}}
{{--            </p>--}}
{{--            <p class="text-center text-2xl text-gray-600 font-light">Notes with upcoming reminders appear--}}
{{--                here</p>--}}
{{--        @endif--}}
    </div>
@endsection

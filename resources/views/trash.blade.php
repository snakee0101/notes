@extends('layouts.main')

@section('content')
    <empty-trash-component></empty-trash-component>

{{--    @if( ! $notes->isEmpty() )--}}
        <notes-container-component :pinned_notes="{{ $pinned_notes }}"
                                   :other_notes="{{ $other_notes }}"
                                   :is-trashed="true">

        </notes-container-component>
{{--    @else--}}
{{--        <p class="text-center text-2xl mb-6 mt-14">
            <i class="bi bi-trash-fill icon-xl"></i>
        </p>
        <p class="text-center text-2xl text-gray-600 font-light">No notes in Trash</p>
    @endif--}}
@endsection

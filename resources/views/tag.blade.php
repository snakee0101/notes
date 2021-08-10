@extends('layouts.main')

@section('content')
    <div class="notes-container">
        <div class="mb-10">
            <new-note-component owner="{{ auth()->user() }}"
                                tag="{{ $tag }}">

            </new-note-component>
        </div>

        <notes-container-component :pinned_notes="{{ $pinned_notes }}"
                                   :other_notes="{{ $other_notes }}">

        </notes-container-component>
    </div>
@endsection

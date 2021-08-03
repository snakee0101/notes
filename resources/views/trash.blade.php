@extends('layouts.main')

@section('content')
    <empty-trash-component></empty-trash-component>

    <div class="notes-container">
        <notes-container-component :pinned_notes="{{ $pinned_notes }}"
                                   :other_notes="{{ $other_notes }}"
                                   :is-trashed="true">

        </notes-container-component>
    </div>
@endsection

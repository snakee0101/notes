@extends('layouts.main')

@section('content')
    <div class="notes-container">
        <notes-container-component :pinned_notes="{{ $pinned_notes }}"
                                   :other_notes="{{ $other_notes }}">

        </notes-container-component>
    </div>
@endsection

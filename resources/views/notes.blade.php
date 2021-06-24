@extends('layouts.main')

@section('content')
    <div class="notes-container">
        <div class="mb-10">
            <new-note-component owner="{{ auth()->user() }}">

            </new-note-component>
        </div>

        <notes-container-component :notes="{{ $notes }}">

        </notes-container-component>
    </div>
@endsection

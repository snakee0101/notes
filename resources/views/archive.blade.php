@extends('layouts.main')

@section('content')
        <notes-container-component :pinned_notes="{{ $pinned_notes }}"
                                   :other_notes="{{ $other_notes }}">

        </notes-container-component>
@endsection

@extends('layouts.main')

@section('content')
    @if( ! $notes->isEmpty() )
        <notes-container-component :notes="{{ $notes }}">

        </notes-container-component>
    @else
        <p class="text-center text-2xl mb-6 mt-14">
            <i class="bi bi-save2-fill icon-xl"></i>
        </p>
        <p class="text-center text-2xl text-gray-600 font-light">Your archived notes appear here</p>
    @endif
@endsection

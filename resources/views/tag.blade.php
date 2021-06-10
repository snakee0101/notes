@extends('layouts.main')

@section('content')
    <div class="notes-container">
        <div class="mb-10">
            <new-note-component owner="{{ auth()->user() }}"
                                tag_name="{{ $tag_name }}">

            </new-note-component>
        </div>

        @if( ! $notes->isEmpty() )
            <div class="pinned">
                <p class="font-bold text-sm mb-2">PINNED</p>
                @foreach($notes->where('pinned', true) as $note)
                    <note-component note="{{ $note }}">

                    </note-component>
                @endforeach
            </div>

            <div class="others">
                <p class="font-bold text-sm mt-20 mb-2">OTHERS</p>
                @foreach($notes->where('pinned', false) as $note)
                    <note-component note="{{ $note }}">

                    </note-component>
                @endforeach
            </div>
        @else
            <p class="text-center text-2xl mb-6 mt-20">
                <i class="bi bi-tag-fill icon-xl"></i>
            </p>
            <p class="text-center text-2xl text-gray-600 font-light">No notes with this label yet</p>
        @endif
    </div>
@endsection

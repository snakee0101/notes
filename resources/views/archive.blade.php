@extends('layouts.main')

@section('content')
    <div class="notes-container">
        @forelse($notes as $note)
            <note-component note="{{ $note }}">

            </note-component>
        @empty
            <p class="text-center text-2xl mb-6 mt-14">
                <svg class="icon icon-xl icon-box-add" viewBox="0 0 32 32" fill="rgba(107, 114, 128, 0.2)">
                    <path
                        d="M26 2h-20l-6 6v21c0 0.552 0.448 1 1 1h30c0.552 0 1-0.448 1-1v-21l-6-6zM16 26l-10-8h6v-6h8v6h6l-10 8zM4.828 6l2-2h18.343l2 2h-22.343z"></path>
                </svg>
            </p>
            <p class="text-center text-2xl text-gray-600 font-light">Your archived notes appear here</p>
        @endforelse
    </div>
@endsection

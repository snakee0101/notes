@extends('layouts.main')

@section('content')
    <empty-trash-component></empty-trash-component>
    <div class="notes-container">
        @forelse($notes as $note)
            <note-component note="{{ $note }}" :isTrashed="true">

            </note-component>
        @empty
            <p class="text-center text-2xl mb-6 mt-20">
                <svg class="icon icon-xl icon-bin" viewBox="0 0 32 32" fill="rgba(107, 114, 128, 0.2)">
                    <path
                        d="M4 10v20c0 1.1 0.9 2 2 2h18c1.1 0 2-0.9 2-2v-20h-22zM10 28h-2v-14h2v14zM14 28h-2v-14h2v14zM18 28h-2v-14h2v14zM22 28h-2v-14h2v14z"></path>
                    <path
                        d="M26.5 4h-6.5v-2.5c0-0.825-0.675-1.5-1.5-1.5h-7c-0.825 0-1.5 0.675-1.5 1.5v2.5h-6.5c-0.825 0-1.5 0.675-1.5 1.5v2.5h26v-2.5c0-0.825-0.675-1.5-1.5-1.5zM18 4h-6v-1.975h6v1.975z"></path>
                </svg>
            </p>
            <p class="text-center text-2xl text-gray-600 font-light">No notes in Trash</p>
        @endforelse
    </div>
@endsection

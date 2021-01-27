@extends('layouts.main')

@section('content')
    <!--TODO: There should be 2 sections of the notes: PINNED and OTHERS-->
    <div class="notes-container">
        <div class="mb-10">
            <new-note-component>

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
                <svg class="icon icon-xl icon-price-tags" viewBox="0 0 40 32" fill="rgba(107, 114, 128, 0.2)">
                    <path
                        d="M38.5 0h-12c-0.825 0-1.977 0.477-2.561 1.061l-14.879 14.879c-0.583 0.583-0.583 1.538 0 2.121l12.879 12.879c0.583 0.583 1.538 0.583 2.121 0l14.879-14.879c0.583-0.583 1.061-1.736 1.061-2.561v-12c0-0.825-0.675-1.5-1.5-1.5zM31 12c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z"></path>
                    <path
                        d="M4 17l17-17h-2.5c-0.825 0-1.977 0.477-2.561 1.061l-14.879 14.879c-0.583 0.583-0.583 1.538 0 2.121l12.879 12.879c0.583 0.583 1.538 0.583 2.121 0l0.939-0.939-13-13z"></path>
                </svg>
            </p>
            <p class="text-center text-2xl text-gray-600 font-light">No notes with this label yet</p>
        @endif
    </div>
@endsection

@extends('layouts.main')

@section('content')
    <div class="notes-container">
        <div class="mb-10">
            <new-note-component owner="{{ auth()->user() }}">

            </new-note-component>
        </div>

        <div class="pinned">
            <p class="font-bold text-sm mb-2">PINNED</p>

            <div v-masonry="app" transition-duration="0.3s" item-selector=".note" gutter=".gutter">
                <div class="gutter" style="width: 10px; height: 10px;">
                @foreach($notes->where('pinned', true) as $note)
                    <note-component note="{{ $note }}" v-masonry-tile>

                    </note-component>
                @endforeach
            </div>

        </div>

        <div class="others">
            <p class="font-bold text-sm mt-20 mb-2">OTHERS</p>

            <div v-masonry="app" transition-duration="0.3s" item-selector=".note" gutter=".gutter">
                <div class="gutter" style="width: 10px; height: 10px;">
                    @foreach($notes->where('pinned', false) as $note)
                        <note-component note="{{ $note }}" v-masonry-tile>

                        </note-component>
                    @endforeach
            </div>
        </div>

    </div>
@endsection

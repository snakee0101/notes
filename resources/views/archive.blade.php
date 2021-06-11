@extends('layouts.main')

@section('content')
    <div class="notes-container" v-masonry="app" transition-duration="0.3s" item-selector=".note" gutter=".gutter">
        @forelse($notes as $note)
            <div class="gutter" style="width: 10px; height: 10px;"></div>
            <note-component note="{{ $note }}" v-masonry-tile>

            </note-component>
        @empty
            <p class="text-center text-2xl mb-6 mt-14">
                <i class="bi bi-save2-fill icon-xl"></i>
            </p>
            <p class="text-center text-2xl text-gray-600 font-light">Your archived notes appear here</p>
        @endforelse
    </div>
@endsection

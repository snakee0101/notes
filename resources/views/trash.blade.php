@extends('layouts.main')

@section('content')
    <empty-trash-component></empty-trash-component>
    <div class="notes-container" v-masonry="app" transition-duration="0.3s" item-selector=".note" gutter=".gutter">
        <div class="gutter" style="width: 10px; height: 10px;"></div>

        @forelse($notes as $note)
            <note-component note="{{ $note }}" :isTrashed="true" v-masonry-tile>

            </note-component>
        @empty
            <p class="text-center text-2xl mb-6 mt-14">
                <i class="bi bi-trash-fill icon-xl"></i>
            </p>
            <p class="text-center text-2xl text-gray-600 font-light">No notes in Trash</p>
        @endforelse
    </div>
@endsection

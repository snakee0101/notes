@extends('layouts.main')

@section('content')
    <div class="notes-container">
        @forelse($notes as $note)
            <note-component note="{{ $note }}">

            </note-component>
        @empty
            <p class="text-center text-2xl mb-6 mt-14">
                <i class="bi bi-save2-fill icon-xl"></i>
            </p>
            <p class="text-center text-2xl text-gray-600 font-light">Your archived notes appear here</p>
        @endforelse
    </div>
@endsection

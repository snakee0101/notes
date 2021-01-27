@extends('layouts.main')

@section('content')
    <!--TODO: There should be 2 sections of the notes: PINNED and OTHERS-->
    <div class="notes-container">
        <div class="mb-10">
            <new-note-component>

            </new-note-component>
        </div>

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

    </div>
@endsection

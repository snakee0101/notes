@extends('layouts.main')

@section('content')
    <!--TODO: There should be 2 sections of the notes: PINNED and OTHERS-->
    <div class="notes-container">
        <div class="mb-10">
            <note-component :newNote="true" class="m-auto new-note">

            </note-component>
        </div>

        @foreach($notes as $note)
            <note-component note="{{ $note }}">

            </note-component>
        @endforeach

    </div>
@endsection

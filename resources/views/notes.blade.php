@extends('layouts.main')

@section('content')
    <!--TODO: There should be 2 sections of the notes: PINNED and OTHERS-->
    <div class="notes-container">
        <div class="mb-10">
            <note-component noteColor="white" :newNote="true" class="m-auto new-note">

            </note-component>
        </div>

        <note-component noteColor="orange">

        </note-component>
    </div>
@endsection

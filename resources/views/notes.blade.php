@extends('layouts.main')

@section('content')
    <!--TODO: There should be 2 sections of the notes: PINNED and OTHERS-->

    <div class="mb-10">
        <note-component noteColor="white" :newNote="true" class="m-auto">

        </note-component>
    </div>

    <div class="notes-container">
        <note-component noteColor="orange">

        </note-component>
    </div>
@endsection

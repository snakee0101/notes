@extends('layouts.main')

@section('content')
    <empty-trash-component></empty-trash-component>
    <div class="notes-container">
        @foreach($notes as $note)
            <note-component note="{{ $note }}" :isTrashed="true">

            </note-component>
        @endforeach
    </div>
@endsection

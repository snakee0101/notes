@extends('layouts.main')

@section('content')
    <div class="notes-container">
        @foreach($notes as $note)
            <note-component note="{{ $note }}">

            </note-component>
        @endforeach
    </div>
@endsection

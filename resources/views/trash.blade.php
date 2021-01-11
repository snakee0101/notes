@extends('layouts.main')

@section('content')
    <empty-trash-component></empty-trash-component>
    <div class="notes-container">
        <note-component noteColor="orange" :isTrashed="true">

        </note-component>
    </div>
    <!--TODO: Deleted note should have only 2 buttons (only with text) - "Delete forever" and "Restore"-->
@endsection

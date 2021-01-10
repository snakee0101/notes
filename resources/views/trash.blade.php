@extends('layouts.main')

@section('content')
    <empty-trash-component></empty-trash-component>
    <note-component></note-component>

    <!--TODO: Deleted note should have only 2 buttons (only with text) - "Delete forever" and "Restore"-->
@endsection

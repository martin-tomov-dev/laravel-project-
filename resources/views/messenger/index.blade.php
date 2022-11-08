@extends('layouts.master')

@section('content')
        @each('messenger.partials.message', $messages, 'message', 'messenger.partials.no-messages')
@stop

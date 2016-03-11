
# Extends the master template
@extends('template.master')

# Add code to the 'content' section
@section('content')
    Hello World!
    {{ $test }}

    @foreach ([1, 2, 3, 4, 5] as $a)
        {{ $a }}
    @endforeach

    <br><br>

    @forelse ($myArray as $a)
        {{ $a }}
    @empty
        There are no items in your array
    @endforelse
@endsection

# Add code to the 'navigation' section
@section('navigation')
    Some navigation stuff here...
@endsection

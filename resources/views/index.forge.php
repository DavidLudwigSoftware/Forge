
# Extends the master template
@extends('template.master')

# Add code to the 'content' section
@section('content')
    @forelse ($myArray as $a)
        {{ $a }}
    @empty
        There are no items in your array
    @endforelse
@endsection

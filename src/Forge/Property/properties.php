<?php

return [
    'if'    => 'Forge\Property\IfProperty@if',
    'elif'  => 'Forge\Property\IfProperty@elif',
    'else'  => 'Forge\Property\IfProperty@else',
    'endif' => 'Forge\Property\IfProperty@endif',

    'for'    => 'Forge\Property\ForProperty@for',
    'endfor' => 'Forge\Property\ForProperty@endfor',

    'foreach'    => 'Forge\Property\ForEachProperty@foreach',
    'endforeach' => 'Forge\Property\ForEachProperty@endforeach',

    'while'    => 'Forge\Property\WhileProperty@while',
    'endwhile' => 'Forge\Property\WhileProperty@endwhile',

    'section'    => 'Forge\Property\SectionProperty@section',
    'endsection' => 'Forge\Property\SectionProperty@endsection',
    'yield'      => 'Forge\Property\SectionProperty@yield',

    'include' => 'Forge\Property\IncludeProperty@include',
    'extends' => 'Forge\Property\IncludeProperty@extends',
];

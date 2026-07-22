@props(['condition'])

<x-badge :color="$condition->color()">{{ $condition->label() }}</x-badge>
@props(['title' => 'No records found', 'subtitle' => null])

<div class="text-center py-12">
    <p class="text-sm font-medium text-gray-900">{{ $title }}</p>
    @isset($subtitle)
        <p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>
    @endisset
</div>
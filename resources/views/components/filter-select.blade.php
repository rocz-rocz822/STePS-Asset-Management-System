@props(['name', 'options', 'selected' => null, 'placeholder' => 'All'])

<select name="{{ $name }}" class="rounded-lg border-gray-300 text-sm">
    <option value="">{{ $placeholder }}</option>
    @foreach ($options as $value => $label)
        <option value="{{ $value }}" @selected($selected == $value)>{{ $label }}</option>
    @endforeach
</select>
@props(['name', 'label', 'type' => 'text', 'required' => false, 'options' => null, 'placeholder' => '', 'prefix' => null])
@php($value = is_scalar(old($name)) ? old($name) : '')
<div class="field">
    <label for="{{ $name }}">{{ $label }} @if($required)<span class="required" aria-hidden="true">*</span>@endif</label>
    <div class="input-wrap {{ $prefix ? 'has-prefix' : '' }}">
        @if($prefix)<span class="input-prefix" aria-hidden="true">{{ $prefix }}</span>@endif
        @if($options !== null)
            <select id="{{ $name }}" name="{{ $name }}" @required($required) @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror>
                <option value="">{{ $placeholder ?: 'Pilih salah satu' }}</option>
                @foreach($options as $option)<option value="{{ $option }}" @selected($value === $option)>{{ $option }}</option>@endforeach
            </select>
        @elseif($type === 'textarea')
            <textarea id="{{ $name }}" name="{{ $name }}" rows="3" placeholder="{{ $placeholder }}" {{ $attributes }} @required($required) @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror>{{ $value }}</textarea>
        @else
            <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ $value }}" placeholder="{{ $placeholder }}" {{ $attributes }} @required($required) @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror>
        @endif
    </div>
    @error($name)<p class="field-error" id="{{ $name }}-error">{{ $message }}</p>@enderror
</div>

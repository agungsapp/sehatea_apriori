@props([
    'label' => 'Input Label', // Label input
    'id' => 'input-id', // ID input
    'name' => 'input-name', // Name input
    'value' => null, // Nilai input
    'customErrors' => [], // Error custom (opsional)
])

<div>
  <label for="{{ $id }}" class="form-label">
    {{ $label }}
  </label>
  <input type="number"
    class="form-control @error($name) is-invalid @enderror @if (isset($customErrors[$name])) is-invalid @endif"
    id="{{ $id }}" name="{{ $name }}" wire:model.live="{{ $name }}" placeholder="0"
    aria-describedby="{{ $id }}Help" value="{{ $value }}">

  @if ($value != null)
    <div id="{{ $id }}Help" class="form-text text-end">
      {{ \Illuminate\Support\Str::rupiah($value) }}
    </div>
  @endif

  {{-- Error validasi bawaan Laravel --}}
  @error($name)
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror

  {{-- Error custom, jika ada --}}
  @if (isset($customErrors[$name]))
    <div class="invalid-feedback">{{ $customErrors[$name] }}</div>
  @endif
</div>

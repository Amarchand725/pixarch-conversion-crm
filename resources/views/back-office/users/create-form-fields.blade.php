{{-- resources/views/roles/_form.blade.php --}}
<div class="row">
  <div class="col-12 mb-3">
    <label for="name" class="form-label">Name</label>
    <input id="name" name="name" type="text"
           class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $role->name ?? '') }}">
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-12 mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea id="description" name="description" class="form-control">{{ old('description', $role->description ?? '') }}</textarea>
  </div>
</div>

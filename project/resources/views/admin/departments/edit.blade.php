@extends('layouts.app')

@section('title', 'Edit Department')
@section('page-title', 'Edit Department')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Departments', 'url' => route('admin.departments.index')],
    ['label' => 'Edit: ' . $department->name],
]" />

<div class="card border-0 shadow-sm" style="max-width: 600px;">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="fas fa-edit me-2 text-warning"></i>Edit Department</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.departments.update', $department) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Department Name <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $department->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" rows="3"
                          class="form-control @error('description') is-invalid @enderror">{{ old('description', $department->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="head_doctor_id" class="form-label">Head Doctor</label>
                <select id="head_doctor_id" name="head_doctor_id" class="form-select">
                    <option value="">None</option>
                    @foreach($doctors as $doctor)
                    <option value="{{ $doctor->id }}"
                        {{ old('head_doctor_id', $department->head_doctor_id) == $doctor->id ? 'selected' : '' }}>
                        Dr. {{ $doctor->full_name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-8">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" id="location" name="location"
                           class="form-control @error('location') is-invalid @enderror"
                           value="{{ old('location', $department->location) }}">
                    @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="phone_extension" class="form-label">Phone Ext.</label>
                    <input type="text" id="phone_extension" name="phone_extension"
                           class="form-control @error('phone_extension') is-invalid @enderror"
                           value="{{ old('phone_extension', $department->phone_extension) }}">
                    @error('phone_extension')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Changes</button>
                <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

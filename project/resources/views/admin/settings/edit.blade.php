@extends('layouts.app')
@section('title', 'System Settings')
@section('content')
<x-breadcrumb :items="[['label' => 'Settings']]" />
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold"><i class="fas fa-cog me-2 text-primary"></i>System Settings</h4>
</div>

<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white fw-semibold">Hospital Profile</div>
                <div class="card-body">
                    @foreach(['hospital_name' => 'Hospital Name', 'hospital_address' => 'Address', 'hospital_phone' => 'Phone Number', 'hospital_email' => 'Email Address'] as $key => $label)
                    <div class="mb-3">
                        <label class="form-label">{{ $label }}</label>
                        <input type="text" name="settings[{{ $key }}]"
                               class="form-control"
                               value="{{ old("settings.$key", $settings[$key]->value ?? '') }}">
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white fw-semibold">Regional Settings</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Timezone</label>
                            <input type="text" name="settings[timezone]"
                                   class="form-control"
                                   value="{{ old('settings.timezone', $settings['timezone']->value ?? 'Africa/Kampala') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Currency</label>
                            <input type="text" name="settings[currency]"
                                   class="form-control"
                                   value="{{ old('settings.currency', $settings['currency']->value ?? 'UGX') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tax Rate (%)</label>
                            <input type="number" name="settings[tax_rate]" step="0.01" min="0" max="100"
                                   class="form-control"
                                   value="{{ old('settings.tax_rate', $settings['tax_rate']->value ?? '0') }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white fw-semibold">Patient Portal</div>
                <div class="card-body">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="show_notes"
                               name="settings[show_notes_to_patient]" value="1"
                               @if(($settings['show_notes_to_patient']->value ?? '0') == '1') checked @endif>
                        <label class="form-check-label" for="show_notes">
                            Show clinical notes to patients in their portal
                        </label>
                    </div>
                    @if(!request()->has('settings.show_notes_to_patient'))
                    <input type="hidden" name="settings[show_notes_to_patient]" value="0">
                    @endif
                </div>
            </div>

            <button type="submit" class="btn btn-primary px-4">
                <i class="fas fa-save me-1"></i>Save Settings
            </button>
        </div>
    </div>
</form>
@endsection

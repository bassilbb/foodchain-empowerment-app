{{-- resources/views/admin/settings/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Settings')
@section('page-title', 'System Settings')

@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf

    @foreach($settings as $group => $groupSettings)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4 capitalize">{{ str_replace('_', ' ', $group) }} Settings</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($groupSettings as $setting)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ ucwords(str_replace('_', ' ', str_replace($group . '_', '', $setting->key))) }}
                </label>

                @if($setting->type === 'boolean')
                    <select name="settings[{{ $setting->key }}]" class="w-full px-3 py-2 border rounded-lg">
                        <option value="true" {{ $setting->value == 'true' ? 'selected' : '' }}>Yes</option>
                        <option value="false" {{ $setting->value == 'false' ? 'selected' : '' }}>No</option>
                    </select>
                @else
                    <input type="text" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}"
                           class="w-full px-3 py-2 border rounded-lg">
                @endif

                @if($setting->description)
                <p class="text-xs text-gray-500 mt-1">{{ $setting->description }}</p>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    <div class="flex justify-end">
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Save Settings
        </button>
    </div>
</form>
@endsection

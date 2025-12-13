@php
    $page = __('Activity Logs');
    $breadcrumb = [
        ["title" => __('Activity Logs')],
    ];
@endphp
@extends('dashboard.mt.main')
@section('content')
<x-layout.mt.table.basic-card :id="'activity-logs'">
    <x-slot:thead>
        <tr>
            <th>@lang('ID')</th>
            <th>@lang('Description')</th>
            <th>@lang('Causer')</th>
            <th>@lang('Subject')</th>
            <th>@lang('Properties')</th>
            <th>@lang('Created')</th>
        </tr>
    </x-slot:thead>
    <x-slot:tbody>
        @forelse($logs as $log)
            <tr>
                <td>{{ $log->id }}</td>
                <td>{{ $log->description ?? '—' }}</td>
                <td>{{ $log->causer?->name ?? $log->causer_id ?? '—' }}</td>
                <td>
                    @if($log->subject)
                        {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                    @else
                        —
                    @endif
                </td>
                <td>
                    @php $props = $log->properties ?? []; @endphp
                    @if(empty($props))
                        <span class="text-muted">—</span>
                    @else
                        <div class="small text-muted">
                            @foreach($props as $k => $v)
                                <div><strong>{{ $k }}:</strong> {{ is_scalar($v) ? $v : json_encode($v) }}</div>
                            @endforeach
                        </div>
                    @endif
                </td>
                <td>{{ optional($log->created_at)->format('Y-m-d H:i') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-6">@lang('No logs found.')</td>
            </tr>
        @endforelse
    </x-slot:tbody>
    <x-slot:pagination>
        {{ $logs->links() }}
    </x-slot:pagination>
</x-layout.mt.table.basic-card>
@endsection

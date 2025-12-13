<x-layout.mt.cards.basic>
    <x-slot:title>
        @if(!isset($disableSearch))
        <x-layout.mt.table.fields.basic-search :id="$id"/>
        @endif
        <!--begin::Export buttons-->
		<div id="{{$id}}-dt-buttons" class="d-none"></div>
		<!--end::Export buttons-->
        {{ $title ?? ''}}
    </x-slot:title>
    <x-slot:toolbar>
        @if(!isset($disableExport))
        <x-layout.mt.table.buttons.export-menu
            :id="$id"
            :pdf='true'
            :excel='true'
            :csv='true'
            :copy='true'
            :print='true'/>
        @endif
            {{ $toolbar ?? ''}}
    </x-slot:toolbar>
    <x-layout.mt.table.basic :id="$id">
        <x-slot:thead>
            {{ $thead ?? null }}
        </x-slot:thead>
        <x-slot:tbody>
            {{ $tbody }}
        </x-slot:tbody>
        <x-slot:tfoot>
            {{ $tfoot ?? null }}
        </x-slot:tfoot>
        <x-slot:pagination>
            {{ $pagination ?? null }}
        </x-slot:pagination>
    </x-layout.mt.table.basic>
</x-layout.mt.cards.basic>

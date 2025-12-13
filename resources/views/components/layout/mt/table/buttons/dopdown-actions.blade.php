<!--begin::Export dropdown-->
<button type="button" class="btn btn-primary btn-icon rounded-circle btn-sm"
        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
        <i class="ki-outline ki-dots-vertical fs-1"></i>
</button>
<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-200px py-4"
    data-kt-menu="true">
    @isset($buttons)
        {{ $buttons }}
    @else
        {{ $beforeButtons ?? null }}
        @if(!isset($ignoreShow))
        <x-layout.mt.table.buttons.dropdown-action-basic :url="$showUrl"/>
        @endif
        @if(!isset($ignoreEdit))
        <x-layout.mt.table.buttons.dropdown-action-basic
            :url="$editUrl"
            :title="__('common.Edit')">
            <x-slot:icon>
                <i class="ki-duotone ki-feather">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </x-slot:icon>
        </x-layout.mt.table.buttons.dropdown-action-basic>
        @endif
        @if(!isset($ignoreDelete))
        <x-layout.mt.table.buttons.dropdown-action-alert :method="'delete'" :action="$deleteUrl"/>
        @endif
        {{ $afterButtons ?? null }}
    @endisset
</div>

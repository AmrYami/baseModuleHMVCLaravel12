<table class="table align-middle rounded table-row-bordered fs-6 g-5 table-striped table-hover data-table" id="{{ $id ?? '' }}-table">
    @isset($thead)
    <thead>
        {{ $thead}}
    </thead>
    @endisset
    <tbody class="fw-semibold text-gray-600">
        {{ $tbody }}
    </tbody>
    @isset($foot)
    <tfoot>
        {{ $tfoot}}
    </tfoot>
    @endisset
</table>
{{$pagination ?? null}}
@push("foot")
<script nonce="{{ csp_nonce() }}">
    (function() {
        var selector = "#{{$id}}-table";
        if ($.fn.DataTable.isDataTable(selector)) {
            $(selector).DataTable().clear().destroy();
        }
        initDataTable("{{$id}}");
    })();
</script>
@endpush

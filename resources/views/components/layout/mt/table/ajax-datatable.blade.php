
@php
    $id = uniqid()."-dt";
@endphp

<table id="{{ $id }}" class="table align-middle table-row-dashed fs-6 gy-5">
    <thead>
        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
            {{ $thead ?? '' }}
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold"></tbody>
</table>

@push('foot')
    <script nonce="{{ csp_nonce() }}">
        let datatable = $('#{{$id}}').DataTable({
            searchDelay: 10,
            processing: true,
            serverSide: true,
            stateSave: true,
            searching: true, 
            paging: true,
            ajax: {
                url: "{{ $url }}",
                type: 'GET',
                contentType: 'application/json',
            }
            @if(isset($columns))
            ,columns : {{ $columns }}
            @endif
            
            @if(isset($columnDefs))
            ,columnDefs : {{ $columnDefs }}
            @endif
        });
        @if(isset($searchFilter))
            const filterSearch = document.querySelector('#{{$searchFilter}}');
            filterSearch.addEventListener('keyup', function (e) {
                datatable.search(e.target.value).draw();
            });
        @endif
    </script>
@endpush

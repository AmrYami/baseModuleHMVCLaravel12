<!--begin::Javascript-->
<script nonce="{{ csp_nonce() }}">
    var hostUrl = "{{ asset('mt/assets/') }}";
</script>
<script src="https://code.jquery.com/jquery-3.5.0.js"></script>

<!--begin::Global Javascript Bundle(mandatory for all pages)-->
<script src="{{ asset('mt/assets/plugins/global/plugins.bundle.js') }}"></script>
<script src="{{ asset('assets/plugins/custom/prismjs/prismjs.bundle.js') }}"></script>
<script src="{{ asset('mt/assets/js/scripts.bundle.js') }}"></script>
<!--end::Global Javascript Bundle-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="{{ asset('mt/assets/js/widgets.bundle.js') }}"></script>
<script src="{{ asset('mt/assets/js/custom/widgets.js') }}"></script>
<script src="{{ asset('mt/assets/js/custom/apps/chat/chat.js') }}"></script>
<script src="{{ asset('mt/assets/js/custom/utilities/modals/upgrade-plan.js') }}"></script>
<script src="{{ asset('mt/assets/js/custom/utilities/modals/create-app.js') }}"></script>
<script src="{{ asset('mt/assets/js/custom/utilities/modals/new-target.js') }}"></script>
<script src="{{ asset('mt/assets/js/custom/utilities/modals/users-search.js') }}"></script>
<script src="{{ asset("mt/assets/plugins/custom/datatables/datatables.bundle.js") }}"></script>
<script src="{{ asset('mt/assets/js/general.js') }}"></script>
<script src="{{ asset('js/main.js') }}"></script>

<script nonce="{{ csp_nonce() }}">
var salert = function (item, id, message='{{ __('common.Do You Really Want To remove This Item ... ?') }}')
{
    Swal.fire({
        html: message,
        icon: "question",
        buttonsStyling: false,
        showCancelButton: true,
        confirmButtonText: "{{ trans("Yes, Sure ... !") }}",
        cancelButtonText: "{{ trans('Nope, Cancel it ... !') }}",
        customClass: {
            confirmButton: "btn btn-primary",
            cancelButton: 'btn btn-danger'
        }
    }).then((result) => {
        if(result.isConfirmed){
            let form = $("#"+id+"-form");
            form.submit();
        }else if(result.isDenied){
            alert('cancel');
        }
    });
}
</script>

{{-- socket.io temporarily disabled until server is ready --}}



<!--end::Custom Javascript-->
<!--end::Javascript-->
@stack('foot')
@stack('js')

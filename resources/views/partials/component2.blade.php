{{-- @if (session()->has('success'))
<script>
    swal("{{session()->get('success')}}", "", "success", {
        button: "Close",

    });
</script>
@endif

@if (session()->has('error'))
<script>
    swal("{{session()->get('error')}}", "", "error", {
        button: "Close",

    });
</script>
@endif --}}

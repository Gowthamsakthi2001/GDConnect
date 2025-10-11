<x-app-layout>
   <div class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <h2 class="page-header-title">            
            <span>Add Hub</span>
        </h2>
    </div>
    <!-- End Page Header -->
    
    
    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card h-100">
                <div class="card-body">
                    <form action="{{route('admin.Green-Drive-Ev.clients.hub.store')}}" method="post">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="form-group">
                                    <select class="form-control basic-single" id="client_id" name="client_id" required>
                                        @if(isset($clients))
                                            <option value="">Select Client</option>
                                            @foreach($clients as $data)
                                                <option value="{{ $data->id }}" {{ old('client_id') == $data->id ? 'selected' : '' }}>
                                                    {{ $data->client_name ?? ''}} ({{ $data->client_location ?? '' }})
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 text-end">
                                <a href="javascript:void(0);" class="btn btn-dark btn-sm p-2" id="add-hub">
                                    <svg class="svg-inline--fa fa-circle-plus" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="circle-plus" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
                                        <path fill="currentColor" d="M256 512c141.4 0 256-114.6 256-256S397.4 0 256 0S0 114.6 0 256S114.6 512 256 512zM232 344V280H168c-13.3 0-24-10.7-24-24s10.7-24 24-24h64V168c0-13.3 10.7-24 24-24s24 10.7 24 24v64h64c13.3 0 24 10.7 24 24s-10.7 24-24 24H280v64c0 13.3-10.7 24-24 24s-24-10.7-24-24z"></path>
                                    </svg>
                                    Add Multiple
                                </a>
                            </div>
                        </div>
                        <!-- Hub Name Rows -->
                        <div class="row mb-3" id="hub_name_show_rows">
                            <div class="col-md-6 col-12 mb-3 hub-row">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" name="hub_name[]" placeholder="Hub Name" required>
                                    <button class="btn btn-danger remove-hub" type="button"><i class="bi bi-trash-fill"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                             <div class="d-md-flex d-flex d-grid align-items-center justify-content-end text-white gap-3">
                                <button type="button" onclick="reset_call_function()" class="btn btn-dark btn-round">{{ __('Reset') }}</button>
                                <button type="submit" class="btn btn-success btn-round">{{ __('Submit') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@section('script_js')
<script>
    $(document).ready(function () {
        $('#add-hub').click(function () {
            const hubRow = `
                <div class="col-md-6 col-12 mb-3 hub-row">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="hub_name[]" placeholder="Hub Name" required>
                        <button class="btn btn-danger remove-hub" type="button"><i class="bi bi-trash-fill"></i></button>
                    </div>
                </div>
            `;
            $('#hub_name_show_rows').append(hubRow);
        });

        // Remove hub row
        $(document).on('click', '.remove-hub', function () {
            $(this).closest('.hub-row').remove();
        });
    });
    function reset_call_function(){
        window.location.reload();
    }
</script>
@endsection
</x-app-layout>


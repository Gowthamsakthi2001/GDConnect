<x-app-layout>
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h2 class="page-header-title d-flex justify-content-between">
                <div> <img src="{{asset('admin-assets/icons/custom/lead_verify.png')}}" class="img-fluid rounded"><span
                        class="ps-2">Asset Import data's verify</span></div>
                <a class="btn btn-primary" href="{{ route('admin.Green-Drive-Ev.asset-master.asset_master_vehicle_index') }}">Back</a>
            </h2>
        </div>
        <!-- End Page Header -->


        <!--page card-->
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-md-6 col-12 mt-3">
                        <div class="card h-100">
                            <div class="card-body scrollable-content">
                                <h6>Asset Status </h6>
                                <div class="row mt-4">
                                    <div class="table-responsive" style="max-height:300px; overflow-y:auto; overflow-x:auto;">
                                        <table class="table table-bordered table-striped">
                                            <thead class="text-white" style="background:#17c653;">
                                                <tr>
                                                    <th>Name</th>
                                                    <th>ID</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                 @if(isset($asset_status) && count($asset_status) > 0)
                                                    @foreach($asset_status as $data)
                                                      <tr>
                                                          <td>{{$data->status_name ?? ''}}</td>
                                                          <td>{{$data->id}}</td>
                                                      </tr>
                                                    @endforeach
                                                 @else
                                                    <tr><td colspan="2">No data found</td></tr>
                                                 @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-12 mt-3">
                        <div class="card h-100">
                            <div class="card-body scrollable-content">
                                <h6>Swappable Status</h6>
                                <div class="row mt-4">
                                    <div class="table-responsive" style="max-height:300px; overflow-y:auto; overflow-x:auto;">
                                        <table class="table table-bordered table-striped">
                                            <thead class="text-white" style="background:#17c653;">
                                                <tr>
                                                    <th>Name</th>
                                                    <th>ID</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Yes</td>
                                                    <td>1</td>
                                                </tr>
                                                <tr>
                                                    <td>No</td>
                                                    <td>0</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @section('script_js')
    <script>

    </script>
    @endsection
</x-app-layout>
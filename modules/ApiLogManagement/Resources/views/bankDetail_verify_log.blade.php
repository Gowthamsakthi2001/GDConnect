<x-app-layout>
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h2 class="page-header-title">            
                <img src="{{asset('admin-assets/icons/custom/ahaar_card_log.png')}}" class="img-fluid rounded"><span class="ps-2">Bank Detail Verify Logs</span>
            </h2>
        </div>
        <!-- End Page Header -->
        
        
           <x-card>
            <x-slot name='actions'>
                <!--<a href="{{ route('admin.Green-Drive-Ev.leavemanagement.index') }}" class="btn btn-success btn-sm">-->
                <!--    <i class="fa fa-plus-circle"></i>&nbsp;-->
                <!--    {{ localize('Back') }}-->
                <!--</a>-->
            </x-slot>
            <div>
                  <x-data-table :dataTable="$dataTable" />
            </div>
        </x-card>
    </div>
@section('script_js')
<script>
   
</script>
@endsection
</x-app-layout>

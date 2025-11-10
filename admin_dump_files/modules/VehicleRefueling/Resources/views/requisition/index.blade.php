<x-app-layout>
    <x-card>
        <x-slot name='actions'>
            <a class="btn btn-success" href="javascript:void(0);"
                onclick="axiosModal('{{ route(\config('theme.rprefix') . '.create') }}')">
                <i class="fa fa-plus"></i>&nbsp;
                {{ localize('Create') }}
            </a>
        </x-slot>
        <div>
            <x-data-table :dataTable="$dataTable" />
            <div id="page-axios-data" data-table-id="#fuel-requisition-table"></div>
        </div>
    </x-card>

    @push('lib-styles')
        <link rel="stylesheet" href="{{ nanopkg_asset('vendor/select2/select2.min.css') }}">
    @endpush
    @push('lib-scripts')
        <script src="{{ nanopkg_asset('vendor/select2/select2.min.js') }}"></script>
    @endpush
    @push('js')
        <script src="{{ module_asset('VehicleRefueling/js/app.min.js') }}"></script>
    @endpush
</x-app-layout>

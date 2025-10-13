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
            <div id="page-axios-data" data-table-id="#vehicle-rta-office-table"></div>
        </div>
    </x-card>
</x-app-layout>

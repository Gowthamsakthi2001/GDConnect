<x-app-layout>
    <x-card>
        <x-slot name='actions'>
            <div class="accordion-header d-flex justify-content-end" id="flush-headingOne">
                <a class="btn btn-success btn-sm" href="javascript:void(0);"
                    onclick="axiosModal('{{ route(\config('theme.rprefix') . '.create') }}')">
                    <i class="fa fa-plus"></i>&nbsp;
                    {{ localize('Add Requisition') }}
                </a>
            </div>
        </x-slot>


        <div>
            <x-data-table :dataTable="$dataTable" />
            <div id="page-axios-data" data-table-id="#vehicle-requisition-purpose-table"></div>
        </div>
    </x-card>

</x-app-layout>

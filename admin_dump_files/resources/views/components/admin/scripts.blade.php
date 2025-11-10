<!--Global script(used by all pages)-->
<script src="{{ admin_asset('vendor/jQuery/jquery.min.js') }}"></script>
<!--<script src="{{ admin_asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="{{ admin_asset('vendor/emojionearea/dist/emojionearea.min.js') }}"></script>
 <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
 
  <!-- Select2 cdn added by Gowtham.s-->
 <script src="{{ asset('public/admin-assets/js/select2.min.js') }}"></script>
 
 <!-- DataTables cdn added by Gowtham.s-->
 <script src="{{ asset('public/admin-assets/js/jquery.dataTables.min.js') }}"></script>
 <script src="{{ asset('public/admin-assets/js/dataTables.buttons.min.js') }}"></script>
 <script src="{{ asset('public/admin-assets/js/jszip.min.js') }}"></script>
 <script src="{{ asset('public/admin-assets/js/pdfmake.min.js') }}"></script>
 <script src="{{ asset('public/admin-assets/js/buttons.html5.min.js') }}"></script>
 <script src="{{ asset('public/admin-assets/js/buttons.print.min.js') }}"></script>
 
 <!-- Dropzone JS added by Gowtham.s-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>

<!-- chart JS added by Gowtham.s-->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@1.4.0/dist/chartjs-plugin-annotation.min.js"></script>
 
  <!-- JS script tag added by Gowtham.s-->
 <script> 
     


     
    //  $(document).ready(function(){
    //      if('.custom-select2-field'.length > 0){
    //       $(".custom-select2-field").select2({
    //         width: '100%' 
    //     });  
    //      }
        
    // });

     $(document).ready(function () {
       $('.custom-table').DataTable({
            // dom: 'Blfrtip',
            dom: 'frtip',
            buttons: ['excel', 'pdf', 'print'],
            // order: [[0, 'desc']],
            columnDefs: [
                { orderable: false, targets: '_all' }
            ],
            lengthMenu: [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "All"] ],
            responsive: false,
            scrollX: true,
        });
    });
    
    function hr_dashboard_redirect(val){
        // alert(val);
        if(val == 1){
            var redirect_url = "{{route('admin.Green-Drive-Ev.hr_status.dashboard')}}";
             window.location.href = redirect_url;
        }
        
    }
     function bgv_dashboard_redirect(){
        var redirect_url = "{{route('admin.Green-Drive-Ev.bgvvendor.dashboard')}}";
        window.location.href = redirect_url;
    }
</script>

<script>
// document.addEventListener('DOMContentLoaded', function () { //multiples upload 
//     const uploadArea = document.querySelector('.custom-upload-area');
//     const fileInput = document.getElementById('documents');
//     const fileCountDisplay = document.getElementById('file-count');

//     if (uploadArea && fileInput && fileCountDisplay) {
//         uploadArea.addEventListener('dragover', function (e) {
//             e.preventDefault();
//             uploadArea.style.borderColor = '#28a745';
//         });

//         uploadArea.addEventListener('dragleave', function () {
//             uploadArea.style.borderColor = '#ccc';
//         });

//         uploadArea.addEventListener('drop', function (e) {
//             e.preventDefault();
//             uploadArea.style.borderColor = '#ccc';
//             const files = e.dataTransfer.files;
//             fileInput.files = files;

//             // Show file count
//             fileCountDisplay.textContent = files.length > 0 
//                 ? `${files.length} file(s) selected` 
//                 : '';
//         });

//         fileInput.addEventListener('change', function () {
//             const files = fileInput.files;
//             fileCountDisplay.textContent = files.length > 0 
//                 ? `${files.length} file(s) selected` 
//                 : '';
//         });
//     }
// });

document.addEventListener('DOMContentLoaded', function () {

    const selectFields = document.querySelectorAll('.custom-select2-field');
    if (selectFields.length > 0) {
        $(selectFields).select2({ width: '100%' });

        if (selectFields.length > 1) {
            selectFields.forEach(function (el) {
                el.classList.add('multi-select2'); 
            });
        }
    }

    const uploadArea = document.querySelector('.custom-upload-area');
    const fileInput = document.getElementById('documents');
    const fileCountDisplay = document.getElementById('file-count');

    if (uploadArea && fileInput && fileCountDisplay) {
        uploadArea.addEventListener('dragover', function (e) {
            e.preventDefault();
            uploadArea.style.borderColor = '#28a745';
        });

        uploadArea.addEventListener('dragleave', function () {
            uploadArea.style.borderColor = '#ccc';
        });

        uploadArea.addEventListener('drop', function (e) {
            e.preventDefault();
            uploadArea.style.borderColor = '#ccc';
            const files = e.dataTransfer.files;

            if (files.length > 1) {
                toastr.error('Only one file is allowed.');
                return;
            }

            fileInput.files = files;
            fileCountDisplay.textContent = `${files.length} file selected`;
        });

        fileInput.addEventListener('change', function () {
            const files = fileInput.files;

            if (files.length > 1) {
                toastr.error('Only one file is allowed.');
                fileInput.value = ''; // clear the input
                fileCountDisplay.textContent = '';
                return;
            }

            fileCountDisplay.textContent = `${files.length} file selected`;
        });
    }
});

$(document).ready(function () {
    $('#sidebar_searchdata').on('keyup', function () {
        let search = $(this).val().toLowerCase();

        // Loop through all <a> with .has-arrow
        $('.has-arrow').each(function () {
            let text = $(this).text().toLowerCase();

            if (text.includes(search)) {
                $(this).closest('li').show(); // show the parent <li> or wrapper
            } else {
                $(this).closest('li').hide();
            }
        });
    });
});

</script>

  
@stack('lib-scripts')
{{-- <script src="{{ nanopkg_asset('vendor/highlight/highlight.min.js') }}"></script> --}}
<script src="{{ admin_asset('vendor/metisMenu/metisMenu.min.js') }}"></script>
<script src="{{ admin_asset('vendor/perfect-scrollbar/dist/perfect-scrollbar.min.js') }}"></script>
<script src="{{ nanopkg_asset('vendor/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ nanopkg_asset('vendor/fontawesome-free-6.3.0-web/js/all.min.js') }}"></script>
<script src="{{ nanopkg_asset('vendor/toastr/build/toastr.min.js') }}"></script>
<script src="{{ nanopkg_asset('vendor/axios/dist/axios.min.js') }}"></script>
<script src="{{ nanopkg_asset('vendor/typed.js/lib/typed.min.js') }}"></script>
<script src="{{ nanopkg_asset('vendor/jquery-validation-1.19.5/jquery.validate.min.js') }}"></script>
<script src="{{ nanopkg_asset('js/axios.init.min.js') }}"></script>
<script src="{{ nanopkg_asset('js/arrow-hidden.min.js') }}"></script>
<script src="{{ nanopkg_asset('js/img-src.min.js') }}"></script>
<script src="{{ nanopkg_asset('js/delete.min.js') }}"></script>
<script src="{{ nanopkg_asset('js/user-status-update.min.js') }}"></script>
<script src="{{ nanopkg_asset('js/main.js') }}"></script>

<!--Page Scripts(used by all page)-->
<script src="{{ admin_asset('js/sidebar.min.js') }}"></script>
@stack('js')

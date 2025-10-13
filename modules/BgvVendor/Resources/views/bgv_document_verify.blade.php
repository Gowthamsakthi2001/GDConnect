<x-app-layout>

    <div class="main-content">
           <!--https://evms.greendrivemobility.com/public/EV/images/dummy.jpg-->
           <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row d-flex justify-content-between g-3">
                        <div class="col">
                            <div class="card-title h4 fw-bold">View Documents</div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">BGV</li>
                                    <li class="breadcrumb-item"><a href="javascript:void(0);">View Documents</a></li>
                                </ol>
                            </nav>
                        </div>

                        <div class="col text-end">
                            @php
                                $previousUrl = request()->headers->get('referer');
                                $type = 'total_application'; // default fallback
                            
                                if ($previousUrl) {
                                    $segments = explode('/', trim(parse_url($previousUrl, PHP_URL_PATH), '/'));
                                    $last = end($segments);
                                    if (in_array($last, ['pending_application', 'hold_application', 'complete_application', 'reject_application'])) {
                                        $type = $last;
                                    }
                                }
                            @endphp

                           <a href="{{ route('admin.Green-Drive-Ev.bgvvendor.bgv_list', ['type' => $type]) }}" class="btn btn-dark px-4">Back</a>
                        </div>
                    </div>
                </div>
            </div>
        <!-- End Page Header -->
        
        <div>
            <div class="card mb-3">
                <div class="card-header">
                    <div class="row d-flex justify-content-between g-3">
                        <div class="col">
                            <div class="card-title h4 fw-bold">Aadhar Card Details</div>
                        </div>

                        <div class="col text-end">
                           @if($dm->aadhar_verify == 1)
                            <button class="btn btn-success px-5" onclick="status_change_alert(
                                    '{{ route('admin.Green-Drive-Ev.delivery-man.verification', [$dm->id, $dm->aadhar_verify, 'aadhar_verify']) }}', 
                                    '{{ $dm->aadhar_verify ? 'UnVerified' : 'Verified' }} this Aadhar?', 
                                    event
                                )">Verified</button>
                           @else
                             <button class="btn btn-danger px-4"  onclick="status_change_alert(
                                    '{{ route('admin.Green-Drive-Ev.delivery-man.verification', [$dm->id, $dm->aadhar_verify, 'aadhar_verify']) }}', 
                                    '{{ $dm->aadhar_verify ? 'UnVerified' : 'Verified' }} this Aadhar?', 
                                    event
                                )">Verify</button>
                           @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                     <?php
                        $user = \App\Models\User::find($dm->who_aadhar_verify_id);
                        $verify_name = $user->name ?? '';
                        $verify_role = '';
                    
                        if ($user && $user->role) {
                            $verify_by = \Illuminate\Support\Facades\DB::table('roles')->where('id', $user->role)->first();
                            $verify_role = $verify_by->name ?? '';
                        }
                        $front = isset($dm->aadhar_card_front) ? asset('public/EV/images/aadhar/' . $dm->aadhar_card_front) : asset('public/EV/images/dummy.jpg');
                        $back = isset($dm->aadhar_card_back) ? asset('public/EV/images/aadhar/' . $dm->aadhar_card_back) : asset('public/EV/images/dummy.jpg');
                    ?>
                    <h6 class="mb-3">
                        Verified by: 
                        <span class="text-success">
                            {{ $verify_name ? $verify_name . ' (' . $verify_role . ')' : '' }}
                        </span>
                    </h6>

                    <h6>Verified At: 
                        <span class="text-success">
                            {{ !empty($dm->aadhar_verify_date) ? date('d M Y h:i:s A', strtotime($dm->aadhar_verify_date)) : '' }}
                        </span>
                    </h6>
                    
                    <div class="row mt-5">
                        <div class="col-md-4 mt-3 mt-md-0 d-flex justify-content-start">
                            <div class="image-container" onclick="OpenImageModal('{{$front}}')">
                                <img id=""
                                    src="{{$front}}"
                                    class="preview-image img-fluid" alt="Image"
                                    style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                            </div>
                        </div>
                    
                        <div class="col-md-4 mt-3 mt-md-0 d-flex justify-content-sart">
                            <div class="image-container" onclick="OpenImageModal('{{$back}}')">
                                <img id=""
                                    src="{{$back}}"
                                    class="preview-image img-fluid" alt="Image"
                                    style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                            </div>
                        </div>
                    </div>


                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header">
                    <div class="row d-flex justify-content-between g-3">
                        <div class="col">
                            <div class="card-title h4 fw-bold">Pan Card Details</div>
                        </div>

                        <div class="col text-end">
                           @if($dm->pan_verify == 1)
                            <button class="btn btn-success px-5"  onclick="status_change_alert(
                                    '{{ route('admin.Green-Drive-Ev.delivery-man.verification', [$dm->id, $dm->pan_verify, 'pan_verify']) }}', 
                                    '{{ $dm->pan_verify ? 'UnVerified' : 'Verified' }} this Pan?', 
                                    event
                                )">Verified</button>
                           @else
                             <button class="btn btn-danger px-4" onclick="status_change_alert(
                                    '{{ route('admin.Green-Drive-Ev.delivery-man.verification', [$dm->id, $dm->pan_verify, 'pan_verify']) }}', 
                                    '{{ $dm->pan_verify ? 'UnVerified' : 'Verified' }} this Pan?', 
                                    event
                                )">Verify</button>
                           @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php
                        $user1 = \App\Models\User::find($dm->who_pan_verify_id);
                        $verify_name1 = $user1->name ?? '';
                        $verify_role1 = '';
                    
                        if ($user1 && $user1->role) {
                            $verify_by1 = \Illuminate\Support\Facades\DB::table('roles')->where('id', $user1->role)->first();
                            $verify_role1 = $verify_by1->name ?? '';
                        }
                        
                        $pan_image = isset($dm->pan_card_front) ? asset('public/EV/images/pan/' . $dm->pan_card_front) : asset('public/EV/images/dummy.jpg');
                    ?>
                    <h6 class="mb-3">
                        Verified by: 
                        <span class="text-success">
                            {{ $verify_name1 ? $verify_name1 . ' (' . $verify_role1 . ')' : '' }}
                        </span>
                    </h6>

                   <h6>Verified At: 
                        <span class="text-success">
                            {{ !empty($dm->pan_verify_date) ? date('d M Y h:i:s A', strtotime($dm->pan_verify_date)) : '' }}
                        </span>
                    </h6>

                    
                    <div class="row mt-5">
                        <div class="col-md-4 mt-3 mt-md-0 d-flex justify-content-start">
                            <div class="image-container" onclick="OpenImageModal('{{$pan_image}}')">
                                <img id=""
                                    src="{{$pan_image}}"
                                    class="preview-image img-fluid" alt="Image"
                                    style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header">
                    <div class="row d-flex justify-content-between g-3">
                        <div class="col">
                            <div class="card-title h4 fw-bold">Bank Details</div>
                        </div>

                        <div class="col text-end">
                           @if($dm->bank_verify == 1)
                            <button class="btn btn-success px-5" onclick="status_change_alert(
                                    '{{ route('admin.Green-Drive-Ev.delivery-man.verification', [$dm->id, $dm->bank_verify, 'bank_verify']) }}', 
                                    '{{ $dm->bank_verify ? 'UnVerified' : 'Verified' }} this Bank Details?', 
                                    event
                                )">Verified</button>
                           @else
                             <button class="btn btn-danger px-4" onclick="status_change_alert(
                                    '{{ route('admin.Green-Drive-Ev.delivery-man.verification', [$dm->id, $dm->bank_verify, 'bank_verify']) }}', 
                                    '{{ $dm->bank_verify ? 'UnVerified' : 'Verified' }} this Bank Details?', 
                                    event
                                )">Verify</button>
                           @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php
                        $user2 = \App\Models\User::find($dm->who_bank_verify_id);
                        $verify_name2 = $user2->name ?? '';
                        $verify_role2 = '';
                    
                        if (!empty($user2?->role)) {
                            $verify_by2 = \Illuminate\Support\Facades\DB::table('roles')->where('id', $user2->role)->first();
                            $verify_role2 = $verify_by2->name ?? '';
                        }
                        $bank_image = isset($dm->pan_card_front) ? asset('public/EV/images/bank_passbook/' . $dm->bank_passbook) : asset('public/EV/images/dummy.jpg');
                    ?>
                    <h6 class="mb-3">
                        Verified by: 
                        <span class="text-success">
                            {{ $verify_name2 ? $verify_name2 . ' (' . $verify_role2 . ')' : '' }}
                        </span>
                    </h6>

                    <h6>Verified At: 
                        <span class="text-success">
                            {{ !empty($dm->bank_verify_date) ? date('d M Y h:i:s A', strtotime($dm->bank_verify_date)) : '' }}
                        </span>
                    </h6>
                    
                    <div class="row mt-5">
                        <div class="col-md-4 mt-3 mt-md-0 d-flex justify-content-start">
                            <div class="image-container" onclick="OpenImageModal('{{$bank_image}}')">
                                <img id=""
                                    src="{{$bank_image}}"
                                    class="preview-image img-fluid" alt="Image"
                                    style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                            </div>
                        </div>
                    
                        <div class="col-md-6 mt-3 mt-md-0">
                             <h6 class="my-3"> Bank Holder Name:&nbsp; <span class="text-secondary">{{ $dm->account_holder_name ?? ''}}</span></h6>
                             <h6 class="mb-3"> Bank Name: &nbsp;<span class="text-secondary">{{ $dm->bank_name ?? ''}}</span></h6>
                             <h6 class="mb-3"> IFSC Code:&nbsp; <span class="text-secondary">{{ $dm->ifsc_code ?? '' }}</span></h6>
                             <h6 class="mb-3"> Account Number:&nbsp; <span class="text-secondary">{{ $dm->account_number ?? '' }}</span></h6>
                        </div>
                    </div>


                </div>
            </div>
            @if($dm->work_type != "" && $dm->work_type != "in-house")
            <div class="card mb-3">
                <div class="card-header">
                    <div class="row d-flex justify-content-between g-3">
                        <div class="col">
                            @if(empty($dm->driving_license_front) && empty($dm->driving_license_back) && empty($dm->license_number))

                            <div class="card-title h4 fw-bold">LLR Details</div>
                            
                            @else
                            
                            <div class="card-title h4 fw-bold">License Details</div>
                            @endif
                        </div>

                        <div class="col text-end">
                           @if($dm->lisence_verify == 1)
                            <button class="btn btn-success px-5" onclick="status_change_alert(
                                    '{{ route('admin.Green-Drive-Ev.delivery-man.verification', [$dm->id, $dm->lisence_verify, 'license_verify']) }}', 
                                    '{{ $dm->lisence_verify ? 'UnVerified' : 'Verified' }} this License?', 
                                    event
                                )">Verified</button>
                           @else
                             <button class="btn btn-danger px-4" onclick="status_change_alert(
                                    '{{ route('admin.Green-Drive-Ev.delivery-man.verification', [$dm->id, $dm->lisence_verify, 'license_verify']) }}', 
                                    '{{ $dm->lisence_verify ? 'UnVerified' : 'Verified' }} this License?', 
                                    event
                                )">Verify</button>
                           @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                     <?php
                        $user3 = \App\Models\User::find($dm->who_license_verify_id);
                        $verify_name3 = $user3->name ?? '';
                        $verify_role3 = '';
                    
                        if (!empty($user3?->role)) {
                            $verify_by3 = \Illuminate\Support\Facades\DB::table('roles')->where('id', $user3->role)->first();
                            $verify_role3 = $verify_by3->name ?? '';
                        }
                        
                        // $front1 = isset($dm->driving_license_front) ? asset('public/EV/images/driving_license/' . $dm->driving_license_front) : asset('public/EV/images/dummy.jpg');
                        // $back1 = isset($dm->driving_license_back) ? asset('public/EV/images/driving_license/' . $dm->driving_license_back) : asset('public/EV/images/dummy.jpg');
                        
                            $front1 = !empty($dm->driving_license_front) 
                                ? asset('public/EV/images/driving_license/' . $dm->driving_license_front) 
                                : null;
                        
                            $back1 = !empty($dm->driving_license_back) 
                                ? asset('public/EV/images/driving_license/' . $dm->driving_license_back) 
                                : null;
                        
                            $defaultImage = asset('public/EV/images/dummy.jpg');
                    ?>
                    <h6 class="mb-3">
                        Verified by: 
                        <span class="text-success">
                            {{ $verify_name3 ? $verify_name3 . ' (' . $verify_role3 . ')' : '' }}
                        </span>
                    </h6>

                     <h6>Verified At: 
                        <span class="text-success">
                            {{ !empty($dm->lisence_verify_date) ? date('d M Y h:i:s A', strtotime($dm->lisence_verify_date)) : '' }}
                        </span>
                    </h6>
                    
                 @if(empty($dm->driving_license_front) && empty($dm->driving_license_back) && empty($dm->license_number))
                    
                    @php
                        $llr_image = isset($dm->llr_image) ? asset('public/EV/images/llr_images/' . $dm->llr_image) : asset('public/EV/images/dummy.jpg');
                        $fileUrl = $llr_image ?? null;
                        $extension = $fileUrl ? strtolower(pathinfo($fileUrl, PATHINFO_EXTENSION)) : null;
                    @endphp
                    <?php 
                    
                    ?>
                    
                    <div class="row mt-5">
                        <div class="col-md-4 mt-3 mt-md-0 d-flex justify-content-start">
                            <div class="image-container" onclick="OpenImageModal('{{ $fileUrl ?? $defaultImage }}')">
                                @if($fileUrl && in_array($extension, ['jpg', 'jpeg', 'png']))
                                    <img src="{{ $fileUrl }}" class="preview-image img-fluid" alt="Image"
                                         style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                                @elseif($fileUrl && $extension === 'pdf')
                                    <iframe src="{{ $fileUrl }}" width="270" height="180"
                                            style="border-radius: 10px; border: 1px solid #ccc;"></iframe>
                                @else
                                    <img src="{{ $defaultImage }}" class="preview-image img-fluid" alt="No Image"
                                         style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                                @endif
                            </div>
                        </div>
                    </div>

                    
                    
                    @else
                    
                        <div class="row mt-5">
                        <div class="col-md-4 mt-3 mt-md-0 d-flex justify-content-start">
                            <div class="image-container" onclick="OpenImageModal('{{$front1 ?? $defaultImage }}')">
                                <img id=""
                                    src="{{$front1 ?? $defaultImage}}"
                                    class="preview-image img-fluid" alt="Image"
                                    style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                            </div>
                        </div>
                    
                        <div class="col-md-4 mt-3 mt-md-0 d-flex justify-content-sart">
                            <div class="image-container" onclick="OpenImageModal('{{$back1 ?? $defaultImage }}')">
                                <img id=""
                                    src="{{$back1 ?? $defaultImage }}"
                                    class="preview-image img-fluid" alt="Image"
                                    style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                            </div>
                        </div>
                    </div>
                    
                    @endif
                    



                </div>
            </div>
            @endif
        </div>
    </div>
    
       <!--<div class="modal fade" id="BKYC_Verify_view_modal" tabindex="-1" aria-labelledby="BKYC_Verify_viewLabel" aria-hidden="true">-->
       <!--   <div class="modal-dialog modal-lg modal-dialog-scrollable">-->
       <!--     <div class="modal-content rounded-4">-->
        
              <!-- Fixed header -->
       <!--       <div class="modal-header border-0 d-flex justify-content-end gap-1 position-sticky top-0 bg-white z-1">-->
       <!--         <button class="btn btn-sm btn-dark" onclick="zoomIn()">-->
       <!--           <i class="bi bi-zoom-in"></i>-->
       <!--         </button>-->
       <!--         <button class="btn btn-sm btn-dark" onclick="zoomOut()">-->
       <!--           <i class="bi bi-zoom-out"></i>-->
       <!--         </button>-->
       <!--         <button class="btn btn-sm btn-dark" onclick="rotateImage()">-->
       <!--           <i class="bi bi-arrow-repeat"></i>-->
       <!--         </button>-->
       <!--         <button class="btn btn-sm btn-dark" data-bs-dismiss="modal">-->
       <!--           <i class="bi bi-x-lg"></i>-->
       <!--         </button>-->
       <!--       </div>-->
        
              <!-- Scrollable image container -->
       <!--       <div class="modal-body text-center mt-5" style="overflow: auto; max-height: 75vh;">-->
       <!--         <img src="" id="kyc_image" class="img-fluid" style="transition: transform 0.3s ease;">-->
       <!--       </div>-->
              
       <!--     </div>-->
       <!--   </div>-->
       <!-- </div>-->

<div class="modal fade" id="BKYC_Verify_view_modal" tabindex="-1" aria-labelledby="BKYC_Verify_viewLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content rounded-4">

      <!-- Header with fixed control buttons -->
      <div class="modal-header border-0 d-flex justify-content-end gap-1">
        <button class="btn btn-sm btn-dark" onclick="zoomIn()">
          <i class="bi bi-zoom-in"></i>
        </button>
        <button class="btn btn-sm btn-dark" onclick="zoomOut()">
          <i class="bi bi-zoom-out"></i>
        </button>
        <button class="btn btn-sm btn-dark" onclick="rotateImage()">
          <i class="bi bi-arrow-repeat"></i>
        </button>
        <button class="btn btn-sm btn-dark" data-bs-dismiss="modal">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>

      <!-- Scrollable modal body -->
      <div class="modal-body text-center py-6" style="overflow: auto; max-height: 80vh;">
        <img src="" id="kyc_image" style="max-width: 100%; transition: transform 0.3s ease;">
      </div>

    </div>
  </div>
</div>


    
@section('script_js')
<script>
 function status_change_alert(url, message, e) {
            e.preventDefault();
            Swal.fire({
                title: "Are you sure?",
                text: message,
                icon: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#FC6A57',
                cancelButtonText: "No",
                confirmButtonText: "Yes",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    location.href = url;
                }
            });
        }
        
function OpenImageModal(img_url) {
    $("#kyc_image").attr("src", ""); // Clear image first
    $("#BKYC_Verify_view_modal").modal('show'); // Corrected selector
    $("#kyc_image").attr("src", img_url); // Load new image
}

let scale = 1;
let rotation = 0;

function OpenImageModal(img_url) {
    scale = 1;
    rotation = 0;
    updateImageTransform();
    $("#kyc_image").attr("src", img_url);
    $("#BKYC_Verify_view_modal").modal('show');
}

function zoomIn() {
    scale += 0.1;
    updateImageTransform();
}

function zoomOut() {
    if (scale > 0.2) {
        scale -= 0.1;
        updateImageTransform();
    }
}

function rotateImage() {
    rotation = (rotation + 90) % 360;
    updateImageTransform();
}

function updateImageTransform() {
    const img = document.getElementById("kyc_image");
    img.style.transform = `scale(${scale}) rotate(${rotation}deg)`;
}


</script>
@endsection
</x-app-layout>

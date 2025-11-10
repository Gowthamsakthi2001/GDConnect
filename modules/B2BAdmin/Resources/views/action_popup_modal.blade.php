<div class="modal fade" id="BKYC_Verify_view_modal" tabindex="-1" aria-labelledby="BKYC_Verify_viewLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4">
          <div class="modal-header border-0 d-flex justify-content-end gap-1">
            <button class="btn btn-sm btn-dark" id="zoomInBtn" onclick="zoomIn()" title="Zoom In">
              <i class="bi bi-zoom-in"></i>
            </button>
            <button class="btn btn-sm btn-dark" id="zoomOutBtn" onclick="zoomOut()" title="Zoom Out">
              <i class="bi bi-zoom-out"></i>
            </button>
            <button class="btn btn-sm btn-dark" id="rotateBtn" onclick="rotateImage()" title="Rotate">
              <i class="bi bi-arrow-repeat"></i>
            </button>
            <button class="btn btn-sm btn-dark" id="downloadBtn" title="Download">
              <i class="bi bi-download"></i>
            </button>
            <button class="btn btn-sm btn-dark" data-bs-dismiss="modal" title="Close">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
    
          <div class="modal-body text-center py-3" style="overflow: auto; max-height: 80vh;">
            <img id="kyc_image"
                 src=""
                 style="max-width: 100%; display: none; transition: transform 0.3s ease;"
                 class="rounded shadow">
            <iframe id="kyc_pdf"
                    src=""
                    style="width: 100%; height: 80vh; border: none; display: none;"
                    class="rounded shadow">
            </iframe>
          </div>
    
        </div>
      </div>
    </div>
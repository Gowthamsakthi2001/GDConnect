<x-app-layout>
    <div class="main-content">

           <div class="card bg-transparent my-4">
                <div class="card-header d-flex align-items-center justify-content-between" style="background:#fbfbfb;">
                    <div>
                        <div class="card-title h4 fw-bold">View BGV Documents</div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('admin.Green-Drive-Ev.hr_status.index')}}">Recruiters</a></li>
                                <li class="breadcrumb-item"><a href="javascript:void(0);">View BGV Documents</a></li>
                            </ol>
                        </nav>
                    </div>
                    <!-- Role Selector -->
                    <div class="p-3 rounded d-flex align-items-center rounded">
                       <a href="{{route('admin.Green-Drive-Ev.hr_status.index')}}" class="btn btn-dark btn-md">Back</a>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                     <div>
                        <h1 class="h3 fs-5 text-center">Candidate ID : {{$dm->emp_id ?? ''}}</h1>
                        <p class="text-center">Candidate Name : {{ucfirst($dm->first_name) ?? ''}} {{ucfirst($dm->last_name) ?? ''}}</p>
                      </div>
                </div>
                <div class="card-body">
                    <div class="row">
                          @if(isset($documents) && count($documents) > 0)
                             @foreach($documents as $val)
                                <div class="col-md-3 col-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1">{{$val->documents ?? 'N/A'}}</label><br><br>
                                        <a href="{{asset('EV/bgv_upload_docs/'.$val->documents)}}"
                                                class="me-1 icon-btn" target="_blank">
                                                <img src="{{asset('public/admin-assets/img/document_img.jpg')}}" class="rounded img-fluid" alt="Image">
                                        </a>
                                    </div>
                                </div>
                             @endforeach
                        @else
                         <div class="col-md-3 col-6 mb-3 text-center">
                             <h6 class="text-center">No Documents</h6>
                         </div>
                        @endif
                    </div>
                </div>
            </div>
            
    </div>
    
   
@section('script_js')
<script>

 
</script>
@endsection
</x-app-layout>

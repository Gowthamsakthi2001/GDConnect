<x-app-layout>
    <div class="main-content">

           <div class="card bg-transparent my-4">
                <div class="card-header d-flex align-items-center justify-content-between" style="background:#fbfbfb;">
                    <div>
                        <div class="card-title h4 fw-bold">View BGV Comments</div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('admin.Green-Drive-Ev.hr_status.index')}}">Recruiters</a></li>
                                <li class="breadcrumb-item"><a href="javascript:void(0);">View BGV Comments</a></li>
                            </ol>
                        </nav>
                    </div>
                    
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
                            
            
                    <!-- Role Selector -->
                    <div class="p-3 rounded d-flex align-items-center rounded">
                       <a href="{{ route('admin.Green-Drive-Ev.bgvvendor.bgv_list', ['type' => $type]) }}" class="btn btn-dark btn-md">Back</a>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                     <div>
                        <h1 class="h3 fs-5 text-center">Query</h1>
                        <p class="text-center">From HR</p>
                      </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="mobile_no">{{'Comments'}}</label>
                                @if(isset($quries) && count($quries) > 0)
                                 @foreach($quries as $val)
                                    <textarea class="form-control mb-3" rows="5">{{$val->remarks ?? ''}}</textarea>
                                 @endforeach
                                @else
                                 <textarea class="form-control" rows="5">No Update comments</textarea>
                                @endif

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

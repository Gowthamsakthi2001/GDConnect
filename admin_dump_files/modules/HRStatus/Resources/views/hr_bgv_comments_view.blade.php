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
                        <div class="col-12 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="mobile_no">{{'Comments'}}</label>
                                @if(isset($comments) && count($comments) > 0)
                                 @foreach($comments as $val)
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

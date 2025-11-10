<x-app-layout>
    <x-card>
        <x-auth::setting active_tab="{{ $active_tab }}">
            <h3>{{ localize(config('theme.title')) }}</h3>
            <hr>
            <form action="{{ route('user-profile-information.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group ">
                            <label for="name" class="col-form-label">{{ localize('Name') }}</label>
                            <input type="text" class="form-control input-py" id="name" name="name"
                                placeholder="name" value="{{ auth()->user()->name }}" required>
                            @error('name')
                                <span class="error" role="alert">
                                    <strong>{{ localize($message) }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group ">
                            <label for="email" class="col-form-label">{{ localize('Email') }}</label>
                            <input type="email" class="form-control input-py" id="email" name="email"
                                placeholder="Email" value="{{ auth()->user()->email }}" required>
                            @error('name')
                                <span class="error" role="alert">
                                    <strong>{{ localize($message) }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <?php
    
                        $userData1 = \App\Models\User::find(auth()->id());
                        if ($userData1 && !empty($userData1->profile_photo_path)) {
                            $Pimg = str_starts_with($userData1->profile_photo_path, 'http') 
                                ? $userData1->profile_photo_path 
                                : asset('/uploads/users/' . $userData1->profile_photo_path);
                        } else {
                            $Pimg = asset('storage/setting/byQpJL3dVU32cdP6xIpHNL2MTi9AtXu0UfPdJTuG.png');
                        }
                    ?>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="avatar" class="col-form-label">{{ localize('Avatar') }}</label>
                            <input type="file" class="form-control input-py" name="avatar" id="avatar"
                                onchange="get_img_url(this, '#avatar_image');" placeholder="select avatar image">
                            <img id="avatar_image" src="{{ $Pimg }}" width="120px"
                                class="mt-1">
                            @error('avatar')
                                <p class="text-danger pt-2">{{ localize($message) }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12 pt-5">
                        <button class="btn btn-success">{{ localize('Update') }}</button>
                    </div>
                </div>
            </form>
        </x-auth::setting>
    </x-card>

</x-app-layout>

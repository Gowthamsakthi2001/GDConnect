<form id="{{ $prefix }}_AppVersionUpdateForm" action="javascript:void(0);" method="POST">
    @csrf
    <div class="row">
        <!-- App Live Version -->
        <div class="col-md-6 col-12">
            <div class="mb-3">
                <label class="col-form-label text-capitalize" for="{{ $prefix }}_app_live_version">
                    App Live Version <span class="text-danger">*</span>
                </label>
                <input class="form-control input-py" type="text"
                       name="{{ $prefix }}_app_live_version"
                       id="{{ $prefix }}_app_live_version"
                       placeholder="EX : 1.4.6+42"
                       value="{{ old($prefix.'_app_live_version', $live_version) }}" required>
            </div>
        </div>

        <!-- App Test Version -->
        <div class="col-md-6 col-12">
            <div class="mb-3">
                <label class="col-form-label text-capitalize" for="{{ $prefix }}_app_test_version">
                    App Test Version <span class="text-danger">*</span>
                </label>
                <input class="form-control input-py" type="text"
                       name="{{ $prefix }}_app_test_version"
                       id="{{ $prefix }}_app_test_version"
                       placeholder="EX : 1.4.5+41+UAT"
                       value="{{ old($prefix.'_app_test_version', $test_version) }}" required>
            </div>
        </div>

        <!-- Live Download URL -->
        <div class="col-12">
            <div class="mb-3">
                <label class="col-form-label text-capitalize" for="{{ $prefix }}_live_latest_apk_url">
                    Live Download URL <span class="text-danger">*</span>
                </label>
                <textarea class="form-control" name="{{ $prefix }}_live_latest_apk_url" id="{{ $prefix }}_live_latest_apk_url" required>{{ old($prefix.'_live_latest_apk_url', $live_url) }}</textarea>
            </div>
        </div>

        <!-- Test Download URL -->
        <div class="col-12">
            <div class="mb-3">
                <label class="col-form-label text-capitalize" for="{{ $prefix }}_test_latest_apk_url">
                    Test Download URL <span class="text-danger">*</span>
                </label>
                <textarea class="form-control" name="{{ $prefix }}_test_latest_apk_url" id="{{ $prefix }}_test_latest_apk_url" required>{{ old($prefix.'_test_latest_apk_url', $test_url) }}</textarea>
            </div>
        </div>

        <div class="col-12 pt-2 text-end">
            <button type="button" class="btn btn-success input-py"
                    onclick="Auth_Confirm_Update('{{ $prefix }}_AppVersionUpdateForm', '{{ $update_route }}')">
                Save Changes
            </button>
        </div>
    </div>
</form>

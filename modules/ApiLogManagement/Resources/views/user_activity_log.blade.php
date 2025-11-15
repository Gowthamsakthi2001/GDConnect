<x-app-layout>
<style>
    :root {
        --primary-color: #4F46E5;
        --secondary-color: #4338CA;
        --accent-color: #6366F1;
        --light-color: #f9fafb;
        --dark-color: #111827;
        --success-color: #22c55e;
        --warning-color: #f59e0b;
        --info-color: #0ea5e9;
        --danger-color: #ef4444;
    }

    .main-content { padding: 20px; background: var(--light-color); border-radius: 12px; }
    .card { border-radius: 16px; overflow: hidden; }
    .card-header { border-bottom: 1px solid #f1f1f1; }

    /* Timeline container */
    .activity-log-container { position: relative; padding: 30px 0; margin-left: 50px; }
    .activity-log-container::before {
        content: '';
        position: absolute;
        top: 0; bottom: 0; left: 0;
        width: 3px;
        background: linear-gradient(to bottom, var(--primary-color), var(--accent-color));
        border-radius: 6px;
    }

    /* Timeline item */
    .activity-item {
        position: relative;
        margin-bottom: 35px;
        padding-left: 40px;
        display: flex;
        align-items: flex-start;
        gap: 15px;
    }

    .activity-item::before {
        content: '';
        position: absolute;
        width: 16px; height: 16px;
        border-radius: 50%;
        top: 10px; left: -7px;
        border: 3px solid #fff;
        background: var(--primary-color);
        box-shadow: 0 0 0 4px var(--primary-color);
        z-index: 2;
    }

    /* Icon circle */
    .activity-icon {
        width: 42px; height: 42px;
        border-radius: 50%;
        background: var(--accent-color);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 16px;
        flex-shrink: 0;
        box-shadow: 0 3px 6px rgba(0,0,0,0.1);
    }
    /*.opacity-0 { opacity: 0; transform: translateY(10px); }*/
    /*.opacity-100 { opacity: 1; transform: translateY(0); }*/
    /*.transition-all { transition: all 0.5s ease; }*/

    /* Content box */
    .activity-content {
        background: #fff;
        border-radius: 12px;
        padding: 15px 20px;
        flex: 1;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }

    .activity-content:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.15);
    }

    .activity-header-line {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
    }

    .activity-user {
        color: var(--dark-color);
        font-weight: 600;
        font-size: 0.9rem;
    }

    .activity-time {
        font-size: 0.85rem;
        color: #6b7280;
    }

    .activity-description {
        color: var(--dark-color);
        font-weight: 500;
        font-size: 0.9rem;
    }

    /* Load more */
    .load-more-container { text-align: center; margin-top: 25px; }
    .load-more-btn {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 10px 26px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .load-more-btn:hover {
        background: var(--secondary-color);
        transform: translateY(-3px);
        box-shadow: 0 5px 12px rgba(0,0,0,0.15);
    }

    @media (max-width: 768px) {
        .activity-log-container { margin-left: 30px; }
        .activity-item { flex-direction: column; align-items: flex-start; }
        .activity-icon { margin-bottom: 10px; }
    }
</style>

<div class="main-content my-0 py-0" style="padding:0px !important;">
    <div class="card border-0 shadow-sm bg-white my-4 rounded-4">
        <div class="card-header bg-transparent py-3">
            <div class="row g-3 align-items-center justify-content-between activity-header">
                <div class="col-md-8 d-flex align-items-center">
                    <a href="#" class="btn btn-outline-primary btn-sm rounded-circle me-3 shadow-sm">
                        <i class="bi bi-clock-history fs-6"></i>
                    </a>
                    <div>
                        <h5 class="mb-1">User Activity Logs <span id="Total_Log_count" class="rounded badge bg-info text-white">0</span></h5>
                        <p class="mb-0 text-muted">Track and monitor all user activities in real-time</p>
                    </div>
                </div>
                <div class="col-md-4 d-flex justify-content-end gap-2">
                    <a href="#" class="btn btn-outline-primary btn-sm shadow-sm px-3" onclick="AMVDashRightSideFilerOpen()">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm bg-white my-4 rounded-4">
        <div class="card-body">
            <div class="activity-log-container" id="activityLogs"></div>
            <div class="load-more-container">
                <button class="load-more-btn" id="loadMore"><i class="fas fa-sync-alt me-2"></i>Load More</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="activityModal" tabindex="-1" aria-labelledby="activityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="activityModalLabel">Activity Log in Detail</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="modalDescription" class="mb-4"></p>
                <div class="d-flex justify-content-between my-3">
                    <p class="text-muted mb-1"><i class="bi bi-person-circle me-1"></i><span id="modalUser"></span></p>
                    <p class="text-muted mb-0"><i class="bi bi-clock-history me-1"></i><span id="modalTime"></span></p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="offcanvas offcanvas-end" tabindex="-1" id="UserActivityLogRightAMV" aria-labelledby="UserActivityLogRightAMVLabel">
            <div class="offcanvas-header">
                <h5 class="custom-dark mb-0" id="UserActivityLogRightAMVLabel">Activity Log Filter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
        
            <div class="offcanvas-body">
                <!-- Top Buttons -->
                <div class="d-flex gap-2 mb-3">
                    <button class="btn btn-outline-secondary w-50" onclick="clearUserLogFilter()">Clear All</button>
                    <button class="btn btn-primary w-50" onclick="applyUserLogFilter()">Apply</button>
                </div>
        
                <!-- Timeline Card -->
                <div class="card mb-3">
                    <div class="card-header p-2">
                        <h6 class="custom-dark mb-0">Select Time Line</h6>
                    </div>
                    <div class="card-body">
                        <!--<div class="mb-3">-->
                        <!--    <label class="form-label" for="filter-date-range">Date Range</label>-->
                        <!--    <select class="form-control custom-select2-field" id="filter-date-range">-->
                        <!--        <option value="today">Today</option>-->
                        <!--        <option value="yesterday">Yesterday</option>-->
                        <!--        <option value="last7">Last 7 Days</option>-->
                        <!--        <option value="last30">Last 30 Days</option>-->
                        <!--        <option value="custom">Custom</option>-->
                        <!--    </select>-->
                        <!--</div>-->
        
                        <div class="mb-3" id="custom-date-range">
                            <div class="mb-3">
                                <label class="form-label" for="from-date">From Date</label>
                                <input type="date" class="form-control" id="from-date">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="to-date">To Date</label>
                                <input type="date" class="form-control" id="to-date">
                            </div>
                        </div>
                    </div>
                </div>
        
                <!-- Options Card -->
                <div class="card mb-3">
                    <div class="card-header p-2">
                        <h6 class="custom-dark mb-0">Select Options</h6>
                    </div>
                    <div class="card-body">
        
                        <!-- Vehicle Type -->
                        <div class="mb-3">
                            <label class="form-label" for="get_role">Roles</label>
                            <select class="form-control custom-select2-field" id="getRole" name="get_role">
                                <option value="">All</option>
                                @if(isset($roles))
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name ?? '' }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        
                      <!-- Accountability Type -->
                        <div class="mb-3">
                            <label class="form-label" for="getUser">Users</label>
                            <select class="form-control custom-select2-field" id="getUser" name="get_user">
                                <option value="">All</option>
                                @if(isset($users))
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
        
                <!-- Bottom Buttons -->
                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-outline-secondary w-50" onclick="clearUserLogFilter()">Clear All</button>
                    <button class="btn btn-primary w-50" onclick="applyUserLogFilter()">Apply</button>
                </div>
            </div>
        </div>

@section('script_js')
<script>
AOS.init({ duration: 800, once: true, offset: 100 });

function AMVDashRightSideFilerOpen(){
    const bsOffcanvas = new bootstrap.Offcanvas('#UserActivityLogRightAMV');
            $('.custom-select2-field').select2({
        dropdownParent: $('#UserActivityLogRightAMV') 
    });
    bsOffcanvas.show();
}
window.applyUserLogFilter = async function () {
    const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('UserActivityLogRightAMV'));

    const filters = getUserLogFilters(); // 👈 get filters

    window.currentPage = 1;
    window.activeFilters = filters;

    await window.loadActivities(false, filters);

    bsOffcanvas.hide();
};

function getUserLogFilters() {
    // Collect filter values
    const filters = {
        from_date: $('#from-date').val() || '',
        to_date: $('#to-date').val() || '',
        role_id: $('#getRole').val() || '',
        user_id: $('#getUser').val() || ''
    };

    return filters;
}

window.clearUserLogFilter = function () {
    const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('UserActivityLogRightAMV'));
    $('#filter-date-range').val('today').trigger('change');
    $('#from-date, #to-date').val('');
    $('#getRole, #getUser').val('').trigger('change');
    $('.custom-date').addClass('d-none');
    window.currentPage = 1;
    window.loadActivities(false);

    bsOffcanvas.hide();
};
function getInitials(name) {
    if (!name) return '?';
    return name
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase();
}

function timeAgo(dateStr) {
    const now = new Date();
    const past = new Date(dateStr);
    const diff = Math.floor((now - past) / 1000);
    const intervals = { year: 31536000, month: 2592000, day: 86400, hour: 3600, minute: 60 };
    for (let key in intervals) {
        const val = Math.floor(diff / intervals[key]);
        if (val >= 1) return `${val} ${key}${val > 1 ? 's' : ''} ago`;
    }
    return 'Just now';
}

function formatDate(dateStr) {
    const d = new Date(dateStr);
    return d.toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

async function fetchActivityLogs(page = 1, limit = 30) {
    const url = `/admin/Green-Drive-Ev/api-log/get-user-activity?page=${page}&limit=${limit}`;
    try {
        const resp = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });

        if (!resp.ok) {
            console.error(`server responded with ${resp.status}`);
            return { logs: [], total: 0 };
        }

        const result = await resp.json();

        if (result.success && Array.isArray(result.data)) {
            const total = result.total_log_count ?? result.data.length;
            $("#Total_Log_count").text(total);
            return { logs: result.data, total };
        }

        return { logs: [], total: 0 };
    } catch (err) {
        return { logs: [], total: 0 };
    }
}

// $(document).ready(async function () {
//     $("#Total_Log_count").text('Loading...');
//     const container = document.getElementById('activityLogs');
//     let currentPage = 1;
//     const limit = 30;
//     let activityData = [];

//     async function loadActivities(append = false) {
//         $('#loadingOverlay').fadeIn(150);
//         $('#loadMore')
//             .prop('disabled', true)
//             .html('<i class="fas fa-spinner fa-spin me-2"></i>Loading...');
//         const { logs, total } = await fetchActivityLogs(currentPage, limit);

//         $('#loadingOverlay').fadeOut(200);

//         if (logs.length > 0) {
//             if (!append) activityData = logs;
//             else activityData = [...activityData, ...logs];

//             renderActivityLogs(logs, container, append);

//             $('#loadMore')
//                 .prop('disabled', false)
//                 .html('<i class="fas fa-sync-alt me-2"></i>Load More');
//         } else {
//             $('#loadMore')
//                 .prop('disabled', true)
//                 .html('<i class="fas fa-ban me-2"></i>No More Activities');
//         }
//     }

//     await loadActivities();

//     $('#loadMore').on('click', async function () {
//         currentPage++;
//         await loadActivities(true);
//     });

//     $(document).on('click', '.read-more', function () {
//         const id = $(this).data('id');
//         const activity = activityData.find(a => a.id === id);
//         console.log(activity);
//         if (activity) {
//             var userName = activity.user_name +' ( '+activity.role+' )';
//             $('#modalDescription').text(activity.long_description || activity.short_description || 'No description available');
//             $('#modalUser').text(userName || 'Unknown');
//             $('#modalTime').text(`${activity.created_at} (${timeAgo(activity.created_at)})`);
//             $('#activityModal').modal('show');
//         }
//     });
// });
window.clearUserLogFilter = function () {
    const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('UserActivityLogRightAMV'));
    $('#filter-date-range').val('today').trigger('change');
    $('#from-date, #to-date').val('');
    $('#getRole, #getUser').val('').trigger('change');
    $('.custom-date').addClass('d-none');

    // Reset pagination
    window.currentPage = 1;

    // Call global function
    window.loadActivities(false);

    bsOffcanvas.hide();
};

$(document).ready(async function () {
    $("#Total_Log_count").text('Loading...');
    const container = document.getElementById('activityLogs');
    window.currentPage = 1; // <-- make global
    const limit = 30;
    window.activityData = []; // <-- make global

    // ✅ Expose to window
    window.loadActivities = async function (append = false) {
        $('#loadingOverlay').fadeIn(150);
        $('#loadMore')
            .prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin me-2"></i>Loading...');

        const { logs, total } = await fetchActivityLogs(window.currentPage, limit);

        $('#loadingOverlay').fadeOut(200);

        if (logs.length > 0) {
            if (!append) window.activityData = logs;
            else window.activityData = [...window.activityData, ...logs];

            renderActivityLogs(logs, container, append);

            $('#loadMore')
                .prop('disabled', false)
                .html('<i class="fas fa-sync-alt me-2"></i>Load More');
        } else {
            $('#loadMore')
                .prop('disabled', true)
                .html('<i class="fas fa-ban me-2"></i>No More Activities');
        }

        // update total count
        $("#Total_Log_count").text(total);
    };

    // 🟢 Initial load
    await window.loadActivities(false);

    // Load more (pagination)
    $('#loadMore').on('click', async function () {
        window.currentPage++;
        await window.loadActivities(true);
    });

    // Read more modal
    $(document).on('click', '.read-more', function () {
        const id = $(this).data('id');
        const activity = window.activityData.find(a => a.id === id);
        if (activity) {
            var userName = `${activity.user_name} (${activity.role})`;
            $('#modalDescription').text(activity.long_description || activity.short_description || 'No description available');
            $('#modalUser').text(userName || 'Unknown');
            $('#modalTime').text(`${activity.created_at} (${timeAgo(activity.created_at)})`);
            $('#activityModal').modal('show');
        }
    });
});


function renderActivityLogs(activities, container, append = false) {
    if (!append) container.innerHTML = '';
    console.log('Rendering logs:', activities.length);

    activities.forEach((activity, index) => {
        const desc = activity.short_description || '';
        const user = activity.user_name || activity.role || 'Unknown';
        const time = activity.created_at || '';

        const shortText = `
            ${desc.length > 55 ? desc.slice(0, 55) + '...' : desc}
            <a href="javascript:void(0)" class="read-more text-primary ms-1" data-id="${activity.id}">Read more</a>
        `;

        const item = document.createElement('div');
        item.className = 'activity-item opacity-0 translate-y-3 transition-all duration-700';
        item.innerHTML = `
            <div class="activity-icon bg-primary text-white fw-bold">${getInitials(user)}</div>
            <div class="activity-content">
                <div class="activity-header-line d-flex justify-content-between">
                    <span class="activity-user">${user}</span>
                    <span class="activity-time text-muted">${timeAgo(time)}</span>
                </div>
                <div class="activity-description">${shortText}</div>
                <div class="activity-module text-secondary small mt-1">${activity.module_name || ''} • ${activity.page_name || ''}</div>
            </div>`;

        container.appendChild(item);

        // Animate one by one
        setTimeout(() => {
            item.classList.remove('opacity-0', 'translate-y-3');
            item.classList.add('opacity-100', 'translate-y-0');
        }, index * 100);
    });

    AOS.refresh();
}




</script>
@endsection
</x-app-layout>

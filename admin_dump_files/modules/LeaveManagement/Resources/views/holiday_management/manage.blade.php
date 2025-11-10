<x-app-layout>
    @push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .calendar-container {
            display: flex;
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .calendar-wrapper {
            flex: 1;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
        }
        
        .form-wrapper {
            flex: 1;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
        }
        
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .calendar-title {
            font-size: 20px;
            font-weight: 600;
        }
        
        .calendar-nav {
            display: flex;
            gap: 10px;
        }
        
        .calendar-nav button {
            padding: 5px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #f8f9fa;
            cursor: pointer;
        }
        
        .calendar-grid {
            width: 100%;
            border-collapse: collapse;
        }
        
        .calendar-grid th {
            padding: 10px;
            text-align: center;
            font-size: 14px;
            color: #666;
            border-bottom: 1px solid #eee;
        }
        
        .calendar-grid td {
            padding: 10px;
            text-align: center;
            border: 1px solid #f0f0f0;
            cursor: pointer;
            height: 40px;
            position: relative;
        }
        
        .calendar-grid td:hover {
            background: #f5f5f5;
        }
        
        .calendar-grid td.selected {
            background: #0d6efd;
            color: white;
        }
        
        .calendar-grid td.other-month {
            color: #ccc;
        }
        
        .calendar-grid td.has-event {
            position: relative;
        }
        
        .calendar-grid td.has-event::after {
            content: '';
            position: absolute;
            bottom: 5px;
            left: 50%;
            transform: translateX(-50%);
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #0d6efd;
        }
        
        .calendar-grid td.national {
            color: #dc3545;
            font-weight: bold;
        }
        
        .calendar-grid td.company {
            color: #0d6efd;
            font-weight: bold;
        }
        
        .calendar-grid td.regional {
            color: #ffc107;
            font-weight: bold;
        }
        
        .calendar-grid td.national.selected,
        .calendar-grid td.company.selected,
        .calendar-grid td.regional.selected {
            color: white !important;
        }
        
        .event-details {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .delete-event {
            position: absolute;
            top: 2px;
            right: 2px;
            font-size: 10px;
            color: #dc3545;
            cursor: pointer;
            display: none;
        }
        
        .calendar-grid td.has-event:hover .delete-event {
            display: block;
        }
        
        .form-disabled {
            opacity: 0.6;
            pointer-events: none;
        }
        
        .form-enabled {
            opacity: 1;
            pointer-events: all;
        }
        
        .holiday-tooltip {
            position: absolute;
            background:white ;
            color: #333;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 13px;
            z-index: 1000;
            pointer-events: none;
            display: none;
            max-width: 250px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        
        .holiday-tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #333 transparent transparent transparent;
        }
    </style>
    @endpush

    <div class="main-content">
        <div class="page-header">
            <h2 class="page-header-title">            
                <img src="{{ asset('admin-assets/icons/custom/leave-icon-vector.jpg') }}" class="img-fluid rounded">
                <span class="ps-2">Holiday Calendar</span>
            </h2>
        </div>

        <x-card>
            <div class="calendar-container">
                <div class="calendar-wrapper">
                    <div class="calendar-header">
                        <div class="calendar-nav">
                            <button id="prevYear">&lt;&lt;</button>
                            <button id="prevMonth">&lt;</button>
                        </div>
                        <h3 class="calendar-title" id="monthYearDisplay">{{ \Carbon\Carbon::now()->format('F Y') }}</h3>
                        <div class="calendar-nav">
                            <button id="nextMonth">&gt;</button>
                            <button id="nextYear">&gt;&gt;</button>
                        </div>
                    </div>
                    
                    <table class="calendar-grid">
                        <thead>
                            <tr>
                                <th>Sun</th>
                                <th>Mon</th>
                                <th>Tue</th>
                                <th>Wed</th>
                                <th>Thu</th>
                                <th>Fri</th>
                                <th>Sat</th>
                            </tr>
                        </thead>
                        <tbody id="calendarBody">
                            <!-- Calendar days will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
                <div id="holidayTooltip" class="holiday-tooltip"></div>
                <div class="form-wrapper">
                    <div id="eventDetails" class="event-details" style="display: none;">
                        <!--<h5 id="eventTitle"></h5>-->
                        <!--<p><strong>Type:</strong> <span id="eventType"></span></p>-->
                        <!--<p id="eventDescription"></p>-->
                        <button id="editBtn" class="btn btn-sm btn-primary">Edit</button>
                        <button id="deleteBtn" class="btn btn-sm btn-danger">Delete</button>
                    </div>
                    <div id="holidayForm" class="form-disabled">
                        <form method="POST" action="{{ route('admin.Green-Drive-Ev.leavemanagement.holidays.save') }}">
                            @csrf
                            <input type="hidden" name="id" id="holidayId" value="">
                            <input type="hidden" name="date" id="formDate" value="">
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">Title *</label>
                                <input type="text" class="form-control" id="title" name="title" required disabled>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" disabled></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="type" class="form-label">Type *</label>
                                <select class="form-select" id="type" name="type" required disabled>
                                    <option value="national">National</option>
                                    <option value="regional">Regional</option>
                                    <option value="company" selected>Company</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="is_recurring" class="form-label">Recurring</label>
                                        <select class="form-select" id="is_recurring" name="is_recurring" required disabled>
                                            <option value="1">Yes</option>
                                            <option value="0" selected>No</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="is_active" class="form-label">Active</label>
                                        <select class="form-select" id="is_active" name="is_active" required disabled>
                                            <option value="1" selected>Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <button type="button" id="cancelBtn" class="btn btn-outline-secondary me-2" disabled>Cancel</button>
                                <button type="submit" class="btn btn-success" disabled>Save</button>
                            </div>
                        </form>
                    </div>
                    
                </div>
            </div>
            
        </x-card>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    $(document).ready(function() {
        let currentDate = new Date();
        let selectedDate = null;
        let existingHoliday = null;
        const holidays = @json($existingHolidays ?? []);
        
        function renderCalendar(year, month) {
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const daysInMonth = lastDay.getDate();
            const startingDay = firstDay.getDay();
            
            $('#monthYearDisplay').text(firstDay.toLocaleString('default', { month: 'long', year: 'numeric' }));
            
            let calendarHtml = '';
            let day = 1;
            let nextMonthDay = 1;
            
            for (let i = 0; i < 6; i++) {
                calendarHtml += '<tr>';
                
                for (let j = 0; j < 7; j++) {
                    if ((i === 0 && j < startingDay) || day > daysInMonth) {
                        // Days from previous/next month
                        if (i === 0 && j < startingDay) {
                            // Previous month days
                            const prevMonthDay = new Date(year, month, 0).getDate() - (startingDay - j - 1);
                            const date = new Date(year, month - 1, prevMonthDay);
                            const dateString = date.toISOString().split('T')[0];
                            calendarHtml += `<td class="other-month" data-date="${dateString}">${prevMonthDay}</td>`;
                        } else {
                            // Next month days
                            const date = new Date(year, month + 1, nextMonthDay);
                            const dateString = date.toISOString().split('T')[0];
                            calendarHtml += `<td class="other-month" data-date="${dateString}">${nextMonthDay}</td>`;
                            nextMonthDay++;
                        }
                    } else {
                        // Current month days
                        const date = new Date(year, month, day);
                        const dateString = date.toISOString().split('T')[0];
                        const holiday = holidays.find(h => h.date === dateString);
                        let classes = [];
                        
                        if (holiday) {
                            classes.push(holiday.type);
                            classes.push('has-event');
                        }
                        
                        if (date.toDateString() === new Date().toDateString()) {
                            classes.push('today');
                        }
                        
                        if (selectedDate === dateString) {
                            classes.push('selected');
                        }
                        
                        calendarHtml += `<td class="${classes.join(' ')}" 
                                          data-date="${dateString}"
                                          ${holiday ? `data-id="${holiday.id}"
                                          data-title="${holiday.title}"
                                          data-type="${holiday.type}"
                                          data-description="${holiday.description || ''}"
                                          data-is-recurring="${holiday.is_recurring}"
                                          data-is-active="${holiday.is_active}"` : ''}>
                                          ${day}
                                          ${holiday ? `<span class="delete-event" data-id="${holiday.id}">×</span>` : ''}
                                       </td>`;
                        day++;
                    }
                }
                
                calendarHtml += '</tr>';
                
                if (day > daysInMonth && nextMonthDay > 7) break;
            }
            
            $('#calendarBody').html(calendarHtml);
            
            // Rebind click events for all dates (including other months)
            $('.calendar-grid td').click(function() {
                handleDateSelection($(this));
            });
            
            // Bind delete event handlers
            $('.delete-event').click(function(e) {
                e.stopPropagation();
                const holidayId = $(this).data('id');
                console.log(holidayId);
                deleteHoliday(holidayId);
            });
        }
        
        function handleDateSelection($cell) {
            // Remove previous selection
            $('.calendar-grid td').removeClass('selected');
            // Add selection to clicked date
            $cell.addClass('selected');
            
            selectedDate = $cell.data('date');
            $('#formDate').val(selectedDate);
            
            // Check if date has existing holiday
            existingHoliday = $cell.data('title') ? {
                id: $cell.data('id'),
                title: $cell.data('title'),
                type: $cell.data('type'),
                description: $cell.data('description'),
                is_recurring: $cell.data('is-recurring'),
                is_active: $cell.data('is-active')
            } : null;
            
            if (existingHoliday) {
                // Show event details
                $('#holidayForm').addClass('form-disabled');
                $('#eventDetails').show();
                // $('#eventTitle').text(existingHoliday.title);
                // $('#eventType').text(existingHoliday.type.charAt(0).toUpperCase() + existingHoliday.type.slice(1));
                // $('#eventDescription').text(existingHoliday.description || 'No description');
            } else {
                // Show empty form and enable it
                $('#eventDetails').hide();
                $('#holidayForm').removeClass('form-disabled');
                $('#title').val('').prop('disabled', false);
                $('#description').val('').prop('disabled', false);
                $('#type').val('company').prop('disabled', false);
                $('#is_recurring').val('0').prop('disabled', false);
                $('#is_active').val('1').prop('disabled', false);
                $('#holidayId').val('');
                $('#cancelBtn').prop('disabled', false);
                $('form button[type="submit"]').prop('disabled', false);
            }
        }
        
        function deleteHoliday(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.Green-Drive-Ev.leavemanagement.holidays.destroy') }}",
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Deleted!',
                                    'The holiday has been deleted.',
                                    'success'
                                ).then(() => {
                                    window.location.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'There was an error deleting the holiday.',
                                'error'
                            );
                        }
                    });
                }
            });
        }
        
        // Add these variables at the top of your script
            // const tooltip = $('#holidayTooltip');
            
            // Add this inside your $(document).ready() function
            // $('body').on('mouseenter', '.calendar-grid td.has-event', function() {
            //     const title = $(this).data('title');
            //     const description = $(this).data('description');
                
            //     if (title) {
            //         tooltip.html(`<strong>${title}</strong><br>${description || ''}`);
            //         tooltip.css({
            //             'display': 'block',
            //             'left': $(this).offset().left + $(this).width() / 2 - tooltip.width() / 2,
            //             'top': $(this).offset().top - tooltip.height() - 10
            //         });
            //     }
            // }).on('mouseleave', '.calendar-grid td.has-event', function() {
            //     tooltip.hide();
            // });

        // Navigation handlers
        $('#prevMonth').click(function() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar(currentDate.getFullYear(), currentDate.getMonth());
        });
        
        $('#nextMonth').click(function() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar(currentDate.getFullYear(), currentDate.getMonth());
        });
        
        $('#prevYear').click(function() {
            currentDate.setFullYear(currentDate.getFullYear() - 1);
            renderCalendar(currentDate.getFullYear(), currentDate.getMonth());
        });
        
        $('#nextYear').click(function() {
            currentDate.setFullYear(currentDate.getFullYear() + 1);
            renderCalendar(currentDate.getFullYear(), currentDate.getMonth());
        });
        
        // Edit button handler
        $('#editBtn').click(function() {
            $('#eventDetails').hide();
            $('#holidayForm').removeClass('form-disabled');
            $('#title').val(existingHoliday.title).prop('disabled', false);
            $('#description').val(existingHoliday.description).prop('disabled', false);
            $('#type').val(existingHoliday.type).prop('disabled', false);
            $('#is_recurring').val(existingHoliday.is_recurring ? '1' : '0').prop('disabled', false);
            $('#is_active').val(existingHoliday.is_active ? '1' : '0').prop('disabled', false);
            $('#holidayId').val(existingHoliday.id);
            $('#cancelBtn').prop('disabled', false);
            $('form button[type="submit"]').prop('disabled', false);
        });
        
        // Delete button handler
        $('#deleteBtn').click(function(e) {
            e.stopPropagation();
            deleteHoliday(existingHoliday.id);
        });
        
        // Cancel button handler
        $('#cancelBtn').click(function() {
            $('#holidayForm').addClass('form-disabled');
            $('.calendar-grid td').removeClass('selected');
            $('#title').val('').prop('disabled', true);
            $('#description').val('').prop('disabled', true);
            $('#type').val('company').prop('disabled', true);
            $('#is_recurring').val('0').prop('disabled', true);
            $('#is_active').val('1').prop('disabled', true);
            $('#holidayId').val('');
            $('#cancelBtn').prop('disabled', true);
            $('form button[type="submit"]').prop('disabled', true);
            selectedDate = null;
        });
        
        // Form submission
        $('form').submit(function(e) {
            e.preventDefault();
            
            if (!selectedDate) {
                Swal.fire('Error!', 'Please select a date', 'error');
                return;
            }
            
            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.html();
            
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
            
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            'Success!',
                            response.message || 'Holiday saved successfully',
                            'success'
                        ).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error!', response.message || 'An error occurred', 'error');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire('Error!', errorMessage, 'error');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
        
        // Initial render
        renderCalendar(currentDate.getFullYear(), currentDate.getMonth());
    });
    </script>
   
</x-app-layout>
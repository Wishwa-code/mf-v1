@extends('layout.admin')

@section('head')
    <!-- FullCalendar CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.2/main.min.css" />

    <style>
        .style-tr>td {
            padding: 2px 15px;
        }
        #calendar {
            max-width: 100%;
            margin: 0 auto;
            padding: 0 10px;
        }
        .fc-toolbar {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }
        .fc-daygrid-day-number {
            color: #007bff;
        }
        .fc-event {
            background-color: #28a745;
            border: none;
            color: #fff;
            padding: 5px;
            border-radius: 5px;
        }
        .fc-day-today {
            background-color: #e9ecef;
        }
    </style>
@endsection

@section('content')
    <div>
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Modal -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">Event Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="eventForm">
                        <div class="mb-3">
                            <label for="eventTitle" class="form-label">Event Title</label>
                            <input type="text" class="form-control" id="eventTitle" required>
                        </div>
                        <div class="mb-3">
                            <label for="eventStart" class="form-label">Start Date</label>
                            <input type="datetime-local" class="form-control" id="eventStart" required>
                        </div>
                        <div class="mb-3">
                            <label for="eventEnd" class="form-label">End Date</label>
                            <input type="datetime-local" class="form-control" id="eventEnd" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.2/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                initialDate: '{{ date('Y-m-d') }}', // Set the default date to today
                editable: true,
                eventLimit: true, // Allow "more" link when too many events
                events: [
                    {
                        title: 'Event 1',
                        start: '{{ date('Y-m-d') }}T10:30:00',
                        end: '{{ date('Y-m-d') }}T12:30:00'
                    },
                    {
                        title: 'Event 2',
                        start: '{{ date('Y-m-d', strtotime('+1 day')) }}T14:30:00',
                        end: '{{ date('Y-m-d', strtotime('+1 day')) }}T16:30:00'
                    }
                ],
                dateClick: function(info) {
                    // Open modal to add event
                    $('#eventModal').modal('show');
                    $('#eventStart').val(info.dateStr + 'T00:00');
                    $('#eventEnd').val(info.dateStr + 'T23:59');
                },
                eventClick: function(info) {
                    // Display event details in modal
                    var event = info.event;
                    $('#eventModal').modal('show');
                    $('#eventTitle').val(event.title);
                    $('#eventStart').val(event.start.toISOString().slice(0, 16));
                    $('#eventEnd').val(event.end ? event.end.toISOString().slice(0, 16) : '');
                }
            });
            calendar.render();

            // Handle form submission to add or update event
            $('#eventForm').on('submit', function(e) {
                e.preventDefault();
                var title = $('#eventTitle').val();
                var start = $('#eventStart').val();
                var end = $('#eventEnd').val();

                if (title && start) {
                    var newEvent = {
                        title: title,
                        start: start,
                        end: end
                    };

                    calendar.addEvent(newEvent);
                    $('#eventModal').modal('hide');
                }
            });
        });
    </script>
@endsection

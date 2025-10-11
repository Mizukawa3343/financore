<?php
include_once "../../includes/header.php";
?>
<div class="section-header">
    <h1 class="title">Calendar</h1>

</div>

<div id='calendar'></div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            // Configuration
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            height: '100%',
            editable: true,
            selectable: true,
            initialDate: '2025-10-01',

            // --- Event Source ---
            events: {
                url: '/financore/src/handler/events_feed.php',
                method: 'GET',
                failure: function () {
                    alert('There was an error while fetching events!');
                }
            },

            // --- Event Handlers ---
            eventClick: function (info) {
                var type = info.event.extendedProps.type;
                var dbId = info.event.extendedProps.db_id;
                var title = info.event.title;

                alert('Event Clicked! Type: ' + type + ', ID: ' + dbId + ', Title: ' + title);
                // TODO: Implement AJAX call to open a modal for editing this event
            },

            select: function (info) {
                var startStr = info.startStr;
                var endStr = info.endStr;

                var title = prompt('Enter a Title for the new event:');

                if (title) {

                    // CRITICAL FIX: Determine the end date to send. 
                    // If the event is one time slot long, the end should be null.
                    // This prevents sending an invalid end date when selecting a single slot in week/day view.
                    var eventEnd = (info.allDay || startStr === endStr) ? null : endStr;

                    $.ajax({
                        url: '/financore/src/handler/add_event_handler.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            title: title,
                            start: startStr,
                            end: eventEnd // Use the cleaned end date variable
                        },
                        success: function (response) {
                            if (response.status === 'success') {
                                alert('Event added successfully!');
                                calendar.refetchEvents();
                            } else {
                                alert('Error saving event: ' + response.message);
                            }
                        },
                        error: function () {
                            alert('Communication error with the server.');
                        }
                    });
                }

                calendar.unselect();
            },

            eventDrop: function (info) {
                var dbId = info.event.extendedProps.db_id;
                var newStart = info.event.startStr;
                var newEnd = info.event.endStr;

                if (!confirm("Are you sure about this change?")) {
                    info.revert();
                } else {
                    // TODO: Implement AJAX call to update the event's start/end dates in the database
                }
            }
        });

        calendar.render();
    });
</script>

<?php
include_once "../../includes/footer.php";
?>
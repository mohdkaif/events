@extends('layouts.master')
@push('plugin-styles')
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.css" />
    <style>
        .alert {
            padding: 20px;
            background-color: #f44336;
            color: white;
            opacity: 1;
            transition: opacity 0.6s;
            margin-bottom: 15px;
        }

        .alert.success {
            background-color: #04AA6D;
        }

        .alert.info {
            background-color: #2196F3;
        }

        .alert.warning {
            background-color: #ff9800;
        }

        .closebtn {
            margin-left: 15px;
            color: white;
            font-weight: bold;
            float: right;
            font-size: 22px;
            line-height: 20px;
            cursor: pointer;
            transition: 0.3s;
        }

        .closebtn:hover {
            color: black;
        }

    </style>
@endpush

@section('content')
    <div class="content-wrapper">
        <div class="container">
            @if (env('DATABASE_TYPE') == 'csv')
                <a href="{{ url('events.csv') }}" download="" class="btn btn-info">Download Event Data CSV</a>
            @else
                <a href="{{ url('event_export') }}"  class="btn btn-info">Download Event Data CSV</a>
            @endif
            <div class="response">
            </div>
            <div id='calendar'></div>
            <div class="modal" id="myModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form id="event_reset">
                            <!-- Modal Header -->
                            <div class="modal-header">
                                <h4 class="modal-title">Events Schedule</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <!-- Modal body -->
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="title">Enter Event Title</label>
                                    <input type="text" class="form-control" placeholder="Enter Event title" name="title"
                                        id="title">
                                </div>
                            </div>
                            <!-- Modal footer -->
                            <div class="modal-footer">
                                <button type="button" onclick="saveEvent()" class="btn btn-info"
                                    data-dismiss="modal">Submit</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('plugin-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"
        integrity="sha256-4iQZ6BVL4qNKlQ27TExEhBN1HFPvAvAMbFavKKosSWQ=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {

            var SITEURL = "{{ url('/') }}";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var calendar = $('#calendar').fullCalendar({
                editable: true,
                events: SITEURL + "/event",
                displayEventTime: true,
                editable: true,
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                eventRender: function(event, element, view) {
                    if (event.allDay === 'true') {
                        event.allDay = true;
                    } else {
                        event.allDay = false;
                    }
                },
                selectable: true,
                selectHelper: true,
                select: function(start, end, allDay) {
                    $('#myModal').modal('show');
                    var start = $.fullCalendar.formatDate(start, "Y-MM-DD HH:mm:ss");
                    var end = $.fullCalendar.formatDate(end, "Y-MM-DD HH:mm:ss");
                    window.sessionStorage.setItem('start', start);
                    window.sessionStorage.setItem('end', end);
                    window.saveEvent = function saveEvent() {
                        title = $('#title').val();

                        if (title) {
                            var start = window.sessionStorage.getItem('start');
                            var end = window.sessionStorage.getItem('end');

                            $.ajax({
                                url: SITEURL + "/event",
                                data: 'title=' + title + '&start=' + start + '&end=' + end,
                                type: "POST",
                                success: function(data) {
                                    displayMessage("Added Successfully");
                                }
                            });
                            calendar.fullCalendar('renderEvent', {
                                    title: title,
                                    start: start,
                                    end: end,
                                    allDay: allDay
                                },
                                true
                            );
                            document.getElementById("event_reset").reset();
                        }
                        calendar.fullCalendar('unselect');
                    }
                },

                eventDrop: function(event, delta) {
                    var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
                    var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");
                    $.ajax({
                        url: SITEURL + '/event/' + event.id,
                        data: 'title=' + event.title + '&start=' + start + '&end=' +
                            end +
                            '&id=' + event.id,
                        type: "PUT",
                        success: function(response) {
                            displayMessage("Updated Successfully");
                        }
                    });
                },
                eventClick: function(event) {
                    var deleteMsg = confirm("Do you really want to delete?");
                    if (deleteMsg) {
                        $.ajax({
                            type: "DELETE",
                            url: SITEURL + '/event/' + event.id,
                            data: "&id=" + event.id,
                            success: function(response) {
                                if (parseInt(response) > 0) {
                                    $('#calendar').fullCalendar('removeEvents',
                                        event.id);
                                    displayMessage("Deleted Successfully");
                                }
                            }
                        });
                    }
                }

            });
        });

        function displayMessage(message) {
            if (message == 'Deleted Successfully') {
                var $html = '<div class="alert danger">';
            } else {
                var $html = '<div class="alert success">';
            }
            $html += '<span class="closebtn">&times;</span>';
            $html += '<strong>Success! </strong>' + message + '</div>';
            $(".response").html("" + $html + "");
            setInterval(function() {
                $(".alert").fadeOut();
            }, 1500);
        }
        var close = document.getElementsByClassName("closebtn");
        var i;

        for (i = 0; i < close.length; i++) {
            close[i].onclick = function() {
                var div = this.parentElement;
                div.style.opacity = "0";
                setTimeout(function() {
                    div.style.display = "none";
                }, 1000);
            }
        }
    </script>
@endpush

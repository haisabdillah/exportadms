@extends('layouts.app')
@section('content')
<div class="container mt-3">
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="row gy-3">
                <div class="col-xs-6 col-md-4">
                    <a class="card bg-primary" href="{{route('attendance.view')}}">
                        <div class="card-body text-white text-center">
                            <i class='fas fa-calendar-check' style='font-size:36px'></i>
                            <div class="text-bold mt-1">Attendance</div>
                        </div>
                    </a>
                </div>
                <div class="col-xs-6 col-md-4">
                    <a class="card bg-primary" href="{{route('attendance.view')}}">
                        <div class="card-body text-white text-center">
                            <i class='fas fa-file-export' style='font-size:36px'></i>
                            <div class="text-bold mt-1"> Export Atendance</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Fingerprint Machine</h5>
                  <table class="table table-sm table-responsive table-striped" id="deviceTable">
                </table>
                </div>
              </div>
        </div>
    </div>

</div>
@endsection
@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css">
@endpush
@push('js')
    <script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script>

        $(function(){
            'use strict';
            function diff_minutes(dt2, dt1)
            {

            var diff =(dt2.getTime() - dt1.getTime()) / 1000;
            diff /= 60;
            return Math.abs(Math.round(diff));

            }
            var deviceTable = $('#deviceTable').DataTable( {
                    destroy: true,
                    responsive: true,
                    ajax: "{{route('home.devices')}}",
                    columns: [
                        {
                            title: "Name",
                            data: "Alias"
                        },
                        {
                            title: "Serial Number",
                            data: "SN"
                        },
                        {
                            title: "Device",
                            data: "DeviceName"
                        },
                        {
                            title: "Status",
                            data: "LastActivity",
                            render: (data, type, full, meta) => {
                                var time = diff_minutes(new Date(), new Date(data))
                                if (time <= 5) {
                                    return "<span class='badge rounded-pill text-bg-success'>Online</span>"
                                }
                                    return "<span class='badge rounded-pill text-bg-danger'>Offline</span>"
                            }
                        },
                        // {
                        //     title: "Last Activity",
                        //     data: "LastActivity",
                        //     render: (data, type, full, meta) => {
                        //         var time = diff_minutes(new Date(), new Date(data))
                        //         return moment.duration(time, "minutes").humanize(true); // a minute ago
                        //     }
                        // },
                    ]
            });

            setInterval(() => {
                console.log('reload')
                $('#deviceTable').DataTable().ajax.reload()
            }, 10000);
        })
    </script>

@endpush


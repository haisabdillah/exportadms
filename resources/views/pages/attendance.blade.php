@extends('layouts.app')
@section('content')
<div class="container mt-3">
            <div class="card">
                <div class="card-body">
                    <form id="form-export">
                        <div class="row">
                            <h5 class="card-title mb-3">Filter Employee</h5>
                            <div class="col-md-3 mb-3">
                                <label for="exampleInputEmail1" class="form-label">Department</label>
                                <select id="select-department-id" class="form-select" aria-label="Default select example" required>
                                <option selected>Select Department</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="start" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="end" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end" required>
                            </div>
                        </div>
                    <div class="mb-3">
                        <label for="employee" class="form-label">Employee</label>
                        <div class="form-check">
                        <input class="form-check-input" type="radio" name="type" value="all" id="all" checked>
                        <label class="form-check-label" for="all">
                            All
                        </label>
                        </div>
                        <div class="form-check">
                        <input class="form-check-input" type="radio" name="type" value="selected_employee" id="selected-employee">
                        <label class="form-check-label" for="selected-employee">
                            Selected Employee
                        </label>
                        </div>
                        <div class="form-selected-employee mt-2" hidden>
                            <select id="select-employee-id" class="form-select" name="employee[]" aria-label="Default select example" multiple>
                        </select>
                        </div>
                    </div>
                        <button class="btn btn-primary"  id="generate"  type="button">
                        Generate
                        </button>
                    </form>
                </div>
            </div>
            <div class="card mt-2">
                <div class="card-body">
                  <h5 class="card-title">Data Attendance</h5>
                  <table class="table table-sm table-responsive table-striped" id="attendanceTable">
                </table>
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
    <script src="https://cdn.datatables.net/buttons/2.3.4/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.4/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.4/js/buttons.print.min.js"></script>
    <script>
        $("#select-department-id").select2({
            theme: "bootstrap-5",
            width: '100%',
            ajax: {
                delay:500,
                url: "{{route('get.department')}}",
                dataType: 'json',

                data: function (params) {
                return {
                    q: $.trim(params.term)
                };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: false
                // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
            }
        });

        $("input[name=type]").change(function(){
            if ($(this).val() == 'selected_employee') {
                $('.form-selected-employee').removeAttr('hidden')
            }
            else
            {
                $('.form-selected-employee').attr('hidden',true)
            }
            console.log()
        });
        $("#select-employee-id").select2({
            theme: "bootstrap-5",
            ajax: {
                width: '100%',
                multiple:true,
                delay:500,
                url: "{{route('get.employee')}}",
                dataType: 'json',
                data: function (params) {
                return {
                    search: $.trim(params.term),
                    department_id: $('#select-department-id').val()
                };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: false
                // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
            }
    })
    $("#select-department-id").change(function(){
        $("#select-employee-id").val(null).trigger('change')
    });
    $('#generate').click(function(e){
        e.preventDefault()
        $(this).html("<span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span> Generating...").attr('disabled',true)
        let employee = null;
        if ($("input[name=type]:checked").val() == 'selected_employee') {
            employee = $("#select-employee-id").val()
        }
        const post = {
                department_id :  $('#select-department-id').val(),
                start : $('#start').val(),
                end : $('#end').val(),
                employee : employee,
        }
        var title =  $('#select-department-id option:selected').text()+' '+post.start+' s/d '+post.end+' '+ (employee ? 'Selected' : 'All')
        $('#attendanceTable').DataTable( {
                    destroy: true,
                    responsive: true,
                    ajax: {
                        url: "{{route('attendate.generate')}}",
                        method: 'POST',
                        data : post,
                        error: function (xhr, error, code) {
                            Swal.fire({
                                position: 'top-end',
                                toast: true,
                                icon: 'error',
                                text: code,
                                showConfirmButton: false,
                                timer: 1500
                            })
                            $('#generate').html('Generate').removeAttr('disabled')
                        }
                    },
                    columns: [
                        {
                            title: "Absent",
                            data: "badgenumber"
                        },
                        {
                            title: "Name",
                            data: "name"
                        },
                        {
                            title: "Department",
                            data: "department"
                        },
                        {
                            title: "Date",
                            data: "date"
                        },
                        {
                            title: "Time",
                            data: "time"
                        },
                    ],
                    dom: 'Bfrtip',
                    buttons: [
                        {name: 'excel', extend: 'excel', filename: title, sheetName: 'Attendance', title: null},
                        {name: 'pdf', extend: 'pdf', filename: title, title: null},
                    ],
                    initComplete:function(){

                    $('#generate').html('Generate').removeAttr('disabled')
                    }
            });

    })
</script>
    @endpush


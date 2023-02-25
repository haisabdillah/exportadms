@extends('layouts.app')
@section('content')
<div class="container mt-3">
    <div class="row justify-content-center">
      <div class="col-sm-10 col-md-8 col-xl-6">
         <div class="card">
      <div class="card-body">
        <form id="form-export">
          <div class="mb-3">
            <label for="exampleInputEmail1" class="form-label">Department</label>
            <select id="select-department-id" class="form-select" aria-label="Default select example" required>
              <option selected>Select Department</option>
            </select>
          </div>
          <div class="mb-3">
              <label for="start" class="form-label">Start Date</label>
              <input type="date" class="form-control" id="start" required>
          </div>
          <div class="mb-3">
              <label for="end" class="form-label">End Date</label>
              <input type="date" class="form-control" id="end" required>
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
              Export
            </button>
      </form>
      </div>
    </div>
      </div>
    </div>
  </div>
@endsection
    @push('js')
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
                cache: true
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
                cache: true
                // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
            }
    })
    $("#select-department-id").change(function(){
        $("#select-employee-id").val(null).trigger('change')
    });
    $('#generate').click(function(e){
        e.preventDefault()
        $(this).html("<span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span> Exporting...").attr('disabled',true)
        let employee = null;
        if ($("input[name=type]:checked").val() == 'selected_employee') {
            employee = $("#select-employee-id").val()
        }
        $.ajax({
            url: "{{route('export')}}",
            method: 'POST',
            data: {
                department_id :  $('#select-department-id').val(),
                start : $('#start').val(),
                end : $('#end').val(),
                employee : employee,
            },
            headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            xhr: function() {
                const xhr = new XMLHttpRequest();
                xhr.responseType = 'blob'
                return xhr
            },
            success:function(response)
            {
                $('#generate').html('Export').removeAttr('disabled')
                var blob = new Blob([response],{ type: "application/pdf;base64" });
                var link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                window.open(link);
                $('#generate').html('Export').removeAttr('disabled')
            },
            error: function(response) {
                console.log(response)
                Swal.fire({
                    position: 'top-end',
                    toast: true,
                    icon: 'error',
                    text: response.statusText,
                    showConfirmButton: false,
                    timer: 1500
                })
                $('#generate').html('Export').removeAttr('disabled')
            }
        });
    })
</script>
    @endpush


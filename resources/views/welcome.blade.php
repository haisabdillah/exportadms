<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Export ADMS</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
  </head>
  <body>
    <nav class="navbar bg-primary">
        <div class="container-fluid">
          <a class="navbar-brand text-white" href="#">
            {{-- <img src="/docs/5.3/assets/brand/bootstrap-logo.svg" alt="Logo" width="30" height="24" class="d-inline-block align-text-top"> --}}
            Export ADMS
          </a>
        </div>
      </nav>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
  </body>
</html>

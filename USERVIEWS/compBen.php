<?php
    session_start();
    if(!($_SESSION['access']&16)) {
        header('location: ../session.php');
    } else {
        include_once $_SERVER["DOCUMENT_ROOT"]."/psa_hris/header.php";
    }
?>
    <div class="container-fluid container-main">
        <div class="card">
            <div class="header">
                <h1>Compensation and Benefit</h1>
            </div>
  
            <div class="body">
                <table class="table table-bordered table-hover nowrap" id="ope">
                    <thead>
                        <tr>
                            <th>Agency</th>
                            <th>Account</th>
                            <th>Name</th>
                            <th>FName</th>
                            <th>Position</th>
                            <th>Birthday</th>
                            <th>SSS</th>
                            <th>PhilHealth</th>
                            <th>PAGIBIG</th>
                        </tr>
                    </thead>
                </table>
                <!--button type="button" class="btn btn-primary" data-dismiss="modal"><strong class="glyphicon glyphicon-print" style="margin-right: 4px;"></strong>Generate Spreadsheet</button-->
            </div>
        </div>
    </div>

<script>
    $(document).ready( function() {
        $('#ope').DataTable({
            
            "pageLength": 10,
            lengthMenu: [[10,25, 100, -1], [10, 25, 100, "All"]],
            dom: 'lBfrtip',
            buttons: [{
                extend: 'excelHtml5',
                text: '<img style="height:25px" src="https://png.icons8.com/color/50/000000/ms-excel.png">',
                exportOptions: {
                        columns: ":visible"},
            }],
            "processing": true,
            "serverSide": true,
            "ajax": "fetch.php?fetch=cb",

            "columnDefs": [
                {"render": function ( data, type, row ){
                    return row[2] +', '+ row[3]; },
                    "targets": 2 },
                { "visible": false,  "targets": [ 3 ] },
            ]
        });
         $('#ope_wrapper .row:nth-child(2)').css("overflow-x","scroll");
    });
</script>


<?php
    session_start();
    if(!($_SESSION['access']&8)) {
        header('location: ../session.php');
    } else {
        include_once $_SERVER["DOCUMENT_ROOT"]."/psa_hris/header.php";
    }
?>
    <div class="container-fluid container-main">
        <div class="card">
            <div class="header">
                <h1>Human Resources</h1>
            </div>

            <div class="body">
            <table id="hr" class="table table-hover table-bordered nowrap">
                    <thead>
                        <tr>
                            <th>Agency</th>
                            <th>Account</th>
                            <th>Name</th>
                            <th>FName</th>
                            <th>Status</th>
                            <th>Gender</th>
                            <th>Contact Number</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <!--#EmpHismodal-->
    <div id="EmpHismodal" class="modal fade" data-backdrop="static" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>
                        Employment History
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </h4>
                </div>
                <div class="modal-body" id="emp_details">
                </div>      
            </div>
        </div>
    </div>
    <!--#EmpHismodal-->

    </body>
</html>
    <script>
    $(document).ready( function() {
        $('#hr').DataTable({
            "pageLength": 10,
            dom: 'lBfrtip',
            buttons: [{
                extend: 'excelHtml5',
                text: '<img style="height:25px" src="https://png.icons8.com/color/50/000000/ms-excel.png">',
                exportOptions: {
                        columns: ":visible"},
            }],
            "processing": true,
            "serverSide": true,
            "ajax": "fetch.php?fetch=hr",

            "columnDefs": [
                {"render": function ( data, type, row ){
                    return '<div data-toggle="modal" style="cursor:pointer;" name ="view" id="'+row[8]+'" class="view_emp">'+ row[2] +', '+ row[3]+'</div>'; },
                    "targets": 2 },
                { "visible": false,  "targets": [ 3 ] },
                { "visible": false,  "targets": [ 8 ] },
                {"render": function ( data, type, row ){
                    if(row[4]==1){
                        return '<div class="text-center"><span class="label label-success">Active</span></div>';
                    }else{
                        return '<div class="text-center"><span class="label label-danger">Inactive</span></div>';
                }; },"targets": 4 },
            ]
        });
        $(document).on('click', '.view_emp', function() {  
           var emp_id = $(this).attr("id");  
                $.ajax({  
                    url:"/psa_hris/ajax.php",  
                    method:"POST",
                    data:{emp_id:emp_id},
                    success:function(data){
                        //alert(emp_id);
                        jQuery.noConflict();
                        $('#EmpHismodal').modal('show');
                        $('#emp_details').html(data);
                    }  
                });  
        });
    $('#hr_wrapper .row:nth-child(2)').css("overflow-x","scroll");
    });
    </script>
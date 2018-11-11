<?php
    session_start();
    if(!($_SESSION['access']&4)) {
        header('location: ../session.php');
    } else {
        include_once $_SERVER["DOCUMENT_ROOT"]."/psa_hris/header.php";}
?>
    <div class="container-fluid container-main">
        <div class="card">
            <div class="header">
                <h1>Accounting</h1>
            </div>

            <div class="body">
                <table class="table table-bordered table-hover nowrap" id="acco">
                    <thead>
                        <tr>
                            <th>Agency</th>
                            <th>Account</th>
                            <th>Name</th>
                            <th>FName</th>
                            <th>Type</th>
                            <th>Date Hired</th>
                            <th>SSS</th>
                            <th>PhilHealth</th>
                            <th>PAGIBIG</th>
                            <th>NBI Clearance</th>
                            <th>Police Clearance</th>
                            <th>Barangay Clearance</th>
                            <th>ID</th>
                        </tr>
                    </thead>
                </table>
                <!--button type="button" class="btn btn-primary" data-dismiss="modal"><strong class="glyphicon glyphicon-print" style="margin-right: 4px;"></strong>Generate Spreadsheet</button-->
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

<script>
    $(document).ready( function() {
    	var today = new Date().toISOString().split('T')[0];
        $('#acco').DataTable({
            "scrollX": true,
            "lengthChange": true,
            "pageLength": 10,
            lengthMenu: [[10,25, 100, -1], [10, 25, 100, "All"]],
            "autoWidth": true,
            dom: 'lBfrtip',
            buttons: [{
            extend: 'excelHtml5',
            text: '<img style="height:25px" src="https://png.icons8.com/color/50/000000/ms-excel.png">',
            exportOptions: {
                    columns: ":visible"},
            customize: function(xlsx) {
                
                var sheet = xlsx.xl.worksheets['sheet1.xml'];
            
                $('row c[r^="I"]', sheet).not(':first').each( function () {
                    // Get the value
                    if ( $('is t', this).text() <= today && $('is t', this).text()!="") {
                        $(this).attr( 's', '27' );
                    }else if($('is t', this).text()==""){
                        $(this).attr( 's', '25' );
                    }else{
                        $(this).attr( 's', '29' );
                    }
                });
            }
        }],
            "processing": true,
            "serverSide": true,
            "ajax": "fetch.php?fetch=accounting",

            "columnDefs": [
                {"render": function ( data, type, row ){
                    return '<div data-toggle="modal" style="cursor:pointer;" name ="view" id="'+row[12]+'" class="view_emp">'+ row[2] +', '+ row[3]+'</div>'; },
                    "targets": 2 },
                { "visible": false,  "targets": [ 3 ] },
                { "visible": false,  "targets": [ 12 ] },
                {"render": function ( data, type, row ){
                    if(row[10]==1){
                        return '<div class="text-center">YES</span></div>';
                    }else{
                        return '<div class="text-center">NO</span></div>';
                }; },
                "targets": 10},
                {"render": function ( data, type, row ){
                    if(row[11]==1){
                        return '<div class="text-center">YES</span></div>';
                    }else{
                        return '<div class="text-center">NO</span></div>';
                }; },
                "targets": 11, },
                {"render": function ( data, type, row ){
                    if(row[9]==null){
                        return '<div class="none"> </div>';
                    }else if(row[9]<=today){
                        return '<div class="text-center expired">'+row[9]+'</div>';
                    }else{
                        return '<div class="text-center valid">'+row[9]+'</div>';
                    }; },
                "targets": 9 },
            ]
        });
        $(document).on('click', '.view_emp', function() {  
           var emp_id = $(this).attr("id");  
                $.ajax({  
                    url:"/psa_hris/ajax.php",  
                    method:"POST",
                    data:{emp_id:emp_id},
                    success:function(data){
                        jQuery.noConflict();
                        $('#EmpHismodal').modal('show');
                        $('#emp_details').html(data);
                    }  
                });  
        });  
       
    });
</script>

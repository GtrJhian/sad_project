<?php
    session_start();
    if(!($_SESSION['access']&32)) {
        header('location: ../session.php');
    } else {
        include_once $_SERVER["DOCUMENT_ROOT"]."/psa_hris/header.php";}
?>
    <div class="container-fluid container-main">
        <div class="card">
            <div class="header">
                <h1>Operations</h1>
            </div>
  
            <div class="body">
                <table class="table table-bordered table-hover nowrap" id="ope">
                    <thead>
                        <tr>
                            <th>Agency</th>
                            <th>Account</th>
                            <th>Name</th>
                            <th>FName</th>
                            <th>Employment Status</th>
                            <th>Date Hired</th>
                            <th>End Date</th>
                            <th>SSS</th>
                            <th>PhilHealth</th>
                            <th>PAGIBIG</th>
                            <th>NBI Clearance</th>
                            <th>Police Clearance</th>
                            <th>Barangay Clearance</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    </body>

<script>
    $(document).ready( function() {
        var today = new Date().toISOString().split('T')[0];
        $('#ope').DataTable({
                "scrollX": true,
                "lengthChange": true,
                "pageLength": 10,
                dom: 'lBfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    text: '<img style="height:25px" src="https://png.icons8.com/color/50/000000/ms-excel.png">',
                    exportOptions: {
                            columns: ":visible"},
                    customize: function(xlsx) {
                        
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    
                        $('row c[r^="J"]', sheet).not(':first').each( function () {
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
                "ajax": "fetch.php?fetch=operations",

                "columnDefs": [
                    {"render": function ( data, type, row ){
                        return row[2] +', '+ row[3]; },
                        "targets": 2 },
                    { "visible": false,  "targets": [ 3 ] },
                    {"render": function ( data, type, row ){
                    if(row[4]==1){
                        return '<div class="text-center"><span class="label label-success">Active</span></div>';
                    }else{
                        return '<div class="text-center"><span class="label label-danger">Inactive</span></div>';
                   }; },
                    "targets": 4 },
                    {"render": function ( data, type, row ){
                        if(row[11]==1){
                            return '<div class="text-center">YES</span></div>';
                        }else{
                            return '<div class="text-center">NO</span></div>';
                    }; },
                    "targets": 11},
                    {"render": function ( data, type, row ){
                        if(row[12]==1){
                            return '<div class="text-center">YES</span></div>';
                        }else{
                            return '<div class="text-center">NO</span></div>';
                    }; },
                    "targets": 12, },
                    {"render": function ( data, type, row ){
                        if(row[10]==null){
                            return '<div class="none"> </div>';
                        }else if(row[10]<=today){
                            return '<div class="text-center expired">'+row[10]+'</div>';
                        }else{
                            return '<div class="text-center valid">'+row[10]+'</div>';
                        }; },
                    "targets": 10, }
                ]
                
        });
    });
</script>


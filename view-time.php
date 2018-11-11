<?php
    session_start();
    if(!($_SESSION['access']&1)) {
        header('location: session.php');
    } else {
        require_once 'db.php';
        include_once "header.php";
?>
    <div class="container-fluid container-main">
        <div class="card">
            <div class="header">
                <h1>Time Schedule</h1>
            </div>
    
            <div>
                <label>Month: </label>
                <input type="month" class="input-sm" name="" id="month">
                
                <table class="table table-hover" id="timeSummary">
                    <thead>
                        <tr>
                            <th>Agency</th>
                            <th>Account</th>
                            <th>Employee Name</th>
                            <th>Days Tardy</th>
                            <th>Absences</th>
                        </tr>
                    </thead>             
                    <tbody>
                    </tbody>
                </table>
            </div>

            <!--empTimeSummary-->
            <div id="empTimeSummary" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close btn btn-default" data-dismiss="modal">&times;</button>
                    </div>
                                
                    <div class="modal-body">
                    
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                            </tr>
                            </thead>

                            <tbody>
                            </tbody>
                        </table>
                    </div>            
                                
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Save</button>
                    </div>
                
                </div>
                </div>  
            </div>
            <!--empTimeSummary-->
        </div>
    </div>
<?php } ?>
    <script>
    $(document).ready( function() {
        $('#timeSummary').DataTable(
            {
                "lengthChange": false,
                "pageLength": 10,
                "lengthMenu": false,
                "autoWidth": true
            } );
        $('#month').change(function(){
            $('#timeSummary').DataTable().ajax.url("ajax.php?table=viewTime&date="+$('#month').val()).load(
            function(data){
                console.log(data);
            }
        );
        });
    });
    </script>
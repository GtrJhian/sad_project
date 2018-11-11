<?php
    require_once 'db.php';
    session_start();
    include "header.php";
?>

<div class="container-fluid container-main">
    <div class="card">
        
        <div class="header">
            <h1 class="h1">Logs</h1>
        </div>
    
        <div class="body">
            <div class="form-group">
                <label class='label label-success' style="margin-top: 10px">Start Date </label>
                <input type="date"  style="margin-left: 10px" id="from">
                <label class='label label-warning' style="margin-top: 10px">End Date </label>
                <input type="date"  style="margin-left: 12px;" id="to">
            </div>

            <table class="table table-hover table-striped" id="logs">
                <thead>
                <tr>
                    <th>Date and Time</th>
                    <th>Logs</th>
                </tr>
                </thead>
                
                <tbody>
                <tr>
                    <td>
                        YYYY/MM/DD, XX:XX
                    </td>
                    <td>
                        (add) username + 'added' + principal/name + 'in' + table
                    </td>
                </tr>
                <tr>
                    <td>
                        YYYY/MM/DD, XX:XX
                    </td>
                    <td>
                        (update) username + 'updated' + principal/name + column + old.data + 'to' + new.data + 'in' + table 
                    </td>
                </tr>
                </tbody>
            </table>
        </div> 
        
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#logs').DataTable({
            searching: true,
            "lengthChange": false,
            "pageLength": 10,
            }).ajax.url("ajax.php?table=logs&from="+$("#from").val()+"&to="+$("#to").val()).load(function(data){
        });;
    });
    
    $('#to').change(function(){
        $('#logs').DataTable().ajax.url("ajax.php?table=logs&from="+$("#from").val()+"&to="+$("#to").val()).load(function(data){
        console.log(data);
        });
    });
</script>
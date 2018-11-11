<?php
include_once "header.php";
?>

<div class="container-fluid container-main">

    <div class="card">

        <div class="header">
    <h1 class="h1">Agency</h1>
        </div>
    
<div class="body">

    <button type="button" id="addmodal" class="btn btn-primary pull-right" data-toggle="modal" data-target="#addagencymodal">Add Agency</button>

    
    <table class="table table-hover table-bordered " id="agencylist">
        <thead>
        <tr>
            <th>Name</th>
            <th>General Manager</th>
            <th></th>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>

</div>   

<!--ADD AGENCY-->
<div id="addagencymodal" class="modal fade inline" data-backdrop="static" role="dialog">
        <div class="modal-dialog modal-lg">
        <div class="modal-content">
                        
            <div class="modal-header">
                <h3 class="modal-title pull-left"><strong>New Agency</strong></h3>
            </div>
                    
            <form id="add_agency" method="post" action="addAgency.php">
            <div id="fields1" class="modal-body">
                <label>Agency Name: </label><br>
                <input name="agency_name" class="form-control" type="text"><br> 

                <label>General Manager: </label><br>
                <input name="gm_fname" class="form-control" type="text"><br>
                <label>General Manager: </label><br>
                <input name="gm_mname" class="form-control" type="text"><br>
                <label>General Manager: </label><br>
                <input name="gm_lname" class="form-control" type="text"><br>
                            
                <label>E-mail: </label><br>
                <input name="agency_gm_email" class="form-control" type="text"/><br>
                            
                <label>Contact Number: </label><br>
                <input name="agency_contactNumber" class="form-control" type="text"/><br>
                            
                <label>SSS Registration Date: </label><br>
                <input name="sss_regdate" class="form-control" type="date"/><br>	
                            
                <label>Philhealth Registration Date:</label><br>
                <input name="philhealth_regdate" class="form-control" type="date"><br>
                            
                <label>PAGIBIG Registration Date: </label><br>
                <input name="pagibig_regdate" class="form-control" type="date"><br>

                <label>Status:</label><br>
                <select name="agency_status" class="form-control">
                    <option>Active</option>
                    <option>Inactive</option>
                </select>
                
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" name='submit' data-toggle="modal" data-target="#addagencymodal"> Add</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            </div> 
            </form>
                        
            
        </div>
        </div>
        </div>
<!--ADD AGENCY-->

<!--VIEW INFO-->
<div id="viewagencymodal" class="modal fade inline" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title pull-left"><strong id="agency_name">Agency 1</strong></h3>
                    <button class="btn btn-primary pull-right" id="editbtn">Edit</button>
                </div>
                <form id='editAgency' method="post" action="updateAgency.php">
                <div id="fields2" class="modal-body">        
                    <label>General Manager: </label><br>
                    <input id="gm_fname" name="agency_gm_fname" class="form-control" type="text"/><br>
                    <label>General Manager: </label><br>
                    <input id="gm_mname" name="agency_gm_mname" class="form-control" type="text"/><br>
                    <label>General Manager: </label><br>
                    <input id="gm_lname" name="agency_gm_lname" class="form-control" type="text"/><br>
                    
                    <label>E-mail: </label><br>
                    <input id="agency_gm_email" name="agency_gm_email" class="form-control" type="text"/><br>
                    
                    <label>Contact Number: </label><br>
                    <input id="agency_gm_conNum"name="agency_gm_conNum" class="form-control" type="text"/><br>
                    
                    <p><strong>SSS Registration Date:</strong> <span id="agency_sss_regdate">MM/DD/YYYY</span> </p><br>	
                    
                    <p><strong>Philhealth Registration Date:</strong> <span id="agency_philhealth_regdate">MM/DD/YYYY</span></p><br>
                    
                    <p><strong>PAGIBIG Registration Date: </strong> <span id="agency_pagibig_regdate">MM/DD/YYYY</span></p><br>

                    <label>Status:</label><br>
                    <select id="agency_status"name="agency_status" class="form-control">
                        <option value='ACTIVE'>Active</option>
                        <option value='INACTIVE'>Inactive</option>
                    </select>
                </div> 
                
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#viewagencymodal" id='agency_id' name='id' value=''>Save</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
                </form>
            </div>
        </div>
        </div>
<!--VIEW INFO-->

    </div>
    </div>  
    
    </body>
    <script>
    $(document).ready( function() {
        $('#editbtn').click(function(){
            $('#editAgency input').prop('disabled',false);
            $('#editAgency select').prop('disabled',false);
        });
        $('#add_agency').submit(function(e){
           // $('#addagencymodal').modal('hide');
            //alert();
            e.preventDefault();
            $.post(
                'addAgency.php',
                $(this).serialize()+"&submit=1",
                function(){
                    $('#agencylist').DataTable().ajax.url('ajax.php?table=agency').load()
                    $('#add_agency').trigger('reset');
                }
            );
            return false;
            //return false;
        });
        $('#editAgency').submit(function(e){
           // $('#addagencymodal').modal('hide');
            //alert();
            e.preventDefault();
            $.post(
                'updateAgency.php',
                $(this).serialize()+'&id='+$('#agency_id').val(),
                function(){
                    $('#agencylist').DataTable().ajax.url('ajax.php?table=agency').load()
                    $('#editAgency').trigger('reset');
                }
            );
            return false;
            //return false;
        });
        $('#agencylist').DataTable(
            {
                "ajax":"ajax.php?table=agency",
                "lengthChange": false,
                "pageLength": 10,
                "lengthMenu": false,
                "order": [[0, 'asc']],
                "columns": [
                    {"type": "string", "width": '30%'},
                    null,
                    {"width": '25%', "className": "dt-center", "orderable": false}
                ]
            } );
    });
    function viewAgency(id){
        $('#editAgency input').prop('disabled',true);
        $('#editAgency select').prop('disabled',true);
        $.ajax({
            url:'ajax.php?agency_id='+id,
            success:function(result){
                var obj=JSON.parse(result);
                $('#agency_name').text(obj.agency_name);
                $('#gm_fname').val(obj.agency_gm_fname);
                $('#gm_mname').val(obj.agency_gm_mname);
                $('#gm_lname').val(obj.agency_gm_lname);
                $('#agency_gm_email').val(obj.agency_gm_email);
                $('#agency_gm_conNum').val(obj.agency_gm_conNum);
                $('#agency_sss_regdate').text(obj.agency_sss_regdate);
                $('#agency_philhealth_regdate').text(obj.agency_philhealth_regdate);
                $('#agency_pagibig_regdate').text(obj.agency_pagibig_regdate);
                $('#agency_status').val(obj.agency_status);
                $('#agency_id').val(obj.agency_id)
            }
        });
    }
    </script>
    
    <script>
        //$("#addagencymodal").hide();
        
        $("#viewagencymodal").hide();

        $("#modal").click(function(){
            $("#viewagencymodal").show();
            $("#gm").attr("disabled",true);
            $("#email").attr("disabled",true);
            $("#cm").attr("disabled",true);
            $("#status").attr("disabled",true);
        });

        $("#editbtn").click(function(){
            $("#gm").attr("disabled",false);
            $("#email").attr("disabled",false);
            $("#cm").attr("disabled",false); 
            $("#status").attr("disabled",false);          
        });
        $(".close").click(function(){
            $("#viewagencymodal").hide();;
        })
    </script>
    <script>
    $('.disabled').click(function(e){
    e.preventDefault();
});
</script>
    </html>

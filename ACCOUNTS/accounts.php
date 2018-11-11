<?php
    session_start();
    if(!($_SESSION['access']&2)) {
        header('location: ../session.php');
    } else {
        require_once $_SERVER["DOCUMENT_ROOT"].'/psa_hris/db.php';
        include_once $_SERVER["DOCUMENT_ROOT"]."/psa_hris/header.php";
        date_default_timezone_set('Asia/Manila');
        $datenow = date("Y-m-d");
        $fields=[];
        $types="";
        $fields+=["account_status"=>0];
        $types.="ss";
        $db->update_one("account_list",$fields,$types,"account_serAg_eDate",$datenow);
?>
<style type="text/css">
    .form-control, input{
        margin-bottom: 15px;
    }
</style>
<body>

<div class="container-fluid container-main">
    <div class="card">
        <div class="header">
             <h3 class="text-left" style="margin-top:10px;
    margin-bottom: -36px;">ACCOUNTS</h3><div class="div-action" style="text-align: right;">
        <button class="btn btn-primary" data-toggle="modal" data-target="#addModal" style="padding: 2px 12px;"> <i class="glyphicon glyphicon-plus-sign"></i> Add Account </button>
    </div>
        </div>
    
        <div class="body">
            <table id="example" class="table table-hover table-bordered" style="width:100%">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Agency</th>
                    <th>Account Principal</th>
                    <th>Client Name</th>
                    <th>Account Status</th>
                    <th>Account Summary</th>
                </tr>
                </thead>
			
                <tbody>
                <?php foreach($db->select_all("account_list Inner Join agency_list on account_list.agency_id = agency_list.agency_id") as $assoc){ ?>
                <tr>
                    <td><?php echo $assoc['account_id']?></td>
                    <td><?php echo $assoc['agency_name']?></td>
                    <td><?php echo $assoc['account_principal']?></td>
                    <td><?php echo $assoc['account_client_Name']?></td>
                    <td style="text-align: center;  "><?php echo $assoc['account_status']? "<label class='text-success'>Active</label>" : "<label class='text-danger'>Inactive</label>" ; ?></td>
                    <td style="text-align:center;"> <button type="button" class="viewModal btn btn-primary" data-target="#viewModal" data-toggle="modal"  data-id="<?php echo $assoc['account_id']; ?>" > View Account Summary</button></td>
                    </tr> 
                <?php } ?>
                </tbody>
		    </table>
        </div>
    </div>
</div>
<!----------------------------------------------------------------------------------------->
<!---------ADD MODAAAAL-------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------- -->
<div class="modal fade inline" data-backdrop="static" id="addModal" tabindex="-1" role="dialog">
<div class="modal-lg modal-dialog">
    <div class="modal-content">
        
        <form class="form-horizontal" id="addForm" action="accountsCRUD.php" method="POST">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title pull-left"><strong>Add Account</strong></h3>
            </div>
            
            <div class="modal-body">

            <div class="form-group">
                <label class="col-sm-3 control-label">Agency: </label>
                <div class="col-sm-8">
                    <select name="agency" class="form-control" required>
                        <option value="" selected disabled>Select Agency </option>
                        <?php foreach($db->select_all("agency_list") as $assoc){?>
							<option value="<?php echo $assoc['agency_id'];?>"><?php echo $assoc['agency_name']; ?></option>
                        <?php }?>
                    </select>
                </div>
            </div>  
            <div class="form-group">
                <label class="col-sm-3 control-label">Acount Name: </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" id="principal"  name="principal">
                </div>
            </div>           
            <div class="form-group">
                <label class="col-sm-3 control-label">Client Name: </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" id="clientName"  name="clientName">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Address: </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" id="street"  name="street" placeholder="St. Address">
                    <input type="text" class="form-control" id="town"  name="town" placeholder="Town/Municipality">
                    <input type="text" class="form-control" id="province"  name="province" placeholder="Province">
                </div>
            </div>  
            <div class="form-group">
                <label class="col-sm-3 control-label">Service Agreement: </label>
                <div class="col-sm-8">
                    <label class='label label-success' style="margin-top: 10px">Start Date </label>
                    <input type="date"  style="margin-left: 10px;" id="sasDate"  name="sasDate">
                    <label class='label label-warning' style="margin-top: 10px">End Date </label>
                    <input type="date"  style="margin-left: 12px;" id="saeDate"  name="saeDate">
                </div>
            </div> 
            <div class="form-group">
                <label class="col-sm-3 control-label">Cut-off Dates: </label>
                <div class="col-sm-8">
                    <input type="number" id="co1"  name="co1">
                    <input type="number" id="co2"  name="co2">
                </div>
            </div> 
            <div class="form-group">
                <label class="col-sm-3 control-label">Pay Dates: </label>
                <div class="col-sm-8">
                    <input type="number"  id="pd1"  name="pd1">
                    <input type="number"  id="pd2"  name="pd2">
                </div>
            </div>  

            <hr>

            <div id="tbl_persons">
                <table id="account_persons_add" class="table table-striped" style="width:100%">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Contact Number</th>
                        <th>Email</th>
                        <th>Birthday</th>
                        <th></th>
                    </tr>
                    </thead>
                    
                    <tbody>
					<?php
                        for($x=1; $x<=7; $x++, $arrayNumber++){
						$arrayNumber = 0;?>
                    <tr id="row<?php echo $x; ?>" class="<?php echo $arrayNumber; ?>">
                        <td>
                            <input type="text" class="form-control" name="empName[]" id="empName<?php echo $x; ?>">
						</td>
                        <td style="width:20%">
                            <select name="position[]" id="position<?php echo $x; ?>"class="form-control" >
                                <option value="" selected disabled>Select Position</option>
                                <!--
                                <option value="HR">HR</option>
                                <option value="GM">GM</option>
                                <option value="Coordinator">Coordinator</option>
                                <option value="Acco">Acco</option>
                                <option value="Payroll Master">Payroll master</option>
                                -->
                                <?php
                                    if(!$result=$db->query("SELECT * FROM account_pos"))
                                    die($db->error);
                                    while($row=$result->fetch_assoc()){
                                        echo "<option value=".$row['acpos_id'].">".$row['position']."</option>";
                                    }
                                ?>
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control" name="empCon[]" id="empCon<?php echo $x; ?>">
                        </td>
                        <td>
                            <input type="text" class="form-control" name="email[]" id="email<?php echo $x; ?>">
						</td>
                        <td>
                            <input type="date" class="form-control" name="empBday[]" id="empBday<?php echo $x; ?>">
						</td>
					    <td style="padding-top: 12px;"> 
                            <button type="button" class="close" onclick="removeProductRow(<?php echo $x; ?>)"><span aria-hidden="true">&times;</span></button>
						</td>
                    </tr>
                        <?php } ?>
                    </tbody>
                </table> 
            </div>  
            
            <div class="text-center" style="margin-top: -20px;">
                <button type="button" onclick="addRow()" class="btn btn-primary" >
                    <strong class="glyphicon glyphicon-plus" style="margin-right: 4px;"></strong>
                    Add Row
                </button>
            </div>
            
            </div> <!--end of body-->

            <div class="modal-footer">
          	    <button type="submit" name="add" value="add" class="btn btn-primary">
                    <strong class="glyphicon glyphicon-download" style="margin-right: 4px;"></strong>
                    Save Changes
                </button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">
                    <strong class="glyphicon glyphicon-remove" style="margin-right: 4px;"></strong>
                    Close
                </button>
            </div>
        </form>
    </div>
</div>
</div>
<!----------------------------------------------------------------------------------------->
<!---------EDIT MODAAAAL------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------- -->
<div class="modal fade inline" data-backdrop="static" id="viewModal" tabindex="-1" role="dialog">
<div class="modal-lg modal-dialog">
    <div class="modal-content">
        <form class="form-horizontal" id="viewForm" action="accountsCRUD.php" method="POST">
            <div class="modal-header">

                <button type="button" class="btn btn-primary editAccount pull-right">
                    <strong class="glyphicon glyphicon-edit" style="margin-right: 4px;"></strong>
                    Edit
                </button>
                 <button type="submit"  name="update" value="update" class="btn btn-primary updateAccount  pull-right" style=" display: none;">
                    <strong class="glyphicon glyphicon-download" style="margin-right: 4px;"></strong>
                    Save Changes
                </button>
                <h3 class="modal-title" id="viewprincipal" name="principal"></h3>
            </div>
          
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-3 control-label">Agency: </label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="viewagency"  name="agency" disabled="true">
                    </div>
                </div>  
                <input type="hidden" id="account_id"  name="account_id">              
                <div class="form-group">
                    <label class="col-sm-3 control-label">Acount Name: </label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="viewprincipalinput"  name="principal" readonly="readonly">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-3 control-label">Client Name: </label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="viewclientName"  name="clientName" readonly="readonly">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">Address: </label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="viewstreet"  name="street" readonly="readonly" placeholder="St. Address">
                        <input type="text" class="form-control" id="viewtown"  name="town" readonly="readonly" placeholder="Town/Municipality">
                        <input type="text" class="form-control" id="viewprovince"  name="province" readonly="readonly" placeholder="Province">
                    </div>
                </div>  
                <div id="inactivehide">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Service Agreement: </label>
                        <div class="col-sm-8">
                            <label class='label label-success' style="margin-top: 10px">Start Date </label>
                            <input type="date"  style="margin-left: 10px;" id="viewsasDate"  name="sasDate" readonly="readonly">
                            <label class='label label-warning' style="margin-top: 10px">End Date </label>
                            <input type="date"  style="margin-left: 12px;" id="viewsaeDate"  name="saeDate" readonly="readonly">
                        </div>
                    </div> 
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Cut-off Dates: </label>
                        <div class="col-sm-8">
                            <input type="number" id="viewco1"  name="co1" readonly="readonly">
                            <input type="number" id="viewco2"  name="co2" readonly="readonly">
                        </div>
                    </div> 
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Pay Dates: </label>
                        <div class="col-sm-8">
                            <input type="number"  id="viewpd1"  name="pd1" readonly="readonly">
                            <input type="number"  id="viewpd2"  name="pd2" readonly="readonly">
                        </div>
                    </div> 
                </div>
                <div class="form-group" id="disengagement" style="display: none;">
                    <label class="col-sm-3 control-label">Disengagement Date: </label>
                    <div class="col-sm-8">
                        <input type="date"  id="viewddate"  name="ddate" readonly="readonly">
                    </div>
                </div>
                <hr>
                <div id="tbl_persons">
                    <table id="account_persons" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Contact Number</th>
                        <th>Email</th>
                        <th>Birthday</th>
                        <th>Edit</th>
                    </tr>
                    </thead>
                    
                    <tbody>
                
                    </tbody>
                    </table> 
                </div>         
            </div> 
          
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">
                    Close
                </button>
            </div>
        </form>
        <?php } ?>
    
    </div>
</div>
</div>

<script type="text/javascript">
    $(document).ready(function() {

    $('#example_wrapper .row:nth-child(2)').css("overflow-x","scroll");
    $('button[data-dismiss=modal]').click(function(){
        $(".editAccount").show();
        $(".updateAccount").hide();
        $('#viewForm').find(':input').attr("readonly", "true");
    });
    $('.editAccount').click(function(){
        $(this).hide();
        $(".updateAccount").show();
        $('#viewForm').find(':input').removeAttr("readonly");
        $("#inactivehide").show();
        $("#disengagement").hide();
    });
    $('.viewModal').click(function() {
        var accountId = $(this).data('id');
        console.log(accountId);
        $('#account_id').val(accountId);
        $.ajax({ 
            type: "POST",
            datatype:"json",
            url: "accountsCRUD.php",   
            data:"&accountId=" + accountId + "&retrieve=" +1,      
            success: function(data){
                var rdata = JSON.parse(data);
                $('#viewprincipal').html('<i class="fas fa-clipboard-list"></i> ').append(rdata.account_principal);
                $('#viewprincipalinput').val(rdata.account_principal);
                $('#viewagency').val(rdata.agency_name);
                $('#viewclientName').val(rdata.account_client_Name);
                $('#viewstreet').val(rdata.account_add_det);
                $('#viewtown').val(rdata.account_add_town);
                $('#viewprovince').val(rdata.account_add_province);
                $('#viewsasDate').val(rdata.account_serAg_sDate);
                $('#viewsaeDate').val(rdata.account_serAg_eDate);
                $('#viewco1').val(rdata.account_CO_date1);
                $('#viewco2').val(rdata.account_CO_date2);
                $('#viewpd1').val(rdata.account_pay_date1);
                $('#viewpd2').val(rdata.account_pay_date2);

                if (rdata.account_status == 0){
                    $("#disengagement").show()
                    $('#viewddate').val(rdata.account_DE_date);
                    $("#inactivehide").hide();
                }else{
                    $("#disengagement").hide();
                    $("#inactivehide").show();
                }

                $.ajax({ 
                    type: "POST",
                    datatype:"json",
                    url: "accountsCRUD.php",   
                    data:"&accountId=" + accountId + "&account_persons=" +1,      
                    success: function(data){
                        $("#account_persons tbody").html(data);
                    }
                });

            }
        });

    });
    $('#example').DataTable({
        searching: true,
        "lengthChange": true,
        "pageLength": 10,
    });
});

function removeProductRow(row = null) {
	if(row) {
	    $("#row"+row).remove();
	}
}

function addRow() {
	var tableLength = $("#account_persons_add tbody tr").length;
	var tableRow;
	var arrayNumber;
	var count;

	if(tableLength > 0) {		
		tableRow = $("#account_persons_add tbody tr:last").attr('id');
		arrayNumber = $("#account_persons_add tbody tr:last").attr('class');
		count = tableRow.substring(3);	
		count = Number(count) + 1;
		arrayNumber = Number(arrayNumber) + 1;		
	} else {
		count = 1;
		arrayNumber = 0;
	}

			var tr = '<tr id="row'+count+'" class="'+arrayNumber+'">'+			  				
			'<td> <input type="text" class="form-control" name="empName[]" id="empName'+count+'"> </td>' +
			'<td style="width:20%">' +
            	'<select name="position[]" id="position'+count+'" class="form-control"  required>' + 
            		'<option value="" selected disabled>Select Position</option>' +
            		'<option value="HR">HR</option>' +
            		'<option value="GM">GM</option>' +
            		'<option value="Coordinator">Coordinator</option>' +
            		'<option value="Acco">Acco</option>' +
            		'<option value="Payroll Master">Payroll master</option>' +
            	'</select></td>' +
            '<td><input type="text" class="form-control" name="empCon[]" id="empCon'+count+'"></td>' +
            '<td><input type="text" class="form-control" name="email[]" id="email'+count+'"></td>' +
            '<td><input type="date" class="form-control" name="empBday[]" id="empBday'+count+'"></td>' +
	        '<td style="padding-top: 12px;"><button type="button" class="close" onclick="removeProductRow('+count+')"><span aria-hidden="true">&times;</span></button></td> </tr>';
			if(tableLength > 0) {							
				$("#account_persons_add tbody tr:last").after(tr);
			} else {				
				$("#account_persons_add tbody").append(tr);
			}
		
}
function tbledit(row=null){
	if(row) {
	$('#tble'+row).hide();
	$('#tblu'+row).show();
	$("#editrow"+row).find('td.edtbl').attr("contenteditable","true");
	var pos = $('#viewposition'+ row).text();
    var bday = $('#viewempBday'+ row).text();
	$('#viewposition'+ row).replaceWith('<td id="chpos'+row+'"><select name="position[]" id="editposition'+row+'" style="margin-right:-30px;">' + 
            		'<option value="HR">HR</option>' +
            		'<option value="GM">GM</option>' +
            		'<option value="Coordinator">Coordinator</option>' +
            		'<option value="Acco">Acco</option>' +
            		'<option value="Payroll Master">Payroll master</option>' +
           	'</select></td>');
    $('#viewempBday'+ row).replaceWith('<td id="chbday'+row+'"><input type="date" style="margin-right:-40px;" name="empBday[]" id="editempBday'+row+'"></td>');
    $('#editempBday'+ row).val(bday);
	$('#editposition'+ row).val(pos);
	} 
}
function tblupdate(row=null){

    var newname = $('#viewempName'+ row).text();
    var newpos = $('#editposition'+row).val();
    var newcon = $('#viewempCon'+ row).text();
    var newemail = $('#viewemail'+ row).text();
    var newbday = $('#editempBday'+ row).val();

	if(row) {
	$('#tble'+row).show();
	$('#tblu'+row).hide();
	$("#editrow"+row).find('td').removeAttr("contenteditable","true").removeClass('bg-warning') ;

        $('#chpos' + row).replaceWith("<td name='position[]' id='viewposition"+ row +"' value='"+newpos+"'>"+newpos+"</td>");
        $('#chbday' + row).replaceWith("<td name='empBday[]' id='viewempBday"+row+"'>"+newbday+"</td>");

	}

	$.ajax({ 
        type: "POST",
        datatype:"json",
        url: "accountsCRUD.php",   
        data:"&accPer_update=1&accPer_id="+row+
             "&newname="+newname+
             "&newpos="+newpos+
             "&newcon="+newcon+
             "&newemail="+newemail+
             "&newbday="+newbday,      
            success: function(data){
        }
    });

}
</script>
</body>
<?php require_once "db.php"; ?>
<!DOCTYPE html>
<html>
<head>
	<title>
		
	</title>
	<meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">

	<script type="text/javascript" src="jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>

  	<script src="bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
	<link rel="stylesheet" type="text/css" href="bootstrap-3.3.7-dist/css/bootstrap.min.css">

  	<link rel="stylesheet" href="style.css">


	
</head>
<body>
	<div class="search-fields">
	<select class="chosen-select" style="width:150px" data-placeholder="Filter Names" id="filterName">
		<option></option>
		<?php foreach($db->select_all("agency") as $assoc){ 
		echo "<option>".$assoc["agency_name"]."</option>"; }?>
	</select>
</div>
<div id="tbl-container">
<table>
	<thead>
		<th>Agency Name</th>
		<th>General Manager</th>
		<th>Email</th>
		<th>Contact No.</th>
		<th>SSS Registration Date</th>
		<th>Philhealth Registration Date</th>
		<th>Pag-ibig Registration Date</th>
		<th colspan="2">Action</th>
	</thead>
	<tbody id="tbl-body"></tbody>
</table>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
    	
    	<form class="form-horizontal" id="editForm" method="POST">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title"><i class="fa fa-edit"></i> Edit</h4>
	      </div>
	      <div class="modal-body">

	     
            <label>Name: </label>
            <br>
            
            <input type='text' id="agency_name" name="agency_name">
            <label>General Manager: </label>
            <label>First Name: </label>
            <input type='text' id="gm_fname" name="gm_fname">
            <label>Middle Name: </label>
            <input type='text' id="gm_mname" name="gm_mname">
            <label>Last Name: </label>
            <input type='text' id="gm_lname" name="gm_lname">
            <label>Email: </label>
            <input type='text' id="agency_email" name="agency_email">
            <label>Contact Number: </label>
            <input type='text' id="agency_contactNumber" name="agency_contactNumber">
            <label>Philhealth Registration Date: </label>
            <input type='date' id="philhealth_regdate" name="philhealth_regdate">
            <label>PAGIBIG Registration Date: </label>
            <input type='date' id="pagibig_regdate" name="pagibig_regdate">
            <label>SSS Registration Date: </label>
            <input type='date' id="sss_regdate" name="sss_regdate">
            
          
	      </div>
	      
	      <div class="modal-footer editFooter">
	        <button type="button" class="btn btn-default" data-dismiss="modal"> <i class="glyphicon glyphicon-remove-sign"></i> Close</button>
	        
	        <button type="submit" id="update" data-dismiss="modal" class="btn btn-success" autocomplete="off"> <i class="glyphicon glyphicon-ok-sign"></i> Save Changes</button>
	      </div>
     	</form>
    </div>
  </div>
</div>

</body>
</html>
<script>
	$(".chosen-select").chosen();
	$(document).ready(function(){
		
		$("#filterName").on('change',function(){
			var val=$(this).val();

			$.ajax({
				url: "fetchAgency.php",
				type:"POST",
				data: "req=" + val,
				success:function(data){
					$("#tbl-body").html(data);
						$(".edit").click(function(){
							var id = $(this).data("id");
							var agency_name = $("#" + id).children("td[name=agency_name]").text();
							var gm_fname = $("#" + id).children("td[name=gm_fname]").text();
							var gm_mname = $("#" + id).children("td[name=gm_mname]").text();
							var gm_lname = $("#" + id).children("td[name=gm_lname]").text();
							var agency_email = $("#" + id).children("td[name=agency_email]").text();
							var agency_contactNumber = $("#" + id).children("td[name=agency_contactNumber]").text();
							var philhealth_regdate = $("#" + id).children("td[name=agency_philhealth_regdate]").text();
							var pagibig_regdate = $("#" + id).children("td[name=agency_pagibig_regdate]").text();
							var sss_regdate = $("#" + id).children("td[name=agency_sss_regdate]").text();

				            $("#agency_name").val(agency_name);
				            $("#gm_fname").val(gm_fname);
				            $("#agency_email").val(agency_email);
				            $("#agency_contactNumber").val(agency_contactNumber);
				            $("#philhealth_regdate").val(philhealth_regdate);
				            $("#pagibig_regdate").val(pagibig_regdate);
				            $("#sss_regdate").val(sss_regdate);

				            $("#update").click(function(){
							 var id = $(".edit").data("id");
							$.ajax({
									url: "updateAgency.php",
									type:"POST",
									data: $("#editForm").serialize() +'&id=' + id,
									success:function(data){
										location.reload();
									}
								});
						});
						});

						
				},
			});
		});
	});
	
</script>
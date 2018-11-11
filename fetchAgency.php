<?php require_once "db.php"; ?>

<?php
 	if(isset($_POST['req'])){
 		$req = $_POST['req'];
 		foreach($db->filter_column("agency", "agency_name", $req) as $assoc)
 			echo '<tr id="'.$assoc["agency_id"].'">
					<td name="agency_name">'.$assoc["agency_name"].'</td>
					<td name="agency_gm_fname">'.$assoc["agency_gm_fname"].' '.$assoc["agency_gm_lname"].'</td>
					<td name="agency_email">'.$assoc["agency_email"].'</td>
					<td name="agency_contact_number">'.$assoc["agency_contact_number"].'</td>
					<td name="agency_sss_regdate">'.$assoc["agency_sss_regdate"].'</td>
					<td name="agency_philhealth_regdate">'.$assoc["agency_philhealth_regdate"].'</td>
					<td name="agency_pagibig_regdate">'.$assoc["agency_pagibig_regdate"].'</td>
					<td><button class="edit" data-toggle="modal" data-target="#editModal" data-id="'.$assoc["agency_id"].'">Edit</button></td>
					<td><button class="del" data-id="'.$assoc["agency_id"].'">Delete</button</td>
				</tr>';
 	}
 ?>
 
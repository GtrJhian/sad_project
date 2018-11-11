<?php
    require_once "db.php";
    session_start();
    if(isset($_POST["submit"])){
        $data=["agency_name"=>$_POST['agency_name'],
            "agency_gm_fname"=>$_POST['gm_fname'],
            "agency_gm_lname"=>$_POST['gm_lname'],
            "agency_gm_mname"=>$_POST['gm_mname'],
            "agency_gm_email"=>$_POST['agency_gm_email'],
            "agency_gm_conNum"=>$_POST['agency_contactNumber'],
            "agency_philhealth_regdate"=>$_POST['philhealth_regdate'],
            "agency_pagibig_regdate"=>$_POST['pagibig_regdate'],
            "agency_sss_regdate"=>$_POST['sss_regdate'],
            "agency_status"=>strtoupper($_POST['agency_status'])
            ];
        if(!$db->insert("agency_list",$data,"sssssssssi"))
        die($db->error);
        $db->log(0,'agency_list',$data,'agency_id',$db->insert_id);
        header("location: /psa_hris/agency.php");
    }
    
?>
<html>
    <head>
<?php
    //include "header_v3.php";
?>
    </head>
    <body>
        <form method="post" action="addAgency.php">
            <label>Name: </label>
            <br>
            <input type='text' name="agency_name">
            <label>General Manager: </label>
            <label>First Name: </label>
            <input type='text' name="gm_fname">
            <label>Middle Name: </label>
            <input type='text' name="gm_mname">
            <label>Last Name: </label>
            <input type='text' name="gm_lname">
            <label>Email: </label>
            <input type='text' name="agency_email">
            <label>Contact Number: </label>
            <input type='text' name="agency_contactNumber">
            <label>Philhealth Registration Date: </label>
            <input type='date' name="philhealth_regdate">
            <label>PAGIBIG Registration Date: </label>
            <input type='date' name="pagibig_regdate">
            <label>SSS Registration Date: </label>
            <input type='date' name="sss_regdate">
            <input type="submit" name="submit">
        </form>
    </body>
</html>

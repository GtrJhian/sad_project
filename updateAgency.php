<?php
    require_once "db.php";
    session_start();
    if(isset($_POST["id"])){
        $id = $_POST["id"];
        $data=array(
            "agency_gm_fname"=>$_POST['agency_gm_fname'],
            "agency_gm_lname"=>$_POST['agency_gm_lname'],
            "agency_gm_mname"=>$_POST['agency_gm_mname'],
            "agency_gm_email"=>$_POST['agency_gm_email'],
            "agency_gm_conNum"=>$_POST['agency_gm_conNum'],
            "agency_status"=>$_POST['agency_status']
            );
        $fields=implode(', ',array_keys($data));
        if(!$prev=$db->query("SELECT $fields FROM agency_list where agency_id=$id"))
        die($db->error);
        if(!$db->update_one("agency_list",$data,'ssssssi','agency_id',$id))
        die($db->error);
        $db->log(1,'agency_list',$prev->fetch_assoc(),'agency_id',$id);
    }
    
    
?>
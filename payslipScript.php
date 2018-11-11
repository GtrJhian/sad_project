<?php
    require_once "db.php";
    if(isset($_REQUEST['agency_id'])){
        $id=$_REQUEST['agency_id'];
        $result=$db->query("SELECT * FROM account_list where agency_id=$id");
        while($row=$result->fetch_assoc()){
            echo "<option value='".$row['account_id']."'>".$row['account_principal']."</option>";
        }
    }
    else if(isset($_REQUEST['account_id'])){
        $id=$_REQUEST['account_id'];
        $result=$db->query("SELECT * FROM employee_list where account_id=$id");
        while($row=$result->fetch_assoc()){
            echo "<option value='".$row['emp_id']."'>".$row['emp_lname'].', '.$row['emp_fname']."</option>";
        }
    }
?>
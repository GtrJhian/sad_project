<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/psa_hris/db.php';
    if(isset($_POST['addemployee'])){
        $fields=[];
        $types="";
        if(isset($_POST['empacct'])){
            $fields+=["account_id"=>$_POST['empacct']];
            $types.="i";
        }
        if(isset($_POST['emplname'])){
            $fields+=["emp_lname"=>$_POST['emplname']];
            $types.="s";
        }
        if(isset($_POST['empfname'])){
            $fields+=["emp_fname"=>$_POST['empfname']];
            $types.="s";
        }
        if(isset($_POST['empmname'])){
            $fields+=["emp_mname"=>$_POST['empmname']];
            $types.="s";
        }
        if(isset($_POST['empbday'])){
            $fields+=["emp_bday"=>$_POST['empbday']];
            $types.="s";
        }
        if(isset($_POST['empsex'])){
            $fields+=["emp_sex"=>$_POST['empsex']];
            $types.="s";
        }
        if(isset($_POST['empemail'])){
            $fields+=["emp_email"=>$_POST['empemail']];
            $types.="s";
        }
        if(isset($_POST['empcn'])){
            $fields+=["emp_conNum"=>$_POST['empcn']];
            $types.="s";
        }
        if(isset($_POST['emppos'])){
            $fields+=["emp_pos"=>$_POST['emppos']];
            $types.="s";
        }
        if(isset($_POST['emptype'])){
            $fields+=["emp_type"=>$_POST['emptype']];
            $types.="s";
        }
        if(isset($_POST['empdh'])){
            $fields+=["emp_dateHired"=>$_POST['empdh']];
            $types.="s";
        }
        if(isset($_POST['emped'])){
            $fields+=["emp_endDate"=>$_POST['emped']];
            $types.="s";
        }
        if(isset($_POST['empadd'])){
            $fields+=["emp_add_province"=>$_POST['empadd']];
            $types.="s";
        }
        if(isset($_POST['empecp'])){
            $fields+=["emp_emergency_conPer"=>$_POST['empecp']];
            $types.="s";
        }
        
        if(isset($_POST['empcs'])){
            $fields+=["emp_civilStatus"=>$_POST['empcs']];
            $types.="s";
        }
         if(isset($_POST['SSS'])){
            $fields+=["emp_man_SSS"=>$_POST['SSS']];
            $types.="s";
        }
         if(isset($_POST['Philhealth'])){
            $fields+=["emp_man_PhilHealth"=>$_POST['Philhealth']];
            $types.="s";
        }
         if(isset($_POST['Pag-ibig'])){
            $fields+=["emp_man_PAGIBIG"=>$_POST['Pag-ibig']];
            $types.="s";
        }
        if(isset($_POST['NBI1'])){
            $fields+=["emp_man_NBI_expDate"=>$_POST['NBI1']];
            $types.="s";
        }
        if(isset($_POST['NBI2'])){
            $fields+=["emp_man_NBI_ORNum"=>$_POST['NBI2']];
            $types.="s";
        }
        if(isset($_POST['Policeclr'])){
            $fields+=["emp_man_polClear"=>$_POST['Policeclr']];
            $types.="s";
        }
        if(isset($_POST['Brgyclr'])){
            $fields+=["emp_man_brgyClear"=>$_POST['Brgyclr']];
            $types.="s";
        }
            $fields+=["emp_status"=>1];
            $types.="i";
            $fields+=["emp_emergency_conNum"=>$_POST['empecn']];
            $types.="s";
            $fields+=["emp_noOfChildren"=>$_POST['empchiln']];
            $types.="i";
        if(!$db->insert("employee_list",$fields,$types))
        die($db->error);
        echo json_encode($_REQUEST);
       // echo header("Location: employee.php");
    }
    if(isset($_POST['retrievehistory'])){
        $empId = $_POST["empId"];
         foreach($db->select_all_id("employment_history Inner Join employee_list on employment_history.emp_id = employee_list.emp_id","employment_history.emp_id", $empId) as $assoc){
            echo json_encode($assoc);
         }
    }
    if(isset($_POST['fetchMandatory'])){
        $empId = $_POST["empId"];
        foreach($db->select_cols_id("emp_man_SSS, emp_man_PhilHealth, emp_man_PAGIBIG, emp_man_NBI_expDate, emp_man_NBI_ORNum, emp_man_polClear, emp_man_brgyClear","employee_list","emp_id", $empId) as $assoc){
            echo json_encode($assoc);
        }
    }
    if(isset($_POST['fetchAccount'])){
        $empId = $_POST["empId"];
        echo '<select id="accountinput'.$empId.'" class="form-control">';
        foreach($db->select_all("account_list WHERE account_status='1' ORDER by account_principal ASC") as $assoc){
            echo '<option id="'.$assoc['account_principal'].'" value="'.$assoc['account_id'].'">'.$assoc['account_principal'].'</option>';
        }
        echo '</select>';
    }
    if(isset($_POST['updateMan'])){
        date_default_timezone_set('Asia/Manila');
        $datenow = date("Y-m-d");
        $fields=[];
        $types="";
        echo $_POST['vmNBI2'];
        if(isset($_POST['vmSSS'])){
            $fields+=["emp_man_SSS"=>$_POST['vmSSS']];
            $types.="s";
        }
         if(isset($_POST['vmPhilhealth'])){
            $fields+=["emp_man_PhilHealth"=>$_POST['vmPhilhealth']];
            $types.="s";
        }
         if(isset($_POST['vmPag-ibig'])){
            $fields+=["emp_man_PAGIBIG"=>$_POST['vmPag-ibig']];
            $types.="s";
        }
        if(isset($_POST['vmNBI1'])){
            $fields+=["emp_man_NBI_expDate"=>$_POST['vmNBI1']];
            $types.="s";
        }
        if(isset($_POST['vmNBI2'])){
            $fields+=["emp_man_NBI_ORNum"=>$_POST['vmNBI2']];
            $types.="s";
        }
        if(isset($_POST['vmPoliceclr'])){
            $fields+=["emp_man_polClear"=>$_POST['vmPoliceclr']];
            $types.="s";
        }
        if(isset($_POST['vmBrgyclr'])){
            $fields+=["emp_man_brgyClear"=>$_POST['vmBrgyclr']];
            $types.="s";
        }
        $types.="i";
        $db->update_one("employee_list",$fields,$types,"emp_id",$_POST['empId']); 
    }
    if(isset($_POST['updateAll'])){
        date_default_timezone_set('Asia/Manila');
        $datenow = date("Y-m-d");
        $fields=[];
        $types="";
        echo $_POST['enddate']." ".$_POST['updateaccount']." ".$_POST['startdate'];
        if(isset($_POST['enddate'])){
            $fields+=["emp_endDate"=>$_POST['enddate']];
            $types.="s";
        }
         if(isset($_POST['updateaccount'])){
            $fields+=["account_id"=>$_POST['updateaccount']];
            $types.="i";
        }
         if(isset($_POST['startdate'])){
            $fields+=["emp_dateHired"=>$_POST['startdate']];
            $types.="s";
        }
        if(strcmp($datenow, $_POST['enddate']) == 0){
            $fields+=["emp_status"=>0];
            $types.="s";
        }else{
            $fields+=["emp_status"=>1];
            $types.="s";
        }
        $types.="i";
        $db->update_one("employee_list",$fields,$types,"emp_id",$_POST['empId']); 
    } if(isset($_POST['updateEnd'])){
        date_default_timezone_set('Asia/Manila');
        $datenow = date("Y-m-d");

        $fields=[];
        $types="";
        if(isset($_POST['enddate'])){
            $fields+=["emp_endDate"=>$_POST['enddate']];
            $types.="s";
        }
        if(strcmp($datenow, $_POST['enddate']) == 0){
            $fields+=["emp_status"=>0];
            $types.="s";

            foreach($db->select_all_id("employee_list","emp_id", $_POST['empId']) as $assoc){
            $fields1=[];
            $types1="";
            $fields1+=["empmHisto_account"=>$assoc['account_id']];
            $types1.="s";
            $fields1+=["empmHisto_sDate"=>$assoc['emp_dateHired']];
            $types1.="s";
            $fields1+=["empmHisto_eDate"=>$assoc['emp_endDate']];
            $types1.="s";
            $types1.="i";
            $db->insert("employment_history",$fields1,$types1);
         }



        }else{
            $fields+=["emp_status"=>1];
            $types.="s";
        }
        $types.="i";
        $db->update_one("employee_list",$fields,$types,"emp_id",$_POST['empId']); 
    }

   
?>
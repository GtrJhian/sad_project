<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/psa_hris/db.php';
    if(isset($_POST['add'])){
        $fields=[];
        $types="";
        if(isset($_POST['agency'])){
            $fields+=["agency_id"=>$_POST['agency']];
            $types.="i";
        }
        if(isset($_POST['principal'])){
            $fields+=["account_principal"=>$_POST['principal']];
            $types.="s";
        }
        if(isset($_POST['clientName'])){
            $fields+=["account_client_Name"=>$_POST['clientName']];
            $types.="s";
        }
        if(isset($_POST['sasDate'])){
            $fields+=["account_serAg_sDate"=>$_POST['sasDate']];
            $types.="s";
        }
        if(isset($_POST['saeDate'])){
            $fields+=["account_serAg_eDate"=>$_POST['saeDate']];
            $types.="s";
            $fields+=["account_status"=>1];
            $types.="i";
            $fields+=["account_DE_date"=>$_POST['saeDate']];
            $types.="s";
        }
        if(isset($_POST['province'])){
            $fields+=["account_add_province"=>$_POST['province']];
            $types.="s";
        }
        if(isset($_POST['town'])){
            $fields+=["account_add_town"=>$_POST['town']];
            $types.="s";
        }
        if(isset($_POST['street'])){
            $fields+=["account_add_det"=>$_POST['street']];
            $types.="s";
        }
        if(isset($_POST['co1'])){
            $fields+=["account_CO_date1"=>$_POST['co1']];
            $types.="s";
        }
        if(isset($_POST['co2'])){
            $fields+=["account_CO_date2"=>$_POST['co2']];
            $types.="s";
        }
        if(isset($_POST['pd1'])){
            $fields+=["account_pay_date1"=>$_POST['pd1']];
            $types.="s";
        }
        if(isset($_POST['pd2'])){
            $fields+=["account_pay_date2"=>$_POST['pd2']];
            $types.="s";
        }
       if($db->insert("account_list",$fields,$types) === true){
            $accountId = $db->insert_id;
       }
            
          for($x = 0; $x < count($_POST['empName']); $x++) {  
                $fields=[];
                $types="";

                $fields+=["account_id"=>$accountId];
                $types.="s";

                $fields+=["accPer_Name"=>$_POST['empName'][$x]];
                $types.="s";
     
                $fields+=["accPer_pos"=>$_POST['position'][$x]];
                $types.="s";

                $fields+=["accPer_conNum"=>$_POST['empCon'][$x]];
                $types.="s";

                $fields+=["accPer_email"=>$_POST['email'][$x]];
                $types.="s";

                $fields+=["accPer_bday"=>$_POST['empBday'][$x]];
                $types.="s";

            $db->insert("account_persons",$fields,$types);
          }

    }else if(isset($_POST['retrieve'])){
        $account_id = $_POST["accountId"];
         foreach($db->select_all_id("account_list Inner Join agency_list on account_list.agency_id = agency_list.agency_id", "account_id", $account_id) as $assoc){
            echo json_encode($assoc);
         }
        
    }else if(isset($_POST['account_persons'])){
        $account_id = $_POST["accountId"];
        $db->query("SELECT ac.*,p.* FROM account_persons as ac
                    INNER JOIN ac.position=p.acpos_id");
         //foreach($db->select_all_id("account_persons","account_id",$account_id) as $assoc){
             if(!$result=$db->query("SELECT ac.*,p.* FROM account_persons as ac
             INNER JOIN account_pos as p ON ac.accPer_pos=p.acpos_id where account_id=$account_id"))
             die($db->error);
             foreach($result->fetch_all(MYSQLI_ASSOC) as $assoc){
            echo "<tr id='editrow".$assoc['accPer_id']."'>
                <td class='edtbl' name='empName[]' id='viewempName".$assoc['accPer_id']."'>".$assoc['accPer_Name']."</td>
                <td name='position[]' id='viewposition".$assoc['accPer_id']."' value='".$assoc['accPer_pos']."'>".$assoc['position']."</td>
                <td class='edtbl' name='empCon[]' id='viewempCon".$assoc['accPer_id']."'>".$assoc['accPer_conNum']."</td>
                <td class='edtbl' name='email[]' id='viewemail".$assoc['accPer_id']."'>".$assoc['accPer_email']."</td>
                <td name='empBday[]' id='viewempBday".$assoc['accPer_id']."'>".$assoc['accPer_bday']."</td>
                <td style='text-align:center;'> <span class='btn tblbtn' id=tble".$assoc['accPer_id']." onclick='tbledit(".$assoc['accPer_id'].")'><i class='glyphicon glyphicon-edit' readonly></i> Edit</span>
                <span class='btn tblbtn' style='display:none' id='tblu".$assoc['accPer_id']."' onclick='tblupdate(".$assoc['accPer_id'].")'><i class='glyphicon glyphicon-check'></i> Save</span></td>
            </tr>";
         }
        
    }else if(isset($_POST['update'])){
        $fields=[];
        $types="";
         if(isset($_POST['principal'])){
            $fields+=["account_principal"=>$_POST['principal']];
            $types.="s";
        }
        if(isset($_POST['clientName'])){
            $fields+=["account_client_Name"=>$_POST['clientName']];
            $types.="s";
        }
        if(isset($_POST['sasDate'])){
            $fields+=["account_serAg_sDate"=>$_POST['sasDate']];
            $types.="s";
        }
        if(isset($_POST['saeDate'])){
            $fields+=["account_serAg_eDate"=>$_POST['saeDate']];
            $types.="s";
            $fields+=["account_DE_date"=>$_POST['saeDate']];
            $types.="s";
        }
        if(isset($_POST['province'])){
            $fields+=["account_add_province"=>$_POST['province']];
            $types.="s";
        }
        if(isset($_POST['town'])){
            $fields+=["account_add_town"=>$_POST['town']];
            $types.="s";
        }
        if(isset($_POST['street'])){
            $fields+=["account_add_det"=>$_POST['street']];
            $types.="s";
        }
        if(isset($_POST['co1'])){
            $fields+=["account_CO_date1"=>$_POST['co1']];
            $types.="s";
        }
        if(isset($_POST['co2'])){
            $fields+=["account_CO_date2"=>$_POST['co2']];
            $types.="s";
        }
        if(isset($_POST['pd1'])){
            $fields+=["account_pay_date1"=>$_POST['pd1']];
            $types.="s";
        }
        if(isset($_POST['pd2'])){
            $fields+=["account_pay_date2"=>$_POST['pd2']];
            $types.="s";
        }
        $types.="i";
        $db->update_one("account_list",$fields,$types,"account_id",$_POST['account_id']);
       echo header("Location: /psa_hris/ACCOUNTS/accounts.php");

    }else if(isset($_POST['accPer_update'])){
       
            $fields=[];
            $types="";

            $fields+=["accPer_Name"=>$_POST['newname']];
            $types.="s";
 
            $fields+=["accPer_pos"=>$_POST['newpos']];
            $types.="s";

            $fields+=["accPer_conNum"=>$_POST['newcon']];
            $types.="s";

            $fields+=["accPer_email"=>$_POST['newemail']];
            $types.="s";

            $fields+=["accPer_bday"=>$_POST['newbday']];
            $types.="s";

        $types.="i";
        $db->update_one("account_persons",$fields,$types,"accPer_id",$_POST['accPer_id']);
        echo header("Location: /psa_hris/ACCOUNTS/accounts.php");
    }
?>
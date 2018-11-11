<?php
    require_once 'db.php';
    if(isset($_GET['table'])){
        $table=$_GET['table'];
        if($table=='agency'){
            if(!$logs=$db->query("SELECT agency_name, concat(agency_gm_lname,', ',agency_gm_fname,' ',(CASE
                                                                                                       WHEN agency_gm_mname IS NULL then ''
                                                                                                       ELSE agency_gm_mname
                                                                                                       END)) as name, 
                                                                                                       agency_id 
                                  FROM agency_list"))
            die($db->error);
            $data=$logs->fetch_all(MYSQLI_NUM);
            //<button type="button" onclick class="btn btn-primary pull-right" data-toggle="modal" data-target="#addagencymodal">Add Agency</button>
            
            for($ctr=0; $ctr<count($data); $ctr++){
                $data[$ctr][2]="<input type='button' onclick='viewAgency(".$data[$ctr][2].")' class='btn btn-primary' data-toggle='modal' data-target='#viewagencymodal' value='View' style='width:60px;'>";
            }
            $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData"=>$data);
            echo json_encode($results);
        }
        else if($table=='dtr'){
            $date=$_GET['date'];
            if(!$result=$db->query("SELECT e.emp_id,a.agency_name, ac.account_principal,concat(e.emp_lname,', ',e.emp_fname, (
                CASE 
                    WHEN e.emp_mname IS NULL THEN ''
                    ELSE e.emp_mname
                END                                                                                                                                
            )) as name, (CASE
                            WHEN t.dayStat=0 THEN 'ABSENT'
                            WHEN t.dayStat=1 THEN concat(t.time_in,'-', t.time_out)
                            WHEN t.dayStat=2 THEN 'Day-Off'
                            ELSE null
                         END
            )FROM agency_list as a INNER JOIN account_list as ac ON a.agency_id=ac.agency_id
            INNER JOIN employee_list as e ON e.account_id=ac.account_id
            LEFT JOIN (SELECT * FROM employee_time_rec where date='$date') as t ON t.emp_id=e.emp_id WHERE e.emp_status=1"))
            die($db->error);
            $data=$result->fetch_all(MYSQLI_NUM);
            for($ctr=0; $ctr<count($data); $ctr++){
                if($data[$ctr][4]==null)
                $data[$ctr][4]="<input type='button' class='btn btn-primary' data-toggle='modal' data-target='#inputTime' value='Set Time' onclick='setTime(".$data[$ctr][0].")' style='width: 80px;'>";
                array_shift($data[$ctr]);
            }
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData"=>$data);
        echo json_encode($results);
        }
        else if($table=='viewTime'){
            $date_from=$_GET['date'].'-01';
            $date_to=new DateTime($_GET['date'].'-01');
            $date_to->add(new DateInterval('P1M'));
            $date_to=$date_to->format("Y-m-d");
            //die($date_from->format("Y-m-d").'/'.$date_to->format("Y-m-d"));
            
            if(!$data=$db->query("SELECT a.agency_name,ac.account_principal, concat(e.emp_lname,', ',e.emp_fname,' ',(
                CASE
                    WHEN e.emp_mname is null THEN ''
                    ELSE e.emp_mname
                END)),
                t.tardy,ab.absent    
                FROM employee_list as e
                INNER JOIN account_list as ac ON e.account_id=ac.account_id
                INNER JOIN agency_list as a ON a.agency_id=ac.account_id
                LEFT JOIN (SELECT COUNT(*) as tardy, emp_id FROM employee_time_rec where time_out-time_in<90000 and daystat=1 and ('$date_from'<=date AND '$date_to'>date) group by emp_id) as t ON e.emp_id=t.emp_id
                LEFT JOIN (SELECT COUNT(*)as absent, emp_id from employee_time_rec WHERE daystat=0 and ('$date_from'<=date AND '$date_to'>date) group by emp_id) as ab ON ab.emp_id=e.emp_id;"))
            die($db->error);
            $data=$data->fetch_all(MYSQLI_NUM);
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData"=>$data);
        echo json_encode($results);
        }
        else if($table=="logs"){
            $from=$_GET['from'];
            $to=$_GET['to'];
            if(!$logs=$db->query("SELECT a.*,u.username FROM logs as a INNER JOIN users as u ON u.id=a.user_id
                                  WHERE datetime>='$from' and datetime<='$to'"
                                  ))
            die($db->error);
            $data=array();
            while($log=$logs->fetch_assoc()){
                $id=$log['log_id'];
                if(!$results=$db->query("SELECT * FROM log_cols WHERE log_id=$id"))
                die($db->error);
                $log_message="";
                if($log['action']==0){
                    //(add) username + 'added' + principal/name + 'in' + table
                    $log_message=$log['username']." 'added' ";
                    while($column=$results->fetch_assoc()){
                        $log_message.=$column['column_name']."=".$column['new_value']." <br>";
                    }
                    $log_message.=" in ".$log['table_name'];
                }
                else if($log['action']==1){
                    // (update) username + 'updated' + principal/name + column + old.data + 'to' + new.data + 'in' + table 
                    $log_message=$log['username']." 'updated' ";
                    while($column=$results->fetch_assoc()){
                        $log_message.=$column['column_name']." from ".$column['new_value']." to ".$column['prev_value']." <br>";
                    }
                    $log_message.=" in ".$log['table_name'];
                }
                array_push($data,[$log['datetime'],$log_message]);
            }

            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData"=>$data);
        echo json_encode($results);
        }
    }
        
    else if(isset($_GET['agency_id'])){
        $agency_id=$_GET['agency_id'];
        if(!$logs=$db->query("SELECT * FROM agency_list where agency_id=$agency_id"))
        die($db->error);
        echo json_encode($logs->fetch_assoc());
    }
    if(isset($_GET['emp_id'])&&isset($_GET['setTime'])){
        $emp_id=$_GET['emp_id'];
        if(!$e=$db->query("SELECT ac.account_principal,a.agency_name, concat(e.emp_lname,', ',e.emp_fname) as name
        from account_list as ac INNER JOIN agency_list as a ON a.agency_id=ac.agency_id
        INNER JOIN employee_list as e ON E.account_id=AC.account_id where emp_id=$emp_id "))
        die($db->error);
        echo json_encode($e->fetch_assoc());
    }
    

//--FOR DASHBOARD (Employment History) MODAL--//    
    if(isset($_POST["emp_id"])) {
        $id = $_POST["emp_id"];
        $output = '';  
        $connect = new mysqli("localhost", "root", "", "psa_db"); 
        $name = "SELECT emp_status, concat(emp_fname,', ', emp_lname) as name 
                    FROM employee_list 
                    WHERE emp_id = $id ";
        if ($result = $connect->query($name)) {
            if ($result->num_rows > 0) {
                while ($row = mysqli_fetch_array($result)) {
                    $stCL = '';
                    
                    $output .= '<h4>Name: '. $row["name"] .'</h4>';
                    if($row["emp_status"] == 1) {
                        $stCl = "txtA";
                        $output .='<p class= " '.$stCl.' ">Employment Status: Active </p>
                        <br>';
                    } else if ($row["emp_status"] == 0) {
                        $stCl = "txtI";
                        $output .='<p class= " '.$stCl.' ">Employment Status: Inactive</p>
                        <br>';
                    }
                   
                }
            }
        }
        $query = "SELECT * FROM employment_history
                    WHERE emp_id = '$id'";  
        if ($res = $connect->query($query)) {
            if ($res->num_rows > 0) {
                $output .= '
                    <div class="table-responsive">  
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Agency</th>
                                <th>Account</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Reason for Leaving</th>
                            </tr>
                            </thead>
                            <tbody>';
                while ($rows = mysqli_fetch_array($res)) {
                    $output .= '
                        <tr>
                            <td>'.$rows["empmHisto_agency"].'</td>
                            <td>'.$rows["empmHisto_account"].'</td>
                            <td>'.$rows["empmHisto_sDate"].'</td>
                            <td>'.$rows["empmHisto_eDate"].'</td>
                            <td>'.$rows["empmHisto_RFL"].'</td>
                        </tr>';
                }
            }
        }
            $output .= '  
                            </tbody>
                        </table>  
                    </div>'; 
        echo $output;  
    }
//--FOR DASHBOARD (Employment History) MODAL--//

//-------------------


?>
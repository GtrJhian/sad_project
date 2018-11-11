<?php  
    if(isset($_POST["emp_id"])) {
        $id = $_POST["emp_id"];
        $output = '';  
        $connect = new mysqli("localhost", "", "", "psa_db"); 

        $name = "SELECT emp_status, concat(emp_fname,', ', emp_lname) as name 
                    FROM employee_list 
                    WHERE emp_id = $id ";
        
        if ($result = $connect->query($name)) {
            if ($result->num_rows > 0) {
                while ($row = mysqli_fetch_array($result)) {
                    $stCL = '';
                    
                    $output .= '<h4>Name: '. $row["name"] .'</h4>';
                    
                    if($row["emp_status"] == "Active") {
                        $stCl = "txtA";
                    } else if ($row["emp_status"] == "Inactive") {
                        $stCl = "txtI";
                    }

                    $output .='<p class= " '.$stCl.' ">Employment Status: '. $row["emp_status"] .'</p>
                                <br>';
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
 ?>
 
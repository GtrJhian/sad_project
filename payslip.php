<?php
    session_start();
    if(!($_SESSION['access']&1)) {
        header('location: session.php');
    }
        require_once 'db.php';
        include_once "header.php";

        //===============payslip class=====================//
        class payslip{
            function __construct($emp_id,$year_month,$cutoff,$db){
                //-------------------------------- EMPLOYEE DETAILS
                /*
                SELECT e.emp_lname, e.emp_fname, j.job_title, d.pagibig_id, d.philhealth_id, d.sss_id, d.tin_id, dp.dept_name
    FROM emp_info_tbl as e INNER JOIN job_description_tbl AS j ON e.job_id=j.job_id
    INNER JOIN job_department_tbl AS dp ON e.dept_id=dp.dept_id
    LEFT JOIN deductions_tbl AS d ON d.emp_id=e.emp_id WHERE e.emp_id=1
    
                */
                $query="SELECT e.*,p.*,a.agency_name,ac.account_principal FROM employee_list AS e
                INNER JOIN position AS p ON p.pos_id=e.emp_pos
                INNER JOIN account_list AS ac ON ac.account_id=e.account_id
                INNER JOIN agency_list as a ON a.agency_id=ac.agency_id where emp_id=?";
                $result=$db->prepared_query($query,[$emp_id],"i")->fetch_assoc();
                //$result=$result->fetch_assoc();
                //die(json_encode($result));
                $this->emp=new stdClass();
                $this->emp->emp_id=$emp_id;
                $this->emp->emp_name= $result['emp_lname'].", ".$result['emp_fname'];
                $this->emp->job_title=$result['job_pos'];
               // $this->emp->dept_name=$result['dept_name'];
                $this->emp->account=$result['account_principal'];
                $this->emp->agency=$result['agency_name'];
                $this->emp->pagibig_id=$result['emp_man_PAGIBIG'];
                $this->emp->philhealth_id=$result['emp_man_PhilHealth'];
                $this->emp->sss_id=$result['emp_man_SSS'];
                //--------------------------------
                
                if($cutoff==1){
                    $from_date=date_create($year_month);
                    $to_date=date_create($year_month.'-15');
                }
                else{
                    $from_date=date_create($year_month."-16");
                    $to_date=date_create($year_month)->add(new DateInterval("P1M"));
                    $day=new DateInterval("P1D");
                    $day->invert=1;
                    $to_date->add($day);
                }
                $hourly_rate=$result['rate'];
                $from_date=$from_date->format("Y-m-d");
                $to_date=$to_date->format("Y-m-d");
                $this->payperiod=$from_date.'/'.$to_date;
                //SET MONTHLY RATE AND REGULAR PAY
                $this->monthly_rate=$hourly_rate*5*8*4;
                $this->daily_rate=$hourly_rate*8;
                //$working_days=$this->count_days($from_date,$to_date);
                $query="SELECT COUNT(*) as count FROM employee_time_rec where emp_id=? AND date>=? AND date<? AND (daystat=1 OR daystat=0)";
                $data=[$emp_id,$from_date,$to_date];
                $working_days=$db->prepared_query($query,$data,"iss")->fetch_assoc()['count'];
                $this->regular_pay=$hourly_rate*8*$working_days;
    
                
               
                /*
                if(!$dates=$db->query("SELECT log_current_date, log_time_in, log_time_out 
                                       FROM tms_log_tbl 
                                       WHERE emp_id=$emp_id 
                                       AND log_current_date>='$from_date' 
                                       AND log_current_date<='$to_date'"
                                      )
                    )
                    die($db->error);      
                    */
                $query="SELECT * FROM employee_time_rec where emp_id=? AND date>=? AND date<? AND daystat=1";
                $data=[$emp_id,$from_date,$to_date];
                //die(json_encode($data));
                //die(json_encode($data));
                $dates=$db->prepared_query($query,$data,"iss")->fetch_all(MYSQLI_ASSOC);       
                //die(json_encode($dates));                             
                //$dates=$dates->fetch_all(MYSQLI_ASSOC);
                $query="SELECT count(*) as count FROM employee_time_rec where emp_id=? AND date>=? AND date<? AND daystat=0";
                $data=[$emp_id,$from_date,$to_date];
                $absents_count=$db->prepared_query($query,$data,"iss")->fetch_assoc()['count'];
                $this->late_deduction=$this->get_hours_late($dates)*$hourly_rate;
                //$this->absents_deduction=($working_days-count($dates))*$hourly_rate*8;
                $this->absents_deduction=$absents_count*$hourly_rate*8;
                $this->overtime_pay=$hourly_rate*$this->get_overtime_hours($dates);
                //$this->gross_earnings=$this->regular_pay-$this->late_deduction-$this->absents_deduction+$this->overtime_pay;
                //Hourly rate + (hourly rate *10%) night differential
                //Hourly rate + (hourly rate *10%) night differential
                $this->gross_earnings=$this->regular_pay-$this->late_deduction-$this->absents_deduction+$this->overtime_pay;
            


            //====DEDUCTIONS=======//
            $this->SSS_monthly_deduction_half=0;
            $this->pagibig_monthly_deduction_half=0;
            $this->philhealth_monthly_deduction_half=0;
            //======SSS CALCULATION===============//

            if($this->emp->sss_id!=''){
                if($this->monthly_rate>16000){
                    $this->SSS_monthly_total=0.11*16000;
                    $this->SSS_monthly_deduction_half=16000*0.01815;
                    $this->SSS_monthly_employer_half=16000*0.03685;
                }
                else{
                    $this->SSS_monthly_total=0.11*$this->monthly_rate;
                    $this->SSS_monthly_deduction_half=$this->monthly_rate*0.01815;
                    $this->SSS_monthly_employer_half=$this->monthly_rate*0.03685;
                }
                if($this->monthly_rate<14750){
                    $this->SSS_ECER_half=5;
                }
                else $this->SSS_ECER_half=15;

            }
            else{
                $this->SSS_monthly_total=0;
                $this->SSS_monthly_deduction_half=0;
                $this->SSS_monthly_ER_half=0;
                $this->SSS_ECER_half=0;
            }


            //=======PAG IBIG CALCULATION======//
            if($this->emp->pagibig_id!=""){
                if($this->monthly_rate<=1500){
                    $this->pagibig_monthly_total=$this->monthly_rate*0.03;
                    $this->pagibig_monthly_deduction_half=$this->monthly_rate*0.005;
                    $this->pagibig_monthly_ER_half=$this->monthly_rate*0.01;
                }
                else{
                    if($this->monthly_rate>5000){
                        $this->pagibig_monthly_total=5000*0.04;
                        $this->pagibig_monthly_deduction_half=5000*0.01;
                        $this->pagibig_monthly_ER_half=5000*0.01;
                    }
                    else{
                        $this->pagibig_monthly_total=$this->monthly_rate*0.04;
                        $this->pagibig_monthly_deduction_half=$this->monthly_rate*0.01;
                        $this->pagibig_monthly_ER_half=$this->monthly_rate*0.01;
                    }
                }
            }

            //======PhilHealth=============//
            if($this->emp->philhealth_id!=''){
                if($this->monthly_rate<=10000){
                    $this->philhealth_monthly_total=275;
                    $this->philhealth_monthly_deduction_half=275/4;
                }
                else if($this->monthly_rate>=40000){
                    $this->philhealth_monthly_total=1100;
                    $this->philhealth_monthly_deduction_half=1100/4;
                }
                else{
                    $this->philhealth_monthly_total=$this->monthly_rate*0.0275;
                    $this->philhealth_monthly_deduction_half=$this->philhealth_monthly_total/4;
                }
            }
            //======Tax Computation=====//
            $this->netpay=$this->gross_earnings
                         -$this->SSS_monthly_deduction_half
                         -$this->pagibig_monthly_deduction_half
                         -$this->philhealth_monthly_deduction_half;
            //if($this->emp->job_status=='regular'){
                if(true){
                if($this->netpay<=20833){
                    $this->tax_status='M1';
                    $this->tax_withhold=0;
                }
                else if($this->netpay>20833/2&&$this->netpay<33333/2){
                    $this->tax_status='M2';
                    $this->tax_withhold=($this->netpay-20833/2)*0.2;
                }
                else if($this->netpay>=33333/2&&$this->netpay<66667/2){
                    $this->tax_status='M3';
                    $this->tax_withhold=($this->netpay-33333/2)*0.25+2500/2;
                }
                else if($this->netpay>=66667/2&&$this->netpay<1666667/2){
                    $this->tax_status='M4';
                    $this->tax_withhold=($this->netpay-66667/2)*0.3+10833.33/2;
                }
                else if($this->netpay>=1666667/2&&$this->netpay<666666/2){
                    $this->tax_status='M5';
                    $this->tax_withhold=($this->netpay-1666667/2)*0.32+40833.33/2;
                }  
                else{
                    $this->tax_status='M6';
                    $this->tax_withhold=($this->netpay-666666/2)*0.35+200833.33/2;
                }
                

            }
            
            $this->total_deductions=$this->SSS_monthly_deduction_half+
                                   $this->pagibig_monthly_deduction_half+
                                   $this->philhealth_monthly_deduction_half+
                                   $this->tax_withhold;
            $this->netpay=floor(($this->gross_earnings-$this->total_deductions)*100)/100;
        }
        private function get_allowable_leaves($db){
            $leaves=30;
            $current_year=date("Y");
            $id=$this->emp->emp_id;
            if(!$result=$db->query("SELECT absent_count from yearly_absent_tbl where emp_id=$id AND year='$current_year'"))
            die($db->error);
            $result=$result->fetch_assoc();
            if(!isset($result['absent_count'])) return $leaves;
            $leaves-=$result['absent_count'];
            return $leaves<0 ? 0: $leaves;
        }
        private function count_days($from_date,$to_date){
            $x=0;
            $_date=date_create($from_date->format("Y-m-d"));
            $interval=new DateInterval("P1D");
            while($_date<=$to_date){
                $w=$_date->format("w");
                if($w!=0)$x++;                
                $_date->add($interval);
            }          
            return $x;
        }
        private function get_hours_late($dates){
            $x=0;
            foreach($dates as $date){
                $hrs=intval(date_create($date['time_out'])->diff(date_create($date['time_in']))->format("%H"))-1;
                if($hrs<0)$hrs=0;
                if($hrs<8)$x+=8-$hrs;
            }
            return $x;
        }
        private function get_overtime_hours($dates){
            $x=0;
            foreach($dates as $date){
                $hrs=intval(date_create($date['time_out'])->diff(date_create($date['time_in']))->format("%H"))-1;
                if($hrs>8)
                $x+=$hrs-8;
            }
            return $x;
        }
    }
    
        //===============payslip class=====================//
?>  
                        

    <div class="container-fluid container-main row" >
        <form id="printPaySlip" class="row text-center" style="margin:auto">
        <div style="padding-left:480px">
            <div class="row" style="padding-top: 100px">
                <div class="col-md-2">
                    <select name="" class="form-control" id="agency">
                        <option disabled selected hidden>Select Agency</option>
                        <?php
                            $result=$db->query("SELECT * FROM agency_list");
                            while($row=$result->fetch_assoc()){
                                echo "<option value='".$row['agency_id']."'>".$row['agency_name']."</option>";
                            }
                        ?>                        
                    </select>
                </div>
                <div class="col-md-2 ">
                    <select name="" id="account" class="form-control">
                        <option disabled selected hidden>Select Account</option>
                    </select>
                </div>                     
                <div class="col-md-2">
                    <select name="emp_id" id="employee" class="form-control">
                        <option disabled selected hidden value="1">Employee</option>
                    </select>
                </div>        
            </div>
           <div class="row" style="padding-top: 10px">
                <div class="col-md-2">
                    <input type="month" name="month" class="form-control">
                </div>
                <div class="col-md-2">
                    <select name="cutoff" id="" class="form-control">
                        <option disabled selected hidden>Select Period</option>
                        <option value="1">1st</option>
                        <option value="2">2nd</option>
                    </select>
                </div>
            </div>
        </div>
           <div class="row" style="padding-top: 10px">
                <div class="row text-center" style="padding-top: 10px">
                    <div class="col-md-10 col-md-offset-1">
                        <button type="submit" class="btn btn-primary">PRINT</button>
                    </div>                            
                </div>
            </div>   
    </div>
            <script>
                $(document).ready(function(){
                    $("#printPaySlip").submit(function(e){
                        e.preventDefault();
                        var WindowObject = window.open('printPayslip.php?'+$(this).serialize(), "PrintWindow", "width=750,height=650,top=50,left=50,toolbars=no,scrollbars=yes,status=no,resizable=yes");
    			        WindowObject.focus();
                        return false;
                    });
                    $("#agency").change(function(){
                        $.ajax("payslipscript.php?agency_id="+$(this).val()).done(function(data){
                            $("#account")[0].innerHTML=data;
                        });
                        $("#account").change();
                    });
                    $("#account").change(function(){
                        $.ajax("payslipscript.php?account_id="+$(this).val()).done(function(data){
                            $("#employee")[0].innerHTML=data;
                        });
                    });
                });
            </script>
        </form>
    </body>
</html>
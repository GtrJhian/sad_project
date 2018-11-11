<?php
require_once "db.php";
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
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="/psa_hris/ASSETS/jquery-validation-1.17.0/dist/jquery-3.1.1.min.js" ></script>
    <script src="/psa_hris/ASSETS/bootstrap/js/bootstrap.min.js"></script>
    <script src="/psa_hris/ASSETS/DataTables/datatables.min.js"></script>
    <script src="/psa_hris/ASSETS/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="/psa_hris/ASSETS/buttons.html5.min.js"></script>

    <link rel="stylesheet" href="/psa_hris/ASSETS/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="/psa_hris/ASSETS/DataTables/datatables.min.css">   
    
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
    <link rel="stylesheet" href="/psa_hris/custom-style-payslip.css">
    <link rel="stylesheet" href="/psa_hris/custom-style.css">
    
    <script src="/psa_hris/ASSETS/jquery-validation-1.17.0/dist/jquery.validate.min.js" ></script> 
    <script src="/psa_hris/ASSETS/validate.js" ></script> 
    <script src="/psa_hris/ASSETS/jquery-highChartTable/jquery.highchartTable.js"></script>
    <script src="/psa_hris/ASSETS/jquery-highChartTable/highcharts.js"></script>
</head>
<body>
<?php
            if(isset($_REQUEST['emp_id'])&&isset($_REQUEST['cutoff'])&&isset($_REQUEST['month']))
            {
                $payslip=new payslip($_REQUEST['emp_id'],$_REQUEST['month'],$_REQUEST['cutoff'],$db);
        ?>
        <div class="card">  
        <br><br><br><br><br>
        <div class="row">
            <div class="col-xs-1"></div>
                <div class="col-md-5">
                    <div>
                        <div style="font-size: 20px"><strong>PSA</strong></div>
                    </div>

                    <div>
                        <div style="font-size: 16px">800 EDSA, QUEZON CITY 1200, ORTIGAS AVENUE, PASIG CITY</div>
                    </div>
                </div>
            <div class="col-xs-1"></div>

                <div class="col-md-5 pt-3">
                    <div style="font-size: 20px"><strong>PAY SLIP</strong></div>
                </div>
            </div>

            <!-- <hr> -->

            <div class="row hr">
            <div class="col-xs-1"></div>
                
                <div class="col-md-7 vline">           
                    <div class="row pl-5 pt-2 spec-row">
                        <div class="col-md-3">
                            
                            <div class="info" id="">Pay Period : <?=$payslip->payperiod?>
                                <!--
                                <select class="form-control input-sm col-md-2">
                                        <option>Cut-Off 01</option>
                                        <option>Cut-Off 02</option>
                                </select>
-->
                            </div> 
                            
                        </div>

                        <div class="col-md-5">
                            <div class="info" id="">SSS No. : <?=$payslip->emp->sss_id?>  </div>
                        </div>
                        <!-- <div class="col-1"></div> -->
                    </div>

                    <div class="row pl-5 pt-2">
                        <div class="col-md-5">
                            <div class="info">Location : CONFI PAYROLL  </div>
                        </div>
                    </div>

                    <div class="row pl-5 pt-2">
                        <div class="col-md-5">
                            <div class="info" id="department">Agency : <?=$payslip->emp->agency?>  </div>
                        </div>
                        <div class="col-md-5">
                            <div class="info" id="department">Account : <?=$payslip->emp->account?>  </div>
                        </div>
                    </div>

            <!-- <hr> -->

                    <div class="row pl-5 pt-2">
                        <div class="col-xs-4 pt-1 pb-1 second-row">
                            <div class="special_info" id=""><strong>Rate : <?=$payslip->daily_rate?></strong>    
                                
                            </div>
                        </div>
                    </div>

                   
                        
                    <!-- <hr> -->
                    <div class="row pl-5">
                        <div class="col-md-6">
                            <div class="row third-row vline">
                                <div class="t-head">&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp; <strong>EARNINGS</strong></div>
                            </div>

                            <div class="row pt-1 vline">
                                <div class="col pull-left">
                                    <div class="info">Regular Pay</div>
                                </div>
                                <div class="col pull-right">
                                    <div class="info" id=""><?=number_format($payslip->regular_pay,2,'.',',')?></div>
                                </div>
                            </div>

                            <div class="row pt-1 vline">
                                <div class="col pull-left">
                                    <div class="info">Overtime</div>
                                </div>
                                <div class="col pull-right">
                                    <div class="info" id=""><?=number_format($payslip->overtime_pay,2,'.',',')?></div>
                                </div>
                            </div>
                <!--
                            <div class="row pt-1 vline">
                                <div class="col pull-left">
                                    <div class="info">Leaves</div>
                                </div>
                                <div class="col pull-right">
                                    <div class="info" id="leave">0.00</div>
                                </div>
                            </div>
-->
<!--
                            <div class="row pt-1 vline">
                                <div class="col pull-left">
                                    <div class="info">Night Diff.</div>
                                </div>
                                <div class="col pull-right">
                                    <div class="info" id="">0.00</div>
                                </div>
                            </div>
-->
<!--
                            <div class="row pt-1 vline">
                                <div class="col-md pull-left">
                                    <div class="info">Day off</div>
                                </div>
                                <div class="col-md pull-right">
                                    <div class="info" id=""></div>
                                </div>
                            </div>
-->
                            <div class="row pt-1 vline">
                                <div class="col pull-left">
                                    <div class="info">Late</div>
                                </div>
                                <div class="col pull-right">
                                    <div class="info" id=""><?=number_format($payslip->late_deduction,2,'.',',')?></div>
                                </div>
                            </div>
                            <div class="row pt-1 vline">
                                <div class="col pull-left">
                                    <div class="info">Absent</div>
                                </div>
                                <div class="col pull-right">
                                    <div class="info" id=""><?=number_format($payslip->absents_deduction,2,'.',',')?></div>
                                </div>
                            </div>
<!--
                            <div class="row pt-1 vline">
                                <div class="col pull-left">
                                    <div class="info">PD 851</div>
                                </div>
                                <div class="col pull-right">
                                    <div class="info" id="">0.00</div>
                                </div>
                            </div>
-->
                        </div>
                    
                        <div class="col-md-6">
                            <div class="row third-row">
                                <div class="t-head">&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<strong>DEDUCTIONS</strong></div>
                            </div>

                            <div class="row pt-1">
                                <div class="col pull-left">
                                    <div class="info">Tax Widthhold</div>
                                </div>
                                <div class="col pull-right">
                                    <div class="info" id=""><?=number_format($payslip->tax_withhold,2,'.',',')?></div>
                                </div>
                            </div>
                
                            <div class="row pt-1">
                                <div class="col pull-left">
                                    <div class="info">SSS Premium</div>
                                </div>
                                <div class="col pull-right">
                                    <div class="info" id=""><?=number_format($payslip->SSS_monthly_deduction_half,2,'.',',')?></div>
                                </div>
                            </div>

                            <div class="row pt-1">
                                <div class="col pull-left">
                                    <div class="info">Pag-ibig</div>
                                </div>
                                <div class="col pull-right">
                                    <div class="info" id=""><?=number_format($payslip->pagibig_monthly_deduction_half,2,'.',',')?></div>
                                </div>
                            </div>
                            <div class="row pt-1">
                                <div class="col pull-left">
                                    <div class="info">Philhealth</div>
                                </div>
                                <div class="col pull-right">
                                    <div class="info" id=""><?=number_format($payslip->philhealth_monthly_deduction_half,2,'.',',')?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                        <div class="col-md-3 spec-row">
                            <div class="row pt-1">
                                <div class="info" id="">Employee No. : <?=$payslip->emp->emp_id?></div>
                            </div>

                            <div class="row pt-1 pl-5">
                                    <div class="info" id="">Employee Name : <?=$payslip->emp->emp_name?></div>
                            </div>

                            <div class="row  pl-1 pt-1">
                                <div class="info" id="">Pag-ibig No. : <?=$payslip->emp->pagibig_id?></div>
                            </div>

                            <div class="row  pl-1 pt-1">
                                <div class="info" id="">Philhealth No. : <?=$payslip->emp->philhealth_id?></div>
                            </div>

                            <div class="row pl-1 pt-1 right-div">
                                <div class="info" id="">Position: <?=$payslip->emp->job_title?></div>
                            </div>

                            <div class="row">
                                <div class="col-md text-center">
                                    <div class="spec-info">ACKNOWLEDGEMENT</div>
                                </div>
                            </div>

                            <div class="row pt-4">
                                <div class="col-md text-center">
                                    <div class="spec-info-sub">
                                        Received the amount stated
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md text-center">
                                    <div class="spec-info-sub">
                                        herewith and have no further claims
                                    </div>
                                </div>
                            </div>
                    
                            <div class="row">
                                <div class="col-md text-center">
                                    <div class="spec-info-sub">
                                        for services rendered
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row pt-5">
                                <div class="col-md text-center">
                                    <div class="info">____________________________________________</div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md text-center">
                                    <div class="info">Signature</div>
                                </div>
                            </div>

                        </div>
                </div>
                <div class="row pl-5">
                <div class="col-xs-1"></div>                    
                     <div class="col-md-4 last-row">
                        <div class="t-bottom" id=""><strong>GROSS EARNINGS :</strong> <?=number_format($payslip->gross_earnings,2,'.',',')?></div>
                    </div>
                    
                    <div class="col-md-3 last-row">
                        <div class="t-bottom" id=""><strong>TOTAL DEDUCTIONS :</strong> <?=number_format($payslip->total_deductions,2,'.',',')?></div>
                    </div>

                    <div class="col-md-3 last-row">
                        <div class="t-bottom" id=""><strong>NET PAY :</strong> <?=number_format($payslip->netpay,2,'.',',')?></div>
                    </div>
                </div>
            </div>
            </div>

        <button id="print" class="btn btn-primary">Print</button>
        </div>
        <script>
            $(document).ready(function(){
                //window.print();
                $("#print").click(function(){
                    $(this).hide();
                    window.print();
                });
            });
            
        </script>
    <?php }?>
    </body>
</html>
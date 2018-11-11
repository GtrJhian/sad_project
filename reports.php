<?php
session_start();
require_once "db.php";
include_once 'header.php';
    $r=$db->query("
    SELECT 
	(SELECT COUNT(*) FROM employee_list WHERE emp_man_SSS IS NOT NULL AND NOT emp_man_SSS='') AS sss_active,
    (SELECT COUNT(*) FROM employee_list WHERE emp_man_SSS IS NULL OR emp_man_sss='') AS sss_inactive,
    (SELECT COUNT(*) FROM employee_list WHERE emp_man_philhealth IS NOT NULL AND NOT emp_man_philhealth='') as philhealth_active,
    (SELECT COUNT(*) FROM employee_list WHERE emp_man_philhealth IS NULL OR emp_man_philhealth='') as philhealth_inactive,
    (SELECT COUNT(*) FROM employee_list WHERE emp_man_pagibig IS NOT NULL AND NOT emp_man_pagibig='') as pagibig_active,
    (SELECT COUNT(*) FROM employee_list WHERE emp_man_pagibig IS NULL OR emp_man_pagibig='') as pagibig_inactive,
    (SELECT COUNT(*) FROM employee_list WHERE emp_man_nbi_expDate>NOW()) AS nbi_active,   
    (SELECT COUNT(*) FROM employee_list WHERE emp_man_nbi_expDate<=SUBSTRING(NOW(),1,10) OR emp_man_nbi_expDate IS NULL OR emp_man_nbi_expDate='') as nbi_inactive,
    (SELECT COUNT(*) FROM employee_list WHERE 
        emp_man_SSS IS NOT NULL AND NOT emp_man_SSS='' AND
        emp_man_philhealth IS NOT NULL AND NOT emp_man_philhealth='' AND
        emp_man_pagibig IS NOT NULL AND NOT emp_man_pagibig='' AND
        emp_man_nbi_expDate>NOW()
    )as mandatory_complete,
    (SELECT COUNT(*) FROM employee_list WHERE
        emp_man_SSS IS NULL OR emp_man_sss='' OR
        emp_man_philhealth IS NULL OR emp_man_philhealth='' OR
        emp_man_pagibig IS NULL OR emp_man_pagibig='' OR
        emp_man_nbi_expDate<=SUBSTRING(NOW(),1,10) OR emp_man_nbi_expDate IS NULL OR emp_man_nbi_expDate=''
    )AS mandatory_incomplete,
    (SELECT COUNT(*) FROM employee_list WHERE emp_sex='M')as male,
    (SELECT COUNT(*) FROM employee_list WHERE emp_sex='F')as female,
    (SELECT COUNT(*) FROM employee_list WHERE (date(NOW())-date(emp_bday))<210000)as age_20_below,
    (SELECT COUNT(*) FROM employee_list WHERE (date(NOW())-date(emp_bday))>=210000 AND (date(NOW())-date(emp_bday))<310000) as age_21_30,
    (SELECT COUNT(*) FROM employee_list WHERE (date(NOW())-date(emp_bday))>=310000 AND (date(NOW())-date(emp_bday))<410000) as age_31_40,
    (SELECT COUNT(*) FROM employee_list WHERE (date(NOW())-date(emp_bday))>=410000 AND (date(NOW())-date(emp_bday))<510000) as age_41_50,
    (SELECT COUNT(*) FROM employee_list WHERE (date(NOW())-date(emp_bday))>=510000) as age_51_above,
    (SELECT COUNT(*) from employee_list WHERE emp_status=1) as emp_active,
    (SELECT COUNT(*) from employee_list WHERE emp_status=0) as emp_inactive
    ")->fetch_all(MYSQLI_ASSOC);
   $r[0]['date']=date("Y-m")."-01";
   $types="";
   for($ctr=0; $ctr<count($r[0]);$ctr++){
       $types.="s";
   }
    
    
    if(!$db->insert("charts",$r[0],$types)){
        //function update_one($table, $data, $types, $idname,$id){
        $r=$r[0];
        $id=array_pop($r);
        $types="";
        for($ctr=0; $ctr<count($r)+1;$ctr++){
             $types.="s";
        }
        $db->update_one("charts",$r,$types,"date",$id);
    }
    $date=date("Y-m")."-01";
    if(isset($_GET['date'])){
        $date=$_GET['date']."-01";
    }
    if(!$r=$db->query("SELECT * FROM charts where date='$date'")){
        $date=date("Y-m")."-01";
        $r=$db->query("SELECT * FROM charts where date='$date'");
    }
    $r=$r->fetch_assoc();
      
?>
    <div class="container-fluid container-main">
        <div class="card">
            <div class="header">
                <h1>Reports</h1>
                <form class ="form-inline">
                <div class="form-group">
                <!--
                <select class="form-control">
                    <option disabled selected hidden>Select Agency</option>
                    <option></option>
                </select>

                <select class="form-control">
                    <option disabled selected hidden>Select Account</option>
                    <option></option>
                </select>
                -->
                <form method="get" action="reports.php" id="frmreports">
                    <input class="form-control" type="month" name='month' id='month'>
                </form>
                <script>
                    $(document).ready(function(){
                        $("#month").change(function(){
                            window.location.href="/psa_hris/reports.php\?date="+$(this).val();
                        });
                    });
                </script>
                </div>
                </form>   
                
            </div> 
                <div>    
                    <div class ="row">
                        <div id="inc_man" style="width:40%; height:400px;" class="col-lg-6"></div>
                        <div id="man_status" style="width:40%; height:400px;" class="col-lg-6"></div>
                    </div>

                    <br>
                    
                    <div class="row">
                        <div id="emp_gender" style="width:40%; height:400px;" class="col-lg-6"></div>
                        <div id="emp_age" style="width:40%; height:400px;"  class="col-lg-6"></div>
                    </div>

                    <br>
                    <div id="emp_status" style="width:40%; height:400px;" ></div>
                </div>  
                <div class="body">
            </div>
        </div>
    </div>
</body>
</html>

<script>
    $(function () {
        $('#inc_man').highcharts({
            chart: {
                type: 'bar'
            },
            title: {
                text: 'Incomplete Mandatories'
            },
            xAxis: {
                categories: ['SSS', 'PhilHealth', 'PAGIBIG', 'NBI Clearance']
            },
            yAxis: {
                title: {
                    text: 'Number of Employees'
                }
            },
            series: [{
                name: 'Active',
                data: [<?=$r['sss_active']?>, <?=$r['philhealth_active']?>, <?=$r['pagibig_active']?>, <?=$r['nbi_active']?>]
            }, {
                name: 'Inactive',
                data: [<?=$r['sss_inactive']?>, <?=$r['philhealth_inactive']?>, <?=$r['pagibig_inactive']?>, <?=$r['nbi_inactive']?>]
            }],
        });

        $('#emp_status').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'Employment Status'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                }
            },
            series: [{
                name: 'Population',
                colorByPoint: true,
                data: [{
                    name: 'Active',
                    y: <?=$r['emp_active']?>,
                    sliced: true,
                    selected: true
                }, {
                    name: 'Inactive',
                    y: <?=$r['emp_inactive']?>
                }]
            }]
        });

        $('#emp_gender').highcharts({
            chart : {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title : {
                text: 'Gender'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                    enabled: false
                    },
                     showInLegend: true
                    }
            },
            series: [{
                name: 'Gender',
                colorByPoint: true,
                data: [{
                    name: 'Male',
                    y: <?=$r['male']?>,
                    sliced: true,
                    selected: true
                }, {
                name: 'Female',
                y: <?=$r['female']?>
                }]
            }]
        });

        $('#man_status').highcharts({
            chart : {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title : {
                text: 'Mandatory Status'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                    enabled: false
                    },
                     showInLegend: true
                    }
            },
            series: [{
                name: 'Status',
                colorByPoint: true,
                data: [{
                    name: 'Complete',
                    y: <?=$r['mandatory_complete']?>,
                    sliced: true,
                    selected: true
                }, {
                name: 'Incomplete',
                y: <?=$r['mandatory_incomplete']?>
                }]
            }]
        });

        $('#emp_age').highcharts({
            chart : {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title : {
                text: 'Employee Age'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                    enabled: false
                    },
                     showInLegend: true
                    }
            },
            series: [{
                name: 'Age',
                colorByPoint: true,
                data: [{
                    name: '20 - below',
                    y: <?=$r['age_20_below']?>,
                    sliced: true,
                    selected: true
                }, {
                name: '21-30',
                y: <?=$r['age_21_30']?>
                }, {
                name: '31-40',
                y: <?=$r['age_31_40']?>
                }, {
                name: '41-50',
                y: <?=$r['age_41_50']?>
                }, {
                name: '51 - above',
                y: <?=$r['age_51_above']?>
                }]
            }]
        });

    });
</script>
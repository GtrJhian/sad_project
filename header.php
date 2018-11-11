<?php

  //session_start();
?>

<!DOCTYPE html>
<html>
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

<nav class="navbar navbar-default navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"  data-target="#this-navbar" aria-expanded="false">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
      </button>

      <a href="/psa_hris/session.php"><img class="navbar-brand" src="/psa_hris/images/psa_logo.png"></a>
    </div>

    <div class="collapse navbar-collapse" id="this-navbar">
      <ul  class="nav navbar-nav navbar-right">
      <?php if($_SESSION['access']&0b111100){?>
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">Dashboards
          <span class="caret"></span></a>
          <ul class="dropdown-menu dropdown-menu-left">
            <?php 
              if($_SESSION['access']&0b100000){
            ?> 
            <li><a href="/psa_hris/USERVIEWS/operations.php">Operations</a></li>
              <?php }if($_SESSION['access']&0b000100){?>
            <li><a href="/psa_hris/USERVIEWS/accounting.php">Accounting</a></li>
            <li><a href="/psa_hris/payslip.php">Payslip</a></li>
              <?php }if($_SESSION['access']&0b001000){?>
            <li><a href="/psa_hris/USERVIEWS/humanResources.php">Human Resources</a></li>
              <?php }if($_SESSION['access']&0b010000){?>
            <li><a href="/psa_hris/USERVIEWS/compBen.php">Compensation and Benefit </a></li> 
              <?php }?>
          </ul>
        </li>
        <?php }?>
        <?php if($_SESSION['access']&2){?>
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">IMS
          <span class="caret"></span></a>
          <ul class="dropdown-menu dropdown-menu-left">
            <li><a href="/psa_hris/employee.php">Employee</a></li>
            <li><a href="/psa_hris/ACCOUNTS/accounts.php">Account</a></li>
            <li><a href="/psa_hris/agency.php">Agency</a></li>
            <li><a href="/psa_hris/reports.php">Reports</a></li>
          </ul>
        </li>

        <?php }if($_SESSION['access']&1){?>
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">Admin
          <span class="caret"></span></a>
          <ul class="dropdown-menu dropdown-menu-left">
            <li><a href="/psa_hris/ams.php">User Management</a></li>
            <li><a href="/psa_hris/time-tracking.php">Time Tracking</a></li>
            <li><a href="/psa_hris/view-time.php">View Time Records</a></li>
            <li><a href="/psa_hris/logs.php">Logs</a></li>            
          </ul>
        </li>
        <?php }?>

        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">Hello, <?php echo $_SESSION['username'] ?>!
          <span class="caret"></span></a>
          <ul class="dropdown-menu dropdown-menu-left">
            <li><a data-toggle="modal" data-target="#changePword">Change Password</a></li>
            <li><a href="/psa_hris/session.php?logout=1">Log-out</a></li>
        </li>

      </ul>
  </div>

  </div>
</nav>  
<!--Change Password Modal-->
  <div id="changePword" class="modal fade inline" data-backdrop="static" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
                          
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h3 class="modal-title pull-left"><strong>Change Password</strong></h3>
        </div>

        <div class="modal-body">
          <form action="/psa_hris/amsScript.php" id="frmChangePassword"name="a">
            <label><strong>Username :</strong> <?=$_SESSION['username']?></label><br><br>
            
            <label>Old Password : </label><br>
            <input class="form-control oldPasswordField" type="password" required><br>

            <label>New Password : </label><br>
            <input name="newPassword" class="form-control changePasswordField" type="password" required minlength=8><br>

            <label>Confirm Passsword : </label><br>
            <input class="form-control changePasswordField" type="password" required><br>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary" name="a" value="changePW" > 
                <strong class="glyphicon glyphicon-check" style="margin-right: 4px;"></strong>
                Save
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script>
    $(document).ready(function(){
      $(".changePasswordField").change(function(){
      if($(".changePasswordField")[0].value!=$(".changePasswordField")[1].value){
            $(".changePasswordField")[1].setCustomValidity("Password Mismatch");
            //$("#addUser").prop("disabled",true);
        }
        else{            
            $(".changePasswordField")[1].setCustomValidity("");
            $("#addUser").prop("disabled",false);
        }
      });
      $(".oldPasswordField").change(function(){
        $.ajax({
          url: "/psa_hris/amsScript.php?a=checkPW&oldPassword="+$(".oldPasswordField")[0].value,
          context: document.body
        }).done(function(data) {
          if(data=='0'){
            $(".oldPasswordField")[0].setCustomValidity("Wrong password");
          }
          else{
            $(".oldPasswordField")[0].setCustomValidity("");
          }
          $("#frmChangePassword")[0].checkValidity();
        });
      });
    });
    
  </script>
<!--Change Password Modal-->
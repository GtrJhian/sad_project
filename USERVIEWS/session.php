<?php
    require_once '../db.php';
    session_start();
    if(isset($_POST['login'])){
        foreach($db->select_all("users") as $assoc){
            if($assoc['username']==$_POST['username']&&$assoc['password']==hash("sha512",$_POST['password'])) {
                $_SESSION['username']=$assoc['username'];
                $_SESSION['access']=$assoc['access'];
                $_SESSION['password']=$assoc['password'];
                $_SESSION['id']=$assoc['id'];
                $pages=["operations.php","compBen.php","humanResources.php","accounting.php","../employee.php","../ams.php"];
                for($ctr=0; $ctr<6; $ctr++){
                    if(($_SESSION['access']&0b100000>>$ctr)){
                    header("Location: ".$pages[$ctr]);
                    exit();
                    }
                }
            }
        }
    }
    if(isset($_GET['logout'])){
        session_destroy();
        header('Location: ../login.php');
        exit();
    }
    if(!isset($_SESSION['username'])){
        header('Location: ../login.php');
        //header('location: pw.php');
        exit();
    }
    if(isset($_SESSION['username']))  {
        //echo $_SESSION['password'];
        //header('location: header.php');
        /*
        $pages=["operations.php","compBen.php","humanResources.php","accounting.php","employee.php","ams.php"];
        for($ctr=0; $ctr<6; $ctr++){
            if(($_SESSION['access']&0b100000>>$ctr)){
            header("Location: ".$pages[$ctr]);
            exit();
            }
        }
        */
        $pages=["USERVIEWS/operations.php","USERVIEWS/compBen.php","USERVIEWS/humanResources.php","USERVIEWS/accounting.php","employee.php","USERVIEWS/ams.php"];
        for($ctr=0; $ctr<6; $ctr++){
            if(($_SESSION['access']&0b100000>>$ctr)){
                header("Location: ".$pages[$ctr]);
                exit();
            }
        }
    }
?>
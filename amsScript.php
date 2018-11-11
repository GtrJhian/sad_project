<?php
    require_once "db.php";
    session_start();
    $action=$_REQUEST['a'];
    if($action=='addUser'){
        
        $permissions=0;
        foreach($_REQUEST['permissions'] as $permission){
            $permissions|=(1<<intval($permission));
        }

        $query="INSERT INTO users(username,password,email,access)
                VALUES(?,?,?,?)";
        $data=[
            "User Name"=>$_REQUEST['username'],
            "Password"=>hash("sha512",$_REQUEST['password']),
            "Email"=>$_REQUEST['email'],
            "Permissions"=>$permissions
        ];
        $db->prepared_query($query,$data,"sssi");
        $db->log(0,"Users",$data,'user_id',$db->insert_id);
        header("location: ams.php");
    }
    else if($action=='checkPW'){
        $query="SELECT password FROM users where id=?";
        $data=[$_SESSION['id']];
        $oldPassword=$db->prepared_query($query,$data,"i")->fetch_assoc()['password'];
        if($oldPassword==hash("sha512",$_REQUEST['oldPassword'])){
            echo 1;
        }
        else echo 0;
    }
    else if($action=="changePW"){
        $query="UPDATE users SET password=? WHERE id=?";
        $data=[
            hash("sha512",$_REQUEST['newPassword']),
            $_SESSION['id']
        ];
        $db->prepared_query($query,$data,"si");
        session_destroy();
        header("location: /psa_hris/login.php");
    }
    else if($action=="resetRequest"){
        $query="SELECT username,password from users WHERE email=?";
        $result=$db->prepared_query($query,[$_REQUEST['email']],"s")->fetch_assoc();
        $email=$_REQUEST['to_email']=$_REQUEST['email'];
        $_REQUEST['to_name']=$result['username'];
        $_REQUEST['subject']="Password Reset";
        $hash=$result['password'];
        $body="<form action='http://localhost/psa_hris/amsScript.php' method='post' name='a'>
            <input type='hidden' value='$hash' name='hash'>
            <input type='hidden' value='$email' name='email'>
            <input type='text' name='newPassword'>
            <button type='submit' name='a' value='resetPW'>Set Password</button>
        </form>";
        $_REQUEST['body']=$body;
        include("email/sendEmail.php");
    }
    else if($action=="resetPW"){
        $query="SELECT * FROM users where password=? AND email=?";
        $result=$db->prepared_query($query,[$_REQUEST['hash'],$_REQUEST['email']],"ss");
        if(!$result->fetch_assoc()) die("Token Expired");
        $query="UPDATE users SET password=? where password=? AND email=?";
        $db->prepared_query($query,[hash("sha512",$_REQUEST['newPassword']),$_REQUEST['hash'],$_REQUEST['email']],"sss");
        echo "Password Reset Successful";
        echo "<br>Click <a href='/psa_hris/login.php'>here</a> to login.";
    }
    else{
        echo "Unknown Action";
    }
?>

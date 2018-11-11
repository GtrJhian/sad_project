<?php
    session_start();
    if(isset($_SESSION['id'])) session_destroy();
?>
<html>
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="/psa_hris/ASSETS/bootstrap/css/bootstrap.min.css">
        <script src="/psa_hris/ASSETS/jquery-validation-1.17.0/dist/jquery-3.1.1.min.js" ></script>
        <script src="/psa_hris/ASSETS/jquery-validation-1.17.0/dist/jquery.validate.min.js" ></script>
        <script src="/psa_hris/ASSETS/bootstrap/js/bootstrap.min.js"></script>
        <script src="/psa_hris/ASSETS/jquery-validation-1.17.0/dist/additional-methods.min.js"></script>
<script>
$(document).ready(function(){

    $.validator.setDefaults({
    errorClass:"help-block",
    highlight: function(element){
        $(element)
        .closest('.form-group')
        .addClass('has-error');
    },
    unhighlight: function(element){
        $(element)
        .closest('.form-group')
        .removeClass('has-error');
    }
    });

    $("#login-form").validate({
        rules: {
            username: {
                required: true,
            },
            password: {
                required: true,
            }
            },

        messages: {
            username: {
                required: "Usename is required",
                minlength: "Incorrect username"
            },
            password: {
                required: "Password is required",
                minlength: "Incorrect password",
            }
        }
        });
});
</script>

<script>
    $(document).ready(function(){
    $('#password').keypress(function(e) { 
        var s = String.fromCharCode( e.which );

        if((s.toUpperCase() === s && s.toLowerCase() !== s && !e.shiftKey) ||
           (s.toUpperCase() !== s && s.toLowerCase() === s && e.shiftKey)){
            if($('#capsalert').length < 1) $(this).after('<b id="capsalert">CapsLock is on!</b>');
        } else {
            if($('#capsalert').length > 0 ) $('#capsalert').hide();
        }
    });
});

</script>

        <link rel="stylesheet" href="custom-style.css">

        <title>Login</title>
</head>
<body>
    <div class="jumbotron">
        <img class="center-block" src="images/psa_logo.png">
    </div>
    <div class=" container-fluid">
        
    <div class="row" style="margin-top: 30px;">
        <div class="col-sm-offset-3 col-sm-6 col-sm-offset-3">
        <div class="signup-form">
            <form name="login" id="login-form" action="session.php" method="post" >
                <h3 class="text-center h3" style="margin-bottom: 50px;">Human Resources Information System</h3>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>       
                        <input id="username" class="form-control" type="text" name="username" placeholder="Username" >
                    </div>
                    
                </div>
                <div class="form-group">
                    <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                    <input id="password" class="form-control" type="password" name="password" placeholder="Password">
                    </div>
                </div>
                    <input type="submit" class="btn btn-primary form_btn center-block"  value="Login" name="login">
                    
                <a data-target="#forgotPassword" data-toggle="modal">Forgot password?</a>
            </form>
            <div id="forgotPassword" class="modal fade inline" data-backdrop="static" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                          
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h3 class="modal-title pull-left"><strong>Change Password</strong></h3>
                        </div>

                        <div class="modal-body">
                            <form action="/psa_hris/amsScript.php" id="frmResetPassword"name="a"> 
                                <label>Email : </label><br>
                                <input class="form-control email" type="email" name="email" required><br>
                                <div class="modal-footer">
                                <button type="submit" class="btn btn-primary" name="a" value="resetRequest" > 
                                    <strong class="glyphicon glyphicon-check" style="margin-right: 4px;"></strong>
                                    Send
                                </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
    </div>

    


</body>
</html>


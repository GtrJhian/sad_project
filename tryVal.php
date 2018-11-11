<html>
<head>
<style type="text/css">
label.error { 
    float: none; 
    color: red; 
    padding-left: .5em; 
    vertical-align: top; 
}
</style>
    <script src="/psa_hris/ASSETS/jquery-validation-1.17.0/dist/jquery-3.1.1.min.js" ></script>
    <script src="/psa_hris/ASSETS/jquery-validation-1.17.0/dist/jquery.validate.min.js" ></script>
    <script>
        $(function(){
            $("#trialForm").validate({
                rules: {
                    title: {
                        required: true,
                        minlength: 5
                    }
                }
            });
        });
    </script>
</head>
<body>
    <form action="login.php" id="trialForm" name="" method="GET">
        <p>
            <label for="title">Title: &nbsp; </label>
            <input type="text" name="title" id="">
        </p>
        <p>
            <input class="submit" type="submit" value="Submit"/>
        </p>
    </form>
</body>
</html>
<?php
    session_start();
    if(!($_SESSION['access']&1)) {
        header('location: session.php');
    } else {
        require_once 'db.php';
        include_once "header.php";
    if(isset($_GET["action"])){
        $access=$_GET['access'];
        $id=$_GET['id'];
        $db->query("UPDATE users SET access=$access WHERE id=$id");
    }
?>

    <div class="container-fluid container-main">
    <div class="card">
        <div class="header">
            <h1 class="h1">User Management</h1>
            <button type="button" id="addUserBtn" class="btn btn-primary pull-right"
                data-toggle="modal" data-target="#addUserModal">Add User</button>
        </div>
    <div class="body">
            <form method="post" action="ams.php" name="accessForm">            
                <table class="table table-hover table-bordered nowrap" id="ope">
                    <thead>
                    <tr>
                        <th>USERNAME</th>
                        <th>OPS</th>
                        <th>COMPBEN</th>
                        <th>HR</th>
                        <th>ACCO</th>
                        <th>ADMIN</th>
                        <th>SA</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php
                            $x=0;
                            foreach($db->select_all("users") as $assoc){
                        ?>
                            <tr>
                                <td><?php echo $assoc['username']?></td>
                                <td><input class="checkbox" type='checkbox' disabled <?php if($assoc['access']&0b100000) echo "checked=true";?>></td>
                                <td><input class="checkbox" type='checkbox' disabled <?php if($assoc['access']&0b010000) echo "checked=true";?>></td>
                                <td><input class="checkbox" type='checkbox' disabled <?php if($assoc['access']&0b001000) echo "checked=true";?>></td>
                                <td><input class="checkbox" type='checkbox' disabled <?php if($assoc['access']&0b000100) echo "checked=true";?>></td>
                                <td><input class="checkbox" type='checkbox' disabled <?php if($assoc['access']&0b000010) echo "checked=true";?>></td>
                                <td><input class="checkbox" type='checkbox' disabled <?php if($assoc['access']&0b000001) echo "checked=true";?>></td>
                                <td hidden><input hidden type='number'  name='access' id="access<?php echo $assoc['id']?>"value=<?php echo $assoc['access']?> /></td>
                                <td>
                                    <input type="button" class="sample btn-primary" value="Edit" id="editbtn<?php echo $x ?>" onclick="enableCheckBox(<?php echo $x?>)">
                                    <input type='button' class="sample btn-danger" value="Cancel" hidden  id="cnclbtn<?php echo $x?>" onclick="disableCheckBox(<?php echo $x?>)">
                                    <input type='button' class="sample btn-info" value="Save" id="savebtn<?php echo $x ?>"hidden onclick="save(<?php echo $assoc['id']?>,<?php echo $x++?>)">
                                </td>
                            </tr>
                        <?php   
                            }
                        ?>
                    </tbody>
                </table>
                <hr>
            </form>

            <!-- Modal -->
            <div class="modal fade" id="addUserModal" role="dialog" data-backdrop="static">
                <div class="modal-dialog">
                
                <div class="modal-content">

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h3>Add New User</h3>
                    </div>

                    <div class="modal-body">
                        <form action="amsScript.php" name="a">
                            <label>Username : </label><br>
                            <input class="form-control" type="text" name="username" required minlength=5><br>

                            <label>Password : </label><br>
                            <input id="password1" class="form-control passwordField" type="password" name="password" required minlength=8><br>

                            <label>Confirm Password : </label><br>
                            <input id="password2" class="form-control passwordField" type="password" required><br>
                            <span id="passwordError"></span>
                            <br>
                            
                            <label>Email : </label><br>
                            <input class="form-control" type="email" name="email" required><br>

                            <label for="">Level of Access :</label>
                            <div style="padding-left: 50px;">
                            
                                <label><input type="checkbox" name="permissions[]" value="5">Opertions</label><br>
                                <label><input type="checkbox" name="permissions[]" value="4">Compensation & Benefit</label><br>
                                <label><input type="checkbox" name="permissions[]" value="3">Human Resources</label><br>
                                <label><input type="checkbox" name="permissions[]" value="2">Accounting</label><br>
                                <label><input type="checkbox" name="permissions[]" value="1">Admin</label><br>
                                <label><input type="checkbox" name="permissions[]" value="0">Super Admin</label><br>
                            </div>
                            <div class="modal-footer">
                                <button id="addUser type="submit" class="btn btn-primary" value="addUser" name="a">Add User</button>
                            </div>
                        </form>
                    </div>
                    
                </div>
                
                </div>
            </div>
 </body>
<?php } ?>
<script>
    function enableCheckBox(y){
                var x=document.getElementsByClassName("checkbox");
                for(ctr=0; ctr<6; ctr++){
                    x[ctr+(y*6)].disabled=false;
                }
                document.getElementById("savebtn"+y).hidden=false;
                document.getElementById("cnclbtn"+y).hidden=false;
                document.getElementById("editbtn"+y).hidden=true;
                //document.getElementById("savebtn"+y).hidden=false;
            }
            function disableCheckBox(y){
                var x=document.getElementsByClassName("checkbox");
                for(ctr=0; ctr<6; ctr++){
                    x[ctr+(y*6)].disabled=true;
                }
                document.getElementById("savebtn"+y).hidden=true;
                document.getElementById("cnclbtn"+y).hidden=true;
                document.getElementById("editbtn"+y).hidden=false;
            }
            function save(id,y){
                disableCheckBox(y);
                var x=document.getElementsByClassName("checkbox");
                var value=0;
                for(ctr=0; ctr<6; ctr++){
                    if(x[ctr+(y*6)].checked) value+=0b100000>>ctr;
                }
                console.log(value);
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        //document.getElementById("demo").innerHTML = this.responseText;
                    }
                };
                xhttp.open("GET", "ams.php?action=update&id="+id+"&access="+value, true);
                xhttp.send();
            }
</script>

<script>
$(document).ready(function() {
    $('#addUserBtn').click(function() {
    });
    $(".passwordField").change(function(){
        if($(".passwordField")[0].value!=$(".passwordField")[1].value){
            $(".passwordField")[1].setCustomValidity("Password Mismatch");
            //$("#addUser").prop("disabled",true);
        }
        else{            
            $(".passwordField")[1].setCustomValidity("");
            $("#addUser").prop("disabled",false);
        }
    });
});
    

</script>
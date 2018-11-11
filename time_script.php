<?php
    require 'db.php';
    echo json_encode($_REQUEST);
    if(!$db->insert('employee_time_rec',$_REQUEST,'sissi'))
    die($db->error);
?>
<?php

$dbDetails = array(
            'host' => 'localhost',
            'user' => 'root',
            'pass' => '',
            'db'   => 'psa_db'
        );

 if(isset($_GET['fetch'])){
    $fetch=$_GET['fetch'];

    if($fetch=='operations'){   


        $table = <<<EOT
         (
           SELECT G.agency_name, C.account_principal, E.emp_id,
            E.emp_lname, E.emp_fname, E.emp_mname,
            E.emp_status, E.emp_dateHired, E.emp_endDate,
            E.emp_man_SSS, E.emp_man_PhilHealth, E.emp_man_PAGIBIG,
            E.emp_man_NBI_expDate, E.emp_man_polClear, E.emp_man_brgyClear
            FROM employee_list as E
            INNER JOIN account_list C ON C.account_id = E.account_id
            INNER JOIN agency_list G ON G.agency_id = C.agency_id
         ) temp
EOT;

        $primaryKey = 'emp_id';

        $columns = array(

            array( 'db' => 'agency_name', 'dt' => 0 ),
            array( 'db' => 'account_principal',  'dt' => 1 ),
            array( 'db' => 'emp_lname',     'dt' => 2 ),
            array( 'db' => 'emp_fname',     'dt' => 3 ),
            array( 'db' => 'emp_status',     'dt' => 4 ),
            array( 'db' => 'emp_dateHired',    'dt' => 5 ),
            array( 'db' => 'emp_endDate',    'dt' => 6 ),
            array( 'db' => 'emp_man_SSS',    'dt' => 7 ),
            array( 'db' => 'emp_man_PhilHealth',    'dt' => 8 ),
            array( 'db' => 'emp_man_PAGIBIG',    'dt' => 9 ),
            array( 'db' => 'emp_man_NBI_expDate',    'dt' => 10 ),
            array( 'db' => 'emp_man_polClear',    'dt' => 11 ),
            array( 'db' => 'emp_man_brgyClear',    'dt' => 12 ),
            
        );

        require( $_SERVER["DOCUMENT_ROOT"].'/psa_hris/ssp.class.php' );

        echo json_encode(
            SSP::simple( $_GET, $dbDetails, $table, $primaryKey, $columns )
        );

    }else if($fetch=='accounting'){   
        $table = <<<EOT
                 (
                   SELECT G.agency_name, C.account_principal, E.emp_id,
                    E.emp_lname, E.emp_fname, E.emp_mname,
                    E.emp_type, E.emp_dateHired,
                    E.emp_man_SSS, E.emp_man_PhilHealth, E.emp_man_PAGIBIG,
                    E.emp_man_NBI_expDate, E.emp_man_polClear, E.emp_man_brgyClear
                    FROM employee_list as E 
                    INNER JOIN account_list C ON C.account_id = E.account_id
                    INNER JOIN agency_list G ON G.agency_id = C.agency_id
                 ) temp
EOT;

        $primaryKey = 'emp_id';

        $columns = array(
            array( 'db' => 'agency_name', 'dt' => 0 ),
            array( 'db' => 'account_principal',  'dt' => 1 ),
            array( 'db' => 'emp_lname',     'dt' => 2 ),
            array( 'db' => 'emp_fname',     'dt' => 3 ),
            array( 'db' => 'emp_type',     'dt' => 4 ),
            array( 'db' => 'emp_dateHired',    'dt' => 5 ),
            array( 'db' => 'emp_man_SSS',    'dt' => 6 ),
            array( 'db' => 'emp_man_PhilHealth',    'dt' => 7 ),
            array( 'db' => 'emp_man_PAGIBIG',    'dt' => 8 ),
            array( 'db' => 'emp_man_NBI_expDate',    'dt' => 9 ),
            array( 'db' => 'emp_man_polClear',    'dt' => 10 ),
            array( 'db' => 'emp_man_brgyClear',    'dt' => 11 ),
            array( 'db' => 'emp_id', 'dt' => 12 ),
            
        );

        require( $_SERVER["DOCUMENT_ROOT"].'/psa_hris/ssp.class.php' );

        echo json_encode(
            SSP::simple( $_GET, $dbDetails, $table, $primaryKey, $columns )
        );

        }else if($fetch=='hr'){   
                $table = <<<EOT
                         (
                           SELECT G.agency_name, C.account_principal, E.emp_id,
                            E.emp_lname, E.emp_fname, E.emp_mname,
                            E.emp_status, E.emp_bday, E.emp_sex, E.emp_conNum,
                            E.emp_address
                            FROM employee_list as E
                            INNER JOIN account_list C ON C.account_id = E.account_id
                            INNER JOIN agency_list G ON G.agency_id = C.agency_id
                         ) temp
EOT;

                $primaryKey = 'emp_id';

                $columns = array(
                    array( 'db' => 'agency_name', 'dt' => 0 ),
                    array( 'db' => 'account_principal',  'dt' => 1 ),
                    array( 'db' => 'emp_lname',     'dt' => 2 ),
                    array( 'db' => 'emp_fname',     'dt' => 3 ),
                    array( 'db' => 'emp_status',     'dt' => 4 ),
                    array( 'db' => 'emp_sex',    'dt' => 5 ),
                    array( 'db' => 'emp_conNum',    'dt' => 6 ),
                    array( 'db' => 'emp_address',    'dt' => 7 ),
                    array( 'db' => 'emp_id', 'dt' => 8 ),
                    
                );

                require( $_SERVER["DOCUMENT_ROOT"].'/psa_hris/ssp.class.php' );

                echo json_encode(
                    SSP::simple( $_GET, $dbDetails, $table, $primaryKey, $columns )
                );
        }else if($fetch=='cb'){   
                $table = <<<EOT
                         (
                           SELECT G.agency_name, C.account_principal, E.emp_id,
                            E.emp_lname, E.emp_fname, E.emp_mname,
                            E.emp_pos, E.emp_bday,
                            E.emp_man_SSS, E.emp_man_PhilHealth, E.emp_man_PAGIBIG
                            FROM employee_list as E
                            INNER JOIN account_list C ON C.account_id = E.account_id
                            INNER JOIN agency_list G ON G.agency_id = C.agency_id
                         ) temp
EOT;

                $primaryKey = 'emp_id';

                $columns = array(
                    array( 'db' => 'agency_name', 'dt' => 0 ),
                    array( 'db' => 'account_principal',  'dt' => 1 ),
                    array( 'db' => 'emp_lname',     'dt' => 2 ),
                    array( 'db' => 'emp_fname',     'dt' => 3 ),
                    array( 'db' => 'emp_pos',     'dt' => 4 ),
                    array( 'db' => 'emp_bday',    'dt' => 5 ),
                    array( 'db' => 'emp_man_SSS',    'dt' => 6 ),
                    array( 'db' => 'emp_man_PhilHealth',    'dt' => 7 ),
                    array( 'db' => 'emp_man_PAGIBIG',    'dt' => 8 ),
                    
                );

                require( $_SERVER["DOCUMENT_ROOT"].'/psa_hris/ssp.class.php' );

                echo json_encode(
                    SSP::simple( $_GET, $dbDetails, $table, $primaryKey, $columns )
                );
        }
}
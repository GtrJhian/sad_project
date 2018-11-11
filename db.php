<?php
    const SERVER='localhost';
    const USER='root';
    const PASSWORD='';
    const DATABASE='psa_db';
    class connection extends mysqli{
        function __construct($SERVER, $USER, $PASSWORD, $DATABASE){
            return parent::__construct($SERVER, $USER, $PASSWORD, $DATABASE);
        }
        function __destruct(){
          //  $this->close();
        }
        
        function __call($name,$arguments){
            echo $name."<hr>".json_encode($arguments);
        }
        /*
            for param $types
            i	corresponding variable has type integer
            d	corresponding variable has type double
            s	corresponding variable has type string
            b	corresponding variable is a blob and will be sent in packets
        */
        function insert($table,$data,$types){
            // prepare and bind
            $qmarks=$this->qmarks(array_keys($data));
            $fields=$this->getFields(array_keys($data));
            if(!$stmt=$this->prepare("INSERT INTO $table ($fields) VALUES ($qmarks)"))
            die($this->error);
            $params=array('key'=>$types)+$data;
            call_user_func_array(array($stmt,'bind_param'),$this->refValues($params));
            if(!$stmt->execute()){
                echo $stmt->error;
                $stmt->close();
                return false;
            }
            $stmt->close();
            return true;
        }
        function prepared_query($sql,$data,$types){
            if(!$stmt=$this->prepare($sql))
            die($this->error);
            $params=array('key'=>$types)+$data;
            call_user_func_array(array($stmt,'bind_param'),$this->refValues($params));
            if(!$stmt->execute()){
                echo $stmt->error;
                $stmt->close();
                return false;
            }
            return $stmt->get_result();
        }
        function select_cols_id($cols, $table, $idname, $id){
            $result=$this->query("SELECT $cols FROM $table WHERE $idname= $id");
            $assoc_array[]=new stdClass();
            $x=0;
            while($assoc_array[$x++]=$row=$result->fetch_assoc()){}
            array_pop($assoc_array);
            return $assoc_array;
        }
        function log($action,$table,$data,$idname,$id){
            $user_id=$_SESSION['id'];
            $datetime=date('Y-m-d H:i:s');
            if($action==0){
                if(!$this->query("INSERT INTO logs(action,col_index,datetime,table_name,user_id)
                                            VALUES(0,$id,'$datetime','$table',$user_id)"))
                die($this->error);
                $last_id=$this->insert_id;
                $col_names=array_keys($data);
                $col_values=array_values($data);
                while($col_name=array_shift($col_names)){
                    $value=array_shift($col_values);
                    if(!$this->query("INSERT INTO log_cols(column_name,log_id,new_value)
                                                VALUES('$col_name',$last_id,'$value')"))
                    die($this->error);
                }
            }
            else if($action==1){
                $fields=implode(',',array_keys($data));
                if(!$result=$this->query("SELECT $fields FROM $table WHERE $idname=$id"))
                die($this->error);
                $result=$result->fetch_assoc();
               // echo json_encode($result);
                $col_names=array_keys($data);
                $update_count=0;
                while($col_name=array_shift($col_names)){
                     $value=$data[$col_name];
                    
                    if($value==$result[$col_name]){
                        continue;
                    }
                    else{
                        $update_count++;
                        break;
                    }
                }
                if($update_count>0){                    
                    if(!$result=$this->query("SELECT $fields FROM $table  WHERE $idname=$id"))
                    die($this->error);
                    $result=$result->fetch_assoc();
                    if(!$this->query("INSERT INTO logs(action,col_index,datetime,table_name,user_id)
                                                  VALUES(1,$id,'$datetime','$table',$user_id)"))
                    die($this->error);
                    echo $last_id=$this->insert_id;
                    $col_names=array_keys($data);
                    while($col_name=array_shift($col_names)){
                        $value=$data[$col_name];
                        $prev_value=$result[$col_name];
                        if($value!=$result[$col_name]){
                            if(!$this->query("INSERT INTO log_cols(column_name,log_id,new_value,prev_value)
                                                VALUES('$col_name',$last_id,'$value','$prev_value')"))
                            die($this->error);
                        }
                    }
                }
            }
        }
        function select_all($table){
            $result=$this->query("SELECT * FROM $table");
            $assoc_array[]=new stdClass();
            $x=0;
            while($assoc_array[$x++]=$row=$result->fetch_assoc()){}
            array_pop($assoc_array);
            return $assoc_array;
        }
        function select_all_cols($table, $cols){
            $fields=implode(",",$cols);
            if(!$result=$this->query("SELECT $fields FROM $table"))
            die($this->error);
            $assoc_array[]=new stdClass();
            $x=0;
            while($assoc_array[$x++]=$row=$result->fetch_assoc()){}
            array_pop($assoc_array);
            return $assoc_array;
        }
        function select_all_id($table, $idname, $id){
            $result=$this->query("SELECT * FROM $table WHERE $idname= $id");
            $assoc_array[]=new stdClass();
            $x=0;
            while($assoc_array[$x++]=$row=$result->fetch_assoc()){}
            array_pop($assoc_array);
            return $assoc_array;
        }

        function update_one($table, $data, $types, $idname,$id){
            //$qmarks=$this->qmarks(array_keys($data));
            $fields=implode("=?,",array_keys($data))."=?";
            if(!$stmt=$this->prepare("UPDATE $table SET $fields WHERE $idname=?;"))          
            die($this->error);
            $params=array('types'=>$types)+$data+array('id'=>$id);
            call_user_func_array(array($stmt,'bind_param'),$this->refValues($params));
            if(!$stmt->execute()){
                echo $stmt->error;
                $stmt->close();
                return false;
            }
            $stmt->close();
            return true;
        }
        private function refValues($arr){
            if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+
            {
                $refs = array();
                foreach($arr as $key => $value)
                    $refs[$key] = &$arr[$key];
                return $refs;
            }
            return $arr;
        }

        private function getFields($array){
            $string="";
            foreach($array as $field){
                $string.=$field.',';
            }
            return substr($string,0,-1);
        }

        private function qmarks($data){
            $string="";
            foreach($data as $x){
                $string=$string."?,";
            }
            return substr($string,0,strlen($string)-1);
        }
    }
    $db=new connection(SERVER,USER,PASSWORD,DATABASE);
?>
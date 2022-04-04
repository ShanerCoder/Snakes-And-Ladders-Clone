<?php
$db = new mysqli('db','root','password','docker_php');
$sql = "SELECT * FROM docker_php.bingo";
$result = $db->query($sql);
while ($row = $result->fetch_assoc()){
    foreach($row as $key=>$value){
        echo $key . ": "  .$value . "<br />";
    }
}
$db->close();

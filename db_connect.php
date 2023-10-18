<?php
try{
    //connection à la base de donnée
    $db=new PDO ('mysql:host=localhost; dbname=radio_tsiry', 'root','');
    $db->exec('SET NAMES "UTF8"');
} catch(PDOException $e){
    echo'ERREUR: '. $e->getMessage();
}
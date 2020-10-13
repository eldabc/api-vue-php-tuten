<?php

include "config.php";
include "utils.php";
require_once '../vendor/autoload.php';

use Carbon\Carbon; 

$dbConn =  connect($db);

//List all times or une
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{   
      //show time list
      $sql = $dbConn->prepare("SELECT * FROM `times`");
        $sql->execute();
      $sql->setFetchMode(PDO::FETCH_ASSOC);
      
          header("HTTP/1.1 200 OK");
            echo json_encode( $sql->fetchAll()  );
}


// Create times
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
  $post = json_decode(file_get_contents('php://input'), true);

  // validated is hour
  if( !empty($post['hour']) &&  !empty($post['utc']) ){

      if( date('H:i:s', strtotime($post['hour'])) == $post['hour'])
      {
        $sql = "INSERT INTO `times` (hour, utc) VALUES (:hour, :utc)";

        $statement = $dbConn->prepare($sql);
        
          bindAllValues($statement, $post);
        
        $statement->execute();

            if($dbConn->lastInsertId())
            {
              if($post['utc'] > 0)
                  $resHour = Carbon::parse($post['hour'])->addHours($post['utc'])->format('H:i:s');
              else{
              
                  $utc = str_replace('-', '', $post['utc']);
                  $resHour = Carbon::parse($post['hour'])->subHours($utc)->format('H:i:s');
              }
              
              header("HTTP/1.1 200 OK");
                  echo json_encode([
                                    'response' => [
                                                   'hour' => $resHour,
                                                   'utc' => 'utc'
                                                  ]
                                  ]);
        	 }

      }else{
            header("HTTP/1.1 202 Error formato de hora");
            echo json_encode([
                              'response' => [ 'message' => "El formato de hora no es correcto." ]
                             ]);
      }

  }else{
        header("HTTP/1.1 202 Campos Obligatorios");
        echo json_encode([
                          'response' => [ 'message' => "Todos los campos son obligatorios." ]
                         ]);
  }
}

?>
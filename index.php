<?php

ini_set("display_errors",1);
require 'Slim/Slim.php';
require 'userdetails.php';
//if($_POST)
//{
//    echo "asdfasdf";
//    exit;
//}
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

$app->get('/all', 'getAll');
$app->get('/towers', 'getTowers');
$app->post('/towers', 'addKlods');
$app->put('/updaterfid/', 'updateKlods');
$app->put('/updatestack/', 'stack');
$app->delete('/towers/',    'deleteKlods');
$app->post('/arduino/stack/', 'arduinoStack');
$app->post('/arduino/unstack/', 'arduinoUnStack');



$app->run();

function getTowers() {
    $sql_query = "SELECT `klods_id`,`stacked_id` FROM towers ORDER BY klods_id";
    try {
        $dbCon = getConnection();
        $stmt   = $dbCon->query($sql_query);
        $towers  = $stmt->fetchAll(PDO::FETCH_OBJ);
        $dbCon = null;
        echo '{"towers": ' . json_encode($towers) . '}';
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }

}

function getAll() {
    $sql_query = "SELECT `klods_id`,`stacked_id`,`rfid_id` FROM towers ORDER BY klods_id";
    try {
        $dbCon = getConnection();
        $stmt   = $dbCon->query($sql_query);
        $towers  = $stmt->fetchAll(PDO::FETCH_OBJ);
        $dbCon = null;
        echo '{"all": ' . json_encode($towers) . '}';
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }

}


function addKlods() {
    global $app;
    $req = $app->request(); // Getting parameter with names
    $klodsid = $req->params('klods_id'); // Getting parameter with ID
    $rfidid = $req->params('rfid_id'); // Getting parameter with names


    $sql = "INSERT INTO towers (klods_id, rfid_id) VALUES (:klods_id, :rfid_id)";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);
        $stmt->bindParam("klods_id", $klodsid);
        $stmt->bindParam("rfid_id", $rfidid);
        $stmt->execute();
        $dbCon = null;
        echo "DONE";
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function updateKlods() {
    global $app;
    $req = $app->request();
    $klods_ID = $req->params('klods_id');
    $rfid_ID = $req->params('rfid_id');

    $sql = "UPDATE towers SET rfid_id=:rfid_id WHERE klods_id=:id";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);
        $stmt->bindParam("id", $klods_ID);
        $stmt->bindParam("rfid_id", $rfid_ID);

        $status = $stmt->execute();

        $dbCon = null;
        echo json_encode($status);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function stack() {
    global $app;
    $req = $app->request();
    $klods_ID = $req->params('klods_id');
    $stacked_ID = $req->params('stacked_id');

    $sql = "UPDATE towers SET stacked_id=:stacked_id WHERE klods_id=:klods_id";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);
        $stmt->bindParam("stacked_id", $stacked_ID);
        $stmt->bindParam("klods_id", $klods_ID);
        $status = $stmt->execute();

        $dbCon = null;
        echo json_encode($status);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}


function deleteKlods() {
    global $app;
    $request = $app->request();
    $id = $request->params('klods_id');


    $sql = "DELETE FROM towers WHERE klods_id=:id";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);
        $stmt->bindParam("id", $id);
        $status = $stmt->execute();
        $dbCon = null;
        echo json_encode($status);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

/**
 * Used for stacking by the arfuino.
 * Can look op 'klodser' from their rfid_id and stack their id accordingly
 *
 * Inspired by the struture found here: https://secure.php.net/manual/en/mysqli.prepare.php
 */
function arduinoStack() {

    global $app;
    $request = $app->request();
    $klods_id = $request->params('klods_id');
    $stacked_rfid = $request->params('stacked_rfid');


    $sql = "SELECT klods_id FROM towers WHERE rfid_id=:id";
    try {
        $dbconnection = getConnection();
        $statement = $dbconnection->prepare($sql);
        $statement->bindParam('id', $stacked_rfid);
        $status = $statement->execute();
        $resultarray = $statement->fetch();
        $stacked_id = $resultarray[0];

        $sql = "UPDATE towers SET stacked_id=:stacked_id WHERE klods_id=:klods_id";
        $statement = $dbconnection->prepare($sql);
        $statement->bindParam("stacked_id", $stacked_id);
        $statement->bindParam("klods_id", $klods_id);
        $status = $statement->execute();

        $dbconnection = null;
        echo json_encode($status);

    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

/**
 * Used for the arduino to unstack itself
 */
function arduinoUnStack() {

    global $app;
    $request = $app->request();
    $klods_id = $request->params('klods_id');

    $sql = "UPDATE towers SET stacked_id = 0 WHERE klods_id=:klods_id ";

    try {
        $dbconnection = getConnection();
        $statement = $dbconnection->prepare($sql);
        $statement->bindParam('klods_id', $klods_id);
        $status = $statement->execute();


        echo json_encode($status);
    } catch(PDOException $e) {
    }

}


function getConnection() {
    try {

        $conn = new PDO('mysql:host='. HOST . ';dbname=' . NAME , USERNAME, PASSWORD);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }
    return $conn;
}

?>
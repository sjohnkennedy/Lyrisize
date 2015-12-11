<?php
function saveSessionInfo($name, $sessionId, $address, $data, $page = 1){
    if (!$pdo = new PDO("mysql:host=localhost;dbname=lyrisize", 'root', '')) {
        echo 'Could not connect to mysql';
        exit;
    }
    $query = "INSERT INTO sessiondata (name, address, sessionId, data, page) VALUES (:name, :address, :sessionId, :data, :page) ON DUPLICATE KEY UPDATE data = :data, page = :page";
    $q = $pdo->prepare($query);
    $q->execute(
        array(
            ':name' => $name,
            ':address' => $address,
            ':sessionId' => $sessionId,
            ':data' => $data,
            ':page' => $page
        )
    );
}

function getSessionInfo($sessionId, $address){
    if (!$pdo = new PDO("mysql:host=localhost;dbname=lyrisize", 'root', '')) {
        echo 'Could not connect to mysql';
        exit;
    }
    $result = $pdo->prepare("SELECT data FROM sessiondata WHERE sessionId = :sessionId AND address = :address");
    $result->execute(
        array(
            ':sessionId' => $sessionId,
            ':address' => $address
        )
    );
    return $result->fetch()['data'];
}

function getPageInfo($sessionId, $address){
    if (!$pdo = new PDO("mysql:host=localhost;dbname=lyrisize", 'root', '')) {
        echo 'Could not connect to mysql';
        exit;
    }
    $result = $pdo->prepare("SELECT page FROM sessiondata WHERE sessionId = :sessionId AND address = :address");
    $result->execute(
        array(
            ':sessionId' => $sessionId,
            ':address' => $address
        )
    );
    return (int)$result->fetch()['page'];
}

function getContentInfo($sessionId, $address){
    if (!$pdo = new PDO("mysql:host=localhost;dbname=lyrisize", 'root', '')) {
        echo 'Could not connect to mysql';
        exit;
    }
    $result = $pdo->prepare("SELECT name FROM sessiondata WHERE sessionId = :sessionId AND address = :address");
    $result->execute(
        array(
            ':sessionId' => $sessionId,
            ':address' => $address
        )
    );
    return $result->fetch()['name'];
}

function deleteSessionInfo($sessionId){
    if (!$pdo = new PDO("mysql:host=localhost;dbname=lyrisize", 'root', '')) {
        echo 'Could not connect to mysql';
        exit;
    }
    $query = "DELETE FROM sessiondata WHERE sessionId = :sessionId";
    $result = $pdo->prepare($query);
    $result->execute(
        array(
            ':sessionId' => $sessionId
        )
    );
}

?>

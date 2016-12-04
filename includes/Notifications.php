<?php

# check here
//Notifications which could be shown in the bottom corner of the page, and also dismissed.
//This would require another table with system information which can be marked read? Or something like this. It would be best to have it in the account page. 
//Display a header with information? This can be dismissed and stored using local storage? Or maybe using a number to show the most recently read/dismissed notification and would only show higher numbers.

//One of these approaches above could be used. 

//Start this tomorrow
if(($_SERVER['REQUEST_METHOD'] == 'POST') && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    $notification = new Notifications();
    if($_POST['action'] === 'check') {
        $last = $_POST['last'];
        $notification->checkForNewNotifications($last);
    } else if($_POST['action'] === 'get') {
        $last = $_POST['last'];
        $notification->getNewNotifications($last);
    }
}

class Notifications {

    public $db = null;
    function __construct() {
        require_once 'db-connect.inc.php';
        $this->db = Database::ConnectDb();
    }

    function checkForNewNotifications($last) {
		$stmt = $this->db->prepare('SELECT NotificationId FROM note_announcements ORDER BY NotificationId DESC LIMIT 1');
		$stmt->execute();    
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);   

        if($stmt->rowCount() > 0) {
            $result = $result[0]['NotificationId'];

            $send = array('notificationId' => $result);
            echo json_encode($send);
        } else {
            echo json_encode('none_found');
        }

        return;
    }

    function getNewNotifications($last) {
        echo json_encode('placeholder');
    }
}

?>
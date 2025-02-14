<?php
// 1. MariaDB connection
require_once 'config.php';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Optional IP restriction (Illustrative)
// $allowedIPs = array('192.168.1.0/24'); // Replace with your allowed IP ranges
// $visitorIP = $_SERVER['REMOTE_ADDR'];
// if (!in_array($visitorIP, $allowedIPs)) {
//     die("Access denied. Please sign in from an authorized location.");
// }

// 3. Handle actions based on AJAX requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];

    if ($action == 'get_terms') {
        // Fetch terms from the database
        $sql = "SELECT term_text FROM terms";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $term = htmlspecialchars($row['term_text'], ENT_QUOTES, 'UTF-8');
                echo "<label><input type='checkbox' name='terms[]' value='{$term}' required> {$term}</label><br>";
            }
        } else {
            echo "No terms found.";
        }
    } 
    elseif ($action == 'sign_in') {
        // Handle sign-in form submission
        $name = $conn->real_escape_string($_POST['name']);
        $contact = $conn->real_escape_string($_POST['contact']);
        $company = $conn->real_escape_string($_POST['company']);
        $visiting = $conn->real_escape_string($_POST['visiting']);
        $timestamp = date('Y-m-d H:i:s'); 

        $stmt = $conn->prepare("INSERT INTO visitors (name, contact, company, visiting, timestamp) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $contact, $company, $visiting, $timestamp);

        if ($stmt->execute()) {
            echo "Sign-in successful!";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    elseif ($action == 'search_for_sign_out') {
        // Handle sign-out search with improved name search
        $searchTerm = $conn->real_escape_string($_POST['searchTerm']);
        $today = date('Y-m-d');

        // Modified query to search for names containing the search term
        $stmt = $conn->prepare("SELECT id, name FROM visitors 
                WHERE name LIKE ? AND DATE(timestamp) = ? AND sign_out_timestamp IS NULL");
        $searchPattern = "%" . $searchTerm . "%"; // Add % to both sides to match anywhere in the name
        $stmt->bind_param("ss", $searchPattern, $today);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $name = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
                echo "<button type='button' class='sign-out-button' data-visitor-id='{$row['id']}'>{$name}</button><br>";
            }
        } else {
            echo "No matching visitors found.";
        }
        $stmt->close();
    }
    elseif ($action == 'sign_out') {
        // Handle sign-out action
        $visitorId = (int)$_POST['visitorId']; // Cast to integer for safety
        $signoutTimestamp = date('Y-m-d H:i:s');

        $stmt = $conn->prepare("UPDATE visitors SET sign_out_timestamp = ? WHERE id = ? AND sign_out_timestamp IS NULL");
        $stmt->bind_param("si", $signoutTimestamp, $visitorId);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo "Sign-out successful!";
            } else {
                echo "No active visitor found or already signed out.";
            }
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>

<?php
$conn = new mysqli("localhost","hindi_user","HindiFlypped@2025","hindi_flypped_production");

$conn->set_charset("utf8mb4");

$result = $conn->query("SELECT post_title FROM wp_posts WHERE post_status='publish' LIMIT 5");

while($row = $result->fetch_assoc()){
    echo "<pre>";
    var_dump($row['post_title']);
    echo "</pre><hr>";
}
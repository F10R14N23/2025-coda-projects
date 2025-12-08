<?php

require_once "inc/page.inc.php";
require_once "inc/database.inc.php";

try {
    $db = new DatabaseManager(
        dsn: "mysql:host=mysql;dbname=lowify;charset=utf8mb4",
        username: "lowify",
        password: "lowifypassword"
    );
} catch (PDOException $e) {
    echo "Erreur base";
    exit;
}

if (isset($_GET['query'])) {
    $query = $_GET['query'];
} else {
    $query = "";
}

// recherche artistes
$artists = [];
try {
    $artists = $db->executeQuery("
        SELECT id, name, cover
        FROM artist
        WHERE name LIKE :search
        LIMIT 10
    ", ["search" => "%$query%"]);
} catch (PDOException $e) {
    echo "Erreur recherche artistes";
    exit;
}

// recherche albums
$albums = [];
try {
    $albums = $db->executeQuery("
        SELECT album.id, album.name, album.cover, album.release_date, artist.name as artist_name, artist.id as artist_id
        FROM album
        INNER JOIN artist ON artist.id = album.artist_id
        WHERE album.name LIKE :search
        LIMIT 10
    ", ["search" => "%$query%"]);
} catch (PDOException $e) {
    echo "Erreur recherche albums";
    exit;
}

// recherche chansons
$songs = [];
try {
    $songs = $db->executeQuery("
        SELECT song.id, song.name, song.duration, song.note, album.name as album_name, album.id as album_id, artist.name as artist_name, artist.id as artist_id
        FROM song
        INNER JOIN album ON album.id = song.album_id
        INNER JOIN artist ON artist.id = album.artist_id
        WHERE song.name LIKE :search
        LIMIT 10
    ", ["search" => "%$query%"]);
} catch (PDOException $e) {
    echo "Erreur recherche chansons";
    exit;
}

// fonction pour durée
function formatDuration($s)
{
    $m = floor($s / 60);
    $r = $s - $m * 60;
    if ($r < 10) {
        $r = "0" . $r;
    }
    return $m . ":" . $r;
}

$html = "
<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>
<div class='container py-4'>
<h1>Résultats pour '$query'</h1>

<h2>Artistes</h2>
<div class='row mb-4'>
";

foreach ($artists as $a) {
    $id = $a['id'];
    $name = $a['name'];
    $cover = $a['cover'];

    $html .= "
    <div class='col-md-2 col-6 mb-3 text-center'>
        <a href='artist.php?id=$id' class='text-decoration-none text-dark'>
            <img src='$cover' class='rounded-circle mb-2' style='width:100px;height:100px;'>
            <div>$name</div>
        </a>
    </div>
    ";
}

$html .= "</div><h2>Albums</h2><div class='row mb-4'>";

foreach ($albums as $al) {
    $id = $al['id'];
    $name = $al['name'];
    $cover = $al['cover'];
    $date = $al['release_date'];
    $artistName = $al['artist_name'];
    $artistId = $al['artist_id'];

    $html .= "
    <div class='col-md-2 col-6 mb-3 text-center'>
        <a href='album.php?id=$id' class='text-decoration-none text-dark'>
            <img src='$cover' class='mb-2' style='width:100px;height:100px;border-radius:8px;'>
            <div>$name</div>
            <small>$date</small>
            <br><small>Artiste: <a href='artist.php?id=$artistId' class='text-dark'>$artistName</a></small>
        </a>
    </div>
    ";
}

$html .= "</div><h2>Chansons</h2><div class='row mb-4'>";

foreach ($songs as $s) {
    $name = $s['name'];
    $duration = formatDuration($s['duration']);
    $note = $s['note'];
    $albumName = $s['album_name'];
    $albumId = $s['album_id'];
    $artistName = $s['artist_name'];
    $artistId = $s['artist_id'];

    $html .= "
    <div class='col-md-4 mb-3'>
        <div class='card bg-light'>
            <div class='card-body'>
                <h5 class='card-title'>$name</h5>
                <p class='card-text'>Durée: $duration | Note: $note / 10</p>
                <p class='card-text'>Album: <a href='album.php?id=$albumId'>$albumName</a></p>
                <p class='card-text'>Artiste: <a href='artist.php?id=$artistId'>$artistName</a></p>
            </div>
        </div>
    </div>
    ";
}

$html .= "</div></div>";

$page = new HTMLPage("Recherche - Lowify");
$page->addContent($html);
echo $page->render();

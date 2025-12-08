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

$topArtists = [];
try {
    $topArtists = $db->executeQuery("
        SELECT id, name, cover, monthly_listeners
        FROM artist
        ORDER BY monthly_listeners DESC
        LIMIT 5
    ");
} catch (PDOException $e) {
    echo "Erreur top artistes";
    exit;
}

$recentAlbums = [];
try {
    $recentAlbums = $db->executeQuery("
        SELECT id, name, cover, release_date
        FROM album
        ORDER BY release_date DESC
        LIMIT 5
    ");
} catch (PDOException $e) {
    echo "Erreur albums récents";
    exit;
}

$bestAlbums = [];
try {
    $bestAlbums = $db->executeQuery("
        SELECT album.id, album.name, album.cover, AVG(song.note) as avg_note
        FROM album
        INNER JOIN song ON song.album_id = album.id
        GROUP BY album.id
        ORDER BY avg_note DESC
        LIMIT 5
    ");
} catch (PDOException $e) {
    echo "Erreur top albums";
    exit;
}

function formatt($n)
{
    if ($n >= 1000000) {
        $n = $n / 1000000;
        $n = round($n, 1);
        return $n . "M";
    }
    if ($n >= 1000) {
        $n = $n / 1000;
        $n = round($n, 1);
        return $n . "k";
    }
    return $n;
}

$html = "
<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>
<style>
body { background: #f2f2f2; }
</style>

<div class='container py-4'>
<h1 class='mb-4'>Lowify</h1>

<form method='get' action='search.php' class='mb-4 d-flex'>
    <input type='text' name='query' class='form-control me-2' placeholder='Recherche artistes, albums, chansons'>
    <button type='submit' class='btn btn-primary'>Rechercher</button>
</form>

<h2>Top trending</h2>
<div class='row mb-4'>
";

foreach ($topArtists as $a) {
    $id = $a['id'];
    $name = $a['name'];
    $cover = $a['cover'];
    $listeners = formatt($a['monthly_listeners']);

    $html .= "
    <div class='col-md-2 col-6 mb-3 text-center'>
        <a href='artist.php?id=$id' class='text-decoration-none text-dark'>
            <img src='$cover' class='rounded-circle mb-2' style='width:100px;height:100px;'>
            <div>$name</div>
            <small>$listeners</small>
        </a>
    </div>
    ";
}

$html .= "</div>";

$html .= "<h2>Top sorties</h2><div class='row mb-4'>";
foreach ($recentAlbums as $al) {
    $id = $al['id'];
    $name = $al['name'];
    $cover = $al['cover'];
    $date = $al['release_date'];

    $html .= "
    <div class='col-md-2 col-6 mb-3 text-center'>
        <a href='album.php?id=$id' class='text-decoration-none text-dark'>
            <img src='$cover' class='mb-2' style='width:100px;height:100px;border-radius:8px;'>
            <div>$name</div>
            <small>$date</small>
        </a>
    </div>
    ";
}

$html .= "</div>";

$html .= "<h2>Top albums</h2><div class='row mb-4'>";
foreach ($bestAlbums as $b) {
    $id = $b['id'];
    $name = $b['name'];
    $cover = $b['cover'];
    $note = round($b['avg_note'], 1);

    $html .= "
    <div class='col-md-2 col-6 mb-3 text-center'>
        <a href='album.php?id=$id' class='text-decoration-none text-dark'>
            <img src='$cover' class='mb-2' style='width:100px;height:100px;border-radius:8px;'>
            <div>$name</div>
            <small>$note / 10</small>
        </a>
    </div>
    ";
}

$html .= "</div></div>";

$page = new HTMLPage("Accueil - Lowify");
$page->addContent($html);
echo $page->render();

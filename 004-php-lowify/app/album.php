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
    echo "erreur base";
    exit;
}

if (isset($_GET["id"])) {
    $id = $_GET["id"];
} else {
    header("Location: error.php?message=id album pas bon");
    exit;
}

$alb = [];

$qAlb = "
SELECT id, name, cover, release_date, artist_id
FROM album
WHERE id = :id
";

try {
    $alb = $db->executeQuery($qAlb, ["id" => $id]);
} catch (PDOException $e) {
    echo "erreur req album";
    exit;
}

if (!$alb || $alb == []) {
    header("Location: error.php?message=album inconnu");
    exit;
}

$alb = $alb[0];

$art = [];

$qArt = "
SELECT id, name
FROM artist
WHERE id = :id
";

try {
    $art = $db->executeQuery($qArt, ["id" => $alb["artist_id"]]);
} catch (PDOException $e) {
    echo "erreur artiste album";
    exit;
}

if (!$art || $art == []) {
    header("Location: error.php?message=artiste introuvable");
    exit;
}

$art = $art[0];

$songs = [];

$qSongs = "
SELECT id, name, duration, note
FROM song
WHERE album_id = :id
ORDER BY id ASC
";

try {
    $songs = $db->executeQuery($qSongs, ["id" => $id]);
} catch (PDOException $e) {
    echo "erreur titres";
    exit;
}

function ff($s)
{
    $m = floor($s / 60);
    $r = $s - ($m * 60);
    if ($r < 10) {
        $r = "0" . $r;
    }
    return $m . ":" . $r;
}

$liste = "";

foreach ($songs as $s) {
    $nn = $s["name"];
    $d = ff($s["duration"]);
    $nt = $s["note"];

    $liste = $liste . "
    <div class='p-3 rounded' style='background:#222;margin-bottom:15px;'>
        <p class='mb-1'><b>$nn</b></p>
        <p class='mb-1 text-secondary'>$d</p>
        <p class='mb-0 text-info'>$nt / 10</p>
    </div>
    ";
}

$html = "
<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>
<style>
body { background:#111; }
a:hover { opacity:0.8; }
</style>

<div class='container text-white p-4'>

<a class='text-secondary' href='artists.php' style='text-decoration:none;'>&lt; Retour</a>

<h1 class='mt-4 fw-bold'>" . $alb["name"] . "</h1>

<img src='" . $alb["cover"] . "' 
class='shadow mt-3'
style='width:230px;height:230px;border-radius:12px;'>

<p class='mt-3'><b>Sortie :</b> " . $alb["release_date"] . "</p>

<p class='mt-2'>
<b>Artiste :</b> 
<a href='artist.php?id=" . $art["id"] . "' class='text-info fw-bold' style='text-decoration:none;'>" . $art["name"] . "</a>
</p>

<h2 class='mt-5 mb-3'>Titres</h2>

<div>
$liste
</div>

</div>
";

$page = new HTMLPage("Album - " . $alb["name"]);
$page->addContent($html);
echo $page->render();


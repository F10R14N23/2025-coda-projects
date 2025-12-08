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

    header("Location: error.php?message=id manquant");
    exit;

}


$artist = [];

$q = "
SELECT id, name, biography, cover, monthly_listeners
FROM artist
WHERE id = :id
";

try {

    $artist = $db->executeQuery($q, ["id" => $id]);

    if (!$artist || $artist == []) {
        header("Location: error.php?message=artiste introuvable");
        exit;
    }

    $artist = $artist[0];

} catch (PDOException $e) {

    header("Location: error.php?message=Erreur requête artiste");
    exit;

}

if (!$artist || $artist == []) {
    header("Location: error.php?message=Artiste non trouvé");
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

$listen = formatt($artist["monthly_listeners"]);


$top = [];

$sqlSongs = "
SELECT song.name as n,
song.duration as d,
song.note as note,
album.cover as c
FROM song
INNER JOIN album ON album.id = song.album_id
WHERE song.artist_id = :id
ORDER BY song.note DESC
LIMIT 5
";

try {

    $top = $db->executeQuery($sqlSongs, ["id" => $id]);

} catch (PDOException $e) {

    header("Location: error.php?message=Erreur récupération chansons");
    exit;

}


$albums = [];

$sqlAlb = "
SELECT id, name, cover, release_date
FROM album
WHERE artist_id = :id
ORDER BY release_date DESC
";

try {

    $albums = $db->executeQuery($sqlAlb, ["id" => $id]);

} catch (PDOException $e) {

    header("Location: error.php?message=Erreur récupération albums");
    exit;

}


function ddd($s)
{

    $m = floor($s / 60);
    $r = $s - ($m * 60);

    if ($r < 10) {
        $r = "0" . $r;
    }

    return $m . ":" . $r;

}


$songsHTML = "";

foreach ($top as $u) {

    $nom = $u["n"];
    $dur = ddd($u["d"]);
    $no = $u["note"];
    $cov = $u["c"];

    $songsHTML = $songsHTML . "
    <div class='list-group-item bg-secondary text-white border-light mb-2'>
        <div class='d-flex align-items-center'>
            <img src='$cov' style='width:60px;height:60px;border-radius:8px;' class='me-3'>
            <div>
                <p class='mb-1 fw-bold'>$nom</p>
                <p class='mb-0'>$dur — $no / 10</p>
            </div>
        </div>
    </div>
    ";

}


$albumsHTML = "";

foreach ($albums as $a) {

    $nn = $a["name"];
    $cv = $a["cover"];
    $d = $a["release_date"];
    $an = substr($d, 0, 4);

    $albumsHTML = $albumsHTML . "
    <div class='col-md-3 col-6 text-center mb-4'>
        <div class='card bg-dark text-white shadow'>
            <img src='$cv' class='card-img-top' style='height:150px;object-fit:cover;border-radius:8px;'>
            <div class='card-body'>
                <p class='card-title fw-bold'>$nn</p>
                <p class='card-text'>$an</p>
            </div>
        </div>
    </div>
    ";

}


$html = "

<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>

<div class='container-fluid bg-dark text-white min-vh-100 p-4'>

<a class='text-white btn btn-outline-light mb-3' href='artists.php'>← Retour</a>

<div class='text-center mb-5'>
<h1 class='fw-bold display-4'>" . $artist["name"] . "</h1>

<img src='" . $artist["cover"] . "' class='shadow-lg' style='width:220px;height:220px;border-radius:200px;margin-top:20px;'>

<p class='mt-3 fs-5'><b>Auditeurs :</b> $listen</p>
</div>

<div class='bg-secondary rounded p-4 mb-4 shadow'>
<h2 class='mb-3'>Biographie</h2>
<p>" . $artist["biography"] . "</p>
</div>

<div class='bg-secondary rounded p-4 shadow mb-4'>
<h2 class='mb-3'>Top titres</h2>

<div class='list-group list-group-flush'>
$songsHTML
</div>

</div>

<div class='bg-secondary rounded p-4 shadow mb-4'>
<h2 class='mb-3'>Albums</h2>

<div class='row'>
$albumsHTML
</div>

</div>

</div>

";


$page = new HTMLPage("Lowify - " . $artist["name"]);

$page->addContent($html);

echo $page->render();

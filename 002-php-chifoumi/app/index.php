<?php

const NO_CHOICE = 'pas choisi';
const WIN_MESSAGE = 'Vous avez gagné !';
const LIZARD = 'lézard';
const SPOCK = 'spock';

$user = "Florian";
$possibilite = ["pierre", "feuille", "ciseaux", LIZARD, SPOCK];



if (isset($_GET['choix'])) {
    $userChoix = $_GET['choix'];
} else {
    $userChoix = NO_CHOICE;
}

if (in_array($userChoix, $possibilite)) {
    $choix = $userChoix;
} else {
    $choix = NO_CHOICE;
}

if ($choix !== NO_CHOICE) {
    $random = array_rand($possibilite);
    $choixOrdi = $possibilite[$random];
} else {
    $choixOrdi = "–";
}




if ($choix === NO_CHOICE) {
    $result = "Faites un choix pour commencer la partie !";
}
elseif ($choix === $choixOrdi) {
    $result = "Égalité !";
}
elseif ($choix === "pierre" && ($choixOrdi === "ciseaux" || $choixOrdi === LIZARD)) {
    $result = WIN_MESSAGE;
}
elseif ($choix === "feuille" && ($choixOrdi === "pierre" || $choixOrdi === SPOCK)) {
    $result = WIN_MESSAGE;
}
elseif ($choix === "ciseaux" && ($choixOrdi === "feuille" || $choixOrdi === LIZARD)) {
    $result = WIN_MESSAGE;
}
elseif ($choix === LIZARD && ($choixOrdi === "feuille" || $choixOrdi === SPOCK)) {
    $result = WIN_MESSAGE;
}
elseif ($choix === SPOCK && ($choixOrdi === "pierre" || $choixOrdi === "ciseaux")) {
    $result = WIN_MESSAGE;
}
else {
    $result = "Vous avez perdu !";
}




if (isset($_GET['parties'])) {
    $parties = $_GET['parties'];
} else {
    $parties = 0;
}

if (isset($_GET['victoires'])) {
    $victoires = $_GET['victoires'];
} else {
    $victoires = 0;
}

if (isset($_GET['egalites'])) {
    $egalites = $_GET['egalites'];
} else {
    $egalites = 0;
}

if (isset($_GET['defaites'])) {
    $defaites = $_GET['defaites'];
} else {
    $defaites = 0;
}



if ($choix !== NO_CHOICE) {
    $parties = $parties + 1;

    if ($result === WIN_MESSAGE) {
        $victoires = $victoires + 1;
    }
    elseif ($result === "Égalité !") {
        $egalites = $egalites + 1;
    }
    elseif ($result === "Vous avez perdu !") {
        $defaites = $defaites + 1;
    }
}



//html

$html = <<<HTML

<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jeu Pierre, Feuille, Ciseaux, Lézard, Spock</title>
    
    <style>
        body {
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 100px;
        }
        
        h1 {
            color: #333;
        }

        button {
            padding: 10px 20px;
            margin: 5px;
            border-radius: 5px;
            border: none;
            background-color: #3498db;
            color: white;
            cursor: pointer;
            font-size: 16px;
        }
        
        p {
            font-size: 18px;
        }
        
    </style>
    
</head>

<body>

    <h1>Jeu Pierre, Feuille, Ciseaux, Lézard, Spock</h1>

    <div>
        <p><strong>Choix de $user :</strong> $choix</p>
        <p><strong>Choix du robot :</strong> $choixOrdi</p>
        <p><strong>Résultat :</strong> $result</p>
    </div>

    <div>
        <p>Choisissez votre coup :</p>
        
        <div><a href="?choix=pierre"><button>Pierre</button></a></div>
        <div><a href="?choix=feuille"><button>Feuille</button></a></div>
        <div><a href="?choix=ciseaux"><button>Ciseaux</button></a></div>
        <div><a href="?choix=lézard"><button>Lézard</button></a></div>
        <div><a href="?choix=spock"><button>Spock</button></a></div>
    </div>

    <div>
        <a href="index.php"><button>Réinitialiser le jeu</button></a>
    </div>

    <div>
        <h2>Statistiques :</h2>
        <p>Parties jouées : $parties</p>
        <p>Victoires : $victoires</p>
        <p>Égalités : $egalites</p>
        <p>Défaites : $defaites</p>
    </div>

</body>

</html>

HTML;

echo $html;

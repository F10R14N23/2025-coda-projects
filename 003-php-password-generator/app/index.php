<?php

//php

$motDePasse = "...";

if ($_SERVER["REQUEST_METHOD"] == "POST")
    {

    $taille = $_POST["size"] ?? 10;

    $minuscule = isset($_POST["use-alpha-minuscule"]);
    $majuscule = isset($_POST["use-alpha-majuscule"]);
    $number  = isset($_POST["use-number"]);
    $symbole = isset($_POST["use-symbols"]);

} else {
    $taille = 12;
    $minuscule = true;
    $majuscule = true;
    $number = true;
    $symbole = true;
}

$select = "";
for ($i = 8; $i <= 42; $i++) {
    $selected = ($i == $taille) ? "selected" : "";
    $select .= "<option value='$i' $selected>$i</option>";
}

$minusculeChecked = $minuscule ? "checked" : "";
$majusculeChecked = $majuscule ? "checked" : "";
$numberChecked  = $number  ? "checked" : "";
$symboleChecked = $symbole ? "checked" : "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $lettresMinuscule = "abcdefghijklmnopqrstuvwxyz";
    $lettresMajuscule = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $chiffres   = "0123456789";
    $symboles   = "!@#$%^&*()";

    $pool = "";
    if ($minuscule) { $pool .= $lettresMinuscule; }
    if ($majuscule) { $pool .= $lettresMajuscule; }
    if ($number)  { $pool .= $chiffres; }
    if ($symbole) { $pool .= $symboles; }

    if ($pool == "") {
        $motDePasse = "Audun type de caractères sélectionné";
    } else {

        $motObligatoire = "";
        if ($minuscule) { $motObligatoire .= $lettresMinuscule[rand(0, strlen($lettresMinuscule)-1)]; }
        if ($majuscule) { $motObligatoire .= $lettresMajuscule[rand(0, strlen($lettresMajuscule)-1)]; }
        if ($number)  { $motObligatoire .= $chiffres[rand(0, strlen($chiffres)-1)]; }
        if ($symbole) { $motObligatoire .= $symboles[rand(0, strlen($symboles)-1)]; }

        $reste = $taille - strlen($motObligatoire);
        $mdpFinal = $motObligatoire;

        for ($j = 0; $j < $reste; $j++) {
            $mdpFinal .= $pool[rand(0, strlen($pool)-1)];
        }

        $mdpArray = str_split($mdpFinal);
        shuffle($mdpArray);
        $motDePasse = implode("", $mdpArray);
    }
}


//HTML
echo <<<HTML
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Générateur de mot de passe</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <h1>Générateur de mot de passe</h1>

    <div class="password-box">$motDePasse</div>

    <form method="POST">

        <div>
            <label for="size">Taille :</label>
            <select id="size" name="size">
                $select
            </select>
        </div>

        <div class="checkbox-row">
            <input type="checkbox" name="use-alpha-min" $minusculeChecked>
            <label>Minuscules (a-z)</label>
        </div>

        <div class="checkbox-row">
            <input type="checkbox" name="use-alpha-maj" $majusculeChecked>
            <label>Majuscules (A-Z)</label>
        </div>

        <div class="checkbox-row">
            <input type="checkbox" name="use-num" $numberChecked>
            <label>Chiffres (0-9)</label>
        </div>

        <div class="checkbox-row">
            <input type="checkbox" name="use-symbols" $symboleChecked>
            <label>Symboles (!@#\$%^&*())</label>
        </div>

        <button type="submit">Générer !</button>

    </form>

</div>

</body>
</html>
HTML;

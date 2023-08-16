<?php

namespace App\Services;

class Censurator
{
    const INSULTES = ["fdp", "con", "conne", "Con", "Conne"];

    function purify(string $phrase): string  {

//      $motsInterdits = file_get_contents('./profanity.json');

        // Remplacement des insultes pas un nombre d'* exact a l'insulte
        foreach(self::INSULTES as $insulte) {
            $phrase = str_replace($insulte, str_repeat('*', mb_strlen($insulte)) ,$phrase);
        }
        return $phrase;
    }
}


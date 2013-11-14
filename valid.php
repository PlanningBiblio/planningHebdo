<?php
/*
Planning Biblio, Plugin planningHebdo Version 1.2.11
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/planningHebdo/valid.php
Création : 23 juillet 2013
Dernière modification : 3 octobre 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier permettant de valider la saisie de son planning de présence hebdomadaire
*/

include "class.planningHebdo.php";

switch($_POST['action']){
  case "ajout" :
    $p=new planningHebdo();
    $p->add($_POST);
    $message=$p->error?"Ajout-erreur":"Ajout-OK";
    echo "<script type='text/JavaScript'>document.location.href='index.php?page=plugins/planningHebdo/monCompte.php&message=$message';</script>\n";
    break;
  case "modif" :
    $p=new planningHebdo();
    $p->update($_POST);
    $message=$p->error?"Modif-erreur":"Modif-OK";
    echo "<script type='text/JavaScript'>document.location.href='index.php?page=plugins/planningHebdo/{$_POST['retour']}&message=$message';</script>\n";
    break;
  case "copie" :
    $p=new planningHebdo();
    $p->copy($_POST);
    $message=$p->error?"Modif-erreur":"Modif-OK";
    echo "<script type='text/JavaScript'>document.location.href='index.php?page=plugins/planningHebdo/{$_POST['retour']}&message=$message';</script>\n";
    break;
}
?>
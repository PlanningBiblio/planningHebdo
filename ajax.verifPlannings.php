<?php
/*
Planning Biblio, Plugin planningHebdo Version 1.2.9
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/planningHebdo/ajax.verifPlannings.php
Création : 2 octobre 2013
Dernière modification : 7 octobre 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Recherche les plannings enregistrés afin d'éviter les conflits lors de l'enregistrement d'un nouveau planning.
Fichier appelé en arrière plan par la fonction JS plHebdoVerifForm (js/script.planningHebdo.js)
*/

session_start();

$version="1.2.9";
include "../../include/config.php";

// Filtre permettant de ne rechercher que les plannings de l'agent sélectionné
$perso_id=isset($_GET['perso_id'])?$_GET['perso_id']:$_SESSION['login_id'];

// Filtre permettant de ne pas regarder l'actuel planning et les plannings remplacant celui-ci
$id=isset($_GET['id'])?" AND `id`<>'{$_GET['id']}' AND `remplace`<>'{$_GET['id']}' ":null;

// Filtre permettant de ne pas regarder le planning remplacé par le planning sélectionné
$remplace=null;
if(isset($_GET['id'])){
  $db=new db();
  $db->select("planningHebdo","remplace","`id`='{$_GET['id']}'");
  if($db->result[0]['remplace']){
    $remplace=" AND `id`<>'{$db->result[0]['remplace']}' AND `remplace`<>'{$db->result[0]['remplace']}' ";
  }
}

$db=new db();
$db->select("planningHebdo","*","perso_id='$perso_id' AND `debut`<='{$_GET['fin']}' AND `fin`>='{$_GET['debut']}' $id $remplace ");

if(!$db->result){
  echo "###OK###";
}
else{
  echo "###{$db->result[0]['debut']}###{$db->result[0]['fin']}###";
}
?>
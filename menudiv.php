<?php
/*
Planning Biblio, Plugin planningHebdo Version 1.2.12
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/planningHebdo/menudiv.php
Création : 24 juillet 2013
Dernière modification : 24 juillet 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier permettant de trouver le bon emploi du temps pour chaque agent en fonction des dates de début et de fin dans le menu contextuel 
  du planning par poste (menudiv)
Fichier inclus dans le fichier planning/poste/menudiv.php si leplugin planningHebdo est activé.
*/

include_once "class.planningHebdo.php";
$p=new planningHebdo();
$p->perso_id=$elem['id'];
$p->debut=$date;
$p->fin=$date;
$p->valide=true;
$p->fetch();

if(empty($p->elements)){
  $temps=array();
}
else{
  $temps=$p->elements[0]['temps'];
}

?>
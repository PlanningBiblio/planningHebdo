<?php
/*
Planning Biblio, Plugin planningHebdo Version 1.3.1
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
Copyright (C) 2013-2014 - Jérôme Combes

Fichier : plugins/planningHebdo/agenda.php
Création : 21 mars 2014
Dernière modification : 21 mars 2014
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier permettant de trouver le bon emploi du temps en fonction de la date de la cellule courante dans l'agenda
Fichier inclus à chaque changement de cellule dans le fichier planning/agenda/index.php si le plugin planningHebdo est activé.

Variables initialisées dans agenda/index.php :
$perso_id = id de l'agent
$current = date de la cellule courante
*/

include_once "class.planningHebdo.php";
$p=new planningHebdo();
$p->perso_id=$perso_id;
$p->debut=$current;
$p->fin=$current;
$p->valide=true;
$p->fetch();

if(empty($p->elements)){
  $temps=array();
}
else{  
  $temps=$p->elements[0]['temps'];
}
?>
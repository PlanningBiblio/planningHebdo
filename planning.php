<?php
/*
Planning Biblio, Plugin planningHebdo Version 1.4.5
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
Copyright (C) 2013-2015 - Jérôme Combes

Fichier : plugins/planningHebdo/planning.php
Création : 22 mars 2014
Dernière modification : 10 avril 2015
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier permettant de trouver le bon emploi du temps et le bon site de chaque agent
Inclus dans planning/poste/index.php pour afficher ces informations dans la liste des agents présents
Inclus dans planning/poste/menudiv.php pour exclure les agents non prévus sur le site séléctionné

Variables initialisées dans planning/poste/index.php et dans  planning/poste/menudiv.php :
$elem['id'] = id de l'agent
$date = date courante
*/

include_once "class.planningHebdo.php";
$p=new planningHebdo();
$p->debut=$date;
$p->fin=$date;
$p->valide=true;
$p->fetch();

$tempsPlanningHebdo=array();

if(!empty($p->elements)){
  foreach($p->elements as $elem){
    $tempsPlanningHebdo[$elem["perso_id"]]=$elem["temps"];
  }
}
?>
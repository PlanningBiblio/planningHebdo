<?php
/*
Planning Biblio, Plugin planningHebdo Version 1.3
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/planningHebdo/menudiv.php
Création : 26 septembre 2013
Dernière modification : 26 septembre 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier appelé lors de la suppression d'un agent par le fichier personnel/class.personnel.php, fonction personnel::delete()
Permet de supprimer les plannings de présence des agents supprimés définitivement
La variables $liste et la liste des ids des agents à supprimer, séparés par des virgules
*/

require_once "class.planningHebdo.php";

// recherche des personnes à exclure (congés)
$p=new planningHebdo();
$p->suppression_agents($liste);
?>
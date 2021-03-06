<?php
/*
Planning Biblio, Plugin planningHebdo Version 1.3.9
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
Copyright (C) 2013-2015 - Jérôme Combes

Fichier : plugins/planningHebdo/ajax.delete.php
Création : 17 septembre 2013
Dernière modification : 5 novembre 2014
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier permettant la suppression d'un planning de présence en arrière plan.
Appelé par la fonction JS plHebdoSupprime (plugins/planningHebdo/js/script.planningHebdo.js)
*/

require_once "../../include/config.php";

$id=$_GET['id'];
$db=new db();
$db->delete("planningHebdo","id=$id");
$db=new db();
$db->update("planningHebdo","remplace='0'","remplace='$id'");
?>
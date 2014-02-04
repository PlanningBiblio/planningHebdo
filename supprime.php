<?php
/*
Planning Biblio, Plugin planningHebdo Version 1.3.1
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013-2014 - Jérôme Combes

Fichier : plugins/planningHebdo/supprime.php
Création : 17 septembre 2013
Dernière modification : 9 octobre 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier permettant la suppression d'un planning de présence en arrière plan.
Appelé par la fonction JS plHebdoSupprime (plugins/planningHebdo/js/script.planningHebdo.js)
*/

$id=$_GET['id'];
$db=new db();
$db->delete("planningHebdo","id=$id");
$db=new db();
$db->update("planningHebdo","remplace='0'","remplace='$id'");
?>
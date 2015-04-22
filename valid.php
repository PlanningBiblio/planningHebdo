<?php
/*
Planning Biblio, Plugin planningHebdo Version 1.4.6
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
Copyright (C) 2013-2015 - Jérôme Combes

Fichier : plugins/planningHebdo/valid.php
Création : 23 juillet 2013
Dernière modification : 22 avril 2015
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier permettant de valider la saisie de son planning de présence hebdomadaire
*/

require_once "class.planningHebdo.php";

// Initialisation des variables
$post=filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING);

switch($post["action"]){
  case "ajout" :
    $p=new planningHebdo();
    $p->add($post);
    if($p->error){
    	$msg=urlencode("Une erreur est survenue lors de l'enregistrement du planning.");
    	$msgType="error";    	
    }else{
    	$msg=urlencode("Le planning a été ajouté avec succés.");
    	$msgType="success";    	
    }
    echo "<script type='text/JavaScript'>document.location.href='index.php?page=plugins/planningHebdo/monCompte.php&msg=$msg&msgType=$msgType';</script>\n";
    break;

  case "modif" :
    $p=new planningHebdo();
    $p->update($post);
    if($p->error){
    	$msg=urlencode("Une erreur est survenue lors de la modification du planning.");
    	$msgType="error";    	
    }else{
    	$msg=urlencode("Le planning a été modifié avec succés.");
    	$msgType="success";    	
    }
    echo "<script type='text/JavaScript'>document.location.href='index.php?page=plugins/planningHebdo/{$post['retour']}&msg=$msg&msgType=$msgType';</script>\n";
    break;
 
  case "copie" :
    $p=new planningHebdo();
    $p->copy($post);
    if($p->error){
    	$msg=urlencode("Une erreur est survenue lors de la modification du planning.");
    	$msgType="error";    	
    }else{
    	$msg=urlencode("Le planning a été modifié avec succés.");
    	$msgType="success";    	
    }
    echo "<script type='text/JavaScript'>document.location.href='index.php?page=plugins/planningHebdo/{$post['retour']}&msg=$msg&msgType=$msgType';</script>\n";
    break;
}
?>
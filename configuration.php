<?php
/*
Planning Biblio, Plugin planningHebdo Version 1.2.5
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/planningHebdo/index.php
Création : 25 juillet 2013
Dernière modification : 25 juillet 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Affiche la liste des plannings de présence pour l'administrateur
Page accessible à partir du menu administration/planning de présence
*/

include "class.planningHebdo.php";

// Mise à jour de la configHebdo
if($_POST){
  $error=false;
  $p=new planningHebdo();
  $p->updateConfig($_POST);
  $error=$p->error?true:$error;

  $p=new planningHebdo();
  $p->updatePeriodes($_POST);
  $error=$p->error?true:$error;

  // Notifications
  if($error){
    $message="Une erreur est survenue lors de la modification de la configuration."; $class="MessageErreur";
  }
  else{
    $message="La configuration a été modifiée avec succés."; $class="MessageOK";
  }
  echo "<div class='$class' id='information'>$message</div>\n";
  echo "<script type='text/JavaScript'>setTimeout(\"document.getElementById('information').style.display='none'\",3000);</script>\n";
}



// Recherche de la config
$p=new planningHebdo();
$p->getConfig();
$configHebdo=$p->config;

// Initialisation des variables
$annee_courante=date("n")<9?(date("Y")-1)."-".(date("Y")):(date("Y"))."-".(date("Y")+1);
$annee_suivante=date("n")<9?(date("Y"))."-".(date("Y")+1):(date("Y")+1)."-".(date("Y")+2);
$checked[0]=$configHebdo['periodesDefinies']?"checked='checked'":null;
$checked[1]=$configHebdo['periodesDefinies']?null:"checked='checked'";

// Recherche des dates de début et de fin de chaque période
$p->dates=array($annee_courante,$annee_suivante);
$p->getPeriodes();
$dates=$p->periodes;


echo <<<EOD
<h3>Configuration du plugin "Planning Hebdo"</h3>
<form name='form' action='index.php' method='post'>
<input type='hidden' name='page' value='plugins/planningHebdo/configuration.php'/>
<input type='hidden' name='annee[0]' value='$annee_courante'/>
<input type='hidden' name='annee[1]' value='$annee_suivante'/>

<table>
<tr><td colspan='2'>Utiliser des périodes définies pour les plannings hebdomadaires</td>
  <td><input type='radio' value='1' name='periodesDefinies' {$checked[0]} /> Oui
    <input type='radio' value='0' name='periodesDefinies' {$checked[1]} /> Non</td></tr>
<tr><td colspan='3' style='padding:20px 0 20px 0;'>Si vous utilisez les périodes définies, veuillez saisir ci-dessous les dates de début et de fin de chaque période</td></tr>
<tr><td>Année $annee_courante, horaires normaux</td>
  <td>Début <input type='text' name='dates[0][0]' value='{$dates[0][0]}' />
    <img src="img/calendrier.gif" onclick="calendrier('dates[0][0]');" alt="calendrier">
  <td>Fin <input type='text' name='dates[0][1]' value='{$dates[0][1]}' />
    <img src="img/calendrier.gif" onclick="calendrier('dates[0][1]');" alt="calendrier">
  </td></tr>
<tr><td>Année $annee_courante, horaires réduits</td>
  <td>Début <input type='text' name='dates[0][2]' value='{$dates[0][2]}' />
    <img src="img/calendrier.gif" onclick="calendrier('dates[0][2]');" alt="calendrier">
  <td>Fin <input type='text' name='dates[0][3]' value='{$dates[0][3]}' />
    <img src="img/calendrier.gif" onclick="calendrier('dates[0][3]');" alt="calendrier">
  </td></tr>
<tr><td>Année $annee_suivante, horaires normaux</td>
  <td>Début <input type='text' name='dates[1][0]' value='{$dates[1][0]}' />
    <img src="img/calendrier.gif" onclick="calendrier('dates[1][0]');" alt="calendrier">
  <td>Fin <input type='text' name='dates[1][1]' value='{$dates[1][1]}' />
    <img src="img/calendrier.gif" onclick="calendrier('dates[1][1]');" alt="calendrier">
  </td></tr>
<tr><td>Année $annee_suivante, horaires réduits</td>
  <td>Début <input type='text' name='dates[1][2]' value='{$dates[1][2]}' />
    <img src="img/calendrier.gif" onclick="calendrier('dates[1][2]');" alt="calendrier">
  <td>Fin <input type='text' name='dates[1][3]' value='{$dates[1][3]}' />
    <img src="img/calendrier.gif" onclick="calendrier('dates[1][3]');" alt="calendrier">
  </td></tr>
<tr><td colspan='3' style='padding:20px 0 0 30px;'>
  <input type='button' value='Retour' onclick='document.location.href="index.php?page=plugins/planningHebdo/index.php";' />
  <input type='submit' value='Valider' style='margin-left:30px;'/></td></tr>
</table>
</form>
EOD;
?>
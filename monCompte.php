<?php
/*
Planning Biblio, Plugin planningHebdo Version 1.2.3
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/planningHebdo/monCompte.php
Création : 23 juillet 2013
Dernière modification : 5 septembre 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier permettant de modifier son mot de passe et son planning de présence hebdomadaire
*/

include "class.planningHebdo.php";

// Recherche de la config
$p=new planningHebdo();
$p->getConfig();
$configHebdo=$p->config;

// Initialisation des variables
// Plannings de présence
$annee_courante=date("n")<9?(date("Y")-1)."-".(date("Y")):(date("Y"))."-".(date("Y")+1);
$annee_suivante=date("n")<9?(date("Y"))."-".(date("Y")+1):(date("Y")+1)."-".(date("Y")+2);

// Crédits (congés, récupérations)
if(in_array("conges",$plugins)){
  $p=new personnel();
  $p->fetchById($_SESSION['login_id']);
  $credits['annuel']=$p->elements[0]['congesAnnuel'];
  $credits['conges']=$p->elements[0]['congesCredit'];
  $credits['reliquat']=$p->elements[0]['congesReliquat'];
  $credits['anticipation']=$p->elements[0]['congesAnticipation'];
  $credits['recuperation']=$p->elements[0]['recupSamedi'];
}

// Notifications
if(isset($_GET['message'])){
  switch($_GET['message']){
    case "Ajout-OK" : $message="Le planning a été ajouté avec succés."; $class="MessageOK";	break;
    case "Ajout-erreur" : $message="Une erreur est survenue lors de l'enregistrement du planning."; $class="MessageErreur"; break;
    case "Modif-OK" : $message="Le planning a été modifié avec succés."; $class="MessageOK";	break;
    case "Modif-erreur" : $message="Une erreur est survenue lors de la modification du planning."; $class="MessageErreur"; break;
  }
  echo "<div class='$class' id='information'>$message</div>\n";
  echo "<script type='text/JavaScript'>setTimeout(\"document.getElementById('information').style.display='none'\",3000);</script>\n";
}

?>
<!--	Menu	-->
<div id='onglets'>
<font id='titre'>Mon Compte</font>
<ul>
<?php
if(in_array("conges",$plugins)){
  echo <<<EOD
    <li id='current'><a href='javascript:show("planningPresence","credits,motDePasse","li1");'>Mes plannings de présence</a></li>
    <li id='li2'><a href='javascript:show("credits","planningPresence,motDePasse","li2");'>Mes crédits</a></li>
    <li id='li3'><a href='javascript:show("motDePasse","planningPresence,credits","li3");'>Mon mot de passe</a></li>
EOD;
}
else{
  echo <<<EOD
    <li id='current'><a href='javascript:show("planningPresence","motDePasse","li1");'>Mes plannings de présence</a></li>
    <li id='li2'><a href='javascript:show("motDePasse","planningPresence","li2");'>Mon mot de passe</a></li>
EOD;
}
?>
</ul>
</div>
<br/><br/><br/><br/>

<!-- Planning de présence -->
<div id='planningPresence' style='margin-left:80px;'>
<table style='width:750px;'>
<tr><td><h3>Planning de présence</h3></td>
<td style='text-align:right;'>
  <a href='#' onclick='document.getElementById("nouveauPlanning").style.display="";this.style.display="none";document.getElementById("historique").style.display="none";'>
  Entrer un nouveau planning</a></td></tr>
</table>

<!-- Formulaire nouveau planning -->
<div id='nouveauPlanning' style='display:none;'>
Nouveau planning de présence
<br/>
<form name='form1' method='post' action='index.php' onsubmit='return verif_form("debut=date1;fin=date2Obligatoire","form1");'>
<input type='hidden' name='page' value='plugins/planningHebdo/valid.php' />
<input type='hidden' name='action' value='ajout' />

<!-- Affichage des tableaux avec la sélection des horaires -->
<?php
switch($config['nb_semaine']){
  case 2	: $cellule=array("Semaine Impaire","Semaine Paire");		break;
  case 3	: $cellule=array("Semaine 1","Semaine 2","Semaine 3");		break;
  default 	: $cellule=array("Jour");					break;
}
$fin=$config['Dimanche']?array(8,15,22):array(7,14,21);
$debut=array(1,8,15);
$jours=Array("Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi","Dimanche");
?>

<?php
for($j=0;$j<$config['nb_semaine'];$j++){
  echo "<br/>\n";
  // Si périodes définies : les dates de début et de fin sont forcées et il y a 2 plannings à saisir (horaires normaux et horaires réduits)
  if($configHebdo['periodesDefinies']){
    echo <<<EOD
      Sélectionnez l'année
      <select name='annee'>
	<option value='$annee_courante'>$annee_courante</option>
	<option value='$annee_suivante'>$annee_suivante</option>
	</select>
EOD;

    echo "<br/><br/>Horaires normaux <font id='heures' style='font-weight:bold;position:absolute;left:300px;'>&nbsp;</font><br/>";
  }
  echo "<table border='1' cellspacing='0'>\n";
  echo "<tr style='text-align:center;'><td style='width:150px;'>{$cellule[$j]}</td><td style='width:150px;'>Heure d'arrivée</td>";
  echo "<td style='width:150px;'>Début de pause</td><td style='width:150px;'>Fin de pause</td>";
  echo "<td style='width:150px;'>Heure de départ</td>";
  if($config['Multisites-nombre']>1 and $config['Multisites-agentsMultisites']){
    echo "<td>Site</td>";
  }
  echo "</tr>\n";
  for($i=$debut[$j];$i<$fin[$j];$i++){
    $k=$i-($j*7)-1;
    echo "<tr><td>{$jours[$k]}</td><td>".selectTemps($i-1,0,null,"select")."</td><td>".selectTemps($i-1,1,null,"select")."</td>";
    echo "<td>".selectTemps($i-1,2,null,"select")."</td><td>".selectTemps($i-1,3,null,"select")."</td>";
    if($config['Multisites-nombre']>1 and $config['Multisites-agentsMultisites']){
      echo "<td><select name='temps[".($i-1)."][4]'>\n";
      echo "<option value=''>&nbsp;</option>\n";
      echo "<option value='1' >{$config['Multisites-site1']}</option>\n";
      echo "<option value='2' >{$config['Multisites-site2']}</option>\n";
      echo "</select></td>";
    }
    echo "</tr>\n";
  }
  echo "</table>\n";

  // Affichage du nombre d'heures si les periodes ne sont pas définies
  if(!$configHebdo['periodesDefinies']){
    echo "Nombre d'heures : <font id='heures' style='font-weight:bold;'>&nbsp;</font><br/>\n";
  }

  // Si périodes définies : formulaires pour la périodes horaires réduits
  if($configHebdo['periodesDefinies']){
    echo "<br/>\n";
    echo "Horaires réduits <font id='heures2' style='font-weight:bold;;position:absolute;left:300px;'>&nbsp;</font><br/>";
    echo "<table border='1' cellspacing='0'>\n";
    echo "<tr style='text-align:center;'><td style='width:150px;'>{$cellule[$j]}</td><td style='width:150px;'>Heure d'arrivée</td>";
    echo "<td style='width:150px;'>Début de pause</td><td style='width:150px;'>Fin de pause</td>";
    echo "<td style='width:150px;'>Heure de départ</td>";
    if($config['Multisites-nombre']>1 and $config['Multisites-agentsMultisites']){
      echo "<td>Site</td>";
    }
    echo "</tr>\n";
    for($i=$debut[$j];$i<$fin[$j];$i++){
      $k=$i-($j*7)-1;
      echo "<tr><td>{$jours[$k]}</td><td>".selectTemps($i-1,0,2,"select2")."</td><td>".selectTemps($i-1,1,2,"select2")."</td>";
      echo "<td>".selectTemps($i-1,2,2,"select2")."</td><td>".selectTemps($i-1,3,2,"select2")."</td>";
      if($config['Multisites-nombre']>1 and $config['Multisites-agentsMultisites']){
	echo "<td><select name='temps2[".($i-1)."][4]'>\n";
	echo "<option value=''>&nbsp;</option>\n";
	echo "<option value='1' >{$config['Multisites-site1']}</option>\n";
	echo "<option value='2' >{$config['Multisites-site2']}</option>\n";
	echo "</select></td>";
      }
      echo "</tr>\n";
    }
    echo "</table>\n";
  }

}

// Choix de la période d'utilisation et validation 
echo <<<EOD
<br/>
EOD;
if(!$configHebdo['periodesDefinies']){
  echo <<<EOD
  <b>Choisissez la période d'utilisation et validez</b> :
  <table style='width:750px;'>
  <tr>
  <td>Date de début</td>
  <td><input type='text' name='debut' />&nbsp;<img src='img/calendrier.gif' onclick='calendrier("debut","form1");' alt='calendrier' /></td>
  <td>Date de fin</td>
  <td><input type='text' name='fin' />&nbsp;<img src='img/calendrier.gif' onclick='calendrier("fin","form1");' alt='calendrier' /></td>
  <td><input type='submit' value='Valider' />
  </tr>
  </table>
EOD;
}
else{
  echo "<input type='submit' value='Valider' />\n";
}

?>
</form>
<script type='text/JavaScript'>
$(".select").change(function(){plHebdoCalculHeures("");});
$(".select2").change(function(){plHebdoCalculHeures(2);});
</script>
</div> <!-- nouveauPlanning -->


<!-- Historique des plannings de présence -->
<div id='historique'>
Mes plannings de présence
<br/>
<table border='0' cellspacing='0' style='width:750px;'>
<tr class='th' style='vertical-align:top;text-align:center;'><td>&nbsp;</td><td>Début</td><td>Fin</td><td>Validation</td><td>Actuel</td></tr>
<?php
$p=new planningHebdo();
$p->perso_id=$_SESSION['login_id'];
$p->fetch();
$class="tr1";
foreach($p->elements as $elem){
  $class=$class=="tr1"?"tr2":"tr1";
  $actuel=$elem['actuel']?"Oui":null;
  $validation="N'est pas validé";
  if($elem['valide']){
    $validation=nom($elem['valide']).", ".dateFr($elem['validation']);
  }
  echo "<tr style='text-align:center;' class='$class'>";
  echo "<td><a href='index.php?page=plugins/planningHebdo/modif.php&amp;id={$elem['id']}&amp;retour=monCompte.php'/>";
    echo "<img src='img/modif.png' alt='Voir' border='0'/></a></td>";
  echo "<td>".dateFr($elem['debut'])."</td>";
  echo "<td>".dateFr($elem['fin'])."</td>";
  echo "<td>$validation</td>";
  echo "<td>$actuel</td>";
  echo "</tr>\n";
}

?>
</table>
</div> <!-- Historique' -->

</div> <!-- PlanningPresence -->

<!-- Crédits -->
<?php
if(in_array("conges",$plugins)){
  echo <<<EOD
  <div id='credits' style='margin-left:80px;display:none;'>
  <h3>Crédits</h3>
  <table class='tableauFiches'>
  <tr><td style='font-weight:bold;' colspan='2'>Congés</td></tr>
EOD;
  echo "<tr><td>Crédit annuel</td><td style='text-align:right;'>".heure4($credits['annuel'])."</td></tr>\n";
  echo "<tr><td>Crédit restant</td><td style='text-align:right;'>".heure4($credits['conges'])."</td></tr>\n";
  echo "<tr><td>Reliquat</td><td style='text-align:right;'>".heure4($credits['reliquat'])."</td></tr>\n";
//   echo "<tr><td>Pris par anticipation</td><td style='text-align:right;'>".heure4($credits['anticipation'])."</td></tr>\n";
  echo "<tr><td style='font-weight:bold;padding-top:20px;' colspan='2'>Récupérations du samedi</td></tr>\n";
  echo "<tr><td>Crédit</td><td style='text-align:right;'>".heure4($credits['recuperation'])."</td></tr>\n";
  echo "</table>\n";
  echo "</div>\n";
}
?>
<!-- Crédits-->

<!-- Mot de Passe -->
<div id='motDePasse' style='margin-left:80px;display:none;'>
<?php
// Mot de passe modifiable seulement si authentification SQL
if($_SESSION['oups']['Auth-Mode']=="SQL"){
  include "personnel/password.php";
}
else{
  echo "<h3>Modification du mot de passe</h3>\n";
  echo "Vous utilisez un système d'authentification centralisé.<br/>\n";
  echo "Votre mot de passe ne peut pas être modifié à partir du planning.<br/>\n";
}
?>
</div> <!-- motDePasse -->
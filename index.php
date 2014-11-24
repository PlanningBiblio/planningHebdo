<?php
/*
Planning Biblio, Plugin planningHebdo Version 1.3.6
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
Copyright (C) 2013-2014 - Jérôme Combes

Fichier : plugins/planningHebdo/index.php
Création : 23 juillet 2013
Dernière modification : 24 juin 2014
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Affiche la liste des plannings de présence pour l'administrateur
Page accessible à partir du menu administration/planning de présence
*/

include "class.planningHebdo.php";

// Initialisation des variables
$tri=isset($_GET['tri'])?$_GET['tri']:"`debut`,`fin`,`nom`,`prenom`";
$debut=isset($_GET['debut'])?$_GET['debut']:(array_key_exists("planningHebdoDebut",$_SESSION['oups'])?$_SESSION['oups']['planningHebdoDebut']:null);
$fin=isset($_GET['fin'])?$_GET['fin']:(array_key_exists("planningHebdoFin",$_SESSION['oups'])?$_SESSION['oups']['planningHebdoFin']:null);
if(isset($_GET['reset'])){
  $debut=null;
  $fin=null;
}
$_SESSION['oups']['planningHebdoDebut']=$debut;
$_SESSION['oups']['planningHebdoFin']=$fin;
$message=null;

// Recherche des plannings
$p=new planningHebdo();
$p->debut=dateFr($debut);
$p->fin=dateFr($fin);
$p->tri=$tri;
$p->fetch();

// Notifications
if(isset($_GET['message'])){
  switch($_GET['message']){
    case "Ajout-OK" :	$message="Le planning a été ajouté avec succés."; $type="highlight";	break;
    case "Ajout-erreur" : $message="Une erreur est survenue lors de l'enregistrement du planning.";  $type="error";	break;
    case "Modif-OK" : 	$message="Le planning a été modifié avec succés."; $type="highlight";	break;
    case "Modif-erreur" : $message="Une erreur est survenue lors de la modification du planning."; $type="error";	break;
  }
  if($message){
    echo "<script type='text/JavaScript'>information('$message','$type');</script>\n";
  }
}

echo <<<EOD
<h3>Plannings de présence</h3>
<form name='form' method='get' action='index.php'>
<input type='hidden' name='page' value='plugins/planningHebdo/index.php' />
Début : <input type='text' name='debut' class='datepicker' value='$debut' />
&nbsp;&nbsp;Fin : <input type='text' name='fin' class='datepicker' value='$fin' />
&nbsp;&nbsp;<input type='submit' value='OK' class='ui-button' />
&nbsp;&nbsp;<input type='button' value='Effacer' onclick='location.href="index.php?page=plugins/planningHebdo/index.php&amp;reset="' class='ui-button' />
<a href='index.php?page=plugins/planningHebdo/configuration.php' style='position:absolute;right:10px;'>Configuration</a>
</form>

<br/>

<table id='tablePlanningHebdo'>
<thead>
  <tr><th>&nbsp;</th><th>Agent</th><th>Service</th><th>Début</th><th>Fin</th><th>Saisie</th><th>Validation</th><th>Actuel</th><th>Commentaires</th></tr>
  </thead>
<tbody>
EOD;
foreach($p->elements as $elem){
  $actuel=$elem['actuel']?"Oui":null;
  $validation="<font style='display:none;'>En attente</font><b>En attente</b>";
  if($elem['valide']){
    $validation="<font style='display:none;'>Valid {$elem['validation']}</font>";
    $validation.=dateFr($elem['validation'],true);
    $validation.=", ".nom($elem['valide']);
  }
  $planningRemplace=$elem['remplace']==0?dateFr($elem['saisie'],true):$planningRemplace;
  $commentaires=$elem['remplace']?"Remplace le planning <br/>du $planningRemplace":null;
  $arrow=$elem['remplace']?"<font style='font-size:20pt;'>&rdsh;</font>":null;

  echo "<tr>";
  echo "<td style='white-space:nowrap;'>$arrow <a href='index.php?page=plugins/planningHebdo/modif.php&amp;id={$elem['id']}&amp;retour=index.php'/>";
    echo "<span class='pl-icon pl-icon-edit' title='Voir'></span></a>";
    echo "<a href='javascript:plHebdoSupprime({$elem['id']});' style='margin-left:6px;'/>";
    echo "<span class='pl-icon pl-icon-drop' title='Supprimer'></span></a></td>";
  echo "<td>{$elem['nom']}</td>";
  echo "<td>{$elem['service']}</td>";
  echo "<td>".dateFr($elem['debut'])."</td>";
  echo "<td>".dateFr($elem['fin'])."</td>";
  echo "<td>".dateFr($elem['saisie'],true)."</td>";
  echo "<td>$validation</td>";
  echo "<td>$actuel</td>";
  echo "<td>$commentaires</td>";
  echo "</tr>\n";
}
echo "</tbody></table>\n";
?>

<script type='text/JavaScript'>
$(document).ready(function() {
  $("#tablePlanningHebdo").dataTable({
    "bJQueryUI": true,
    "sPaginationType": "full_numbers",
    "bStateSave": true,
    "aaSorting" : [[3,"asc"],[4,"asc"],[1,"asc"]],
    "aoColumns" : [{"bSortable":false},{"bSortable":true},{"bSortable":true},{"sType": "date-fr"},{"sType": "date-fr"},{"sType": "date-fr"},{"bSortable":true},{"bSortable":true},{"bSortable":true},],
    "aLengthMenu" : [[25,50,75,100,-1],[25,50,75,100,"Tous"]],
    "iDisplayLength" : 25,
    "oLanguage" : {"sUrl" : "vendor/dataTables.french.lang"}
  });
});
</script>
<?php
/*
Planning Biblio, Plugin planningHebdo Version 1.2.1
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/planningHebdo/index.php
Création : 23 juillet 2013
Dernière modification : 24 juillet 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Affiche la liste des plannings de présence pour l'administrateur
Page accessible à partir du menu administration/planning de présence
*/

include "class.planningHebdo.php";

// Initialisation des variables
$agent=isset($_GET['agent'])?$_GET['agent']:null;
$tri=isset($_GET['tri'])?$_GET['tri']:"`debut`,`fin`,`nom`,`prenom`";
$debut=isset($_GET['debut'])?$_GET['debut']:null;
$fin=isset($_GET['fin'])?$_GET['fin']:null;

$p=new planningHebdo();
$p->agent=$agent;
$p->debut=$debut;
$p->fin=$fin;
$p->tri=$tri;
$p->fetch();

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


echo <<<EOD
<h3>Plannings de présence</h3>
<form name='form' method='get' action='index.php'>
<input type='hidden' name='page' value='plugins/planningHebdo/index.php' />
Début : <input type='text' name='debut' value='$debut' />&nbsp;<img src='img/calendrier.gif' onclick='calendrier("debut");' alt='calendrier' />
&nbsp;&nbsp;Fin : <input type='text' name='fin' value='$fin' />&nbsp;<img src='img/calendrier.gif' onclick='calendrier("fin");' alt='calendrier' />
&nbsp;&nbsp;Agent : <input type='text' name='agent' value='$agent' />
&nbsp;&nbsp;<input type='submit' value='OK' />
&nbsp;&nbsp;<input type='button' value='Effacer' onclick='location.href="index.php?page=plugins/planningHebdo/index.php"' />
<a href='index.php?page=plugins/planningHebdo/configuration.php' style='margin-left:100px;'>Configuration</a>
</form>

<table border='0' cellspacing='0' style='width:850px;margin-top:20px;'>
<tr class='th' style='vertical-align:top;text-align:center;'><td>&nbsp;</td><td>Agent</td><td>Début</td><td>Fin</td><td>Validation</td><td>Actuel</td></tr>
EOD;
$class="tr1";
foreach($p->elements as $elem){
  $class=$class=="tr1"?"tr2":"tr1";
  $actuel=$elem['actuel']?"Oui":null;
  $validation="<b>N'est pas validé</b>";
  if($elem['valide']){
    $validation=nom($elem['valide']).", ".dateFr($elem['validation'],true);
  }
  echo "<tr style='text-align:center;' class='$class'>";
  echo "<td><a href='index.php?page=plugins/planningHebdo/modif.php&amp;id={$elem['id']}&amp;retour=index.php'/>";
    echo "<img src='img/modif.png' alt='Voir' border='0'/></a></td>";
  echo "<td>".nom($elem['perso_id'])."</td>";
  echo "<td>".dateFr($elem['debut'])."</td>";
  echo "<td>".dateFr($elem['fin'])."</td>";
  echo "<td>$validation</td>";
  echo "<td>$actuel</td>";
  echo "</tr>\n";
}
echo "</table>\n";
?>
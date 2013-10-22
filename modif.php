<?php
/*
Planning Biblio, Plugin planningHebdo Version 1.2.10
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/planningHebdo/index.php
Création : 23 juillet 2013
Dernière modification : 8 octobre 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Affiche la liste des plannings de présence pour l'administrateur
Page accessible à partir du menu administration/planning de présence
*/

include "class.planningHebdo.php";

// Recherche de la config
$p=new planningHebdo();
$p->getConfig();
$configHebdo=$p->config;

// Initialisation des variables
$id=$_GET['id'];

$p=new planningHebdo();
$p->id=$id;
$p->fetch();
$debut1=$p->elements[0]['debut'];
$fin1=$p->elements[0]['fin'];
$perso_id=$p->elements[0]['perso_id'];
$temps=$p->elements[0]['temps'];
$valide=$p->elements[0]['valide'];
$remplace=$p->elements[0]['remplace'];

// Sécurité
$admin=in_array(24,$droits)?true:false;
if(!$admin and $perso_id!=$_SESSION['login_id']){
  echo "<div id='acces_refuse'>Accès refusé</div>\n";
  include "include/footer.php";
  exit;
}

// Modif autorisée si n'est pas validé ou si validé avec des périodes non définies (BSB).
// Dans le 2eme cas copie du planning avec modification des dates
$action="modif";
$modifAutorisee=true;
if(!$admin and $valide and $configHebdo['periodesDefinies']){
  $modifAutorisee=false;
}
if(!$admin and $valide){
  $action="copie";
}

?>

<!-- Formulaire Planning-->
<h3>Planning de présence</h3>
<?php echo "Planning de <b>".nom($perso_id,"prenom nom")."</b> du ".dateFr($debut1)." au ".dateFr($fin1);?>
<div id='planning'>
<?php
if(!$configHebdo['periodesDefinies']){
  echo "<form name='form1' method='post' action='index.php' onsubmit='return plHebdoVerifForm();'>\n";
}else{
  echo "<form name='form1' method='post' action='index.php' onsubmit='return verif_form(\"debut=date1;fin=date2Obligatoire\",\"form1\");'>\n";
}
?>
<input type='hidden' name='page' value='plugins/planningHebdo/valid.php' />
<input type='hidden' name='action' value='<?php echo $action; ?>' />
<input type='hidden' name='validation' value='0' />
<input type='hidden' name='retour' value='<?php echo $_GET['retour']; ?>' />
<input type='hidden' name='id' value='<?php echo $id; ?>' />
<input type='hidden' name='perso_id' value='<?php echo $perso_id; ?>' />
<input type='hidden' name='valide' value='<?php echo $valide; ?>' />
<input type='hidden' name='remplace' value='<?php echo $remplace; ?>' />

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
  echo "<table border='1' cellspacing='0' id='tableau{$j}' >\n";
  echo "<tr style='text-align:center;'><td style='width:150px;'>{$cellule[$j]}</td><td style='width:150px;'>Heure d'arrivée</td>";
  echo "<td style='width:150px;'>Début de pause</td><td style='width:150px;'>Fin de pause</td>";
  echo "<td style='width:150px;'>Heure de départ</td>";
  if($config['Multisites-nombre']>1 and $config['Multisites-agentsMultisites']){
    echo "<td>Site</td>";
  }
  echo "</tr>\n";
  for($i=$debut[$j];$i<$fin[$j];$i++){
    $k=$i-($j*7)-1;
    echo "<tr style='text-align:center;'><td>{$jours[$k]}</td>";
    if($modifAutorisee){
      echo "<td>".selectTemps($i-1,0,null,"select")."</td><td>".selectTemps($i-1,1,null,"select")."</td>";
      echo "<td>".selectTemps($i-1,2,null,"select")."</td><td>".selectTemps($i-1,3,null,"select")."</td>";
    }
    else{
      echo "<td>".heure2($temps[$i-1][0])."</td><td>".heure2($temps[$i-1][1])."</td>";
      echo "<td>".heure2($temps[$i-1][2])."</td><td>".heure2($temps[$i-1][3])."</td>";
    }
    if($config['Multisites-nombre']>1 and $config['Multisites-agentsMultisites']){
      echo "<td><select name='temps[".($i-1)."][4]'><option value=''>&nbsp;</option>\n";
      echo "<option value='1' >{$config['Multisites-site1']}</option>\n";
      echo "<option value='2' >{$config['Multisites-site2']}</option>\n";
      echo "</select></td>";
    }
    echo "</tr>\n";
  }
  echo "</table>\n";
  echo "Nombre d'heures : <font id='heures_{$j}' style='font-weight:bold;'>&nbsp;</font><br/>\n";
}

if(!$modifAutorisee){
  echo "<p><b class='important'>Vos horaires ont été validés.</b><br/>Pour les modifier, contactez votre chef de service.</p>\n";
}
elseif($valide and !$admin){
  echo "<p><b class='important'>Vos horaires ont été validés.</b><br/>Si vous souhaitez les changer, modifiez la date de début et/ou de fin d'effet.<br/>";
  echo "Vos nouveaux horaires seront enregistrés et devront être validés par un administrateur.<br/>";
  echo "Les anciens horaires seront conservés en attendant la validation des nouveaux.</p>\n";
}
elseif($valide and $admin){
  echo "<p style='width:850px;text-align:justify;'><b class='important'>Vos horaires ont été validés.</b><br/>";
  echo "En tant qu'administrateur, vous pouvez les modifier et les enregistrer en tant que copie.<br/>";
  echo "Dans ce cas, modifiez la date de début et/ou de fin d'effet. ";
  echo "Vos nouveaux horaires seront enregistrés et devront ensuite être validés. ";
  echo "Les anciens horaires seront conservés en attendant la validation des nouveaux.<br/>";
  echo "Vous pouvez également les enregistrer directement mais dans ce cas, vous ne conserverez pas les 
    anciens horaires.</p>\n";
}

    

// Choix de la période d'utilisation et validation
echo "<br/>\n";

if($modifAutorisee){
  echo "<b>Choisissez la période d'utilisation et validez</b> :\n";
}

echo "<table style='width:900px;'>\n";

// if($modifAutorisee and !$configHebdo['periodesDefinies']){
if(!$configHebdo['periodesDefinies']){
  echo <<<EOD
    <tr>
    <td>Date de début</td>
    <td><input type='text' name='debut' value='$debut1'/>&nbsp;<img src='img/calendrier.gif' onclick='calendrier("debut","form1");' alt='calendrier' /></td>
    <td>Date de fin</td>
    <td><input type='text' name='fin' value='$fin1'/>&nbsp;<img src='img/calendrier.gif' onclick='calendrier("fin","form1");' alt='calendrier' /></td></tr>
EOD;
}
else{
  echo <<<EOD
    <tr>
    <td><input type='hidden' name='debut' value='$debut1'/></td>
    <td><input type='hidden' name='fin' value='$fin1'/></td></tr>
EOD;
}
echo <<<EOD
  <tr><td colspan='4' style='padding-top:20px;'>
  <input type='button' value='Retour' onclick='location.href="index.php?page=plugins/planningHebdo/{$_GET['retour']}";' />
EOD;

if($admin){
  echo "<input type='submit' value='Enregistrer les modifications SANS valider' style='margin-left:30px;'/>\n";
  if(!$configHebdo['periodesDefinies']){
    echo "<input type='button' value='Enregistrer et VALIDER'  style='margin-left:30px;' onclick='document.forms[\"form1\"].validation.value=1;if(plHebdoVerifForm()){document.forms[\"form1\"].submit();}'/>";
  }else{
    echo "<input type='button' value='Enregistrer et VALIDER'  style='margin-left:30px;' onclick='document.forms[\"form1\"].validation.value=1;document.forms[\"form1\"].submit();'/>";
  }
  if($valide){
    echo "<input type='button' value='Enregistrer une copie' style='margin-left:30px;' onclick='$(\"input[name=action]\").val(\"copie\");$(\"form[name=form1]\").submit();'/>\n";
  }
  echo "</td></tr>\n";
}
elseif($modifAutorisee){
  echo "<input type='submit' value='Enregistrer les modifications' style='margin-left:30px;'/>\n";
}

?>
</table>

</form>
<script type='text/JavaScript'>
$("document").ready(function(){plHebdoCalculHeures2();});
$(".select").change(function(){plHebdoCalculHeures($(this),"");});
</script>

</div> <!-- Planning -->
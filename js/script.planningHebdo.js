/*
Planning Biblio, Plugin planningHebdo Version 1.3.3
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013-2014 - Jérôme Combes

Fichier : plugins/planningHebdo/js/script.planningHebdo.js
Création : 26 août 2013
Dernière modification : 27 février 2014
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier regroupant les fonctions JavaScript utiles à la gestion des plannings de présence
*/

// Fonction permettant d'afficher les heures correspondantes à chaque tableau
// lors de la modification d'un select ou au chargement de la page modif
function plHebdoCalculHeures(object,num){
  // Num : si horaires prédéfinis, 2 tableaux, num = null ou 2
  // Numero : numéro du tableau, en fonction de la variable $config['nb_semaine'], on peut avoir jusqu'à 3 tableaux

  // Récupération du numéro du tableau
  // Si object est un objet, la fonction est appelée par l'événement "change" sur un select
  if(object instanceof jQuery){
    tableau= object.closest("table").attr("id");
    numero = tableau.substring(tableau.length-1,tableau.length);
  }
  // Sinon, object est un entier, la fonction est appelée par la fonction plHebdoCalculHeures2
  // lorsque le document modif.php est chargé
  else{
    numero=object;
  }
  debut=numero*7;
  fin=debut+7;
  heures=0;
  elements=document.forms["form1"].elements;
  
  for(i=debut;i<fin;i++){
    if(elements["temps"+num+"["+i+"][0]"]){
      debut1=elements["temps"+num+"["+i+"][0]"].value;
      fin1=elements["temps"+num+"["+i+"][1]"].value;
      debut2=elements["temps"+num+"["+i+"][2]"].value;
      fin2=elements["temps"+num+"["+i+"][3]"].value;
    }
    else{
      debut1=heure5($("#temps"+num+"_"+i+"_0").text());
      fin1=heure5($("#temps"+num+"_"+i+"_1").text());
      debut2=heure5($("#temps"+num+"_"+i+"_2").text());
      fin2=heure5($("#temps"+num+"_"+i+"_3").text());
    }
    if(debut1){
      diff=0;
      // Journée avec pause le midi
      if(debut1 && fin1 && debut2 && fin2){
	diff=diffMinutes(debut1,fin1);
	diff+=diffMinutes(debut2,fin2);
      }
      // Matin uniquement
      else if(debut1 && fin1){
	diff=diffMinutes(debut1,fin1);
      }
      // Après midi seulement
      else if(debut2 && fin2){
	diff=diffMinutes(debut2,fin2);
      }
      // Journée complète sans pause
      else if(debut1 && fin2){
	diff=diffMinutes(debut1,fin2);
      }
      heures+=diff;
      
      // Affichage du nombre d'heure pour chaque ligne
      if(diff){
	$("#heures"+num+"_"+numero+"_"+(i+1)).html(heure4(diff/60));
      }
    }
  }
  heures=heure4(heures/60);
  document.getElementById("heures"+num+"_"+numero).innerHTML=heures;
}

// Fonction permettant d'afficher les heures correspondantes à chaque tableau
// lors de l'affichage de la page modif.php. Appelle la fonction plHebdoCalculHeures.
function plHebdoCalculHeures2(){
  $("table[id^='tableau']").each(function(){
    id=$(this).attr("id");
    numero=id.substring(id.length-1,id.length);
    plHebdoCalculHeures(numero,"");
  });
}

function plHebdoSupprime(id){
  if(confirm("Etes vous sûr(e) de vouloir supprimer ce planning de présence ?")){
    f=file("index.php?page=plugins/planningHebdo/supprime.php&id="+id);
    document.location.reload(false);
  }
}

function plHebdoVerifForm(){
  debut=$("input[name=debut]").val().replace(/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/g,"$3-$2-$1");
  fin=$("input[name=fin]").val().replace(/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/g,"$3-$2-$1");
  id=$("input[name=id]").val();
  perso_id=$("input[name=perso_id]").val();
  
  id=id?"&id="+id:null;
  perso_id=perso_id?"&perso_id="+perso_id:null;
  
  if(!debut || !fin){
    alert("Les dates de début et de fin sont obligatoires");
    return false;
  }
  
  if(fin<debut){
    alert("La date de fin doit être supérieure à la date de début");
    return false;
  }
  
  f=file("plugins/planningHebdo/ajax.verifPlannings.php?debut="+debut+"&fin="+fin+id+perso_id);
  tmp=f.split("###");
  if(tmp[1]=="OK"){
    return true;
  }
  
  if(perso_id){
    message="Un planning est enregistré pour cet agent pour la période du "+dateFr(tmp[1])+" au "+dateFr(tmp[2])
      +"\nVeuillez modifier les dates de début et/ou de fin ou modifier le premier planning."
  }
  else{
    message="Vous avez déjà enregistré un planning pour la période du "+dateFr(tmp[1])+" au "+dateFr(tmp[2])
      +"\nVeuillez modifier les dates de début et/ou de fin ou modifier le premier planning."
  }
  alert(message);
  return false;
}

function plHebdoVerifFormPeriodesDefinies(){
  var result=true;
  $(".selectAnnee").each(function(){
    if(!$(this).val()){
      result=false;
    }
  });
  if(!result){
    alert("Vous devez choisir l'année universitaire.");
  }
  return result; 
}

/*
Planning Biblio, Plugin planningHebdo Version 1.2.6
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/planningHebdo/js/script.planningHebdo.js
Création : 26 août 2013
Dernière modification : 17 septembre 2013
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
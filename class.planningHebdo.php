<?php
/*
Planning Biblio, Plugin planningHebdo Version 1.2.3
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.txt et COPYING.txt
Copyright (C) 2013 - Jérôme Combes

Fichier : plugins/planningHebdo/class.planningHebdo.php
Création : 23 juillet 2013
Dernière modification : 5 septembre 2013
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Fichier regroupant le fonctions planningHebdo.
Appelé par les autres fichiers du dossier plugins/planningHebdo
*/

// pas de $version=acces direct aux pages de ce dossier => redirection vers la page index.php
if(!$version){
  header("Location: ../../index.php");
}

$path=substr($_SERVER['SCRIPT_NAME'],-9)=="index.php"?null:"../../";
require_once "{$path}personnel/class.personnel.php";

class planningHebdo{
  public $agent=null;
  public $config=array();
  public $dates=array();
  public $debut=null;
  public $elements=array();
  public $error=null;
  public $fin=null;
  public $id=null;
  public $ignoreActuels=null;
  public $periodes=null;
  public $perso_id=null;
  public $tri=null;
  public $valide=null;


  public function planningHebdo(){
  }

  public function add($data){
    // Si $data['annee'] : il y a 2 périodes distinctes avec des horaires définis 
    // (horaires normaux et horaires réduits) soit 2 tableaux à insérer
    if(array_key_exists("annee",$data)){
      // Récupération des horaires
      $this->dates=array($data['annee']);
      $this->getPeriodes();
      $dates=$this->periodes;

      // 1er tableau
      $insert=array("perso_id"=>$_SESSION['login_id'],"debut"=>$dates[0][0],"fin"=>$dates[0][1],"temps"=>serialize($data['temps']));
      $db=new db();
      $db->insert2("planningHebdo",$insert);
      $this->error=$db->error;
      // 2ème tableau
      $insert=array("perso_id"=>$_SESSION['login_id'],"debut"=>$dates[0][2],"fin"=>$dates[0][3],"temps"=>serialize($data['temps2']));
      $db=new db();
      $db->insert2("planningHebdo",$insert);
      $this->error=$db->error?$db->error:$this->error;
    }
    // Sinon, insertion d'un seul tableau
    else{
      $insert=array("perso_id"=>$_SESSION['login_id'],"debut"=>$data['debut'],"fin"=>$data['fin'],"temps"=>serialize($data['temps']));
      $db=new db();
      $db->insert2("planningHebdo",$insert);
      $this->error=$db->error;
    }

    // Envoi d'un mail aux responsables
    $destinataires=array();
    $p=new personnel();
    $p->fetch("nom");

    foreach($p->elements as $elem){
      $tmp=unserialize($elem['droits']);
      if(in_array(24,$tmp)){
	$destinataires[]=$elem['mail'];
      }
    }
    if(!empty($destinataires)){
      $destinataires=join(";",$destinataires);
      $sujet="Nouveau planning de présence, ".html_entity_decode(nom($_SESSION['login_id'],"prenom nom"),ENT_QUOTES|ENT_IGNORE,"UTF-8");
      $message=nom($_SESSION['login_id'],"prenom nom");
      $message.=" a enregistré un nouveau planning de présence dans l'application Planning Biblio<br/>";
      $message.="Rendez-vous dans le menu administration / Plannings de présence de votre application Planning Biblio pour le valider.";
      sendmail($sujet,$message,$destinataires);
    }
  }

  public function fetch(){
    // Filtre de recherche
    $filter="1";

    // Perso_id
    if($this->perso_id){
      $filter.=" AND `perso_id`='{$this->perso_id}'";
    }

    // Date, debut, fin
    $debut=$this->debut;
    $fin=$this->fin;
    $date=date("Y-m-d");
    if($debut){
      $fin=$fin?$fin:$date;
      $filter.=" AND `debut`<='$fin' AND `fin`>='$debut'";
    }
    else{
      $filter.=" AND `fin`>='$date'";
    }


    // Recherche des agents actifs seulement
    $perso_ids=array(0);
    $p=new personnel();
    $p->fetch("nom");
    foreach($p->elements as $elem){
      $perso_ids[]=$elem['id'];
    }

    // Recherche avec le nom de l'agent
    if($this->agent){
      $perso_ids=array(0);
      $p=new personnel();
      $p->fetch("nom",null,$this->agent);
      foreach($p->elements as $elem){
	$perso_ids[]=$elem['id'];
      }
    }

    // Filtre pour agents actifs seulement et recherche avec nom de l'agent
    $perso_ids=join(",",$perso_ids);
    $filter.=" AND `perso_id` IN ($perso_ids)";

    // Valide
    if($this->valide){
      $filter.=" AND `valide`<>0";
    }
  
    // Ignore actuels (pour l'import)
    if($this->ignoreActuels){
      $filter.=" AND `actuel`=0";
    }
  
    // Filtre avec ID, si ID, les autres filtres sont effacés
    if($this->id){
      $filter="`id`='{$this->id}'";
    }

    $db=new db();
    $db->select("planningHebdo","*",$filter,"ORDER BY debut,fin,saisie");
    if($db->result){
      foreach($db->result as $elem){
	$elem['temps']=unserialize($elem['temps']);
	$this->elements[]=$elem;
      }
    }
  }

  public function getConfig(){
    $db=new db();
    $db->select("planningHebdoConfig");
    if($db->result){
      foreach($db->result as $elem){
	$this->config[$elem['nom']]=$elem['valeur'];
      }
    }
  }

  public function getPeriodes(){
    if(!empty($this->dates)){
      $dates=array();
      $annees=$this->dates;
      sort($annees);
      $i=0;
      foreach($annees as $annee){
	$db=new db();
	$db->select("planningHebdoPeriodes","*","`annee`='$annee'","ORDER BY `annee`");
	if($db->result){
	  $dates[$i++]=unserialize($db->result[0]['dates']);
	}
	else{
	  $dates[$i++]=null;
	}
      }
    }
  $this->periodes=$dates;
  }


  public function update($data){
    $temps=serialize($data['temps']);
    $update=array("debut"=>$data['debut'],"fin"=>$data['fin'],"temps"=>$temps,"modif"=>$_SESSION['login_id'],"modification"=>date("Y-m-d H:i:s"));
    if($data['validation']){
      $update['valide']=$_SESSION['login_id'];
      $update['validation']=date("Y-m-d H:i:s");
    }
    $db=new db();
    $db->update2("planningHebdo",$update,array("id"=>$data['id']));
    $this->error=$db->error;

    // Remplacement du planning de la fiche agent si validation et date courante entre debut et fin
    if($data['validation'] and $data['debut']<=date("Y-m-d") and $data['fin']>=date("Y-m-d")){
      $db=new db();
      $db->update("personnel","`temps`='$temps'","`id`='{$data['perso_id']}'");
      $db=new db();
      $db->update("planningHebdo","`actuel`='0'","`perso_id`='{$data['perso_id']}'");
      $db=new db();
      $db->update("planningHebdo","`actuel`='1'","`id`='{$data['id']}'");
    }

    // Envoi d'un mail aux responsables et à l'agent concerné
    // L'agent
    $destinataires=array();
    $p=new personnel();
    $p->fetchById($data['perso_id']);
    $destinataires[]=$p->elements[0]['mail'];

    // Les admins
    $p=new personnel();
    $p->fetch("nom");

    foreach($p->elements as $elem){
      $tmp=unserialize($elem['droits']);
      if(in_array(24,$tmp) and $elem['id']!=$_SESSION['login_id']){
	$destinataires[]=$elem['mail'];
      }
    }
    if(!empty($destinataires)){
      if($data['validation']){
	$sujet="Validation d'un planning de présence, ".html_entity_decode(nom($data['perso_id'],"prenom nom"),ENT_QUOTES|ENT_IGNORE,"UTF-8");
	$message="Un planning de présence de ";
	$message.=nom($data['perso_id'],"prenom nom");
	$message.=" a été validé dans l'application Planning Biblio<br/>";
      }
      else{
	$sujet="Modification d'un planning de présence, ".html_entity_decode(nom($data['perso_id'],"prenom nom"),ENT_QUOTES|ENT_IGNORE,"UTF-8");
	$message="Un planning de présence de ";
	$message.=nom($data['perso_id'],"prenom nom");
	$message.=" a été modifié dans l'application Planning Biblio<br/>";
      }
      $destinataires=join(";",$destinataires);
      sendmail($sujet,$message,$destinataires);
    }
  }
  
  public function updateConfig($data){
    $set=array("valeur"=>$data['periodesDefinies']);
    $where=array("nom"=>"periodesDefinies");
    $db=new db();
    $db->update2("planningHebdoConfig",$set,$where);
    $this->error=$db->error?true:false;
  }

  public function updatePeriodes($data){
    $annee=array($data['annee'][0],$data['annee'][1]);
    $dates=array(serialize($data['dates'][0]),serialize($data['dates'][1]));

    for($i=0;$i<count($annee);$i++){
      $db=new db();
      $db->delete("planningHebdoPeriodes","`annee`='{$annee[$i]}'");
      $this->error=$db->error?true:false;
      $insert=array("annee"=>$annee[$i],"dates"=>$dates[$i]);
      $db=new db();
      $db->insert2("planningHebdoPeriodes",$insert);
      $this->error=$db->error?true:$this->error;
    }
  }
    
}

?>
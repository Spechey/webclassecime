<?php
class classecime
{
	static $sep = "-";
	static $bdd;
	static $bddCode = array();
	
	
    public function __get($property)
    {
		switch ($property)
		{
			case "id":
				return implode(self::$sep,$this->ids);
			break;
			case "Epreuve":
				$className = get_class($this);
				if (!isset($this->mother))
					$this->mother = new $property($this->id);
				return $this->mother;
			case "Code_manche":
				return (isset($this->ids["Code_manche"]))?$this->ids["Code_manche"]:1;				
			default:
				return $this->$property;
		}
		
    }
  
	function __construct($id=0,$data=array())
    {
		if (!is_array($id))
			$id = explode(self::$sep,$id);
		
		switch(get_class($this))
		{
				case "classecime":
		
					// vide : class cime obj
					$this->ids  = $id;
					$this->data = $data;
				break;
				default:
					$this->ids   = $id;
					if (sizeof($data) > 0)
					{
						$this->data = $data;
					}
					else
						$this->loadData();
				break;
		}
		
    }
	function getObj($query,$className)
	{
		$rt = array();
		$rs = self::$bdd->query($query);
		while($e = $rs->fetchArray(SQLITE3_ASSOC))
		{
			$ids = array();
			for($i=0;$i<sizeof($className::$bddCode);$i++)
				$ids[$className::$bddCode[$i]] = $e[$className::$bddCode[$i]];
			// $e[get_class($this)] = $this->data; // load automatiques...
			$rt[] = new $className($ids,$e);
		}
		return $rt;
	}
	function reload()
	{
			$ids = array();
			$className = get_class($this);
			for($i=0;$i<sizeof($className::$bddCode);$i++)
					$ids[] = $this->ids [$className::$bddCode[$i]];
			$this->ids = $ids;
			$this->loadData();
	}
	function loadData()
	{	
		$sql = $this->sqlLoadData();
		$rs = self::$bdd->query($sql);
		$row=0;
		while ($this->data = $rs->fetchArray(SQLITE3_ASSOC))
			$row++;
		
		if ($row == 1) 
		{ 
			$this->data = $rs->fetchArray(SQLITE3_ASSOC);

				$className = get_class($this);
				for($i=0;$i<sizeof($className::$bddCode);$i++)
					$ids[$className::$bddCode[$i]] = $this->data [$className::$bddCode[$i]];			
				$this->ids = $ids;
		}
		elseif($row == 0)
		{
				$className = get_class($this);
				for($i=0;$i<sizeof($className::$bddCode);$i++)
					$ids[$className::$bddCode[$i]] = $this->ids [$i];			
				$this->ids = $ids;
		}
		else
		{
				throw new Exception('Nombre incoherent de resulat au chargement de l\'object '+get_class($this)+" "+$q);
		}
	}	
	function getEvenements()
	{	
		return $this->getObj("select * from Evenement","Evenement");
	}

}
class Evenement extends classecime
{
	static $bddCode = array("Code");
	
	function sqlLoadData()
	{
		return "select * from Evenement where code='".$this->ids[0]."'";
	}	
	function getEpreuves()
	{
		return $this->getObj("select * from Epreuve 
									where Code_evenement = '".$this->data["Code"]."'","Epreuve");
	}
	function getConcurents()
	{
		//var_dump($this);
		return $this->getObj("select * from resultat
										where Code_evenement = '".$this->ids["Code"]."'
										order by Dossard","Coureur");		
	}
}
class Epreuve extends classecime
{
	static $bddCode = array("Code_evenement","Code_epreuve");
	
	function sqlLoadData()
	{
		return "select * from Epreuve where Code_evenement ='".$this->ids[0]."' and Code_epreuve='".$this->ids[1]."'";
	}
	function getManches()
	{
		return $this->getObj("select * from Epreuve_Escalade_Diff 
									where Code_evenement = '".$this->data["Code_evenement"]."'
									and    Code_epreuve  = '".$this->data["Code_epreuve"]."'","Manche");
	}
}
class Manche extends classecime
{
	static $bddCode = array("Code_evenement","Code_epreuve","Code_niveau");
	var $filters = array();
	function sqlLoadData()
	{
		return "select * from Epreuve_Escalade_Diff 
									where Code_evenement = '".$this->ids[0]."'
									and   Code_epreuve   = '".$this->ids[1]."'
									and   Code_niveau    = '".$this->ids[2]."'";
	}	
	function addFilter($k,$v)
	{
		$this->filters[] = array("name"=>addslashes ($k), "value"=>addslashes ($v)); 
		
	}
	function getFiltersValues($names=array("Categ","Sexe","Club","Dept","Ligue","Certificat_Medical"))
	{
		$filters = array();
		for ($i=0;$i<sizeof($names);$i++)
		{
			$filters[$names[$i]] = $this->getFilter($names[$i]);
			$filters[$names[$i]][] = "*";
		}
		return $filters;
	}
	function getFilter($filterCol)
	{
		$r = array();
		$q = "select distinct $filterCol as filter_values from Resultat_Manche, resultat 
										where resultat.Code_coureur = Resultat_Manche.Code_coureur 
										and Resultat_Manche.Code_evenement = '".$this->data["Code_evenement"]."'
										and Resultat_Manche.Code_manche > ".$this->data["Code_niveau"]."000
										and Resultat_Manche.Code_manche < ".$this->data["Code_niveau"]."999";
		
		$rs = self::$bdd->query($q);
		while($e = $rs->fetchArray(SQLITE3_ASSOC))
		{
			$r[] = $e["filter_values"];
		}
		return $r;							
	}
	function getResultats()
	{
		return $this->getObj("select *,(Code_manche - ".$this->data["Code_niveau"]."000) as Bloc from Resultat_Manche 
										where Code_evenement = '".$this->data["Code_evenement"]."'
										and Code_manche > ".$this->data["Code_niveau"]."000
										and Code_manche < ".$this->data["Code_niveau"]."999","Resultat");
	}
	function getResultatsByCoureurs()
	{
		$bp = $this->getPointsBlocs();
		$filter = "";
		/*
		$byC = $this->getObj("select code_coureur,GROUP_CONCAT( (Code_manche - ".$this->data["Code_niveau"]."000) ) as Blocs from Resultat_Manche 
										where Code_evenement = '".$this->data["Code_evenement"]."'
										and Code_manche > ".$this->data["Code_niveau"]."000
										and Code_manche < ".$this->data["Code_niveau"]."999
										group by code_coureur","Resultat");
				
		if ($this->data["Categ"] != "*")
				echo "<th>".$c->data["Categ"]." ".$c->data["Sexe"]."</th>";
*/
/*
		if ($this->mother->data["Code_categorie"] != '*')
			$filter .= " AND Resultat.Categ = '".$this->mother->data["Code_categorie"]."'";
		if ($this->mother->data["Sexe"] != 'T')
			$filter .= " AND Resultat.Sexe = '".$this->mother->data["Sexe"]."'";
		if (isset($this->mother->data["Distance"]) && $this->mother->data["Distance"] != null)
			$filter .= " AND Resultat.Groupe = '".$this->mother->data["Distance"]."'";
*/


		if ($this->Epreuve->data["Code_categorie"] != '*')
			$filter .= " AND Resultat.Categ = '".$this->Epreuve->data["Code_categorie"]."'";
		if ($this->Epreuve->data["Sexe"] != 'T')
			$filter .= " AND Resultat.Sexe = '".$this->Epreuve->data["Sexe"]."'";
		if (isset($this->Epreuve->data["Distance"]) && $this->Epreuve->data["Distance"] != null)
			$filter .= " AND Resultat.Groupe = '".$this->Epreuve->data["Distance"]."'";


		for($i=0;$i<sizeof($this->filters);$i++)
		{
			
			if ($this->filters[$i]["value"] == "*")
				$filter .= " AND ".$this->filters[$i]["name"]." != ''";
			else
			{
				$val = ($this->filters[$i]["value"])?" = '".$this->filters[$i]["value"]."'":"is NULL";
				$filter .= " AND ".$this->filters[$i]["name"]." $val";
			}
		}
		$q = "SELECT *,GROUP_CONCAT( (Resultat_Manche.Code_manche - ".$this->data["Code_niveau"]."000) ) as Blocs
							FROM resultat
							LEFT JOIN Resultat_Manche ON resultat.Code_evenement = Resultat_Manche.Code_evenement
														AND resultat.code_coureur = Resultat_Manche.code_coureur
							
										WHERE Resultat_Manche.Code_evenement = '".$this->data["Code_evenement"]."'
										and Resultat_Manche.Code_manche > ".$this->data["Code_niveau"]."000
										and Resultat_Manche.Code_manche < ".$this->data["Code_niveau"]."999
										".$filter."
										group by resultat.code_coureur";
		$q = str_replace("\'","''",$q);
		
		$byC = $this->getObj($q,"Resultat");
				
		$return = array();
		foreach($byC as $e)
		{
			$blocs_num = $e->getBlocsNum();
			$blocs_infos = array();
			$total_pt = 0;
			$total_blocs = 0;
			for ($i=0;$i<sizeof($blocs_num);$i++)
			{
				$blocs_infos[$blocs_num[$i]] = $bp[$blocs_num[$i]];
				$total_blocs++;
				$total_pt += $bp[$blocs_num[$i]];
			}
			$e->setBlocsInfos(array("TotalBlocs" => $total_blocs,
									"TotalPoints"=> $total_pt,
									"Details"    => $blocs_infos));

			$return[] = $e;
		}

		// tri !!
		usort($return,"sortBlocsDescResult" );
		for($i=0;$i<sizeof($return);$i++)
		{
			if ($i>0 
				&& $return[$i]->data["BlocsInfos"]["TotalPoints"] ==  $return[($i-1)]->data["BlocsInfos"]["TotalPoints"])
				$return[$i]->data["Classement"] = $return[($i-1)]->data["Classement"];
			else
				$return[$i]->data["Classement"] = ($i+1);
		}
		
		
		return $return;
	}
	function getPointsBlocs()
	{
		$pointsBlocs = array();
		$rs =self::$bdd->query("select (Code_manche - ".$this->data["Code_niveau"]."000) as Bloc, ROUND(CAST(1000 AS float) / CAST (count(*) AS float),2)  as Points from Resultat_Manche 
										where Code_evenement = '".$this->data["Code_evenement"]."'
										and Code_manche > ".$this->data["Code_niveau"]."000
										and Code_manche < ".$this->data["Code_niveau"]."999
										group by Code_manche");

		while($e = $rs->fetchArray(SQLITE3_ASSOC))
		{
			$pointsBlocs[$e["Bloc"]] = $e["Points"];
		}
		for ($i=1;$i<=$this->getNbVoies();$i++)
		{
			
			if (!isset($pointsBlocs[$i]))
				$pointsBlocs[$i] = 1000;
		}
		ksort($pointsBlocs);
		return $pointsBlocs;
	}
	function getNbVoies()
	{
		if (isset($this->data["Nb_voies"]))
			return $this->data["Nb_voies"];
		
		// pkoi round ???????
		$rs =self::$bdd->query("select round(Nb_voies) as Nb_voies from Epreuve_Escalade_Diff 
									where Code_evenement = '".$this->data["Code_evenement"]."'
									and   Code_niveau    = '".$this->data["Code_niveau"]."'");
		$e = $rs->fetchArray(SQLITE3_ASSOC);

		$this->data["Nb_voies"] = $e["Nb_voies"];

		return $this->data["Nb_voies"];
		
	}
	function getFaitBlocs()
	{
		$fBlocs = array();
		$rs =self::$bdd->query("select (Code_manche - ".$this->data["Code_niveau"]."000) as Bloc, count(*) Fait from Resultat_Manche 
										where Code_evenement = '".$this->data["Code_evenement"]."'
										and Code_manche > ".$this->data["Code_niveau"]."000
										and Code_manche < ".$this->data["Code_niveau"]."999
										group by Code_manche");
		while($e = $rs->fetchArray(SQLITE3_ASSOC))
		{
			$fBlocs[$e["Bloc"]] = $e["Fait"];
		}
		return $fBlocs;
	}	
}

class Resultat extends classecime
{
	static $bddCode = array("Code_evenement","Code_manche","Code_coureur");
	
	function getBlocsNum()
	{
		if (isset($this->data["Blocs"]))
			return explode(",",$this->data["Blocs"]);
		return array();
	}
	function setBlocsInfos($nfo)
	{
		$this->data["BlocsInfos"] = $nfo;
	}
	function getTotalPoints()
	{
		
		if (isset($this->data["BlocsInfos"])
			&& isset($this->data["BlocsInfos"]["TotalPoints"]))
			return $this->data["BlocsInfos"]["TotalPoints"];
		return 0;
	}
}
class Coureur extends classecime
{
	static $bddCode = array("Code_evenement","Code_coureur");
	
	function sqlLoadData()
	{
		return "select * from resultat where Code_evenement = '".$this->ids[0]."'  and (Code_coureur='".$this->ids[1]."' or Dossard='".$this->ids[1]."')";
	}

	function getResultatManches()
	{
		$q = "SELECT * FROM Resultat_Manche 
					   WHERE Code_evenement = '".$this->data["Code_evenement"]."'
					   and   Code_coureur   = '".$this->data["Code_coureur"]."'
					   and   Code_manche    < 1000";
		$r = $this->getObj($q,"Resultat");
		if (sizeof($r) > 0)
			return $r;
		
		/*$evenement = new Evenement($this->data["Code_evenement"]);
		$epreuves = $evenement->getEpreuves();		
		for ()
		$epreuves["Nombre_de_manche"];*/
	    // on gere une seul manche ...

		$Code_manche  = 1;
		$q = "insert into Resultat_Manche (Code_evenement,Code_coureur,Code_manche,Rang,Heure_depart,Status,PtsClt)
					Values('".$this->data["Code_evenement"]."','".$this->data["Code_coureur"]."','".$Code_manche."',null,0,'O',0)";
		self::$bdd->query($q);			
		
		$q = "SELECT * FROM Resultat_Manche 
					   WHERE Code_evenement = '".$this->data["Code_evenement"]."'
					   and   Code_coureur   = '".$this->data["Code_coureur"]."'
					   and   Code_manche    < 1000";
		$r = $this->getObj($q,"Resultat");		

		return $r; 		
	}
	function getResultat($manche=1)
	{
		$mancheObj = new Manche(0,array("Code_evenement"=>$this->data["Code_evenement"],"Code_niveau" => $manche));
		$manchePts = $mancheObj->getPointsBlocs();
		
		$q = "SELECT * FROM Resultat_Manche 
					   WHERE Code_evenement = '".$this->data["Code_evenement"]."'
					   and   Code_coureur   = '".$this->data["Code_coureur"]."'
					   and   Code_manche    > ".$manche."000
					   and   Code_manche    < ".$manche."999
					   order by Code_manche";
					   
					
		$rs = $this->getObj($q,"Resultat");
		$rsParse = array();
		foreach($rs as $r)
		{
			
			$bckId = $r->data["Code_manche"]-($manche*1000);
			$rsParse[$bckId] = $r->data["Status"];
		}
		$return = array();
		

		foreach($manchePts as $bckId => $pt)
		{
			
			$bckIds   = $this->ids;
			$bckIds[] = $bckId+($manche*1000);
			$return[$bckId] = new CoureurBlock( $bckIds,
												array( "pts"     => $pt,
													   "Status" => (isset($rsParse[$bckId]))?$rsParse[$bckId]:false)
											  );
		}
		return $return;
	}	
}
class CoureurBlock extends classecime
{
	static $bddCode = array("Code_evenement","Code_coureur","Code_manche");
	function sqlLoadData()
	{
		return "select * from Resultat_Manche where Code_evenement = '".$this->ids[0]."'  
											and Code_coureur='".$this->ids[1]."'
											and Code_manche='".$this->ids[2]."'";
	}
	
	function getStatus()
	{
		return $this->data["Status"];
	}
	function getPts()
	{
		return $this->data["pts"];
	}
	function isValideString()
	{
		return ($this->data["Status"])?"isValide":"notValide";
	}	
	function isValide()
	{
		return ($this->data["Status"])?true:false;
	}	
	function setValide($b)
	{
		$q = "delete from Resultat_Manche 
					where Code_evenement = '".$this->ids["Code_evenement"]."'  
									and Code_coureur='".$this->ids["Code_coureur"]."'
									and Code_manche='".$this->ids["Code_manche"]."'";
		self::$bdd->query($q);			

		if ($b)
		{
				$q = "insert into Resultat_Manche (Code_evenement,Code_coureur,Code_manche,Rang,Heure_depart,Status,PtsClt)
							Values('".$this->ids["Code_evenement"]."','".$this->ids["Code_coureur"]."','".$this->ids["Code_manche"]."',null,0,'O',0)";
				self::$bdd->query($q);			
		}
 
	}
}

function sortBlocsDescResult($a, $b) {
	if ($a->getTotalPoints() == $b->getTotalPoints()) {
		return 0;
	}
	return ($a->getTotalPoints() > $b->getTotalPoints()) ? -1 : 1;
}


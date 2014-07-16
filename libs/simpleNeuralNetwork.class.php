<?php

class SimpleNeuralNetwork {

	private $w;
	private $h;
	
	private $nbrCouches;
	private $nbrNeurones;
	private $nbrOut;

	private $initRandResolution = 1000;

	
	
	public function create($nbrNeuronesIn, $nbrNeuronesOut, $nbrCouches) {
		
		$this->setNbrCouches($nbrCouches);
		$this->setNbrOut($nbrNeuronesOut);
		$this->setNbrNeurones($nbrNeuronesIn);
	
		$this->initW();
		return $this;
	}
	
	// Setters
	public function setNbrCouches($i) {
		$this->nbrCouches = $i;
		return $this;
	}

	public function setNbrOut($i) {
		$this->nbrOut = $i;
		return $this;
	}

	public function setNbrNeurones($i) {
		$this->nbrNeurones = $i;
		return $this;
	}
	
	public function setEntry($arr) {
		// verif si les tailles correpondent
		$this->h[0] = $arr;
	}
	
	public function calcH() {
		// verif si H0 est set (entrée du réseau)
		
		$i = 0;
		while(isset($this->w[$i])) {
			$j = 0;
			while(isset($this->w[$i][$j])) {
				$this->h[$i + 1][$j] = $this->sigmoideF($this->matrixArrMultSum($this->w[$i][$j], $this->h[$i]) );
//				$this->h[$i + 1][$j] = $this->matrixArrMultSum($this->w[$i][$j], $this->h[$i]);				
				$j++;
			}
			$i++;
		}
	
	}
	
	// méthode permettant de déterminer l'erreur entre la sortie d'un réseau de neurone et le résultat attendu
	// formule e = voulu - obtenu
	public function calculErreurCoucheSortie($wanted) {
		// on determine na couche de sortie
		$refOut = count($this->h) - 1;
		$res = $this->matrixArrSoustract($wanted, $this->h[$refOut]);
		return $res;
	}
	
	
	// Parametre $h pour heuristic représenté par o dans la formule
	// d3 = o * ( 1 - o ) * e
	public function calculSignalErreurCoucheSortie($e, $h) {
		$nbLayers = count($this->w);
		$nbOutNeurons = count($this->w[$nbLayers - 1]);
		$vect1 = $this->build1Vect($nbOutNeurons);
		$unmoins = $this->matrixArrSoustract($vect1, $h);
		$omultunmoinso = $this->matrixArrMult($unmoins, $h);
		$signalErreur = $this->matrixArrMult($omultunmoinso, $e);
		return $signalErreur;
	}
	
	
	// Methode permettant de recalculer la premiere couche de connections
	// Couche de sortie
	// R => Z => Zn*dn
	public function recalcLastWeights($signalE) {
		$nbLayers = count($this->w);
		$nbOutNeurons = count($this->w[$nbLayers - 1]);
		$nbConnections = $this->w[$nbLayers - 1][$nbOutNeurons - 1];
	
		$i = 0;
		while($i < $nbOutNeurons) {
			$j = 0;
			while ($j < $nbConnections) {
				$this->w[$nbLayers - 1][$i][$j] *= $signalE[$j];
				$j++;
			}		
			$i++;
		}
	}
	
	
	// méthode permettant de déterminer l'erreur entre la Couche+1 d'un réseau de neurone 
	// et le résultat attendu
	// formule e = voulu - obtenu
	public function calculErreurCoucheOther($n) {
		
		$nbLayers = count($this->w);
		$nbOutNeurons = count($this->w[$n]);
		$vect1 = $this->build1Vect($nbOutNeurons);

		$ret = array();
		$i = 0;
		while($i < 	$nbOutNeurons) {	
			$ret[$i] = $this->matrixArrMult($this->w[$n+1][$i], $vect1);
			$i++;
		}	
		
		return $ret;
		
	}
	
	// d2 = h * (1 - h) * f;
	public function calculSignalErreurCoucheOther($e, $h, $n) {
		$nbLayers = count($this->w);
		$nbOutNeurons = count($this->w[$n]);
		$vect1 = $this->build1Vect($nbOutNeurons);
		$unmoins = $this->matrixArrSoustract($vect1, $h);
		$omultunmoinso = $this->matrixArrMult($unmoins, $h);
		$signalErreur = $this->matrixArrMult($omultunmoinso, $e);
		return $signalErreur;
	}
	
	
	// w = w + d2 * xT (entrée ou heuristique couche avant)
	public function recalcOthersWeights($signalE, $n) {
		$nbLayers = count($this->w);
		$nbOutNeurons = count($this->w[$n]);

		$multVect = array();
		$i = 0;
		
		$arr = array();
		
		while($i < $nbOutNeurons) {
			$multVect[$i] = $this->matrixArrMult($this->build1Vect(count($this->h[$n]), $signalE[$i]), $this->h[$n]));
			$i ++;
		}
		
		$i = 0;
		
		
		// endev 
		/*
		$arr = array();
		
		while($i < $nbOutNeurons) {
			$multVect[$i] = $this->matrixArrMult($this->build1Vect($nb, $signalE[$i]), $this->h[$n]));
			$i ++;
		}
		*/
		
	}
	
	
	// fabrique un array rempli de 1 de dimention $nb
	private function build1Vect($nb, $value = 1) {
		$i = 0;
		$ret = array();
		while ($i < $nb) {
			$ret[$i] =  $value;
			$i++;
		}
		return $ret;
		
	}
	
	// Méthode permettant d'initialiser les poids
	private function initW() {
		for($c = 0; $c < $this->nbrCouches; $c++) {
			for($n = 0; $n < $this->nbrNeurones; $n++) {
				for($k = 0; $k < $this->nbrNeurones; $k++) {
					$this->w[$c][$n][$k] = rand(0, $this->initRandResolution);
					$this->w[$c][$n][$k] /= $this->initRandResolution;
				}
			}
		}
		return $this;
	}
	
	
	public function initWDemo() {
	
		$this->w[0][0] = array(0.5 , 0.3 , 0.1);
		$this->w[0][1] = array(0.3 , 0.2 , 0.1);


		$this->w[1][0] = array(0.1, 0.2);
		$this->w[1][1] = array(0.3, 0.4);
		$this->w[1][2] = array(0.5, 0.6);

	
	}
	
	
	public function sigmoideF($x) {
		$result = 1 / ( 1 + exp( - $x ) );
		return $result;
	}

	
	public function matrixArrMult($a1, $a2) {
		// verif count a = count a2
		$i = 0;
		$ret = array();
		while ($i < count($a1)) {
			$ret[$i] = $a1[$i] * $a2[$i];
			$i++;
		}
		return $ret;
	
	}
	


	public function matrixArrMultSum($a1, $a2) {
	
		// verif count a = count a2
		$sum = 0;
		$i = 0;
		while ($i < count($a1)) {
			$sum += ($a1[$i] * $a2[$i]);
			$i++;
		}
		return $sum;
	
	}
	
	// Soustrait a1 - a2 element par element 
	public function matrixArrSoustract($a1, $a2) {
	
		$res = array();
		$i = 0;
		while ($i < count($a1)) {
			$res[$i] = $a1[$i] - $a2[$i];
			$i++;
		}
		return $ress;
	}
	

	// Methode de sauvegarde du modele
	public function saveModele($file, $externalData = array()) {
		$data = array();
		$data['w'] = $this->w;
		$data['params']['nbrNeurones'] = $this->nbrNeurones;
		$data['params']['nbrOut'] = $this->nbrOut;
		$data['params']['nbrCouches'] = $this->nbrCouches;
		$data['params']['externalData'] = $externalData;
		$sData = json_encode($data);
		file_put_contents($file, $sData);
	}


	// Methode de load du modele
	public function loadModele($file) {
		$sData = file_get_contents($file);
		$data = json_decode($sData);
		$this->w = $data['w'];
		$this->nbrNeurones = $data['params']['nbrNeurones'];
		$this->nbrOut = $data['params']['nbrOut'];
		$this->nbrCouches = $data['params']['nbrCouches'];
		$externalData = $data['params']['externalData'];
		return $externalData;
	}



};

// Alias
class SNN extends SimpleNeuralNetwork {};

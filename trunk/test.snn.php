<?php
	// Defines des chemins du projet
	define ("PATH_CURRENT", "./" );
	define ("PATH_CONFIGS", PATH_CURRENT . "configs/");
	define ("PATH_LIBS", PATH_CURRENT . "libs/" );
	define ("CACHE", PATH_CURRENT . "cache/");


	
	// inclusion des configs
	//require_once(PATH_CONFIGS."db.php");
	
	// inclusion des libs
	require_once(PATH_LIBS."sql.class.php");
	require_once(PATH_LIBS."simpleNeuralNetwork.class.php");

	
	
	$brain = new SNN();
//	$brain->create(20,20,10);
	
//	$v = $brain->sigmoideF(-1.4);

//	$a1 = array(0.5,0.3,0.1);
//	$a1 = array(0.3,0.2,0.1);
//	$a2 = array(1,2,3);
//	$v = $brain->matrixArrMultSum($a1, $a2);


	$brain->initWDemo();
	$brain->setEntry(array(1,2,3));
	$brain->calcH();
	
	// xT reponse attendue - variable $wanted
	$wanted = array(0.1, 0.3, 0.7);
	// test error out
	$e = $brain->calculErreurCoucheSortie($wanted);
	
	var_dump($e);
	var_dump($brain);


	//var_dump($brain);


	function e($s) {
		echo($s . "\n");
	}

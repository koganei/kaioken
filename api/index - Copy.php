<?php

/****
The main routing controller for the API

*/
$GLOBALS['strCharSet'] = 'utf-8';
mb_internal_encoding("UTF-8");


require_once 'includes/constants.php';
require_once 'libs/underscore.php';
require_once 'libs/epiphany/Epi.php';

if($_SERVER['SERVER_NAME'] != "localhost") {
// we specificy a base of operations
	Epi::setPath('base', '/home/p25lh1dg/public_html/dev/desrochers/api/libs/epiphany/');

}

Epi::init('api', 'database');

EpiDatabase::employ('mysql', DB_NAME, DB_SERVER, DB_USER, DB_PASSWORD);

getDatabase()->execute("SET NAMES 'utf8'");

getRoute()->get('/', '_root');

getApi()->get('/vins.json', 'apiVins', EpiApi::external);
getApi()->put('/vins.json/(\d)+', 'putVins', EpiApi::external);
getApi()->delete('/vins.json/(\d)+', 'deleteVins', EpiApi::external);



getApi()->get('/conseils.json', 'apiConseils', EpiApi::external);
getApi()->put('/conseils.json/(\d)+', 'putConseils', EpiApi::external);

getApi()->get('/evenements.json', 'apiEvenements', EpiApi::external);
getApi()->put('/evenements.json/(\d)+', 'putEvenements', EpiApi::external);

getApi()->get('/types.json', 'apiTypes', EpiApi::external);
getApi()->put('/types.json/(\d)+', 'putTypes', EpiApi::external);

getRoute()->run();

/*
 * ******************************************************************************************
 * Define functions and classes which are executed by EpiCode based on the $_['routes'] array
 * ******************************************************************************************
 */

function _root() {
	// if the root is called

	echo "This isn't where I parked my car!";

}

function showVins($id = 0, $name = false) {
	// $id will always be a digit


	if($id > 0) {
		$vins = getApi()->invoke('/vins.json', EpiRoute::httpGet, array('_GET' => array('id' => $id)));

		//this is where the templating goes for a specific page
		echo '<ul>';
		foreach($vins as $vin) {
			echo "<li>{$vin['id']} - {$vin['name']}</li>";
		}
		echo '</ul>';
	} else {
		// redirect to main page's carte de vins
		$vins = getApi()->invoke('/vins.json', EpiRoute::httpGet);

		//this is where the templating goes for a specific page
		echo '<ul>';
		foreach($vins as $vin) {
			echo "<li>{$vin['id']} - {$vin['name']}</li>";
		}
		echo '</ul>';
	}
}

function showVinsCollection($name) {
	// in case we get a reference to a collection of wine,
	// rather than a specific wine

	showVins(0, $name);
}

function putVins() {
	if($_SERVER['REQUEST_METHOD'] == 'PUT') {
    
	    parse_str(file_get_contents("php://input"), $post_vars);
	    
	    /**
	     *  We can totally just take $post_vars and add him to the database
	     */
		  $keys = array_keys($post_vars);
		  for ($i=0; $i < sizeof($keys); $i++) { 
		  	if(substr_compare($keys[$i], "id", 0)) {
		  		$vin = json_decode($keys[$i]);
		  	}
		  }
		 
			if(isset($vin->id)) {
				
				if($vin->id > 0 && $vin->id != 9000) {
					// we update
					getDatabase()->execute("UPDATE " . DB_VINSTABLE . " SET `name` = :name, `blurb` =  :blurb, `description` =  :description, `price` =  :price, `type` =  :type, `year` =  :year WHERE  `id` =:id", array(':name' => $vin->name, ':blurb' => $vin->blurb, ':description' => $vin->description, ':price' => $vin->price, ':type' => $vin->type, ':year'=>$vin->year, ':id' => $vin->id));

				} else if($vin->id == 9000) {
					// we make a new one
					
					getDatabase()->execute("INSERT INTO " . DB_VINSTABLE . " (`id` ,`name` ,`blurb` ,`description` ,`price` ,`type` ,`year`) VALUES (NULL ,  :name,  :blurb,  :description,  :price,  :type,  :year)", array(':name' => $vin->name, ':blurb' => $vin->blurb, ':description' => $vin->description, ':price' => $vin->price, ':type' => $vin->type, ':year'=>$vin->year));
				} else return false;

			} 



		    return $vin;
	} else return false;
}

function deleteVins($test) {
	parse_str(file_get_contents("php://input"), $post_vars);
	    
	    print_r($test);
	    print_r($post_vars);
	    /**
	     *  We can totally just take $post_vars and add him to the database
	     */
		  $keys = array_keys($post_vars);
		  for ($i=0; $i < sizeof($keys); $i++) { 
		  	if(substr_compare($keys[$i], "id", 0)) {
		  		$vin = json_decode($keys[$i]);
		  	}
		  }
		 
			// if(isset($vin->id)) {
				
			// 	if($vin->id > 0 && $vin->id != 9000) {
			// 		// we update
			// 		getDatabase()->execute("UPDATE " . DB_VINSTABLE . " SET `name` = :name, `blurb` =  :blurb, `description` =  :description, `price` =  :price, `type` =  :type, `year` =  :year WHERE  `id` =:id", array(':name' => $vin->name, ':blurb' => $vin->blurb, ':description' => $vin->description, ':price' => $vin->price, ':type' => $vin->type, ':year'=>$vin->year, ':id' => $vin->id));

			// 	} else if($vin->id == 9000) {
			// 		// we make a new one
					
			// 		getDatabase()->execute("INSERT INTO " . DB_VINSTABLE . " (`id` ,`name` ,`blurb` ,`description` ,`price` ,`type` ,`year`) VALUES (NULL ,  :name,  :blurb,  :description,  :price,  :type,  :year)", array(':name' => $vin->name, ':blurb' => $vin->blurb, ':description' => $vin->description, ':price' => $vin->price, ':type' => $vin->type, ':year'=>$vin->year));
			// 	} else return false;

			// }

		    return $vin;
}

	function putTypes() {
		if($_SERVER['REQUEST_METHOD'] == 'PUT') {
	    
	    parse_str(file_get_contents("php://input"), $post_vars);
	    
	    /**
	     *  We can totally just take $post_vars and add him to the database
	     */
		  $keys = array_keys($post_vars);
		  for ($i=0; $i < sizeof($keys); $i++) { 
		  	if(substr_compare($keys[$i], "id", 0)) {
		  		$item = json_decode($keys[$i]);
		  	}
		  }
		 
		if(isset($item->id)) {
			
			if($item->id > 0) {
				// we update
				getDatabase()->execute("UPDATE " . DB_TYPESTABLE . " SET `type` = :type, `description` = :description WHERE  `id` =:id", array(':type' => $item->type, ':description' => $item->description, ':id' => $item->id));

			} else if($item->id == 0) {
				// we make a new one
			}

		} 



		    return $item;
		} else return false;
	}

	function putConseils() {
		if($_SERVER['REQUEST_METHOD'] == 'PUT') {
	    
	    parse_str(file_get_contents("php://input"), $post_vars);
	    
	    /**
	     *  We can totally just take $post_vars and add him to the database
	     */
		  $keys = array_keys($post_vars);
		  for ($i=0; $i < sizeof($keys); $i++) { 
		  	if(substr_compare($keys[$i], "id", 0)) {
		  		$item = json_decode($keys[$i]);
		  	}
		  }
		 
		if(isset($item->id)) {
			
			if($item->id > 0) {
				// we update
				getDatabase()->execute("UPDATE " . DB_CONSEILSTABLE . " SET `title` = :title, `description` = :description WHERE  `id` =:id", array(':title' => $item->title, ':description' => $item->description, ':id' => $item->id));

			} else if($item->id == 0) {
				// we make a new one
			}

		} 



		    return $item;
		} else return false;
	}

	function putEvenements() {
		if($_SERVER['REQUEST_METHOD'] == 'PUT') {
	    
	    parse_str(file_get_contents("php://input"), $post_vars);
	    
	    /**
	     *  We can totally just take $post_vars and add him to the database
	     */
		  $keys = array_keys($post_vars);
		  for ($i=0; $i < sizeof($keys); $i++) { 
		  	if(substr_compare($keys[$i], "id", 0)) {
		  		$item = json_decode($keys[$i]);
		  	}
		  }
		 
		if(isset($item->id)) {
			
			if($item->id > 0) {
				// we update
				getDatabase()->execute("UPDATE " . DB_EVENEMENTSTABLE . " SET `title` = :title, `description` = :description WHERE  `id` =:id", array(':title' => $item->title, ':description' => $item->description, ':id' => $item->id));

			} else if($item->id == 0) {
				// we make a new one
			}

		} 



		    return $item;
		} else return false;
	}

function apiVins() {
	// we get the stuff from the database

	if(array_key_exists('id', $_GET) > 0) {

		$vins = getDatabase()->all('SELECT * FROM ' . DB_VINSTABLE . ' WHERE id=:id', array(':id' => $_GET['id']));
		
	} else {
		$vins = getDatabase()->all('SELECT * FROM ' . DB_VINSTABLE);
	}

	return $vins;
}

function showConseils($id = 0, $pagestep = -1) {
	// $id will always be a digit

	if($id > 0 && $pagestep < 0) {
		$conseils = getApi()->invoke('/conseils.json', EpiRoute::httpGet, array('_GET' => array('id' => $id)));

		//this is where the templating goes for a specific page
		
	} else if($pagestep > 0) {
		$conseils = getApi()->invoke('/conseils.json', EpiRoute::httpGet, array('_GET' => array('page' => $id, 'pagestep' => $pagestep)));
	} else {
		// redirect to main page's carte de conseils
		$conseils = getApi()->invoke('/conseils.json', EpiRoute::httpGet);

		//this is where the templating goes for a specific page
		
	}

	echo '<ul>';
	foreach($conseils as $conseil) {
		echo "<li>{$conseil['id']} - {$conseil['name']}</li>";
	}
	echo '</ul>';
}



function apiConseils() {
	// we get the stuff from the database
	
	if(array_key_exists('id', $_GET) > 0) {

		$conseils = getDatabase()->all('SELECT * FROM ' . DB_CONSEILSTABLE . ' WHERE id=:id', array(':id' => $_GET['id']));
		
	} else if(array_key_exists('limit', $_GET) || array_key_exists('offset', $_GET)) {
		$limit = ( (array_key_exists( 'limit' , $_GET ) && intval( $_GET['limit'] ) )  ? " LIMIT " . intval( $_GET['limit']):"");
		$offset = ( (array_key_exists('offset' , $_GET ) && intval( $_GET['offset'] ) ) ? " OFFSET ". intval( $_GET['offset']):"");

		$conseils = getDatabase()->all('SELECT * FROM ' . DB_CONSEILSTABLE . $limit . $offset);

	} else {
		$conseils = getDatabase()->all('SELECT * FROM ' . DB_CONSEILSTABLE . ' LIMIT 50');
	}

		return $conseils;
}

function showEvenements($id = 0, $pagestep = -1) {
	// $id will always be a digit

	if($id > 0 && $pagestep < 0) {
		$evenements = getApi()->invoke('/evenements.json', EpiRoute::httpGet, array('_GET' => array('id' => $id)));

		//this is where the templating goes for a specific page
		
	} else if($pagestep > 0) {
		$evenements = getApi()->invoke('/evenements.json', EpiRoute::httpGet, array('_GET' => array('page' => $id, 'pagestep' => $pagestep)));
	} else {
		// redirect to main page's carte de conseils
		$evenements = getApi()->invoke('/evenements.json', EpiRoute::httpGet);

		//this is where the templating goes for a specific page
		
	}

	echo '<ul>';
	foreach($evenements as $evenement) {
		echo "<li>{$evenement['id']} - {$evenement['name']}</li>";
	}
	echo '</ul>';
}



function apiEvenements() {
	// we get the stuff from the database
	
	if(array_key_exists('id', $_GET) > 0) {

		$conseils = getDatabase()->all('SELECT * FROM ' . DB_EVENEMENTSTABLE . ' WHERE id=:id', array(':id' => $_GET['id']));
		
	} else if(array_key_exists('limit', $_GET) || array_key_exists('offset', $_GET)) {
		$limit = ( (array_key_exists( 'limit' , $_GET ) && intval( $_GET['limit'] ) )  ? " LIMIT " . intval( $_GET['limit']):"");
		$offset = ( (array_key_exists('offset' , $_GET ) && intval( $_GET['offset'] ) ) ? " OFFSET ". intval( $_GET['offset']):"");

		$conseils = getDatabase()->all('SELECT * FROM ' . DB_EVENEMENTSTABLE . $limit . $offset);

	} else {
		$conseils = getDatabase()->all('SELECT * FROM ' . DB_EVENEMENTSTABLE . ' LIMIT 50');
	}

		return $conseils;
}

function apiTypes() {
	// we get the stuff from the database
	
	$types = getDatabase()->all('SELECT * FROM ' . DB_TYPESTABLE . ' LIMIT 50');
	

		return $types;
}
?>
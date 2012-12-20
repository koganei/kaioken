<?php

/**** The main routing controller for the API

*/ $GLOBALS['strCharSet'] = 'utf-8'; mb_internal_encoding("UTF-8");


require_once 'includes/constants.php';
require_once 'libs/underscore.php'; 
require_once 'libs/epiphany/Epi.php';

require_once 'classes/user.php';
require_once 'classes/session.php';

if($_SERVER['SERVER_NAME'] != "localhost") { 
// we specificy a base of operations     
	Epi::setPath('base','/home/p25lh1dg/public_html/dev/mypages/api/libs/epiphany/');
}

Epi::init('api', 'database');

EpiDatabase::employ('mysql', DB_NAME, DB_SERVER, DB_USER, DB_PASSWORD);

getDatabase()->execute("SET NAMES 'utf8'");

getRoute()->get('/', '_root');

getApi()->get('/businesses.json', 'getBusinesses', EpiApi::external); 
// getApi()->put('/businesses.json/(\d)+','putBusiness', EpiApi::external); 
// getApi()->delete('/businesses.json/(\d)+', 'deleteBusiness', EpiApi::external);

getApi()->get('/intellisense.json', 'getIntellisense', EpiApi::external);

getApi()->get('/businessesshort.json', 'getBusinessesShort', EpiApi::external);
getApi()->get('/profile.json', 'getProfile', EpiApi::external);

getApi()->get('/groups.json', 'getGroups', EpiApi::external);
getApi()->get('/users.json', 'getUsers', EpiApi::external);



getRoute()->run();

/*  * ************************************************************************** ****************  * Define functions
and classes which are executed by EpiCode based on the $_['routes'] array  *
********************************************* *********************************************  */

function _root() {     // if the root is called

	echo "This isn't where I parked my car!";
}


function getBusinesses() {     // we get the stuff from the database     $what = false;     $where = false;

	$partialQuery = "";

	if(array_key_exists('what', $_GET) > 0) { $what = $_GET['what']; $partialQuery = $partialQuery . " `BusinessName`
	LIKE '%" . $what . "%'"; }
	
		if($partialQuery != "") {     $partialQuery = $partialQuery . " AND "; }
	
	if(array_key_exists('where', $_GET) > 0) { $where = $_GET['where']; $partialQuery = $partialQuery . "
	`BusinessLocationCity` LIKE '%" . $where . "%'"; }

    // use the data from $where and $what for the select     
    if($partialQuery != "") {         $partialQuery = "WHERE " . $partialQuery;     }

	$business = getDatabase()->all('SELECT * FROM ' . DB_BUSINESSTABLE . ' ' . $partialQuery . ' LIMIT 50');

    return $business; 
}


function getBusinessesShort() {
	$businessid = issetor($_GET['businessid']);
	$what = issetor($_GET['what']);
	$where = issetor($_GET['where']);
	$latitude = issetor($_GET['latitude']);
	$longitude = issetor($_GET['longitude']);
	$range = issetor($_GET['range']); /// range en Miles
	$zipcode = issetor($_GET['zipcode']);
	$resultType = issetor($_GET['resulttype']);


	$basequery = 'SELECT B.BusinessId, B.BusinessName, B.BusinessPhoneNumber, B.BusinessWebSiteAddress, B.BusinessLocationAddress, 
				B.BusinessLocationCity, S.StateName, Z.ZipCodeName, B.BusinessLocationZipCodePlusFour, BC.BusinessCategoryName, BS.BusinessStatusName, 
				BL.BusinessLogoLocation
				FROM BusinessCategoryRelation BCR
				JOIN BusinessCategory BC ON (BCR.BusinessCategoryId = BC.BusinessCategoryId)
				JOIN Business B  ON (BCR.BusinessId = B.BusinessId) 
				JOIN State S ON (B.BusinessLocationStateId = S.StateId) 
				JOIN ZipCode Z ON (B.BusinessLocationZipCodeId = Z.ZipCodeId)
				JOIN BusinessStatus BS ON (B.BusinessStatusId = BS.BusinessStatusId)
				JOIN BusinessLogo BL ON (B.BusinessLogoId = BL.BusinessLogoId) ';

	// If we only want to search one specific businessId
	if($businessid != ""){

        $query = $basequery . 'WHERE B.BusinessId = ' . $businessid . ';' ;     
	}

	//If we have WHAT and GEOLOCATION
	else if ($what != ""  AND $latitude != "" AND $longitude != "" ){

				$what = str_replace(" ", "%", trim($_GET['what']));

				if ($range != ""){
					$latituderange = $range/69;
					$longituderange = $range/50;
				}
				else if ($range == ""){
					$latituderange = 10/69;
					$longituderange = 10/50;
				}

				$query = $basequery . 
				'WHERE BC.BusinessCategoryName LIKE "%' . $what . '%" OR B.BusinessName LIKE "%' . $what . '%"
				AND Z.ZipCodeLatitude > ' . ($latitude - $latituderange) . 'AND Z.ZipCodeLatitude < '. ($latitude + $latituderange) . 
				' AND Z.ZipCodeLongitude < ' . ($longitude + $longituderange) . ' AND Z.ZipCodeLongitude > ' . ($longitude - $longituderange) .
				' ORDER BY B.BusinessMoveToFirst DESC, B.BusinessMovetoFirstDate DESC, BS.BusinessStatusId ASC, B.BusinessName ASC
				LIMIT 25;';
	}

	//If we only have the WHAT, no WHERE, ZIPCODE, or GEOLOCATION specified	
	else if ($where == "" AND $what != "" AND $zipcode == "" AND $latitude == "" AND $longitude == ""){

		$what = str_replace(" ", "%", trim($_GET['what']));
		
		$query = $basequery . 
				'WHERE BC.BusinessCategoryName LIKE "%' . $what . '%" OR B.BusinessName LIKE "%' . $what . '%"
				ORDER BY B.BusinessMoveToFirst DESC, B.BusinessMovetoFirstDate DESC, BS.BusinessStatusId ASC, B.BusinessName ASC
				LIMIT 25;' ;

	}
	//If we only have the WHERE ONLY
	else if ($where != "" AND $what == "" AND $zipcode == "" AND $latitude == "" AND $longitude == "" AND $resultType == ""){

		$where = str_replace(" ", "%", trim($_GET['where']));
		
		$query = $basequery . 
				'WHERE B.BusinessLocationAddress LIKE "%' . $where . '%" OR B.BusinessLocationCity LIKE "%' . $where . '%" OR Z.ZipCodeName LIKE "%' . $where . '%" OR S.StateName LIKE "%' . $where . '%"
				ORDER BY B.BusinessMoveToFirst DESC, B.BusinessMovetoFirstDate DESC, BS.BusinessStatusId ASC, B.BusinessName ASC
				LIMIT 25;';		


	}
	//If we only have the WHERE with a result type
	else if ($where != "" AND $what == "" AND $zipcode == "" AND $latitude == "" AND $longitude == "" AND $resultType != ""){

		// actually change it for result types

		$where = str_replace(" ", "%", trim($_GET['where']));
		
		$query = $basequery . 
	
				'WHERE B.BusinessLocationAddress LIKE "%' . $where . '%" OR B.BusinessLocationCity LIKE "%' . $where . '%" OR Z.ZipCodeName LIKE "%' . $where . '%" OR S.StateName LIKE "%' . $where . '%"
				ORDER BY B.BusinessMoveToFirst DESC, B.BusinessMovetoFirstDate DESC, BS.BusinessStatusId ASC, B.BusinessName ASC
				LIMIT 25;';		


	}
	//If we have the WHAT and WHERE
	else if ($where != "" AND $what != ""){


		$what = str_replace(" ", "%", trim($_GET['what']));
		$where = str_replace(" ", "%", trim($_GET['where']));
		
		$query = $basequery . 
				'WHERE (BC.BusinessCategoryName LIKE "%' . $what . '%" OR B.BusinessName LIKE "%' . $what . '%") AND (B.BusinessLocationAddress LIKE "%' . $where . '%" OR B.BusinessLocationCity LIKE "%' . $where . '%" OR Z.ZipCodeName LIKE "%' . $where . '%" OR S.StateName LIKE "%' . $where . '%")
				ORDER BY B.BusinessMoveToFirst DESC, B.BusinessMovetoFirstDate DESC, BS.BusinessStatusId ASC, B.BusinessName ASC
				LIMIT 25;';		

	}
	//If we have WHAT and ZIPCODE
	else if ($what != "" AND $zipcode != ""){
		$what = str_replace(" ", "%", trim($_GET['what']));
		$zipcode = str_replace(" ", "%", trim($zipcode));
		
		$query = $basequery . 
				'WHERE (BC.BusinessCategoryName LIKE "%' . $what . '%" OR B.BusinessName LIKE "%' . $what . '%") AND (Z.ZipCodeName LIKE "%' . $zipcode . '%")
				ORDER BY B.BusinessMoveToFirst DESC, B.BusinessMovetoFirstDate DESC, BS.BusinessStatusId ASC, B.BusinessName ASC
				LIMIT 25;';		

	}
	
	//If we only have GEOLOCATION, no WHAT
	else if ($what == ""  AND $latitude != "" AND $longitude != "" ){

				if ($range != ""){
					$latituderange = $range/69;
					$longituderange = $range/50;
				}
				else if ($range == ""){
					$latituderange = 10/69;
					$longituderange = 10/50;
				}

				$query = $basequery . 
				'WHERE Z.ZipCodeLatitude > ' . ($latitude - $latituderange) . 'AND Z.ZipCodeLatitude < '. ($latitude + $latituderange) . 
				' AND Z.ZipCodeLongitude < ' . ($longitude + $longituderange) . ' AND Z.ZipCodeLongitude > ' . ($longitude - $longituderange) .
				' ORDER BY B.BusinessMoveToFirst DESC, B.BusinessMovetoFirstDate DESC, BS.BusinessStatusId ASC, B.BusinessName ASC
				LIMIT 25;';


	}



	$business = getDatabase()->all($query);

    return $business; 

}


function getIntellisense() {

	$type = $_GET['type']; // could be 'where' or 'what'

	if($type == "what"){ 
		$what = str_replace(" ", "%", trim($_GET['what']));

        $query = 'SELECT BusinessCategoryName FROM BusinessCategory  WHERE BusinessCategoryName LIKE "' . '% ' . $what .
				'%"' . '  GROUP BY BusinessCategoryName ASC LIMIT 5;';

	}	

	if($type == "where"){ 
		$where = issetor($_GET['where']);
		$query = 'SELECT StateName AS Result, "State" AS ResultType FROM State WHERE StateName LIKE "' . $where . '%" LIMIT 25 UNION DISTINCT 
				  SELECT ZipCodePrimaryCity, "City" FROM ZipCode WHERE ZipCodePrimaryCity LIKE "' . $where . '%" LIMIT 25 UNION DISTINCT
				  SELECT CountyName, "County" FROM County WHERE CountyName LIKE  "' . $where . '%" LIMIT 25 UNION DISTINCT
				  SELECT ZipCodeName, "ZipCode" FROM ZipCode WHERE ZipCodeName LIKE  "' . $where . '%" LIMIT 25;';

		// RESTE À RÉGLER LE BOGUE POUR L'ORDRE SUR L'UNION DES 4 SELECTS
	}

	$response = getDatabase()->all($query);

	return $response;
}

function getProfile() {
	$sec_session = New SecureSession();

	$sec_session->sec_session_start();

	$loggedin = $sec_session->login_check();

	if($loggedin && $_SESSION['user_id']) {
		$id = $_SESSION['user_id'];

				

		// $basequery = 'SELECT B.BusinessId
		// 		FROM Business B
		// 		JOIN BusinessCategoryRelation BCR ON (B.BusinessId = BCR.BusinessId) 
		// 		JOIN BusinessCategory BC ON (BCR.BusinessCategoryId = BC.BusinessCategoryId)
		// 		JOIN BusinessKeyword BK ON (B.BusinessId = BK.BusinessId)
		// 		JOIN State S ON (B.BusinessLocationStateId = S.StateId) 
		// 		JOIN State S2 ON (B.BusinessBillingStateId = S2.StateId)
		// 		JOIN ZipCode Z ON (B.BusinessLocationZipCodeId = Z.ZipCodeId)
		// 		JOIN ZipCode Z2 ON (B.BusinessBillingZipCodeId = Z2.ZipCodeId)
		// 		JOIN BusinessContact C ON (B.BusinessId = C.BusinessId)
		// 		JOIN BusinessStatus BS ON (B.BusinessStatusId = BS.BusinessStatusId)
		// 		JOIN BusinessLogo BL ON (B.BusinessLogoId = BL.BusinessLogoId)
		// 		JOIN UserRightBusinessRelation URBR ON (B.BusinessId = URBR.BusinessId) LIMIT 2';




$basequery = 'SELECT B.*,
				S.StateName, Z.ZipCodeName,
				S2.StateName as "BusinessBillingStateName", Z2.ZipCodeName as "BusinessBillingZipCodeName",
				C.*,
				BK.BusinessKeywordName, 
				BC.BusinessCategoryName, BS.BusinessStatusName, 
				BL.BusinessLogoLocation

				FROM BusinessCategoryRelation BCR
				JOIN BusinessCategory BC ON (BCR.BusinessCategoryId = BC.BusinessCategoryId)
				JOIN Business B  ON (BCR.BusinessId = B.BusinessId) 
				JOIN State S ON (B.BusinessLocationStateId = S.StateId) 
				JOIN ZipCode Z ON (B.BusinessLocationZipCodeId = Z.ZipCodeId)
				LEFT JOIN BusinessStatus BS ON (B.BusinessStatusId = BS.BusinessStatusId)
				LEFT JOIN BusinessLogo BL ON (B.BusinessLogoId = BL.BusinessLogoId) 
				LEFT JOIN UserRightBusinessRelation URBR ON (B.BusinessId = URBR.BusinessId) 
				LEFT JOIN State S2 ON (B.BusinessBillingStateId = S2.StateId) 
				LEFT JOIN ZipCode Z2 ON (B.BusinessBillingZipCodeId = Z2.ZipCodeId)
				LEFT JOIN BusinessContact C ON (B.BusinessId = C.BusinessId)
				LEFT JOIN BusinessKeyword BK ON (B.BusinessId = BK.BusinessId) ';


		$query = $basequery . "WHERE URBR.UserId = " . $id;

		$response = getDatabase()->all($query);

		return $response;
	
	}

	echo 'not logged';
}

function getGroups() {

	$query = 'SELECT UG.UserGroupId, UG.UserGroupName, UGR.UserRightId, UR.UserRightName FROM usergroup UG
	LEFT JOIN usergrouprightrelation UGR ON (UG.UserGroupId = UGR.UserGroupId)
	LEFT JOIN userright UR ON (UGR.UserRightId = UR.UserRightId);';

	$response = getDatabase()->all($query);

	return $response;
}

function getUsers() {

	$query = 'SELECT U.UserId, U.UserName, UGM.UserGroupId FROM user U
	LEFT JOIN usergroupmember UGM ON (U.UserId = UGM.UserId);';

	$response = getDatabase()->all($query);

	return $response;
}

?>

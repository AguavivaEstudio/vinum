<?php
/*
/////////////////////////////////////////////////////////////////////////////
CONFIGURATION PARAMETERS
['DB_HOST']          --> Database server.
['DB_PORT']          --> Database port.
['DB_NAME']          --> Database name.
['DB_USER']          --> Database username.
['DB_PASS']          --> Database password.
['PROJECT_NAME']     --> Project name.
['DEFAULT_LANGUAGE'] --> The default language for both the frontend.
/////////////////////////////////////////////////////////////////////////////
*/
$_servers = array();

$_servers['DEV'] = array();
$_servers['DEV']['DB_HOST'] = 'localhost';
$_servers['DEV']['DB_PORT'] = '3306';
$_servers['DEV']['DB_NAME'] = 'aguaviva_proyectobase';
$_servers['DEV']['DB_USER'] = 'root';
$_servers['DEV']['DB_PASS'] = '';

/* DATOS PARA HOST AGUAVIVA.COM.AR */
$_servers['TST'] = array();
$_servers['TST']['DB_HOST'] = 'localhost';
$_servers['TST']['DB_PORT'] = '3306';
$_servers['TST']['DB_NAME'] = 'aguaviva_proyectobase';
$_servers['TST']['DB_USER'] = 'aguaviva_test';
$_servers['TST']['DB_PASS'] = 'AGV_17mySQL20';


$_servers['PRD'] = array();
$_servers['PRD']['DB_HOST'] = 'localhost';
$_servers['PRD']['DB_PORT'] = '3306';
$_servers['PRD']['DB_NAME'] = 'aguaviva_proyectobase';
$_servers['PRD']['DB_USER'] = 'aguaviva_test';
$_servers['PRD']['DB_PASS'] = 'AGV_17mySQL20';

// THE CURRENT SELECTED SERVER.
if(     strstr($_SERVER['SERVER_NAME'], 'localhost'))           	{ $_server = $_servers['DEV']; }
elseif( strstr($_SERVER['SERVER_NAME'], 'aguaviva.com.ar'))  		{ $_server = $_servers['TST']; }
else                                                            	{ $_server = $_servers['PRD']; }


?>
<?php
/* v02 · El kernel pasa de mysqli a PDO.
   Una sola conexión por petición: la primera llamada la crea y las siguientes
   reutilizan la misma. Ya no hace falta desconectar: PDO cierra solo al acabar. */
function mod001_connectDB () {
	static $pdo = null;	// static: la variable sobrevive entre una llamada y la siguiente

	if ( $pdo === null ) {
		// v04b: el usuario y la contraseña ya no se escriben aquí: están en config/database.php
		$file = __DIR__ . "/../config/database.php";
		if ( !is_file( $file ) ) {
			throw new LogicException( "Falta config/database.php: cópialo de config/database.example.php" );
		}
		$db  = require $file;
		$dsn = "mysql:host=" . $db[ "host" ] . ";port=" .$db[ "port" ]. ";dbname=" . $db[ "database" ] . ";charset=utf8mb4";

		$pdo = new PDO( $dsn, $db[ "user" ], $db[ "password" ], [
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,	// si falla, lanza una excepción
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,	// filas con los nombres de columna
			PDO::ATTR_EMULATE_PREPARES   => false,	// consultas preparadas de verdad
		] );
	}

	return $pdo;
}

/* Una única sesión por petición, con una cookie no accesible desde JavaScript. */
function mod001_startSession() {
	if ( session_status() === PHP_SESSION_NONE ) {
		session_set_cookie_params( [
			"httponly" => true,
			"secure"   => !empty( $_SERVER[ "HTTPS" ] ) && $_SERVER[ "HTTPS" ] !== "off",
			"samesite" => "Lax",
			"path"     => "/",
		] );
		session_start();
	}
}

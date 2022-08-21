<?php

namespace src\core;

use \PDO;
use \PDOException;

class Connect
{
	private static $conn;
	private static $error;
	
	public static function getConn(): ?PDO
	{
		if (is_null(self::$conn)) {
			try {
				self::$conn = new PDO(
					'sqlite:' . dirname(__DIR__, 2) . '/data/var/database.sqlite',
				);
				self::$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
				self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			} catch (PDOException $e) {
				self::$error = $e;
			}
		}
		return self::$conn;
	}
	
	public function getError(): ?PDOException
	{
		return self::$error;
	}
}

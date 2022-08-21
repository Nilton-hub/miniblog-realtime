<?php

require __DIR__ . '/../vendor/autoload.php';

$conn = src\core\Connect::getConn();

$sql = readline('Dgite a sua SQL: ');
try {
	$stmt = $conn->prepare($sql);
	var_dump($stmt->execute(), "Último id inserido {$conn->lastInsertId()}");
	$showRows = mb_convert_case(readline('Ver resultados? '), MB_CASE_UPPER)[0];
	var_dump("Linhas " . $stmt->rowCount());
	if ($showRows === 'S') {
		// if ($numRows = $stmt->rowCount()) {
			echo "{$stmt->rowCount()} Linhas" . PHP_EOL;
			while ($row = $stmt->fetch()) {
				print_r($row);
			}
		// } else {
			// echo "Nenhuma linha de resultados obtida";
		// }
	}
} catch (PDOException $e) {
	echo $e->getMessage() . PHP_EOL;
	print_r($e->getTrace());
}

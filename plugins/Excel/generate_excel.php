<?php

$excel=$_POST["output"];
$year =$_POST["year"];
    $filename = "Horas_laboratorio_" . $year . ".xls";
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Type: application/vnd.ms-excel");

    print $excel;

?>
<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
$reader = IOFactory::createReader('Xlsx');
echo "ok\n";

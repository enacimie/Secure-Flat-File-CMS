<?php
require 'vendor/autoload.php';
use App\Core\Indexer;

echo "Index Content:\n";
print_r(Indexer::all());


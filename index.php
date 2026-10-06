<?php
require 'db.php';
header('Location: ' . (empty($_SESSION['lietotaja_id']) ? 'login.php' : 'piezimes.php'));

<?php
// admin/index.php
// Route physical /admin/ directory requests to the clean dashboard route

$_GET['route'] = 'admin/dashboard';
require_once dirname(__DIR__) . '/index.php';

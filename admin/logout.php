<?php

require_once dirname(__DIR__) . '/includes/init.php';

use Core\Auth;
use Core\Session;

Auth::logout();

redirect('/admin/login.php');

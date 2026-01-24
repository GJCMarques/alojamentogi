<?php
/**
 * A Casa do Gi - Admin Logout
 */

require_once dirname(__DIR__) . '/includes/init.php';

use Core\Auth;
use Core\Session;

Auth::logout();

Session::flash('success', 'Sessao terminada com sucesso.');
redirect('/admin/login.php');

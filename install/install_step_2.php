<?php
/**
 * WebEngine CMS
 * https://webenginecms.org/
 * 
 * @version 1.2.6-dvteam
 * @author Lautaro Angelico <http://lautaroangelico.com/>
 * @copyright (c) 2013-2025 Lautaro Angelico, All Rights Reserved
 * 
 * Licensed under the MIT license
 * http://opensource.org/licenses/MIT
 */

if(!defined('access') or !access or access != 'install') die();
?>
<h3>Database Connection</h3>
<br />
<?php
if(isset($_POST['install_step_2_submit'])) {
	try {

		$_SESSION['install_sql_host'] = $_POST['install_step_2_1'];
		if(!isset($_POST['install_step_2_1'])) throw new Exception('You must complete all required fields.');
		
		$_SESSION['install_sql_port'] = $_POST['install_step_2_7'];
		if(!isset($_POST['install_step_2_7'])) throw new Exception('You must complete all required fields.');
		
		$_SESSION['install_sql_user'] = $_POST['install_step_2_2'];
		if(!isset($_POST['install_step_2_2'])) throw new Exception('You must complete all required fields.');
		
		$_SESSION['install_sql_pass'] = $_POST['install_step_2_3'];
		if(!isset($_POST['install_step_2_3'])) throw new Exception('You must complete all required fields.');
		
		$_SESSION['install_sql_db1'] = $_POST['install_step_2_4'];
		if(!isset($_POST['install_step_2_4'])) throw new Exception('You must complete all required fields.');
		
		$_SESSION['install_sql_db2'] = ((isset($_POST['install_step_2_5']) && !empty($_POST['install_step_2_5'])) ? $_POST['install_step_2_5'] : null);
		
		# test connection (db1)
		$db1 = new dB($_SESSION['install_sql_host'], $_SESSION['install_sql_port'], $_SESSION['install_sql_db1'], $_SESSION['install_sql_user'], $_SESSION['install_sql_pass']);
		if($db1->dead) {
			throw new Exception("Could not connect to database (1)");
		}
		
		# test connection (db2)
		if(isset($_SESSION['install_sql_db2'])) {
			$db2 = new dB($_SESSION['install_sql_host'], $_SESSION['install_sql_port'], $_SESSION['install_sql_db2'], $_SESSION['install_sql_user'], $_SESSION['install_sql_pass']);
			if($db2->dead) {
				throw new Exception("Could not connect to database (2)");
			}
		}
		
		# move to next step
		$_SESSION['install_cstep']++;
		header('Location: install.php');
		die();
	} catch (Exception $ex) {
		echo '<div class="alert alert-danger" role="alert">'.$ex->getMessage().'</div>';
	}
}
?>
<form class="form-horizontal" method="post">
	<div class="form-group">
		<label for="input_1" class="col-sm-2 control-label">Host</label>
		<div class="col-sm-10">
			<input type="text" name="install_step_2_1" class="form-control" id="input_1" value="<?php echo (isset($_SESSION['install_sql_host']) ? $_SESSION['install_sql_host'] : null); ?>">
			<p class="help-block">Set the IP address of your MySQL server.</p>
		</div>
	</div>
	<div class="form-group">
		<label for="input_7" class="col-sm-2 control-label">Port</label>
		<div class="col-sm-10">
			<input type="text" name="install_step_2_7" class="form-control" id="input_7" value="<?php echo (isset($_SESSION['install_sql_port']) ? $_SESSION['install_sql_port'] : '3306'); ?>">
			<p class="help-block">Default: 3306.</p>
		</div>
	</div>
	<div class="form-group">
		<label for="input_2" class="col-sm-2 control-label">Username</label>
		<div class="col-sm-10">
			<input type="text" name="install_step_2_2" class="form-control" id="input_2" value="<?php echo (isset($_SESSION['install_sql_user']) ? $_SESSION['install_sql_user'] : 'sa'); ?>">
			<p class="help-block">It is recommended that you create a new MySQL user just for the web connection (better security).</p>
		</div>
	</div>
	<div class="form-group">
		<label for="input_3" class="col-sm-2 control-label">Password</label>
		<div class="col-sm-10">
			<input type="text" name="install_step_2_3" class="form-control" id="input_3" value="<?php echo (isset($_SESSION['install_sql_pass']) ? $_SESSION['install_sql_pass'] : null); ?>">
			<p class="help-block">It is recommended that you use a strong password to ensure maximum security.</p>
		</div>
	</div>
	<div class="form-group">
		<label for="input_4" class="col-sm-2 control-label">Database (1)</label>
		<div class="col-sm-10">
			<input type="text" name="install_step_2_4" class="form-control" id="input_4" value="<?php echo (isset($_SESSION['install_sql_db1']) ? $_SESSION['install_sql_db1'] : 'MuOnline'); ?>">
			<p class="help-block">Usually <strong>MuOnline</strong>. WebEngine tables will be created in this database.</p>
		</div>
	</div>
	<div class="form-group">
		<label for="input_5" class="col-sm-2 control-label">Database (2)</label>
		<div class="col-sm-10">
			<input type="text" name="install_step_2_5" class="form-control" id="input_5" value="<?php echo (isset($_SESSION['install_sql_db2']) ? $_SESSION['install_sql_db2'] : null); ?>">
			<p class="help-block">Usually <strong>Me_MuOnline</strong>. Leave empty if you only use one database.</p>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" name="install_step_2_submit" value="continue" class="btn btn-success">Continue</button>
		</div>
	</div>
</form>
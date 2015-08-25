<?php
	$pathinfo = pathinfo(ew_ScriptName());
	$script_name = $pathinfo['basename'];
?>
<table width="100%" border="0" cellspacing="0" cellpadding="2" class="menutable">
	<tr>
<?php if (IsLoggedIn()) { ?>
	<td><span class="phpmaker"><a href="categorieslist.php?cmd=resetall"<?php echo ($script_name=="categorieslist.php"||$script_name=="categoriesedit.php"?' class="selected"':'') ?>>敏感关键字</a></span></td>
<?php } ?>
<?php if (IsLoggedIn()) { ?>
	<td><span class="phpmaker"><a href="hoturlslist.php?cmd=resetall"<?php echo ($script_name=="hoturlslist.php"||$script_name=="hoturlsview.php"||$script_name=="hoturlsedit.php"?' class="selected"':'') ?>>命中数据</a></span></td>
<?php } ?>
<?php if (IsLoggedIn()) { ?>
	<td><span class="phpmaker"><a href="postslist.php?cmd=resetall"<?php echo ($script_name=="postslist.php"||$script_name=="postsview.php"||$script_name=="postsedit.php"?' class="selected"':'') ?>>泛控数据</a></span></td>
<?php } ?>
<?php if (IsLoggedIn()) { ?>
	<td><span class="phpmaker"><a href="archiveslist.php"<?php echo ($script_name=="archiveslist.php"?' class="selected"':'') ?>>归档查看</a></span></td>
<?php } ?>
<?php if (IsLoggedIn()) { ?>
	<td><span class="phpmaker"><a href="userlist.php?cmd=resetall"<?php echo ($script_name=="userlist.php"||$script_name=="useradd.php"?' class="selected"':'') ?>>用户管理</a></span></td>
<?php } ?>
<?php if (IsLoggedIn() && IsSysAdmin()) { ?>
	<td><span class="phpmaker"><a href="logslist.php?cmd=resetall"<?php echo ($script_name=="logslist.php"?' class="selected"':'') ?>>系统日志</a></span></td>
<?php } ?>
<?php if (IsLoggedIn() && IsSysAdmin()) { ?>
	<td><span class="phpmaker"><a href="changepwd.php"<?php echo ($script_name=="changepwd.php"?' class="selected"':'') ?>>修改密码</a></span></td>
<?php } ?>
<?php if (IsLoggedIn()) { ?>
	<td><span class="phpmaker"><a href="logout.php">退出系统</a></span></td>
<?php } elseif ($script_name <> "login.php") { ?>
	<td><span class="phpmaker"><a href="login.php">登录系统</a></span></td>
<?php } ?>
	</tr>
</table>

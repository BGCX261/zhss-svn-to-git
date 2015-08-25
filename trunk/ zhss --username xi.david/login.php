<?php
define("EW_PAGE_ID", "login", TRUE); // Page ID
?>
<?php 
session_start(); // Initialize session data
ob_start(); // Turn on output buffering
?>
<?php include "ewcfg50.php" ?>
<?php include "ewmysql50.php" ?>
<?php include "phpfn50.php" ?>
<?php include "userinfo.php" ?>
<?php include "userfn50.php" ?>
<?php include "logsinfo.php" ?>
<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // Always modified
header("Cache-Control: private, no-store, no-cache, must-revalidate"); // HTTP/1.1 
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP/1.0
?>
<?php

// Open connection to the database
$conn = ew_Connect();
?>
<?php
$Security = new cAdvancedSecurity();
?>
<?php

// Common page loading event (in userfn*.php)
Page_Loading();
?>
<?php

// Page load event, used in current page
Page_Load();
?>
<?php
$sLastUrl = $Security->LastUrl(); // Get Last Url
if ($sLastUrl == "") $sLastUrl = "login.php";
if (!$Security->IsLoggedIn()) $Security->AutoLogin();
$bValidate = FALSE;
if (@$_POST["submit"] <> "") {

	// Setup variables
	$sUsername = ew_StripSlashes(@$_POST["username"]);
	$sPassword = ew_StripSlashes(@$_POST["password"]);
	$sLoginType = strtolower(@$_POST["rememberme"]);
	$bValidate = TRUE;
} else {
	if ($Security->IsLoggedIn()) {
		if (@$_SESSION[EW_SESSION_MESSAGE] == "") Page_Terminate($sLastUrl); // Return to last accessed page
	}
}
if ($bValidate) {
	$bValidPwd = FALSE;

	// Call loggin in event
	$bValidate = User_LoggingIn($sUsername, $sPassword);
	if ($bValidate) {
		$bValidPwd = $Security->ValidateUser($sUsername, $sPassword);
		if (!$bValidPwd) $_SESSION[EW_SESSION_MESSAGE] = "无效的用户名或密码"; // Invalid User ID/password
	} else {
		if (@$_SESSION[EW_SESSION_MESSAGE] == "") $_SESSION[EW_SESSION_MESSAGE] = "Login cancelled"; // Login cancelled
	}
	//$_SESSION[EW_SESSION_SYS_ADMIN] = '5';
	if ($bValidPwd) {

		// Write cookies
		$expirytime = time() + 24*60*60; // Change cookie expiry time here
		if ($sLoginType == "a") { // Auto login
			setcookie(EW_PROJECT_NAME . '[AutoLogin]',  "autologin", $expirytime); // Set up autologin cookies
			setcookie(EW_PROJECT_NAME . '[UserName]', $sUsername, $expirytime); // Set up user name cookies
			setcookie(EW_PROJECT_NAME . '[Password]', TEAencrypt($sPassword, EW_RANDOM_KEY), $expirytime); // Set up password cookies
		} elseif ($sLoginType == "u") { // Remember user name
			setcookie(EW_PROJECT_NAME . '[AutoLogin]', "rememberusername", $expirytime); // Set up remember user name cookies
			setcookie(EW_PROJECT_NAME . '[UserName]', $sUsername, $expirytime); // Set up user name cookies			
		} else {
			setcookie(EW_PROJECT_NAME . '[AutoLogin]', "", $expirytime); // Clear autologin cookies
		}

		// Call loggedin event
		User_LoggedIn($sUsername);
		Page_Terminate('index.php'); // Return to last accessed url
	}
}
?>
<html>
<head>
<title>中和监控系统</title>
<link href="rying.css" rel="stylesheet" type="text/css" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="generator" content="PHPMaker v5.0.0.1" />
</head>
<body style="background:url(036bj.jpg);color:#fff;">
<script type="text/javascript">
<!--
var EW_DATE_SEPARATOR; // Default date separator
EW_DATE_SEPARATOR = "/";
if (EW_DATE_SEPARATOR == '') EW_DATE_SEPARATOR = '/';
EW_UPLOAD_ALLOWED_FILE_EXT = "gif,jpg,jpeg,bmp,png,doc,xls,pdf,zip"; // Allowed upload file extension
var EW_FIELD_SEP = ', '; // Default field separator

// Ajax settings
EW_LOOKUP_FILE_NAME = "ewlookup50.php"; // lookup file name
EW_ADD_OPTION_FILE_NAME = "ewaddopt50.php"; // add option file name

// Auto suggest settings
var EW_AST_SELECT_LIST_ITEM = 0;
var EW_AST_TEXT_BOX_ID;
var EW_AST_CANCEL_SUBMIT;
var EW_AST_OLD_TEXT_BOX_VALUE = "";
var EW_AST_MAX_NEW_VALUE_LENGTH = 5; // Only get data if value length <= this setting

// Multipage settings
var ew_PageIndex = 0;
var ew_MaxPageIndex = 0;
var ew_MinPageIndex = 0;
var EW_TABLE_CLASSNAME = "ewTable"; // Note: changed the class name as needed
var ew_MultiPageElements = new Array();

//-->
</script>
<script type="text/javascript" src="ewp50.js"></script>
<script type="text/javascript" src="userfn50.js"></script>
<script language="JavaScript" type="text/javascript">
<!--

// Write your client script here, no need to add script tags.
// To include another .js script, use:
// ew_ClientScriptInclude("my_javascript.js");
//-->

</script>
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
	<!-- content (begin) -->
	<tr>
<td height="100%" valign="top">
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top" class="ewContentColumn"><br><br><br><br><br><br><br><br>
<h3 align="center">中和监控系统</h3>
<script language="JavaScript" type="text/javascript">
<!--

// Write your client script here, no need to add script tags.
// To include another .js script, use:
// ew_ClientScriptInclude("my_javascript.js"); 
//-->

</script>
<script type="text/javascript">
function ew_ValidateForm(fobj) {
	if (!ew_HasValue(fobj.username)) {
		if  (!ew_OnError(fobj.username, "Please enter user ID"))
			return false;
	}
	if (!ew_HasValue(fobj.password)) {
		if (!ew_OnError(fobj.password, "Please enter password"))
			return false;
	}
	return true;
}

</script>
<?php
if (@$_SESSION[EW_SESSION_MESSAGE] <> "") {
?>
<p><span class="ewmsg"><?php echo @$_SESSION[EW_SESSION_MESSAGE] ?></span></p>
<?php
	$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message
}
?>
<form action="login.php" method="post" onSubmit="return ew_ValidateForm(this);">
<table border="0" cellspacing="0" cellpadding="4" align="center">
	<tr>
		<td><span class="phpmaker">用户名</span></td>
		<td><span class="phpmaker"><input type="text" name="username" id="username" size="20" value="<?php echo @$_COOKIE[EW_PROJECT_NAME]['UserName'] ?>"></span></td>
		<td><span class="phpmaker">密码</span></td>
		<td><span class="phpmaker"><input type="password" name="password" id="password" size="20"></span></td>
		<td><input type="submit" name="submit" id="submit" value="登陆"></td>
	</tr>
	<tr>
		<td colspan="4" align="center"><span class="phpmaker">
		<?php if (@$_COOKIE[EW_PROJECT_NAME]['AutoLogin'] == "autologin") { ?>
		<input type="radio" name="rememberme" id="rememberme" value="a" checked>自动登陆<input type="radio" name="rememberme" id="rememberme" value="u">保存用户名<input type="radio" name="rememberme" id="rememberme" value="n">始终询问用户名密码
		<?php } elseif (@$_COOKIE[EW_PROJECT_NAME]['AutoLogin'] == "rememberusername") { ?>
		<input type="radio" name="rememberme" id="rememberme" value="a">自动登陆<input type="radio" name="rememberme" id="rememberme" value="u" checked>保存用户名<input type="radio" name="rememberme" id="rememberme" value="n">始终询问用户名密码
		<?php } else { ?>
		<input type="radio" name="rememberme" id="rememberme" value="a">自动登陆<input type="radio" name="rememberme" id="rememberme" value="u">保存用户名<input type="radio" name="rememberme" id="rememberme" value="n" checked>始终询问用户名密码
		<?php } ?></span>
		</td>
	</tr>
</table>
</form>
		</td>
	</tr>
</table>
		</td>
	</tr>
<!-- content (end) -->
</table>
<script language="JavaScript" type="text/javascript">
<!--

// Write your global startup script here
// document.write("page loaded");
//-->

</script>
</body>
</html>

<?php

// If control is passed here, simply terminate the page without redirect
Page_Terminate();

// -----------------------------------------------------------------
//  Subroutine Page_Terminate
//  - called when exit page
//  - clean up connection and objects
//  - if url specified, redirect to url, otherwise end response
function Page_Terminate($url = "") {
	global $conn;

	// Page unload event, used in current page
	Page_Unload();

	// Global page unloaded event (in userfn*.php)
	Page_Unloaded();

	 // Close Connection
	$conn->Close();

	// Go to url if specified
	if ($url <> "") {
		ob_end_clean();
		header("Location: $url");
	}
	exit();
}
?>
<?php

// Page Load event
function Page_Load() {

	//echo "Page Load";
}

// Page Unload event
function Page_Unload() {

	//echo "Page Unload";
}
?>
<?php

// User Logging In event
function User_LoggingIn($usr, $pwd) {

	// Enter your code here
	// To cancel, set return value to False

	return TRUE;
}

// User Logged In event
function User_LoggedIn($usr) {

	//echo "User Logged In";
	global $logs;
	global $conn;
	$rsnew = array();
	$rsnew['client'] = $usr;
	$rsnew['group'] = 'USER';
	$rsnew['message'] = '登陆';
	$conn->Execute($logs->InsertSQL($rsnew));
}
?>

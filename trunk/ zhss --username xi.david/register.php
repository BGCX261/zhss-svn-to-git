<?php
define("EW_PAGE_ID", "register", TRUE); // Page ID
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
$bUserExists = FALSE;

// Create form object
$objForm = new cFormObj();
if (@$_POST["a_register"] <> "") {

	// Get action
	$user->CurrentAction = $_POST["a_register"];
	LoadFormValues(); // Get form values
} else {
	$user->CurrentAction = "I"; // Display blank record
	LoadDefaultValues(); // Load default values
}
switch ($user->CurrentAction) {
	case "I": // Blank record, no action required
		break;
	case "A": // Add

		// Check for Duplicate User ID
		$sFilter = "(`user` = '" . ew_AdjustSql($user->user->CurrentValue) . "')";

		// Set up filter (Sql Where Clause) and get Return Sql
		// Sql constructor in user class, userinfo.php

		$user->CurrentFilter = $sFilter;
		$sUserSql = $user->SQL();
		if ($rs = $conn->Execute($sUserSql)) {
			if (!$rs->EOF) {
				$bUserExists = TRUE;
				RestoreFormValues(); // Restore form values
				$_SESSION[EW_SESSION_MESSAGE] = "用户名已经存在!"; // Set user exist message
			}
			$rs->Close();
		}
		if (!$bUserExists) {
			$user->SendEmail = TRUE; // Send email on add success
			if (AddRow()) { // Add record
				$_SESSION[EW_SESSION_MESSAGE] = "注册成功"; // Register success
				Page_Terminate("login.php"); // Go to login page
			} else {
				RestoreFormValues(); // Restore form values
			}
		}
}

// Render row
$user->RowType = EW_ROWTYPE_ADD; // Render add
RenderRow();
?>
<?php include "header.php" ?>
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<!-- left column (begin) -->
		<td valign="top" class="ewMenuColumn">
		</td>
		<!-- left column (end) -->
		<!-- right column (begin) -->
		<td valign="top" class="ewContentColumn">
<p><b>中和集成监控系统</b></p>
<script type="text/javascript">
<!--
var EW_PAGE_ID = "register"; // Page id

//-->
</script>
<script type="text/javascript">
<!--

function ew_ValidateForm(fobj) {
	if (fobj.a_confirm && fobj.a_confirm.value == "F")
		return true;
	var i, elm, aelm, infix;
	var rowcnt = (fobj.key_count) ? Number(fobj.key_count.value) : 1;
	for (i=0; i<rowcnt; i++) {
		infix = (fobj.key_count) ? String(i+1) : "";
		elm = fobj.elements["x" + infix + "_user"];
		if (elm && !ew_HasValue(elm)) {
			if (!ew_OnError(elm, "Please enter required field - user"))
				return false;
		}
		elm = fobj.elements["x" + infix + "_level"];
		if (elm && !ew_HasValue(elm)) {
			if (!ew_OnError(elm, "Please enter required field - level"))
				return false;
		}
		elm = fobj.elements["x" + infix + "_level"];
		if (elm && !ew_CheckInteger(elm.value)) {
			if (!ew_OnError(elm, "Incorrect integer - level"))
				return false; 
		}
	}
	return true;
}

//-->
</script>
<script type="text/javascript">
<!--

// js for DHtml Editor
//-->

</script>
<script type="text/javascript">
<!--

// js for Popup Calendar
//-->

</script>
<script type="text/javascript">
<!--
var ew_MultiPagePage = "Page"; // multi-page Page Text
var ew_MultiPageOf = "of"; // multi-page Of Text
var ew_MultiPagePrev = "Prev"; // multi-page Prev Text
var ew_MultiPageNext = "Next"; // multi-page Next Text

//-->
</script>
<script language="JavaScript" type="text/javascript">
<!--

// Write your client script here, no need to add script tags.
// To include another .js script, use:
// ew_ClientScriptInclude("my_javascript.js"); 
//-->

</script>
<p><span class="phpmaker">
Registration Page<br><br>
<a href="login.php">Back to Login Page</a>
</span></p>
<?php 
if (@$_SESSION[EW_SESSION_MESSAGE] <> "") {
?>
<p><span class="ewmsg"><?php echo $_SESSION[EW_SESSION_MESSAGE] ?></span></p>
<?php
	$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message
}
?>
<form name="fuserregister" id="fuserregister" action="register.php" method="post" onSubmit="return ew_ValidateForm(this);">
<p>
<input type="hidden" name="a_register" id="a_register" value="A">
<table class="ewTable">
	<tr class="ewTableRow">
		<td class="ewTableHeader">用户名<span class='ewmsg'>&nbsp;*</span></td>
		<td<?php echo $user->user->CellAttributes() ?>><span id="cb_x_user">
<textarea name="x_user" id="x_user" cols="35" rows="4"<?php echo $user->user->EditAttributes() ?>><?php echo $user->user->EditValue ?></textarea>
</span></td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">密码</td>
		<td<?php echo $user->password->CellAttributes() ?>><span id="cb_x_password">
<textarea name="x_password" id="x_password" cols="35" rows="4"<?php echo $user->password->EditAttributes() ?>><?php echo $user->password->EditValue ?></textarea>
</span></td>
	</tr>
	<!--tr id=""-->
	<tr class="ewTableRow">
		<td class="ewTableHeader">确认密码</td>
		<td<?php echo $user->password->CellAttributes() ?>>
<textarea name="c_password" id="c_password" cols="35" rows="4"<?php echo $user->password->EditAttributes() ?>><?php echo $user->password->EditValue ?></textarea>
</td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">权限<span class='ewmsg'>&nbsp;*</span></td>
		<td<?php echo $user->level->CellAttributes() ?>><span id="cb_x_level">
<input type="text" name="x_level" id="x_level" title="" size="30" value="<?php echo $user->level->EditValue ?>"<?php echo $user->level->EditAttributes() ?>>
</span></td>
	</tr>
</table>
<p>
<input type="submit" name="btnAction" id="btnAction" value=" Register ">
</form>
<script language="JavaScript" type="text/javascript">
<!--

// Write your startup script here
// document.write("page loaded");
//-->

</script>
<?php include "footer.php" ?>
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

// Load default values
function LoadDefaultValues() {
	global $user;
	$user->level->CurrentValue = 0;
}
?>
<?php

// Load form values
function LoadFormValues() {

	// Load from form
	global $objForm, $user;
	$user->user->setFormValue($objForm->GetValue("x_user"));
	$user->password->setFormValue($objForm->GetValue("x_password"));
	$user->level->setFormValue($objForm->GetValue("x_level"));
}

// Restore form values
function RestoreFormValues() {
	global $user;
	$user->user->CurrentValue = $user->user->FormValue;
	$user->password->CurrentValue = $user->password->FormValue;
	$user->level->CurrentValue = $user->level->FormValue;
}
?>
<?php

// Render row values based on field settings
function RenderRow() {
	global $conn, $Security, $user;

	// Call Row Rendering event
	$user->Row_Rendering();

	// Common render codes for all row types
	// user

	$user->user->CellCssStyle = "";
	$user->user->CellCssClass = "";

	// password
	$user->password->CellCssStyle = "";
	$user->password->CellCssClass = "";

	// level
	$user->level->CellCssStyle = "";
	$user->level->CellCssClass = "";
	if ($user->RowType == EW_ROWTYPE_VIEW) { // View row
	} elseif ($user->RowType == EW_ROWTYPE_ADD) { // Add row

		// user
		$user->user->EditCustomAttributes = "";
		$user->user->EditValue = $user->user->CurrentValue;

		// password
		$user->password->EditCustomAttributes = "";
		$user->password->EditValue = $user->password->CurrentValue;

		// level
		$user->level->EditCustomAttributes = "";
		$user->level->EditValue = $user->level->CurrentValue;
	} elseif ($user->RowType == EW_ROWTYPE_EDIT) { // Edit row
	} elseif ($user->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$user->Row_Rendered();
}
?>
<?php

// Add record
function AddRow() {
	global $conn, $Security, $user;

	// Check for duplicate key
	$bCheckKey = TRUE;
	$sFilter = $user->SqlKeyFilter();
	if (trim(strval($user->id->CurrentValue)) == "") {
		$bCheckKey = FALSE;
	} else {
		$sFilter = str_replace("@id@", ew_AdjustSql($user->id->CurrentValue), $sFilter); // Replace key value
	}
	if (!is_numeric($user->id->CurrentValue)) {
		$bCheckKey = FALSE;
	}
	if ($bCheckKey) {
		$rsChk = $user->LoadRs($sFilter);
		if ($rsChk && !$rsChk->EOF) {
			$_SESSION[EW_SESSION_MESSAGE] = "Duplicate value for primary key";
			$rsChk->Close();
			return FALSE;
		}
	}
	$rsnew = array();

	// Field user
	$user->user->SetDbValueDef($user->user->CurrentValue, "");
	$rsnew['user'] =& $user->user->DbValue;

	// Field password
	$user->password->SetDbValueDef($user->password->CurrentValue, NULL);
	$rsnew['password'] =& $user->password->DbValue;

	// Field level
	$user->level->SetDbValueDef($user->level->CurrentValue, 0);
	$rsnew['level'] =& $user->level->DbValue;

	// Call Row Inserting event
	$bInsertRow = $user->Row_Inserting($rsnew);
	if ($bInsertRow) {
		$conn->raiseErrorFn = 'ew_ErrorFn';
		$AddRow = $conn->Execute($user->InsertSQL($rsnew));
		$conn->raiseErrorFn = '';
	} else {
		if ($user->CancelMessage <> "") {
			$_SESSION[EW_SESSION_MESSAGE] = $user->CancelMessage;
			$user->CancelMessage = "";
		} else {
			$_SESSION[EW_SESSION_MESSAGE] = "Insert cancelled";
		}
		$AddRow = FALSE;
	}
	if ($AddRow) {
		$user->id->setDbValue($conn->Insert_ID());
		$rsnew['id'] =& $user->id->DbValue;

		// Call Row Inserted event
		$user->Row_Inserted($rsnew);
	}
	return $AddRow;
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

<?php
define("EW_PAGE_ID", "edit", TRUE); // Page ID
define("EW_TABLE_NAME", 'user', TRUE);
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

// Common page loading event (in userfn*.php)
Page_Loading();
?>
<?php

// Page load event, used in current page
Page_Load();
?>
<?php
$user->Export = @$_GET["export"]; // Get export parameter
$sExport = $user->Export; // Get export parameter, used in header
$sExportFile = $user->TableVar; // Get export file, used in header
?>
<?php

// Load key from QueryString
if (@$_GET["id"] <> "") {
	$user->id->setQueryStringValue($_GET["id"]);
}

// Create form object
$objForm = new cFormObj();
if (@$_POST["a_edit"] <> "") {
	$user->CurrentAction = $_POST["a_edit"]; // Get action code
	LoadFormValues(); // Get form values
} else {
	$user->CurrentAction = "I"; // Default action is display
}

// Check if valid key
if ($user->id->CurrentValue == "") Page_Terminate($user->getReturnUrl()); // Invalid key, exit
switch ($user->CurrentAction) {
	case "I": // Get a record to display
		if (!LoadRow()) { // Load Record based on key
			$_SESSION[EW_SESSION_MESSAGE] = "No records found"; // No record found
			Page_Terminate($user->getReturnUrl()); // Return to caller
		}
		break;
	Case "U": // Update
		$user->SendEmail = TRUE; // Send email on update success
		if (EditRow()) { // Update Record based on key
			$_SESSION[EW_SESSION_MESSAGE] = "Update successful"; // Update success
			Page_Terminate($user->getReturnUrl()); // Return to caller
		} else {
			RestoreFormValues(); // Restore form values if update failed
		}
}

// Render the record
$user->RowType = EW_ROWTYPE_EDIT; // Render as edit
RenderRow();
?>
<?php include "header.php" ?>
<script type="text/javascript">
<!--
var EW_PAGE_ID = "edit"; // Page id

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
<p><span class="phpmaker">Edit TABLE: user<br><br><a href="<?php echo $user->getReturnUrl() ?>">Go Back</a></span></p>
<?php
if (@$_SESSION[EW_SESSION_MESSAGE] <> "") {
?>
<p><span class="ewmsg"><?php echo $_SESSION[EW_SESSION_MESSAGE] ?></span></p>
<?php
	$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message
}
?>
<form name="fuseredit" id="fuseredit" action="useredit.php" method="post" onSubmit="return ew_ValidateForm(this);">
<p>
<input type="hidden" name="a_edit" id="a_edit" value="U">
<table class="ewTable">
	<tr class="ewTableRow">
		<td class="ewTableHeader">id</td>
		<td<?php echo $user->id->CellAttributes() ?>><span id="cb_x_id">
<div<?php echo $user->id->ViewAttributes() ?>><?php echo $user->id->EditValue ?></div>
<input type="hidden" name="x_id" id="x_id" value="<?php echo ew_HtmlEncode($user->id->CurrentValue) ?>">
</span></td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">user<span class='ewmsg'>&nbsp;*</span></td>
		<td<?php echo $user->user->CellAttributes() ?>><span id="cb_x_user">
<input type="text" name="x_user" id="x_user"  size="30" maxlength="128" value="<?php echo $user->user->EditValue ?>"<?php echo $user->user->EditAttributes() ?>>
</span></td>
	</tr>
	<tr class="ewTableRow">
		<td class="ewTableHeader">password</td>
		<td<?php echo $user->password->CellAttributes() ?>><span id="cb_x_password">
<input type="text" name="x_password" id="x_password"  size="30" maxlength="128" value="<?php echo $user->password->EditValue ?>"<?php echo $user->password->EditAttributes() ?>>
</span></td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">level<span class='ewmsg'>&nbsp;*</span></td>
		<td<?php echo $user->level->CellAttributes() ?>><span id="cb_x_level">
<input type="text" name="x_level" id="x_level"  size="30" value="<?php echo $user->level->EditValue ?>"<?php echo $user->level->EditAttributes() ?>>
</span></td>
	</tr>
</table>
<p>
<input type="submit" name="btnAction" id="btnAction" value="   Edit   ">
</form>
<script language="JavaScript" type="text/javascript">
<!--

// Write your table-specific startup script here
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

// Load form values
function LoadFormValues() {

	// Load from form
	global $objForm, $user;
	$user->id->setFormValue($objForm->GetValue("x_id"));
	$user->user->setFormValue($objForm->GetValue("x_user"));
	$user->password->setFormValue($objForm->GetValue("x_password"));
	$user->level->setFormValue($objForm->GetValue("x_level"));
}

// Restore form values
function RestoreFormValues() {
	global $user;
	$user->id->CurrentValue = $user->id->FormValue;
	$user->user->CurrentValue = $user->user->FormValue;
	$user->password->CurrentValue = $user->password->FormValue;
	$user->level->CurrentValue = $user->level->FormValue;
}
?>
<?php

// Load row based on key values
function LoadRow() {
	global $conn, $Security, $user;
	$sFilter = $user->SqlKeyFilter();
	if (!is_numeric($user->id->CurrentValue)) {
		return FALSE; // Invalid key, exit
	}
	$sFilter = str_replace("@id@", ew_AdjustSql($user->id->CurrentValue), $sFilter); // Replace key value

	// Call Row Selecting event
	$user->Row_Selecting($sFilter);

	// Load sql based on filter
	$user->CurrentFilter = $sFilter;
	$sSql = $user->SQL();
	if ($rs = $conn->Execute($sSql)) {
		if ($rs->EOF) {
			$LoadRow = FALSE;
		} else {
			$LoadRow = TRUE;
			$rs->MoveFirst();
			LoadRowValues($rs); // Load row values

			// Call Row Selected event
			$user->Row_Selected($rs);
		}
		$rs->Close();
	} else {
		$LoadRow = FALSE;
	}
	return $LoadRow;
}

// Load row values from recordset
function LoadRowValues(&$rs) {
	global $user;
	$user->id->setDbValue($rs->fields('id'));
	$user->user->setDbValue($rs->fields('user'));
	$user->password->setDbValue($rs->fields('password'));
	$user->level->setDbValue($rs->fields('level'));
}
?>
<?php

// Render row values based on field settings
function RenderRow() {
	global $conn, $Security, $user;

	// Call Row Rendering event
	$user->Row_Rendering();

	// Common render codes for all row types
	// id

	$user->id->CellCssStyle = "";
	$user->id->CellCssClass = "";

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
	} elseif ($user->RowType == EW_ROWTYPE_EDIT) { // Edit row

		// id
		$user->id->EditCustomAttributes = "";
		$user->id->EditValue = $user->id->CurrentValue;
		$user->id->CssStyle = "";
		$user->id->CssClass = "";
		$user->id->ViewCustomAttributes = "";

		// user
		$user->user->EditCustomAttributes = "";
		$user->user->EditValue = ew_HtmlEncode($user->user->CurrentValue);

		// password
		$user->password->EditCustomAttributes = "";
		$user->password->EditValue = ew_HtmlEncode($user->password->CurrentValue);

		// level
		$user->level->EditCustomAttributes = "";
		$user->level->EditValue = ew_HtmlEncode($user->level->CurrentValue);
	} elseif ($user->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$user->Row_Rendered();
}
?>
<?php

// Update record based on key values
function EditRow() {
	global $conn, $Security, $user;
	$sFilter = $user->SqlKeyFilter();
	if (!is_numeric($user->id->CurrentValue)) {
		return FALSE;
	}
	$sFilter = str_replace("@id@", ew_AdjustSql($user->id->CurrentValue), $sFilter); // Replace key value
	$user->CurrentFilter = $sFilter;
	$sSql = $user->SQL();
	$conn->raiseErrorFn = 'ew_ErrorFn';
	$rs = $conn->Execute($sSql);
	$conn->raiseErrorFn = '';
	if ($rs === FALSE)
		return FALSE;
	if ($rs->EOF) {
		$EditRow = FALSE; // Update Failed
	} else {

		// Save old values
		$rsold =& $rs->fields;
		$rsnew = array();

		// Field id
		// Field user

		$user->user->SetDbValueDef($user->user->CurrentValue, "");
		$rsnew['user'] =& $user->user->DbValue;

		// Field password
		$user->password->SetDbValueDef($user->password->CurrentValue, NULL);
		$rsnew['password'] =& $user->password->DbValue;

		// Field level
		$user->level->SetDbValueDef($user->level->CurrentValue, 0);
		$rsnew['level'] =& $user->level->DbValue;

		// Call Row Updating event
		$bUpdateRow = $user->Row_Updating($rsold, $rsnew);
		if ($bUpdateRow) {
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$EditRow = $conn->Execute($user->UpdateSQL($rsnew));
			$conn->raiseErrorFn = '';
		} else {
			if ($user->CancelMessage <> "") {
				$_SESSION[EW_SESSION_MESSAGE] = $user->CancelMessage;
				$user->CancelMessage = "";
			} else {
				$_SESSION[EW_SESSION_MESSAGE] = "Update cancelled";
			}
			$EditRow = FALSE;
		}
	}

	// Call Row Updated event
	if ($EditRow) {
		$user->Row_Updated($rsold, $rsnew);
	}
	$rs->Close();
	return $EditRow;
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

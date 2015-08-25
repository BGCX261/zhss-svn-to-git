<?php
define("EW_PAGE_ID", "edit", TRUE); // Page ID
define("EW_TABLE_NAME", 'logs', TRUE);
?>
<?php 
session_start(); // Initialize session data
ob_start(); // Turn on output buffering
?>
<?php include "ewcfg50.php" ?>
<?php include "ewmysql50.php" ?>
<?php include "phpfn50.php" ?>
<?php include "logsinfo.php" ?>
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
$logs->Export = @$_GET["export"]; // Get export parameter
$sExport = $logs->Export; // Get export parameter, used in header
$sExportFile = $logs->TableVar; // Get export file, used in header
?>
<?php

// Load key from QueryString
if (@$_GET["id"] <> "") {
	$logs->id->setQueryStringValue($_GET["id"]);
}

// Create form object
$objForm = new cFormObj();
if (@$_POST["a_edit"] <> "") {
	$logs->CurrentAction = $_POST["a_edit"]; // Get action code
	LoadFormValues(); // Get form values
} else {
	$logs->CurrentAction = "I"; // Default action is display
}

// Check if valid key
if ($logs->id->CurrentValue == "") Page_Terminate($logs->getReturnUrl()); // Invalid key, exit
switch ($logs->CurrentAction) {
	case "I": // Get a record to display
		if (!LoadRow()) { // Load Record based on key
			$_SESSION[EW_SESSION_MESSAGE] = "No records found"; // No record found
			Page_Terminate($logs->getReturnUrl()); // Return to caller
		}
		break;
	Case "U": // Update
		$logs->SendEmail = TRUE; // Send email on update success
		if (EditRow()) { // Update Record based on key
			$_SESSION[EW_SESSION_MESSAGE] = "Update successful"; // Update success
			Page_Terminate($logs->getReturnUrl()); // Return to caller
		} else {
			RestoreFormValues(); // Restore form values if update failed
		}
}

// Render the record
$logs->RowType = EW_ROWTYPE_EDIT; // Render as edit
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
		elm = fobj.elements["x" + infix + "_time"];
		if (elm && !ew_HasValue(elm)) {
			if (!ew_OnError(elm, "Please enter required field - time"))
				return false;
		}
		elm = fobj.elements["x" + infix + "_time"];
		if (elm && !ew_CheckDate(elm.value)) {
			if (!ew_OnError(elm, "Incorrect date, format = yyyy/mm/dd - time"))
				return false; 
		}
		elm = fobj.elements["x" + infix + "_type"];
		if (elm && !ew_CheckInteger(elm.value)) {
			if (!ew_OnError(elm, "Incorrect integer - type"))
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
<p><span class="phpmaker">Edit TABLE: logs<br><br><a href="<?php echo $logs->getReturnUrl() ?>">Go Back</a></span></p>
<?php
if (@$_SESSION[EW_SESSION_MESSAGE] <> "") {
?>
<p><span class="ewmsg"><?php echo $_SESSION[EW_SESSION_MESSAGE] ?></span></p>
<?php
	$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message
}
?>
<form name="flogsedit" id="flogsedit" action="logsedit.php" method="post" onSubmit="return ew_ValidateForm(this);">
<p>
<input type="hidden" name="a_edit" id="a_edit" value="U">
<table class="ewTable">
	<tr class="ewTableRow">
		<td class="ewTableHeader">id</td>
		<td<?php echo $logs->id->CellAttributes() ?>><span id="cb_x_id">
<div<?php echo $logs->id->ViewAttributes() ?>><?php echo $logs->id->EditValue ?></div>
<input type="hidden" name="x_id" id="x_id" value="<?php echo ew_HtmlEncode($logs->id->CurrentValue) ?>">
</span></td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">time<span class='ewmsg'>&nbsp;*</span></td>
		<td<?php echo $logs->time->CellAttributes() ?>><span id="cb_x_time">
<input type="text" name="x_time" id="x_time"  value="<?php echo $logs->time->EditValue ?>"<?php echo $logs->time->EditAttributes() ?>>
</span></td>
	</tr>
	<tr class="ewTableRow">
		<td class="ewTableHeader">client</td>
		<td<?php echo $logs->client->CellAttributes() ?>><span id="cb_x_client">
<input type="text" name="x_client" id="x_client"  size="30" maxlength="30" value="<?php echo $logs->client->EditValue ?>"<?php echo $logs->client->EditAttributes() ?>>
</span></td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">group</td>
		<td<?php echo $logs->group->CellAttributes() ?>><span id="cb_x_group">
<input type="text" name="x_group" id="x_group"  size="30" maxlength="20" value="<?php echo $logs->group->EditValue ?>"<?php echo $logs->group->EditAttributes() ?>>
</span></td>
	</tr>
	<tr class="ewTableRow">
		<td class="ewTableHeader">type</td>
		<td<?php echo $logs->type->CellAttributes() ?>><span id="cb_x_type">
<input type="text" name="x_type" id="x_type"  size="30" value="<?php echo $logs->type->EditValue ?>"<?php echo $logs->type->EditAttributes() ?>>
</span></td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">message</td>
		<td<?php echo $logs->message->CellAttributes() ?>><span id="cb_x_message">
<input type="text" name="x_message" id="x_message"  size="30" maxlength="100" value="<?php echo $logs->message->EditValue ?>"<?php echo $logs->message->EditAttributes() ?>>
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
	global $objForm, $logs;
	$logs->id->setFormValue($objForm->GetValue("x_id"));
	$logs->time->setFormValue($objForm->GetValue("x_time"));
	$logs->time->CurrentValue = ew_UnFormatDateTime($logs->time->CurrentValue, 5);
	$logs->client->setFormValue($objForm->GetValue("x_client"));
	$logs->group->setFormValue($objForm->GetValue("x_group"));
	$logs->type->setFormValue($objForm->GetValue("x_type"));
	$logs->message->setFormValue($objForm->GetValue("x_message"));
}

// Restore form values
function RestoreFormValues() {
	global $logs;
	$logs->id->CurrentValue = $logs->id->FormValue;
	$logs->time->CurrentValue = $logs->time->FormValue;
	$logs->time->CurrentValue = ew_UnFormatDateTime($logs->time->CurrentValue, 5);
	$logs->client->CurrentValue = $logs->client->FormValue;
	$logs->group->CurrentValue = $logs->group->FormValue;
	$logs->type->CurrentValue = $logs->type->FormValue;
	$logs->message->CurrentValue = $logs->message->FormValue;
}
?>
<?php

// Load row based on key values
function LoadRow() {
	global $conn, $Security, $logs;
	$sFilter = $logs->SqlKeyFilter();
	if (!is_numeric($logs->id->CurrentValue)) {
		return FALSE; // Invalid key, exit
	}
	$sFilter = str_replace("@id@", ew_AdjustSql($logs->id->CurrentValue), $sFilter); // Replace key value

	// Call Row Selecting event
	$logs->Row_Selecting($sFilter);

	// Load sql based on filter
	$logs->CurrentFilter = $sFilter;
	$sSql = $logs->SQL();
	if ($rs = $conn->Execute($sSql)) {
		if ($rs->EOF) {
			$LoadRow = FALSE;
		} else {
			$LoadRow = TRUE;
			$rs->MoveFirst();
			LoadRowValues($rs); // Load row values

			// Call Row Selected event
			$logs->Row_Selected($rs);
		}
		$rs->Close();
	} else {
		$LoadRow = FALSE;
	}
	return $LoadRow;
}

// Load row values from recordset
function LoadRowValues(&$rs) {
	global $logs;
	$logs->id->setDbValue($rs->fields('id'));
	$logs->time->setDbValue($rs->fields('time'));
	$logs->client->setDbValue($rs->fields('client'));
	$logs->group->setDbValue($rs->fields('group'));
	$logs->type->setDbValue($rs->fields('type'));
	$logs->message->setDbValue($rs->fields('message'));
}
?>
<?php

// Render row values based on field settings
function RenderRow() {
	global $conn, $Security, $logs;

	// Call Row Rendering event
	$logs->Row_Rendering();

	// Common render codes for all row types
	// id

	$logs->id->CellCssStyle = "";
	$logs->id->CellCssClass = "";

	// time
	$logs->time->CellCssStyle = "";
	$logs->time->CellCssClass = "";

	// client
	$logs->client->CellCssStyle = "";
	$logs->client->CellCssClass = "";

	// group
	$logs->group->CellCssStyle = "";
	$logs->group->CellCssClass = "";

	// type
	$logs->type->CellCssStyle = "";
	$logs->type->CellCssClass = "";

	// message
	$logs->message->CellCssStyle = "";
	$logs->message->CellCssClass = "";
	if ($logs->RowType == EW_ROWTYPE_VIEW) { // View row
	} elseif ($logs->RowType == EW_ROWTYPE_ADD) { // Add row
	} elseif ($logs->RowType == EW_ROWTYPE_EDIT) { // Edit row

		// id
		$logs->id->EditCustomAttributes = "";
		$logs->id->EditValue = $logs->id->CurrentValue;
		$logs->id->CssStyle = "";
		$logs->id->CssClass = "";
		$logs->id->ViewCustomAttributes = "";

		// time
		$logs->time->EditCustomAttributes = "";
		$logs->time->EditValue = ew_HtmlEncode(ew_FormatDateTime($logs->time->CurrentValue, 5));

		// client
		$logs->client->EditCustomAttributes = "";
		$logs->client->EditValue = ew_HtmlEncode($logs->client->CurrentValue);

		// group
		$logs->group->EditCustomAttributes = "";
		$logs->group->EditValue = ew_HtmlEncode($logs->group->CurrentValue);

		// type
		$logs->type->EditCustomAttributes = "";
		$logs->type->EditValue = ew_HtmlEncode($logs->type->CurrentValue);

		// message
		$logs->message->EditCustomAttributes = "";
		$logs->message->EditValue = ew_HtmlEncode($logs->message->CurrentValue);
	} elseif ($logs->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$logs->Row_Rendered();
}
?>
<?php

// Update record based on key values
function EditRow() {
	global $conn, $Security, $logs;
	$sFilter = $logs->SqlKeyFilter();
	if (!is_numeric($logs->id->CurrentValue)) {
		return FALSE;
	}
	$sFilter = str_replace("@id@", ew_AdjustSql($logs->id->CurrentValue), $sFilter); // Replace key value
	$logs->CurrentFilter = $sFilter;
	$sSql = $logs->SQL();
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
		// Field time

		$logs->time->SetDbValueDef(ew_UnFormatDateTime($logs->time->CurrentValue, 5), ew_CurrentDate());
		$rsnew['time'] =& $logs->time->DbValue;

		// Field client
		$logs->client->SetDbValueDef($logs->client->CurrentValue, NULL);
		$rsnew['client'] =& $logs->client->DbValue;

		// Field group
		$logs->group->SetDbValueDef($logs->group->CurrentValue, NULL);
		$rsnew['group'] =& $logs->group->DbValue;

		// Field type
		$logs->type->SetDbValueDef($logs->type->CurrentValue, NULL);
		$rsnew['type'] =& $logs->type->DbValue;

		// Field message
		$logs->message->SetDbValueDef($logs->message->CurrentValue, NULL);
		$rsnew['message'] =& $logs->message->DbValue;

		// Call Row Updating event
		$bUpdateRow = $logs->Row_Updating($rsold, $rsnew);
		if ($bUpdateRow) {
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$EditRow = $conn->Execute($logs->UpdateSQL($rsnew));
			$conn->raiseErrorFn = '';
		} else {
			if ($logs->CancelMessage <> "") {
				$_SESSION[EW_SESSION_MESSAGE] = $logs->CancelMessage;
				$logs->CancelMessage = "";
			} else {
				$_SESSION[EW_SESSION_MESSAGE] = "Update cancelled";
			}
			$EditRow = FALSE;
		}
	}

	// Call Row Updated event
	if ($EditRow) {
		$logs->Row_Updated($rsold, $rsnew);
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

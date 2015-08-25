<?php
define("EW_PAGE_ID", "edit", TRUE); // Page ID
define("EW_TABLE_NAME", 'urls', TRUE);
?>
<?php 
session_start(); // Initialize session data
ob_start(); // Turn on output buffering
?>
<?php include "ewcfg50.php" ?>
<?php include "ewmysql50.php" ?>
<?php include "phpfn50.php" ?>
<?php include "urlsinfo.php" ?>
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
$urls->Export = @$_GET["export"]; // Get export parameter
$sExport = $urls->Export; // Get export parameter, used in header
$sExportFile = $urls->TableVar; // Get export file, used in header
?>
<?php

// Load key from QueryString
if (@$_GET["id"] <> "") {
	$urls->id->setQueryStringValue($_GET["id"]);
}

// Create form object
$objForm = new cFormObj();
if (@$_POST["a_edit"] <> "") {
	$urls->CurrentAction = $_POST["a_edit"]; // Get action code
	LoadFormValues(); // Get form values
} else {
	$urls->CurrentAction = "I"; // Default action is display
}

// Check if valid key
if ($urls->id->CurrentValue == "") Page_Terminate($urls->getReturnUrl()); // Invalid key, exit
switch ($urls->CurrentAction) {
	case "I": // Get a record to display
		if (!LoadRow()) { // Load Record based on key
			$_SESSION[EW_SESSION_MESSAGE] = "No records found"; // No record found
			Page_Terminate($urls->getReturnUrl()); // Return to caller
		}
		break;
	Case "U": // Update
		$urls->SendEmail = TRUE; // Send email on update success
		if (EditRow()) { // Update Record based on key
			$_SESSION[EW_SESSION_MESSAGE] = "Update successful"; // Update success
			Page_Terminate($urls->getReturnUrl()); // Return to caller
		} else {
			RestoreFormValues(); // Restore form values if update failed
		}
}

// Render the record
$urls->RowType = EW_ROWTYPE_EDIT; // Render as edit
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
<p><span class="phpmaker">Edit TABLE: urls<br><br><a href="<?php echo $urls->getReturnUrl() ?>">Go Back</a></span></p>
<?php
if (@$_SESSION[EW_SESSION_MESSAGE] <> "") {
?>
<p><span class="ewmsg"><?php echo $_SESSION[EW_SESSION_MESSAGE] ?></span></p>
<?php
	$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message
}
?>
<form name="furlsedit" id="furlsedit" action="urlsedit.php" method="post" onSubmit="return ew_ValidateForm(this);">
<p>
<input type="hidden" name="a_edit" id="a_edit" value="U">
<table class="ewTable">
	<tr class="ewTableRow">
		<td class="ewTableHeader">id</td>
		<td<?php echo $urls->id->CellAttributes() ?>><span id="cb_x_id">
<div<?php echo $urls->id->ViewAttributes() ?>><?php echo $urls->id->EditValue ?></div>
<input type="hidden" name="x_id" id="x_id" value="<?php echo ew_HtmlEncode($urls->id->CurrentValue) ?>">
</span></td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">url</td>
		<td<?php echo $urls->url->CellAttributes() ?>><span id="cb_x_url">
<input type="text" name="x_url" id="x_url"  size="30" maxlength="255" value="<?php echo $urls->url->EditValue ?>"<?php echo $urls->url->EditAttributes() ?>>
</span></td>
	</tr>
	<tr class="ewTableRow">
		<td class="ewTableHeader">digest</td>
		<td<?php echo $urls->digest->CellAttributes() ?>><span id="cb_x_digest">
<input type="text" name="x_digest" id="x_digest"  size="30" maxlength="32" value="<?php echo $urls->digest->EditValue ?>"<?php echo $urls->digest->EditAttributes() ?>>
</span></td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">type</td>
		<td<?php echo $urls->type->CellAttributes() ?>><span id="cb_x_type">
<input type="text" name="x_type" id="x_type"  size="30" value="<?php echo $urls->type->EditValue ?>"<?php echo $urls->type->EditAttributes() ?>>
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
	global $objForm, $urls;
	$urls->id->setFormValue($objForm->GetValue("x_id"));
	$urls->url->setFormValue($objForm->GetValue("x_url"));
	$urls->digest->setFormValue($objForm->GetValue("x_digest"));
	$urls->type->setFormValue($objForm->GetValue("x_type"));
}

// Restore form values
function RestoreFormValues() {
	global $urls;
	$urls->id->CurrentValue = $urls->id->FormValue;
	$urls->url->CurrentValue = $urls->url->FormValue;
	$urls->digest->CurrentValue = $urls->digest->FormValue;
	$urls->type->CurrentValue = $urls->type->FormValue;
}
?>
<?php

// Load row based on key values
function LoadRow() {
	global $conn, $Security, $urls;
	$sFilter = $urls->SqlKeyFilter();
	if (!is_numeric($urls->id->CurrentValue)) {
		return FALSE; // Invalid key, exit
	}
	$sFilter = str_replace("@id@", ew_AdjustSql($urls->id->CurrentValue), $sFilter); // Replace key value

	// Call Row Selecting event
	$urls->Row_Selecting($sFilter);

	// Load sql based on filter
	$urls->CurrentFilter = $sFilter;
	$sSql = $urls->SQL();
	if ($rs = $conn->Execute($sSql)) {
		if ($rs->EOF) {
			$LoadRow = FALSE;
		} else {
			$LoadRow = TRUE;
			$rs->MoveFirst();
			LoadRowValues($rs); // Load row values

			// Call Row Selected event
			$urls->Row_Selected($rs);
		}
		$rs->Close();
	} else {
		$LoadRow = FALSE;
	}
	return $LoadRow;
}

// Load row values from recordset
function LoadRowValues(&$rs) {
	global $urls;
	$urls->id->setDbValue($rs->fields('id'));
	$urls->url->setDbValue($rs->fields('url'));
	$urls->digest->setDbValue($rs->fields('digest'));
	$urls->type->setDbValue($rs->fields('type'));
}
?>
<?php

// Render row values based on field settings
function RenderRow() {
	global $conn, $Security, $urls;

	// Call Row Rendering event
	$urls->Row_Rendering();

	// Common render codes for all row types
	// id

	$urls->id->CellCssStyle = "";
	$urls->id->CellCssClass = "";

	// url
	$urls->url->CellCssStyle = "";
	$urls->url->CellCssClass = "";

	// digest
	$urls->digest->CellCssStyle = "";
	$urls->digest->CellCssClass = "";

	// type
	$urls->type->CellCssStyle = "";
	$urls->type->CellCssClass = "";
	if ($urls->RowType == EW_ROWTYPE_VIEW) { // View row
	} elseif ($urls->RowType == EW_ROWTYPE_ADD) { // Add row
	} elseif ($urls->RowType == EW_ROWTYPE_EDIT) { // Edit row

		// id
		$urls->id->EditCustomAttributes = "";
		$urls->id->EditValue = $urls->id->CurrentValue;
		$urls->id->CssStyle = "";
		$urls->id->CssClass = "";
		$urls->id->ViewCustomAttributes = "";

		// url
		$urls->url->EditCustomAttributes = "";
		$urls->url->EditValue = ew_HtmlEncode($urls->url->CurrentValue);

		// digest
		$urls->digest->EditCustomAttributes = "";
		$urls->digest->EditValue = ew_HtmlEncode($urls->digest->CurrentValue);

		// type
		$urls->type->EditCustomAttributes = "";
		$urls->type->EditValue = ew_HtmlEncode($urls->type->CurrentValue);
	} elseif ($urls->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$urls->Row_Rendered();
}
?>
<?php

// Update record based on key values
function EditRow() {
	global $conn, $Security, $urls;
	$sFilter = $urls->SqlKeyFilter();
	if (!is_numeric($urls->id->CurrentValue)) {
		return FALSE;
	}
	$sFilter = str_replace("@id@", ew_AdjustSql($urls->id->CurrentValue), $sFilter); // Replace key value
	$urls->CurrentFilter = $sFilter;
	$sSql = $urls->SQL();
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
		// Field url

		$urls->url->SetDbValueDef($urls->url->CurrentValue, NULL);
		$rsnew['url'] =& $urls->url->DbValue;

		// Field digest
		$urls->digest->SetDbValueDef($urls->digest->CurrentValue, NULL);
		$rsnew['digest'] =& $urls->digest->DbValue;

		// Field type
		$urls->type->SetDbValueDef($urls->type->CurrentValue, NULL);
		$rsnew['type'] =& $urls->type->DbValue;

		// Call Row Updating event
		$bUpdateRow = $urls->Row_Updating($rsold, $rsnew);
		if ($bUpdateRow) {
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$EditRow = $conn->Execute($urls->UpdateSQL($rsnew));
			$conn->raiseErrorFn = '';
		} else {
			if ($urls->CancelMessage <> "") {
				$_SESSION[EW_SESSION_MESSAGE] = $urls->CancelMessage;
				$urls->CancelMessage = "";
			} else {
				$_SESSION[EW_SESSION_MESSAGE] = "Update cancelled";
			}
			$EditRow = FALSE;
		}
	}

	// Call Row Updated event
	if ($EditRow) {
		$urls->Row_Updated($rsold, $rsnew);
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

<?php
define("EW_PAGE_ID", "edit", TRUE); // Page ID
define("EW_TABLE_NAME", 'archives', TRUE);
?>
<?php 
session_start(); // Initialize session data
ob_start(); // Turn on output buffering
?>
<?php include "ewcfg50.php" ?>
<?php include "ewmysql50.php" ?>
<?php include "phpfn50.php" ?>
<?php include "archivesinfo.php" ?>
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
$archives->Export = @$_GET["export"]; // Get export parameter
$sExport = $archives->Export; // Get export parameter, used in header
$sExportFile = $archives->TableVar; // Get export file, used in header
?>
<?php

// Load key from QueryString
if (@$_GET["id"] <> "") {
	$archives->id->setQueryStringValue($_GET["id"]);
}

// Create form object
$objForm = new cFormObj();
if (@$_POST["a_edit"] <> "") {
	$archives->CurrentAction = $_POST["a_edit"]; // Get action code
	LoadFormValues(); // Get form values
} else {
	$archives->CurrentAction = "I"; // Default action is display
}

// Check if valid key
if ($archives->id->CurrentValue == "") Page_Terminate($archives->getReturnUrl()); // Invalid key, exit
switch ($archives->CurrentAction) {
	case "I": // Get a record to display
		if (!LoadRow()) { // Load Record based on key
			$_SESSION[EW_SESSION_MESSAGE] = "No records found"; // No record found
			Page_Terminate($archives->getReturnUrl()); // Return to caller
		}
		break;
	Case "U": // Update
		$archives->SendEmail = TRUE; // Send email on update success
		if (EditRow()) { // Update Record based on key
			$_SESSION[EW_SESSION_MESSAGE] = "Update successful"; // Update success
			Page_Terminate($archives->getReturnUrl()); // Return to caller
		} else {
			RestoreFormValues(); // Restore form values if update failed
		}
}

// Render the record
$archives->RowType = EW_ROWTYPE_EDIT; // Render as edit
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
		elm = fobj.elements["x" + infix + "_url"];
		if (elm && !ew_HasValue(elm)) {
			if (!ew_OnError(elm, "Please enter required field - url"))
				return false;
		}
		elm = fobj.elements["x" + infix + "_title"];
		if (elm && !ew_HasValue(elm)) {
			if (!ew_OnError(elm, "Please enter required field - title"))
				return false;
		}
		elm = fobj.elements["x" + infix + "_datetime"];
		if (elm && !ew_HasValue(elm)) {
			if (!ew_OnError(elm, "Please enter required field - datetime"))
				return false;
		}
		elm = fobj.elements["x" + infix + "_datetime"];
		if (elm && !ew_CheckDate(elm.value)) {
			if (!ew_OnError(elm, "Incorrect date, format = yyyy/mm/dd - datetime"))
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
<p><span class="phpmaker">Edit TABLE: archives<br><br><a href="<?php echo $archives->getReturnUrl() ?>">Go Back</a></span></p>
<?php
if (@$_SESSION[EW_SESSION_MESSAGE] <> "") {
?>
<p><span class="ewmsg"><?php echo $_SESSION[EW_SESSION_MESSAGE] ?></span></p>
<?php
	$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message
}
?>
<form name="farchivesedit" id="farchivesedit" action="archivesedit.php" method="post" onSubmit="return ew_ValidateForm(this);">
<p>
<input type="hidden" name="a_edit" id="a_edit" value="U">
<table class="ewTable">
	<tr class="ewTableRow">
		<td class="ewTableHeader">id</td>
		<td<?php echo $archives->id->CellAttributes() ?>><span id="cb_x_id">
<div<?php echo $archives->id->ViewAttributes() ?>><?php echo $archives->id->EditValue ?></div>
<input type="hidden" name="x_id" id="x_id" value="<?php echo ew_HtmlEncode($archives->id->CurrentValue) ?>">
</span></td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">url<span class='ewmsg'>&nbsp;*</span></td>
		<td<?php echo $archives->url->CellAttributes() ?>><span id="cb_x_url">
<input type="text" name="x_url" id="x_url"  size="30" maxlength="255" value="<?php echo $archives->url->EditValue ?>"<?php echo $archives->url->EditAttributes() ?>>
</span></td>
	</tr>
	<tr class="ewTableRow">
		<td class="ewTableHeader">projectname</td>
		<td<?php echo $archives->projectname->CellAttributes() ?>><span id="cb_x_projectname">
<input type="text" name="x_projectname" id="x_projectname"  size="30" maxlength="255" value="<?php echo $archives->projectname->EditValue ?>"<?php echo $archives->projectname->EditAttributes() ?>>
</span></td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">title<span class='ewmsg'>&nbsp;*</span></td>
		<td<?php echo $archives->title->CellAttributes() ?>><span id="cb_x_title">
<input type="text" name="x_title" id="x_title"  size="30" maxlength="255" value="<?php echo $archives->title->EditValue ?>"<?php echo $archives->title->EditAttributes() ?>>
</span></td>
	</tr>
	<tr class="ewTableRow">
		<td class="ewTableHeader">datetime<span class='ewmsg'>&nbsp;*</span></td>
		<td<?php echo $archives->datetime->CellAttributes() ?>><span id="cb_x_datetime">
<input type="text" name="x_datetime" id="x_datetime"  value="<?php echo $archives->datetime->EditValue ?>"<?php echo $archives->datetime->EditAttributes() ?>>
</span></td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">content</td>
		<td<?php echo $archives->content->CellAttributes() ?>><span id="cb_x_content">
<textarea name="x_content" id="x_content" cols="35" rows="4"<?php echo $archives->content->EditAttributes() ?>><?php echo $archives->content->EditValue ?></textarea>
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
	global $objForm, $archives;
	$archives->id->setFormValue($objForm->GetValue("x_id"));
	$archives->url->setFormValue($objForm->GetValue("x_url"));
	$archives->projectname->setFormValue($objForm->GetValue("x_projectname"));
	$archives->title->setFormValue($objForm->GetValue("x_title"));
	$archives->datetime->setFormValue($objForm->GetValue("x_datetime"));
	$archives->datetime->CurrentValue = ew_UnFormatDateTime($archives->datetime->CurrentValue, 5);
	$archives->content->setFormValue($objForm->GetValue("x_content"));
}

// Restore form values
function RestoreFormValues() {
	global $archives;
	$archives->id->CurrentValue = $archives->id->FormValue;
	$archives->url->CurrentValue = $archives->url->FormValue;
	$archives->projectname->CurrentValue = $archives->projectname->FormValue;
	$archives->title->CurrentValue = $archives->title->FormValue;
	$archives->datetime->CurrentValue = $archives->datetime->FormValue;
	$archives->datetime->CurrentValue = ew_UnFormatDateTime($archives->datetime->CurrentValue, 5);
	$archives->content->CurrentValue = $archives->content->FormValue;
}
?>
<?php

// Load row based on key values
function LoadRow() {
	global $conn, $Security, $archives;
	$sFilter = $archives->SqlKeyFilter();
	if (!is_numeric($archives->id->CurrentValue)) {
		return FALSE; // Invalid key, exit
	}
	$sFilter = str_replace("@id@", ew_AdjustSql($archives->id->CurrentValue), $sFilter); // Replace key value

	// Call Row Selecting event
	$archives->Row_Selecting($sFilter);

	// Load sql based on filter
	$archives->CurrentFilter = $sFilter;
	$sSql = $archives->SQL();
	if ($rs = $conn->Execute($sSql)) {
		if ($rs->EOF) {
			$LoadRow = FALSE;
		} else {
			$LoadRow = TRUE;
			$rs->MoveFirst();
			LoadRowValues($rs); // Load row values

			// Call Row Selected event
			$archives->Row_Selected($rs);
		}
		$rs->Close();
	} else {
		$LoadRow = FALSE;
	}
	return $LoadRow;
}

// Load row values from recordset
function LoadRowValues(&$rs) {
	global $archives;
	$archives->id->setDbValue($rs->fields('id'));
	$archives->url->setDbValue($rs->fields('url'));
	$archives->projectname->setDbValue($rs->fields('projectname'));
	$archives->title->setDbValue($rs->fields('title'));
	$archives->datetime->setDbValue($rs->fields('datetime'));
	$archives->content->setDbValue($rs->fields('content'));
}
?>
<?php

// Render row values based on field settings
function RenderRow() {
	global $conn, $Security, $archives;

	// Call Row Rendering event
	$archives->Row_Rendering();

	// Common render codes for all row types
	// id

	$archives->id->CellCssStyle = "";
	$archives->id->CellCssClass = "";

	// url
	$archives->url->CellCssStyle = "";
	$archives->url->CellCssClass = "";

	// projectname
	$archives->projectname->CellCssStyle = "";
	$archives->projectname->CellCssClass = "";

	// title
	$archives->title->CellCssStyle = "";
	$archives->title->CellCssClass = "";

	// datetime
	$archives->datetime->CellCssStyle = "";
	$archives->datetime->CellCssClass = "";

	// content
	$archives->content->CellCssStyle = "";
	$archives->content->CellCssClass = "";
	if ($archives->RowType == EW_ROWTYPE_VIEW) { // View row
	} elseif ($archives->RowType == EW_ROWTYPE_ADD) { // Add row
	} elseif ($archives->RowType == EW_ROWTYPE_EDIT) { // Edit row

		// id
		$archives->id->EditCustomAttributes = "";
		$archives->id->EditValue = $archives->id->CurrentValue;
		$archives->id->CssStyle = "";
		$archives->id->CssClass = "";
		$archives->id->ViewCustomAttributes = "";

		// url
		$archives->url->EditCustomAttributes = "";
		$archives->url->EditValue = ew_HtmlEncode($archives->url->CurrentValue);

		// projectname
		$archives->projectname->EditCustomAttributes = "";
		$archives->projectname->EditValue = ew_HtmlEncode($archives->projectname->CurrentValue);

		// title
		$archives->title->EditCustomAttributes = "";
		$archives->title->EditValue = ew_HtmlEncode($archives->title->CurrentValue);

		// datetime
		$archives->datetime->EditCustomAttributes = "";
		$archives->datetime->EditValue = ew_HtmlEncode(ew_FormatDateTime($archives->datetime->CurrentValue, 5));

		// content
		$archives->content->EditCustomAttributes = "";
		$archives->content->EditValue = ew_HtmlEncode($archives->content->CurrentValue);
	} elseif ($archives->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$archives->Row_Rendered();
}
?>
<?php

// Update record based on key values
function EditRow() {
	global $conn, $Security, $archives;
	$sFilter = $archives->SqlKeyFilter();
	if (!is_numeric($archives->id->CurrentValue)) {
		return FALSE;
	}
	$sFilter = str_replace("@id@", ew_AdjustSql($archives->id->CurrentValue), $sFilter); // Replace key value
	$archives->CurrentFilter = $sFilter;
	$sSql = $archives->SQL();
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

		$archives->url->SetDbValueDef($archives->url->CurrentValue, "");
		$rsnew['url'] =& $archives->url->DbValue;

		// Field projectname
		$archives->projectname->SetDbValueDef($archives->projectname->CurrentValue, NULL);
		$rsnew['projectname'] =& $archives->projectname->DbValue;

		// Field title
		$archives->title->SetDbValueDef($archives->title->CurrentValue, "");
		$rsnew['title'] =& $archives->title->DbValue;

		// Field datetime
		$archives->datetime->SetDbValueDef(ew_UnFormatDateTime($archives->datetime->CurrentValue, 5), ew_CurrentDate());
		$rsnew['datetime'] =& $archives->datetime->DbValue;

		// Field content
		$archives->content->SetDbValueDef($archives->content->CurrentValue, NULL);
		$rsnew['content'] =& $archives->content->DbValue;

		// Call Row Updating event
		$bUpdateRow = $archives->Row_Updating($rsold, $rsnew);
		if ($bUpdateRow) {
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$EditRow = $conn->Execute($archives->UpdateSQL($rsnew));
			$conn->raiseErrorFn = '';
		} else {
			if ($archives->CancelMessage <> "") {
				$_SESSION[EW_SESSION_MESSAGE] = $archives->CancelMessage;
				$archives->CancelMessage = "";
			} else {
				$_SESSION[EW_SESSION_MESSAGE] = "Update cancelled";
			}
			$EditRow = FALSE;
		}
	}

	// Call Row Updated event
	if ($EditRow) {
		$archives->Row_Updated($rsold, $rsnew);
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

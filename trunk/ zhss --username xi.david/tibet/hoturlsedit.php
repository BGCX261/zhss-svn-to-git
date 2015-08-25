<?php
define("EW_PAGE_ID", "edit", TRUE); // Page ID
define("EW_TABLE_NAME", 'hoturls', TRUE);
?>
<?php 
session_start(); // Initialize session data
ob_start(); // Turn on output buffering
?>
<?php include "ewcfg50.php" ?>
<?php include "ewmysql50.php" ?>
<?php include "phpfn50.php" ?>
<?php include "hoturlsinfo.php" ?>
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
$hoturls->Export = @$_GET["export"]; // Get export parameter
$sExport = $hoturls->Export; // Get export parameter, used in header
$sExportFile = $hoturls->TableVar; // Get export file, used in header
?>
<?php

// Load key from QueryString
if (@$_GET["id"] <> "") {
	$hoturls->id->setQueryStringValue($_GET["id"]);
}

// Create form object
$objForm = new cFormObj();
if (@$_POST["a_edit"] <> "") {
	$hoturls->CurrentAction = $_POST["a_edit"]; // Get action code
	LoadFormValues(); // Get form values
} else {
	$hoturls->CurrentAction = "I"; // Default action is display
}

// Check if valid key
if ($hoturls->id->CurrentValue == "") Page_Terminate($hoturls->getReturnUrl()); // Invalid key, exit
switch ($hoturls->CurrentAction) {
	case "I": // Get a record to display
		if (!LoadRow()) { // Load Record based on key
			$_SESSION[EW_SESSION_MESSAGE] = "No records found"; // No record found
			Page_Terminate($hoturls->getReturnUrl()); // Return to caller
		}
		break;
	Case "U": // Update
		$hoturls->SendEmail = TRUE; // Send email on update success
		if (EditRow()) { // Update Record based on key
			$_SESSION[EW_SESSION_MESSAGE] = "Update successful"; // Update success
			Page_Terminate($hoturls->getReturnUrl()); // Return to caller
		} else {
			RestoreFormValues(); // Restore form values if update failed
		}
}

// Render the record
$hoturls->RowType = EW_ROWTYPE_EDIT; // Render as edit
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
<p><span class="phpmaker">Edit TABLE: hoturls<br><br><a href="<?php echo $hoturls->getReturnUrl() ?>">Go Back</a></span></p>
<?php
if (@$_SESSION[EW_SESSION_MESSAGE] <> "") {
?>
<p><span class="ewmsg"><?php echo $_SESSION[EW_SESSION_MESSAGE] ?></span></p>
<?php
	$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message
}
?>
<form name="fhoturlsedit" id="fhoturlsedit" action="hoturlsedit.php" method="post" onSubmit="return ew_ValidateForm(this);">
<p>
<input type="hidden" name="a_edit" id="a_edit" value="U">
<table class="ewTable">
	<tr class="ewTableRow">
		<td class="ewTableHeader">id</td>
		<td<?php echo $hoturls->id->CellAttributes() ?>><span id="cb_x_id">
<div<?php echo $hoturls->id->ViewAttributes() ?>><?php echo $hoturls->id->EditValue ?></div>
<input type="hidden" name="x_id" id="x_id" value="<?php echo ew_HtmlEncode($hoturls->id->CurrentValue) ?>">
</span></td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">url<span class='ewmsg'>&nbsp;*</span></td>
		<td<?php echo $hoturls->url->CellAttributes() ?>><span id="cb_x_url">
<input type="text" name="x_url" id="x_url"  size="30" maxlength="250" value="<?php echo $hoturls->url->EditValue ?>"<?php echo $hoturls->url->EditAttributes() ?>>
</span></td>
	</tr>
	<tr class="ewTableRow">
		<td class="ewTableHeader">projectname</td>
		<td<?php echo $hoturls->projectname->CellAttributes() ?>><span id="cb_x_projectname">
<input type="text" name="x_projectname" id="x_projectname"  size="30" maxlength="255" value="<?php echo $hoturls->projectname->EditValue ?>"<?php echo $hoturls->projectname->EditAttributes() ?>>
</span></td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">datetime</td>
		<td<?php echo $hoturls->datetime->CellAttributes() ?>><span id="cb_x_datetime">
<input type="text" name="x_datetime" id="x_datetime"  value="<?php echo $hoturls->datetime->EditValue ?>"<?php echo $hoturls->datetime->EditAttributes() ?>>
</span></td>
	</tr>
	<tr class="ewTableRow">
		<td class="ewTableHeader">categories</td>
		<td<?php echo $hoturls->categories->CellAttributes() ?>><span id="cb_x_categories">
<input type="text" name="x_categories" id="x_categories"  size="30" maxlength="100" value="<?php echo $hoturls->categories->EditValue ?>"<?php echo $hoturls->categories->EditAttributes() ?>>
</span></td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">title</td>
		<td<?php echo $hoturls->title->CellAttributes() ?>><span id="cb_x_title">
<input type="text" name="x_title" id="x_title"  size="30" maxlength="250" value="<?php echo $hoturls->title->EditValue ?>"<?php echo $hoturls->title->EditAttributes() ?>>
</span></td>
	</tr>
	<tr class="ewTableRow">
		<td class="ewTableHeader">content</td>
		<td<?php echo $hoturls->content->CellAttributes() ?>><span id="cb_x_content">
<textarea name="x_content" id="x_content" cols="35" rows="4"<?php echo $hoturls->content->EditAttributes() ?>><?php echo $hoturls->content->EditValue ?></textarea>
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
	global $objForm, $hoturls;
	$hoturls->id->setFormValue($objForm->GetValue("x_id"));
	$hoturls->url->setFormValue($objForm->GetValue("x_url"));
	$hoturls->projectname->setFormValue($objForm->GetValue("x_projectname"));
	$hoturls->datetime->setFormValue($objForm->GetValue("x_datetime"));
	$hoturls->datetime->CurrentValue = ew_UnFormatDateTime($hoturls->datetime->CurrentValue, 5);
	$hoturls->categories->setFormValue($objForm->GetValue("x_categories"));
	$hoturls->title->setFormValue($objForm->GetValue("x_title"));
	$hoturls->content->setFormValue($objForm->GetValue("x_content"));
}

// Restore form values
function RestoreFormValues() {
	global $hoturls;
	$hoturls->id->CurrentValue = $hoturls->id->FormValue;
	$hoturls->url->CurrentValue = $hoturls->url->FormValue;
	$hoturls->projectname->CurrentValue = $hoturls->projectname->FormValue;
	$hoturls->datetime->CurrentValue = $hoturls->datetime->FormValue;
	$hoturls->datetime->CurrentValue = ew_UnFormatDateTime($hoturls->datetime->CurrentValue, 5);
	$hoturls->categories->CurrentValue = $hoturls->categories->FormValue;
	$hoturls->title->CurrentValue = $hoturls->title->FormValue;
	$hoturls->content->CurrentValue = $hoturls->content->FormValue;
}
?>
<?php

// Load row based on key values
function LoadRow() {
	global $conn, $Security, $hoturls;
	$sFilter = $hoturls->SqlKeyFilter();
	if (!is_numeric($hoturls->id->CurrentValue)) {
		return FALSE; // Invalid key, exit
	}
	$sFilter = str_replace("@id@", ew_AdjustSql($hoturls->id->CurrentValue), $sFilter); // Replace key value

	// Call Row Selecting event
	$hoturls->Row_Selecting($sFilter);

	// Load sql based on filter
	$hoturls->CurrentFilter = $sFilter;
	$sSql = $hoturls->SQL();
	if ($rs = $conn->Execute($sSql)) {
		if ($rs->EOF) {
			$LoadRow = FALSE;
		} else {
			$LoadRow = TRUE;
			$rs->MoveFirst();
			LoadRowValues($rs); // Load row values

			// Call Row Selected event
			$hoturls->Row_Selected($rs);
		}
		$rs->Close();
	} else {
		$LoadRow = FALSE;
	}
	return $LoadRow;
}

// Load row values from recordset
function LoadRowValues(&$rs) {
	global $hoturls;
	$hoturls->id->setDbValue($rs->fields('id'));
	$hoturls->url->setDbValue($rs->fields('url'));
	$hoturls->projectname->setDbValue($rs->fields('projectname'));
	$hoturls->datetime->setDbValue($rs->fields('datetime'));
	$hoturls->categories->setDbValue($rs->fields('categories'));
	$hoturls->title->setDbValue($rs->fields('title'));
	$hoturls->content->setDbValue($rs->fields('content'));
}
?>
<?php

// Render row values based on field settings
function RenderRow() {
	global $conn, $Security, $hoturls;

	// Call Row Rendering event
	$hoturls->Row_Rendering();

	// Common render codes for all row types
	// id

	$hoturls->id->CellCssStyle = "";
	$hoturls->id->CellCssClass = "";

	// url
	$hoturls->url->CellCssStyle = "";
	$hoturls->url->CellCssClass = "";

	// projectname
	$hoturls->projectname->CellCssStyle = "";
	$hoturls->projectname->CellCssClass = "";

	// datetime
	$hoturls->datetime->CellCssStyle = "";
	$hoturls->datetime->CellCssClass = "";

	// categories
	$hoturls->categories->CellCssStyle = "";
	$hoturls->categories->CellCssClass = "";

	// title
	$hoturls->title->CellCssStyle = "";
	$hoturls->title->CellCssClass = "";

	// content
	$hoturls->content->CellCssStyle = "";
	$hoturls->content->CellCssClass = "";
	if ($hoturls->RowType == EW_ROWTYPE_VIEW) { // View row
	} elseif ($hoturls->RowType == EW_ROWTYPE_ADD) { // Add row
	} elseif ($hoturls->RowType == EW_ROWTYPE_EDIT) { // Edit row

		// id
		$hoturls->id->EditCustomAttributes = "";
		$hoturls->id->EditValue = $hoturls->id->CurrentValue;
		$hoturls->id->CssStyle = "";
		$hoturls->id->CssClass = "";
		$hoturls->id->ViewCustomAttributes = "";

		// url
		$hoturls->url->EditCustomAttributes = "";
		$hoturls->url->EditValue = ew_HtmlEncode($hoturls->url->CurrentValue);

		// projectname
		$hoturls->projectname->EditCustomAttributes = "";
		$hoturls->projectname->EditValue = ew_HtmlEncode($hoturls->projectname->CurrentValue);

		// datetime
		$hoturls->datetime->EditCustomAttributes = "";
		$hoturls->datetime->EditValue = ew_HtmlEncode(ew_FormatDateTime($hoturls->datetime->CurrentValue, 5));

		// categories
		$hoturls->categories->EditCustomAttributes = "";
		$hoturls->categories->EditValue = ew_HtmlEncode($hoturls->categories->CurrentValue);

		// title
		$hoturls->title->EditCustomAttributes = "";
		$hoturls->title->EditValue = ew_HtmlEncode($hoturls->title->CurrentValue);

		// content
		$hoturls->content->EditCustomAttributes = "";
		$hoturls->content->EditValue = ew_HtmlEncode($hoturls->content->CurrentValue);
	} elseif ($hoturls->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$hoturls->Row_Rendered();
}
?>
<?php

// Update record based on key values
function EditRow() {
	global $conn, $Security, $hoturls;
	$sFilter = $hoturls->SqlKeyFilter();
	if (!is_numeric($hoturls->id->CurrentValue)) {
		return FALSE;
	}
	$sFilter = str_replace("@id@", ew_AdjustSql($hoturls->id->CurrentValue), $sFilter); // Replace key value
	$hoturls->CurrentFilter = $sFilter;
	$sSql = $hoturls->SQL();
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

		$hoturls->url->SetDbValueDef($hoturls->url->CurrentValue, "");
		$rsnew['url'] =& $hoturls->url->DbValue;

		// Field projectname
		$hoturls->projectname->SetDbValueDef($hoturls->projectname->CurrentValue, NULL);
		$rsnew['projectname'] =& $hoturls->projectname->DbValue;

		// Field datetime
		$hoturls->datetime->SetDbValueDef(ew_UnFormatDateTime($hoturls->datetime->CurrentValue, 5), NULL);
		$rsnew['datetime'] =& $hoturls->datetime->DbValue;

		// Field categories
		$hoturls->categories->SetDbValueDef($hoturls->categories->CurrentValue, NULL);
		$rsnew['categories'] =& $hoturls->categories->DbValue;

		// Field title
		$hoturls->title->SetDbValueDef($hoturls->title->CurrentValue, NULL);
		$rsnew['title'] =& $hoturls->title->DbValue;

		// Field content
		$hoturls->content->SetDbValueDef($hoturls->content->CurrentValue, NULL);
		$rsnew['content'] =& $hoturls->content->DbValue;

		// Call Row Updating event
		$bUpdateRow = $hoturls->Row_Updating($rsold, $rsnew);
		if ($bUpdateRow) {
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$EditRow = $conn->Execute($hoturls->UpdateSQL($rsnew));
			$conn->raiseErrorFn = '';
		} else {
			if ($hoturls->CancelMessage <> "") {
				$_SESSION[EW_SESSION_MESSAGE] = $hoturls->CancelMessage;
				$hoturls->CancelMessage = "";
			} else {
				$_SESSION[EW_SESSION_MESSAGE] = "Update cancelled";
			}
			$EditRow = FALSE;
		}
	}

	// Call Row Updated event
	if ($EditRow) {
		$hoturls->Row_Updated($rsold, $rsnew);
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

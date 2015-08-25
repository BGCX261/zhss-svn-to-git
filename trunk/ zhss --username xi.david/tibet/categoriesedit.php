<?php
define("EW_PAGE_ID", "edit", TRUE); // Page ID
define("EW_TABLE_NAME", 'categories', TRUE);
?>
<?php 
session_start(); // Initialize session data
ob_start(); // Turn on output buffering
?>
<?php include "ewcfg50.php" ?>
<?php include "ewmysql50.php" ?>
<?php include "phpfn50.php" ?>
<?php include "categoriesinfo.php" ?>
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
$categories->Export = @$_GET["export"]; // Get export parameter
$sExport = $categories->Export; // Get export parameter, used in header
$sExportFile = $categories->TableVar; // Get export file, used in header
?>
<?php

// Load key from QueryString
if (@$_GET["id"] <> "") {
	$categories->id->setQueryStringValue($_GET["id"]);
}

// Create form object
$objForm = new cFormObj();
if (@$_POST["a_edit"] <> "") {
	$categories->CurrentAction = $_POST["a_edit"]; // Get action code
	LoadFormValues(); // Get form values
} else {
	$categories->CurrentAction = "I"; // Default action is display
}

// Check if valid key
if ($categories->id->CurrentValue == "") Page_Terminate($categories->getReturnUrl()); // Invalid key, exit
switch ($categories->CurrentAction) {
	case "I": // Get a record to display
		if (!LoadRow()) { // Load Record based on key
			$_SESSION[EW_SESSION_MESSAGE] = "No records found"; // No record found
			Page_Terminate($categories->getReturnUrl()); // Return to caller
		}
		break;
	Case "U": // Update
		$categories->SendEmail = TRUE; // Send email on update success
		if (EditRow()) { // Update Record based on key
			$_SESSION[EW_SESSION_MESSAGE] = "Update successful"; // Update success
			Page_Terminate($categories->getReturnUrl()); // Return to caller
		} else {
			RestoreFormValues(); // Restore form values if update failed
		}
}

// Render the record
$categories->RowType = EW_ROWTYPE_EDIT; // Render as edit
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
<p><span class="phpmaker">Edit TABLE: categories<br><br><a href="<?php echo $categories->getReturnUrl() ?>">Go Back</a></span></p>
<?php
if (@$_SESSION[EW_SESSION_MESSAGE] <> "") {
?>
<p><span class="ewmsg"><?php echo $_SESSION[EW_SESSION_MESSAGE] ?></span></p>
<?php
	$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message
}
?>
<form name="fcategoriesedit" id="fcategoriesedit" action="categoriesedit.php" method="post" onSubmit="return ew_ValidateForm(this);">
<p>
<input type="hidden" name="a_edit" id="a_edit" value="U">
<table class="ewTable">
	<tr class="ewTableRow">
		<td class="ewTableHeader">id</td>
		<td<?php echo $categories->id->CellAttributes() ?>><span id="cb_x_id">
<div<?php echo $categories->id->ViewAttributes() ?>><?php echo $categories->id->EditValue ?></div>
<input type="hidden" name="x_id" id="x_id" value="<?php echo ew_HtmlEncode($categories->id->CurrentValue) ?>">
</span></td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">name</td>
		<td<?php echo $categories->name->CellAttributes() ?>><span id="cb_x_name">
<input type="text" name="x_name" id="x_name"  size="30" maxlength="100" value="<?php echo $categories->name->EditValue ?>"<?php echo $categories->name->EditAttributes() ?>>
</span></td>
	</tr>
	<tr class="ewTableRow">
		<td class="ewTableHeader">keywords</td>
		<td<?php echo $categories->keywords->CellAttributes() ?>><span id="cb_x_keywords">
<textarea name="x_keywords" id="x_keywords" cols="35" rows="4"<?php echo $categories->keywords->EditAttributes() ?>><?php echo $categories->keywords->EditValue ?></textarea>
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
	global $objForm, $categories;
	$categories->id->setFormValue($objForm->GetValue("x_id"));
	$categories->name->setFormValue($objForm->GetValue("x_name"));
	$categories->keywords->setFormValue($objForm->GetValue("x_keywords"));
}

// Restore form values
function RestoreFormValues() {
	global $categories;
	$categories->id->CurrentValue = $categories->id->FormValue;
	$categories->name->CurrentValue = $categories->name->FormValue;
	$categories->keywords->CurrentValue = $categories->keywords->FormValue;
}
?>
<?php

// Load row based on key values
function LoadRow() {
	global $conn, $Security, $categories;
	$sFilter = $categories->SqlKeyFilter();
	if (!is_numeric($categories->id->CurrentValue)) {
		return FALSE; // Invalid key, exit
	}
	$sFilter = str_replace("@id@", ew_AdjustSql($categories->id->CurrentValue), $sFilter); // Replace key value

	// Call Row Selecting event
	$categories->Row_Selecting($sFilter);

	// Load sql based on filter
	$categories->CurrentFilter = $sFilter;
	$sSql = $categories->SQL();
	if ($rs = $conn->Execute($sSql)) {
		if ($rs->EOF) {
			$LoadRow = FALSE;
		} else {
			$LoadRow = TRUE;
			$rs->MoveFirst();
			LoadRowValues($rs); // Load row values

			// Call Row Selected event
			$categories->Row_Selected($rs);
		}
		$rs->Close();
	} else {
		$LoadRow = FALSE;
	}
	return $LoadRow;
}

// Load row values from recordset
function LoadRowValues(&$rs) {
	global $categories;
	$categories->id->setDbValue($rs->fields('id'));
	$categories->name->setDbValue($rs->fields('name'));
	$categories->keywords->setDbValue($rs->fields('keywords'));
}
?>
<?php

// Render row values based on field settings
function RenderRow() {
	global $conn, $Security, $categories;

	// Call Row Rendering event
	$categories->Row_Rendering();

	// Common render codes for all row types
	// id

	$categories->id->CellCssStyle = "";
	$categories->id->CellCssClass = "";

	// name
	$categories->name->CellCssStyle = "";
	$categories->name->CellCssClass = "";

	// keywords
	$categories->keywords->CellCssStyle = "";
	$categories->keywords->CellCssClass = "";
	if ($categories->RowType == EW_ROWTYPE_VIEW) { // View row
	} elseif ($categories->RowType == EW_ROWTYPE_ADD) { // Add row
	} elseif ($categories->RowType == EW_ROWTYPE_EDIT) { // Edit row

		// id
		$categories->id->EditCustomAttributes = "";
		$categories->id->EditValue = $categories->id->CurrentValue;
		$categories->id->CssStyle = "";
		$categories->id->CssClass = "";
		$categories->id->ViewCustomAttributes = "";

		// name
		$categories->name->EditCustomAttributes = "";
		$categories->name->EditValue = ew_HtmlEncode($categories->name->CurrentValue);

		// keywords
		$categories->keywords->EditCustomAttributes = "";
		$categories->keywords->EditValue = ew_HtmlEncode($categories->keywords->CurrentValue);
	} elseif ($categories->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$categories->Row_Rendered();
}
?>
<?php

// Update record based on key values
function EditRow() {
	global $conn, $Security, $categories;
	$sFilter = $categories->SqlKeyFilter();
	if (!is_numeric($categories->id->CurrentValue)) {
		return FALSE;
	}
	$sFilter = str_replace("@id@", ew_AdjustSql($categories->id->CurrentValue), $sFilter); // Replace key value
	$categories->CurrentFilter = $sFilter;
	$sSql = $categories->SQL();
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
		// Field name

		$categories->name->SetDbValueDef($categories->name->CurrentValue, NULL);
		$rsnew['name'] =& $categories->name->DbValue;

		// Field keywords
		$categories->keywords->SetDbValueDef($categories->keywords->CurrentValue, NULL);
		$rsnew['keywords'] =& $categories->keywords->DbValue;

		// Call Row Updating event
		$bUpdateRow = $categories->Row_Updating($rsold, $rsnew);
		if ($bUpdateRow) {
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$EditRow = $conn->Execute($categories->UpdateSQL($rsnew));
			$conn->raiseErrorFn = '';
		} else {
			if ($categories->CancelMessage <> "") {
				$_SESSION[EW_SESSION_MESSAGE] = $categories->CancelMessage;
				$categories->CancelMessage = "";
			} else {
				$_SESSION[EW_SESSION_MESSAGE] = "Update cancelled";
			}
			$EditRow = FALSE;
		}
	}

	// Call Row Updated event
	if ($EditRow) {
		$categories->Row_Updated($rsold, $rsnew);
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

<?php
define("EW_PAGE_ID", "add", TRUE); // Page ID
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

// Load key values from QueryString
$bCopy = TRUE;
if (@$_GET["id"] != "") {
  $urls->id->setQueryStringValue($_GET["id"]);
} else {
  $bCopy = FALSE;
}

// Create form object
$objForm = new cFormObj();

// Process form if post back
if (@$_POST["a_add"] <> "") {
  $urls->CurrentAction = $_POST["a_add"]; // Get form action
  LoadFormValues(); // Load form values
} else { // Not post back
  if ($bCopy) {
    $urls->CurrentAction = "C"; // Copy Record
  } else {
    $urls->CurrentAction = "I"; // Display Blank Record
    LoadDefaultValues(); // Load default values
  }
}

// Perform action based on action code
switch ($urls->CurrentAction) {
  case "I": // Blank record, no action required
		break;
  case "C": // Copy an existing record
   if (!LoadRow()) { // Load record based on key
      $_SESSION[EW_SESSION_MESSAGE] = "No records found"; // No record found
      Page_Terminate($urls->getReturnUrl()); // Clean up and return
    }
		break;
  case "A": // ' Add new record
		$urls->SendEmail = TRUE; // Send email on add success
    if (AddRow()) { // Add successful
      $_SESSION[EW_SESSION_MESSAGE] = "Add New Record Successful"; // Set up success message
      Page_Terminate($urls->KeyUrl($urls->getReturnUrl())); // Clean up and return
    } else {
      RestoreFormValues(); // Add failed, restore form values
    }
}

// Render row based on row type
$urls->RowType = EW_ROWTYPE_ADD;  // Render add type
RenderRow();
?>
<?php include "header.php" ?>
<script type="text/javascript">
<!--
var EW_PAGE_ID = "add"; // Page id

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
<p><span class="phpmaker">Add to TABLE: urls<br><br><a href="<?php echo $urls->getReturnUrl() ?>">Go Back</a></span></p>
<?php
if (@$_SESSION[EW_SESSION_MESSAGE] <> "") { // Mesasge in Session, display
?>
<p><span class="ewmsg"><?php echo $_SESSION[EW_SESSION_MESSAGE] ?></span></p>
<?php
  $_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
}
?>
<form name="furlsadd" id="furlsadd" action="urlsadd.php" method="post" onSubmit="return ew_ValidateForm(this);">
<p>
<input type="hidden" name="a_add" id="a_add" value="A">
<table class="ewTable">
  <tr class="ewTableRow">
    <td class="ewTableHeader">url</td>
    <td<?php echo $urls->url->CellAttributes() ?>><span id="cb_x_url">
<input type="text" name="x_url" id="x_url"  size="30" maxlength="255" value="<?php echo $urls->url->EditValue ?>"<?php echo $urls->url->EditAttributes() ?>>
</span></td>
  </tr>
  <tr class="ewTableAltRow">
    <td class="ewTableHeader">digest</td>
    <td<?php echo $urls->digest->CellAttributes() ?>><span id="cb_x_digest">
<input type="text" name="x_digest" id="x_digest"  size="30" maxlength="32" value="<?php echo $urls->digest->EditValue ?>"<?php echo $urls->digest->EditAttributes() ?>>
</span></td>
  </tr>
  <tr class="ewTableRow">
    <td class="ewTableHeader">type</td>
    <td<?php echo $urls->type->CellAttributes() ?>><span id="cb_x_type">
<input type="text" name="x_type" id="x_type"  size="30" value="<?php echo $urls->type->EditValue ?>"<?php echo $urls->type->EditAttributes() ?>>
</span></td>
  </tr>
</table>
<p>
<input type="submit" name="btnAction" id="btnAction" value="    Add    ">
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

// Load default values
function LoadDefaultValues() {
	global $urls;
}
?>
<?php

// Load form values
function LoadFormValues() {

	// Load from form
	global $objForm, $urls;
	$urls->url->setFormValue($objForm->GetValue("x_url"));
	$urls->digest->setFormValue($objForm->GetValue("x_digest"));
	$urls->type->setFormValue($objForm->GetValue("x_type"));
}

// Restore form values
function RestoreFormValues() {
	global $urls;
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

		// url
		$urls->url->EditCustomAttributes = "";
		$urls->url->EditValue = ew_HtmlEncode($urls->url->CurrentValue);

		// digest
		$urls->digest->EditCustomAttributes = "";
		$urls->digest->EditValue = ew_HtmlEncode($urls->digest->CurrentValue);

		// type
		$urls->type->EditCustomAttributes = "";
		$urls->type->EditValue = ew_HtmlEncode($urls->type->CurrentValue);
	} elseif ($urls->RowType == EW_ROWTYPE_EDIT) { // Edit row
	} elseif ($urls->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$urls->Row_Rendered();
}
?>
<?php

// Add record
function AddRow() {
	global $conn, $Security, $urls;

	// Check for duplicate key
	$bCheckKey = TRUE;
	$sFilter = $urls->SqlKeyFilter();
	if (trim(strval($urls->id->CurrentValue)) == "") {
		$bCheckKey = FALSE;
	} else {
		$sFilter = str_replace("@id@", ew_AdjustSql($urls->id->CurrentValue), $sFilter); // Replace key value
	}
	if (!is_numeric($urls->id->CurrentValue)) {
		$bCheckKey = FALSE;
	}
	if ($bCheckKey) {
		$rsChk = $urls->LoadRs($sFilter);
		if ($rsChk && !$rsChk->EOF) {
			$_SESSION[EW_SESSION_MESSAGE] = "Duplicate value for primary key";
			$rsChk->Close();
			return FALSE;
		}
	}
	$rsnew = array();

	// Field url
	$urls->url->SetDbValueDef($urls->url->CurrentValue, NULL);
	$rsnew['url'] =& $urls->url->DbValue;

	// Field digest
	$urls->digest->SetDbValueDef($urls->digest->CurrentValue, NULL);
	$rsnew['digest'] =& $urls->digest->DbValue;

	// Field type
	$urls->type->SetDbValueDef($urls->type->CurrentValue, NULL);
	$rsnew['type'] =& $urls->type->DbValue;

	// Call Row Inserting event
	$bInsertRow = $urls->Row_Inserting($rsnew);
	if ($bInsertRow) {
		$conn->raiseErrorFn = 'ew_ErrorFn';
		$AddRow = $conn->Execute($urls->InsertSQL($rsnew));
		$conn->raiseErrorFn = '';
	} else {
		if ($urls->CancelMessage <> "") {
			$_SESSION[EW_SESSION_MESSAGE] = $urls->CancelMessage;
			$urls->CancelMessage = "";
		} else {
			$_SESSION[EW_SESSION_MESSAGE] = "Insert cancelled";
		}
		$AddRow = FALSE;
	}
	if ($AddRow) {
		$urls->id->setDbValue($conn->Insert_ID());
		$rsnew['id'] =& $urls->id->DbValue;

		// Call Row Inserted event
		$urls->Row_Inserted($rsnew);
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

<?php
define("EW_PAGE_ID", "add", TRUE); // Page ID
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

// Load key values from QueryString
$bCopy = TRUE;
if (@$_GET["id"] != "") {
  $hoturls->id->setQueryStringValue($_GET["id"]);
} else {
  $bCopy = FALSE;
}

// Create form object
$objForm = new cFormObj();

// Process form if post back
if (@$_POST["a_add"] <> "") {
  $hoturls->CurrentAction = $_POST["a_add"]; // Get form action
  LoadFormValues(); // Load form values
} else { // Not post back
  if ($bCopy) {
    $hoturls->CurrentAction = "C"; // Copy Record
  } else {
    $hoturls->CurrentAction = "I"; // Display Blank Record
    LoadDefaultValues(); // Load default values
  }
}

// Perform action based on action code
switch ($hoturls->CurrentAction) {
  case "I": // Blank record, no action required
		break;
  case "C": // Copy an existing record
   if (!LoadRow()) { // Load record based on key
      $_SESSION[EW_SESSION_MESSAGE] = "No records found"; // No record found
      Page_Terminate($hoturls->getReturnUrl()); // Clean up and return
    }
		break;
  case "A": // ' Add new record
		$hoturls->SendEmail = TRUE; // Send email on add success
    if (AddRow()) { // Add successful
      $_SESSION[EW_SESSION_MESSAGE] = "Add New Record Successful"; // Set up success message
      Page_Terminate($hoturls->KeyUrl($hoturls->getReturnUrl())); // Clean up and return
    } else {
      RestoreFormValues(); // Add failed, restore form values
    }
}

// Render row based on row type
$hoturls->RowType = EW_ROWTYPE_ADD;  // Render add type
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
<p><span class="phpmaker">Add to TABLE: hoturls<br><br><a href="<?php echo $hoturls->getReturnUrl() ?>">Go Back</a></span></p>
<?php
if (@$_SESSION[EW_SESSION_MESSAGE] <> "") { // Mesasge in Session, display
?>
<p><span class="ewmsg"><?php echo $_SESSION[EW_SESSION_MESSAGE] ?></span></p>
<?php
  $_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
}
?>
<form name="fhoturlsadd" id="fhoturlsadd" action="hoturlsadd.php" method="post" onSubmit="return ew_ValidateForm(this);">
<p>
<input type="hidden" name="a_add" id="a_add" value="A">
<table class="ewTable">
  <tr class="ewTableRow">
    <td class="ewTableHeader">url<span class='ewmsg'>&nbsp;*</span></td>
    <td<?php echo $hoturls->url->CellAttributes() ?>><span id="cb_x_url">
<input type="text" name="x_url" id="x_url"  size="30" maxlength="250" value="<?php echo $hoturls->url->EditValue ?>"<?php echo $hoturls->url->EditAttributes() ?>>
</span></td>
  </tr>
  <tr class="ewTableAltRow">
    <td class="ewTableHeader">projectname</td>
    <td<?php echo $hoturls->projectname->CellAttributes() ?>><span id="cb_x_projectname">
<input type="text" name="x_projectname" id="x_projectname"  size="30" maxlength="255" value="<?php echo $hoturls->projectname->EditValue ?>"<?php echo $hoturls->projectname->EditAttributes() ?>>
</span></td>
  </tr>
  <tr class="ewTableRow">
    <td class="ewTableHeader">datetime</td>
    <td<?php echo $hoturls->datetime->CellAttributes() ?>><span id="cb_x_datetime">
<input type="text" name="x_datetime" id="x_datetime"  value="<?php echo $hoturls->datetime->EditValue ?>"<?php echo $hoturls->datetime->EditAttributes() ?>>
</span></td>
  </tr>
  <tr class="ewTableAltRow">
    <td class="ewTableHeader">categories</td>
    <td<?php echo $hoturls->categories->CellAttributes() ?>><span id="cb_x_categories">
<input type="text" name="x_categories" id="x_categories"  size="30" maxlength="100" value="<?php echo $hoturls->categories->EditValue ?>"<?php echo $hoturls->categories->EditAttributes() ?>>
</span></td>
  </tr>
  <tr class="ewTableRow">
    <td class="ewTableHeader">title</td>
    <td<?php echo $hoturls->title->CellAttributes() ?>><span id="cb_x_title">
<input type="text" name="x_title" id="x_title"  size="30" maxlength="250" value="<?php echo $hoturls->title->EditValue ?>"<?php echo $hoturls->title->EditAttributes() ?>>
</span></td>
  </tr>
  <tr class="ewTableAltRow">
    <td class="ewTableHeader">content</td>
    <td<?php echo $hoturls->content->CellAttributes() ?>><span id="cb_x_content">
<textarea name="x_content" id="x_content" cols="35" rows="4"<?php echo $hoturls->content->EditAttributes() ?>><?php echo $hoturls->content->EditValue ?></textarea>
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
	global $hoturls;
}
?>
<?php

// Load form values
function LoadFormValues() {

	// Load from form
	global $objForm, $hoturls;
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
	} elseif ($hoturls->RowType == EW_ROWTYPE_EDIT) { // Edit row
	} elseif ($hoturls->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$hoturls->Row_Rendered();
}
?>
<?php

// Add record
function AddRow() {
	global $conn, $Security, $hoturls;

	// Check for duplicate key
	$bCheckKey = TRUE;
	$sFilter = $hoturls->SqlKeyFilter();
	if (trim(strval($hoturls->id->CurrentValue)) == "") {
		$bCheckKey = FALSE;
	} else {
		$sFilter = str_replace("@id@", ew_AdjustSql($hoturls->id->CurrentValue), $sFilter); // Replace key value
	}
	if (!is_numeric($hoturls->id->CurrentValue)) {
		$bCheckKey = FALSE;
	}
	if ($bCheckKey) {
		$rsChk = $hoturls->LoadRs($sFilter);
		if ($rsChk && !$rsChk->EOF) {
			$_SESSION[EW_SESSION_MESSAGE] = "Duplicate value for primary key";
			$rsChk->Close();
			return FALSE;
		}
	}
	$rsnew = array();

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

	// Call Row Inserting event
	$bInsertRow = $hoturls->Row_Inserting($rsnew);
	if ($bInsertRow) {
		$conn->raiseErrorFn = 'ew_ErrorFn';
		$AddRow = $conn->Execute($hoturls->InsertSQL($rsnew));
		$conn->raiseErrorFn = '';
	} else {
		if ($hoturls->CancelMessage <> "") {
			$_SESSION[EW_SESSION_MESSAGE] = $hoturls->CancelMessage;
			$hoturls->CancelMessage = "";
		} else {
			$_SESSION[EW_SESSION_MESSAGE] = "Insert cancelled";
		}
		$AddRow = FALSE;
	}
	if ($AddRow) {
		$hoturls->id->setDbValue($conn->Insert_ID());
		$rsnew['id'] =& $hoturls->id->DbValue;

		// Call Row Inserted event
		$hoturls->Row_Inserted($rsnew);
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

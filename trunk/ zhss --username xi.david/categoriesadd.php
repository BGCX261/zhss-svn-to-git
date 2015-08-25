<?php
define("EW_PAGE_ID", "add", TRUE); // Page ID
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
<?php include "userinfo.php" ?>
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
if (!$Security->IsLoggedIn()) $Security->AutoLogin();
if (!$Security->IsLoggedIn()) {
	$Security->SaveLastUrl();
	Page_Terminate("login.php");
}
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

// Load key values from QueryString
$bCopy = TRUE;
if (@$_GET["id"] != "") {
  $categories->id->setQueryStringValue($_GET["id"]);
} else {
  $bCopy = FALSE;
}

// Create form object
$objForm = new cFormObj();

// Process form if post back
if (@$_POST["a_add"] <> "") {
  $categories->CurrentAction = $_POST["a_add"]; // Get form action
  LoadFormValues(); // Load form values
} else { // Not post back
  if ($bCopy) {
    $categories->CurrentAction = "C"; // Copy Record
  } else {
    $categories->CurrentAction = "I"; // Display Blank Record
    LoadDefaultValues(); // Load default values
  }
}

// Perform action based on action code
switch ($categories->CurrentAction) {
  case "I": // Blank record, no action required
		break;
  case "C": // Copy an existing record
   if (!LoadRow()) { // Load record based on key
      $_SESSION[EW_SESSION_MESSAGE] = "No records found"; // No record found
      Page_Terminate($categories->getReturnUrl()); // Clean up and return
    }
		break;
  case "A": // ' Add new record
		$categories->SendEmail = TRUE; // Send email on add success
    if (AddRow()) { // Add successful
      $_SESSION[EW_SESSION_MESSAGE] = "Add New Record Successful"; // Set up success message
      Page_Terminate($categories->KeyUrl($categories->getReturnUrl())); // Clean up and return
    } else {
      RestoreFormValues(); // Add failed, restore form values
    }
}

// Render row based on row type
$categories->RowType = EW_ROWTYPE_ADD;  // Render add type
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
<p><a href="<?php echo $categories->getReturnUrl() ?>">返回</a></span></p>
<?php
if (@$_SESSION[EW_SESSION_MESSAGE] <> "") { // Mesasge in Session, display
?>
<p><span class="ewmsg"><?php echo $_SESSION[EW_SESSION_MESSAGE] ?></span></p>
<?php
  $_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
}
?>
<form name="fcategoriesadd" id="fcategoriesadd" action="categoriesadd.php" method="post" onSubmit="return ew_ValidateForm(this);">
<p>
<input type="hidden" name="a_add" id="a_add" value="A">
<table class="ewTable">
  <tr class="ewTableRow">
    <td class="ewTableHeader">分类(类别名称用于对关键词进行管理)</td>
    <td<?php echo $categories->name->CellAttributes() ?>><span id="cb_x_name">
<input type="text" name="x_name" id="x_name" title="" size="30" maxlength="100" value="<?php echo $categories->name->EditValue ?>"<?php echo $categories->name->EditAttributes() ?>>
</span></td>
  </tr>
  <tr class="ewTableAltRow">
    <td class="ewTableHeader">关键词(关键词以,分割，如：法轮功,共产党,和谐世界)</td>
    <td<?php echo $categories->keywords->CellAttributes() ?>><span id="cb_x_keywords">
<textarea name="x_keywords" id="x_keywords" cols="35" rows="4"<?php echo $categories->keywords->EditAttributes() ?>><?php echo $categories->keywords->EditValue ?></textarea>
</span></td>
  </tr>
</table>
<p>
<input type="submit" name="btnAction" id="btnAction" value="添加分类">
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
	global $categories;
}
?>
<?php

// Load form values
function LoadFormValues() {

	// Load from form
	global $objForm, $categories;
	$categories->name->setFormValue($objForm->GetValue("x_name"));
	$categories->keywords->setFormValue($objForm->GetValue("x_keywords"));
}

// Restore form values
function RestoreFormValues() {
	global $categories;
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
	// name

	$categories->name->CellCssStyle = "";
	$categories->name->CellCssClass = "";

	// keywords
	$categories->keywords->CellCssStyle = "";
	$categories->keywords->CellCssClass = "";
	if ($categories->RowType == EW_ROWTYPE_VIEW) { // View row
	} elseif ($categories->RowType == EW_ROWTYPE_ADD) { // Add row

		// name
		$categories->name->EditCustomAttributes = "";
		$categories->name->EditValue = ew_HtmlEncode($categories->name->CurrentValue);

		// keywords
		$categories->keywords->EditCustomAttributes = "";
		$categories->keywords->EditValue = $categories->keywords->CurrentValue;
	} elseif ($categories->RowType == EW_ROWTYPE_EDIT) { // Edit row
	} elseif ($categories->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$categories->Row_Rendered();
}
?>
<?php

// Add record
function AddRow() {
	global $conn, $Security, $categories;

	// Check for duplicate key
	$bCheckKey = TRUE;
	$sFilter = $categories->SqlKeyFilter();
	if (trim(strval($categories->id->CurrentValue)) == "") {
		$bCheckKey = FALSE;
	} else {
		$sFilter = str_replace("@id@", ew_AdjustSql($categories->id->CurrentValue), $sFilter); // Replace key value
	}
	if (!is_numeric($categories->id->CurrentValue)) {
		$bCheckKey = FALSE;
	}
	if ($bCheckKey) {
		$rsChk = $categories->LoadRs($sFilter);
		if ($rsChk && !$rsChk->EOF) {
			$_SESSION[EW_SESSION_MESSAGE] = "Duplicate value for primary key";
			$rsChk->Close();
			return FALSE;
		}
	}
	$rsnew = array();

	// Field name
	$categories->name->SetDbValueDef($categories->name->CurrentValue, NULL);
	$rsnew['name'] =& $categories->name->DbValue;

	// Field keywords
	$categories->keywords->SetDbValueDef($categories->keywords->CurrentValue, NULL);
	$rsnew['keywords'] =& $categories->keywords->DbValue;

	// Call Row Inserting event
	$bInsertRow = $categories->Row_Inserting($rsnew);
	if ($bInsertRow) {
		$conn->raiseErrorFn = 'ew_ErrorFn';
		$AddRow = $conn->Execute($categories->InsertSQL($rsnew));
		$conn->raiseErrorFn = '';
	} else {
		if ($categories->CancelMessage <> "") {
			$_SESSION[EW_SESSION_MESSAGE] = $categories->CancelMessage;
			$categories->CancelMessage = "";
		} else {
			$_SESSION[EW_SESSION_MESSAGE] = "Insert cancelled";
		}
		$AddRow = FALSE;
	}
	if ($AddRow) {
		$categories->id->setDbValue($conn->Insert_ID());
		$rsnew['id'] =& $categories->id->DbValue;

		// Call Row Inserted event
		$categories->Row_Inserted($rsnew);
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

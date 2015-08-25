<?php
define("EW_PAGE_ID", "list", TRUE); // Page ID
define("EW_TABLE_NAME", 'archives', TRUE);
?>
<?php
$view = @$_GET['view'];
if( $view == '' ) {
	$view = @$_COOKIE['view'];
}
if( $view == '' ) {
	$view = 'normal';
}
setcookie('view',$view);
session_start(); // Initialize session data
ob_start(); // Turn on output buffering
?>
<?php include "ewcfg50.php" ?>
<?php include "ewmysql50.php" ?>
<?php include "phpfn50.php" ?>
<?php include "archivesinfo.php" ?>
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
//echo $Security->getCurrentUserLevel();exit;
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
$action = @$_POST['action'];
if( $action != '' ) {
	$ids = @$_POST['ids'];
	if( $action == 'delete' ) {
		$idstring = implode('","',$ids);
		$sql = 'DELETE FROM `archives` WHERE `id` IN ("' . $idstring . '")';
		$conn->Execute($sql);
	}
	Page_Terminate("archiveslist.php");
}
// Page load event, used in current page
Page_Load();
?>
<?php
$archives->Export = @$_GET["export"]; // Get export parameter
$sExport = $archives->Export; // Get export parameter, used in header
$sExportFile = $archives->TableVar; // Get export file, used in header
?>
<?php

// Paging variables
$nStartRec = 0; // Start record index
$nStopRec = 0; // Stop record index
$nTotalRecs = 0; // Total number of records
$nDisplayRecs = 20;
$nRecRange = 10;
$nRecCount = 0; // Record count

// Search filters
$sSrchAdvanced = ""; // Advanced search filter
$sSrchBasic = ""; // Basic search filter
$sSrchWhere = ""; // Search where clause
$sFilter = "";

// Master/Detail
$sDbMasterFilter = ""; // Master filter
$sDbDetailFilter = ""; // Detail filter
$sSqlMaster = ""; // Sql for master record

// Handle reset command
ResetCmd();

// Get basic search criteria
$sSrchBasic = BasicSearchWhere();

// Build search criteria
if ($sSrchAdvanced <> "") {
	if ($sSrchWhere <> "") $sSrchWhere .= " AND ";
	$sSrchWhere .= "(" . $sSrchAdvanced . ")";
}
if ($sSrchBasic <> "") {
	if ($sSrchWhere <> "") $sSrchWhere .= " AND ";
	$sSrchWhere .= "(" . $sSrchBasic . ")";
}

// Save search criteria
if ($sSrchWhere <> "") {
	if ($sSrchBasic == "") ResetBasicSearchParms();
	$archives->setSearchWhere($sSrchWhere); // Save to Session
	$nStartRec = 1; // Reset start record counter
	$archives->setStartRecordNumber($nStartRec);
} else {
	RestoreSearchParms();
}

// Build filter
$sFilter = "";
if ($sDbDetailFilter <> "") {
	if ($sFilter <> "") $sFilter .= " AND ";
	$sFilter .= "(" . $sDbDetailFilter . ")";
}
if ($sSrchWhere <> "") {
	if ($sFilter <> "") $sFilter .= " AND ";
	$sFilter .= "(" . $sSrchWhere . ")";
}

// Set up filter in Session
$archives->setSessionWhere($sFilter);
$archives->CurrentFilter = "";

// Set Up Sorting Order
SetUpSortOrder();

// Set Return Url
$archives->setReturnUrl("archiveslist.php");
?>
<?php include "header.php" ?>
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<!-- left column (begin) -->
		<td valign="top" class="ewMenuColumn">
<?php if ($archives->Export == "") { ?>
<script type="text/javascript">
<!--
var EW_PAGE_ID = "list"; // Page id
var firstrowoffset = 1; // First data row start at
var lastrowoffset = 0; // Last data row end at
var EW_LIST_TABLE_NAME = 'ewlistmain'; // Table name for list page
var rowclass = 'ewTableRow'; // Row class
var rowaltclass = 'ewTableAltRow'; // Row alternate class
var rowmoverclass = 'ewTableHighlightRow'; // Row mouse over class
var rowselectedclass = 'ewTableSelectRow'; // Row selected class
var roweditclass = 'ewTableEditRow'; // Row edit class

//-->
</script>
<?php } ?>
<?php

// Load recordset
$bExportAll = (defined("EW_EXPORT_ALL") && $archives->Export <> "");
$bSelectLimit = ($archives->Export == "" && $archives->SelectLimit);
if (!$bSelectLimit) $rs = LoadRecordset();
$nTotalRecs = ($bSelectLimit) ? $archives->SelectRecordCount() : $rs->RecordCount();
$nStartRec = 1;
if ($nDisplayRecs <= 0) $nDisplayRecs = $nTotalRecs; // Display all records
if (!$bExportAll) SetUpStartRec(); // Set up start record position
if ($bSelectLimit) $rs = LoadRecordset($nStartRec-1, $nDisplayRecs);
?>

<?php if ($archives->Export == "") { ?>
<?php if ($Security->IsLoggedIn()) { ?>
<form name="farchiveslistsrch" id="farchiveslistsrch" action="archiveslist.php" >
<div class="menuhead">实时检索</div>
<table class="ewBasicSearch">
	<tr>
		<td width="60">
			<label for="<?php echo EW_TABLE_BASIC_SEARCH ?>">关键词:</label>
		</td>
		<td>
			<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" size="10" class="searchInput" value="<?php echo $archives->getBasicSearchKeyword() ?>"></p>
		</td>
	</tr>
	<tr>
		<td>
			<label for="<?php echo EW_TABLE_BASIC_SEARCH_START_TIME ?>">起始时间:</label>
		</td>
		<td>
			<input type="text" value="<?php echo $archives->getBasicSearchStartTime() ?>" class="dateinput searchInput" onkeypress="return false"size="10" id="<?php echo EW_TABLE_BASIC_SEARCH_START_TIME ?>" name="<?php echo EW_TABLE_BASIC_SEARCH_START_TIME ?>"/></p>
		</td>
	</tr>
	<tr>
		<td>
			<label for="<?php echo EW_TABLE_BASIC_SEARCH_END_TIME ?>">结束时间:</label>
		</td>
		<td>
			<input type="text" value="<?php echo $archives->getBasicSearchEndTime() ?>" class="dateinput searchInput"onkeypress="return false" size="10" id="<?php echo EW_TABLE_BASIC_SEARCH_END_TIME ?>" name="<?php echo EW_TABLE_BASIC_SEARCH_END_TIME ?>"/></p>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<label for="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="" <?php if ($user->getBasicSearchType() == "") { ?>checked="checked"<?php } ?>>匹配以上<b>单个</b>关键词</label>
			<label for="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>AND"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>AND" value="AND" <?php if ($user->getBasicSearchType() == "AND") { ?>checked="checked"<?php } ?>>包含以上<b>全部</b>的关键词</label>
			<label for="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>OR"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>OR" value="OR" <?php if ($user->getBasicSearchType() == "OR") { ?>checked="checked"<?php } ?>>包含以上<b>任意一个</b>关键词</label>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="Submit" value="搜索 (*)">&nbsp;
			<a href="archiveslist.php?cmd=reset">显示全部</a>
<?php
	if( isset($view) && $view == 'classic' ) {
		echo '<input type="hidden" name="view" value="classic" />';
	}
	else {
		echo '<input type="hidden" name="view" value="normal" />';
	}
?>
		</td>
	</tr>
</table>
<script type="text/javascript">
Calendar.setup({
	inputField     :    "<?php echo EW_TABLE_BASIC_SEARCH_START_TIME ?>",
	ifFormat       :    "%Y-%m-%d %H:%M ", 
	showsTime      :    true,
	onUpdate       :    null
});
Calendar.setup({
	inputField     :    "<?php echo EW_TABLE_BASIC_SEARCH_END_TIME ?>",
	ifFormat       :    "%Y-%m-%d %H:%M ",
	showsTime      :    true,
	onUpdate       :    null
});
</script>
</form>
<?php
$csql = 'SELECT * FROM `archives` WHERE `projectname`<>"" GROUP BY `projectname`';
$crs = $conn->Execute($csql);
if( $crs->_numOfRows > 0 ) {
?>
<br/>
<div class="menuhead">网站列表</div>
<table class="ewBasicSearch">
	<tr>
		<td>
			<ul class="t_t">
<?php
while( !$crs->EOF && $crs->fields('projectname') != '' ) {
	echo '<li>';
	echo '<a href="archiveslist.php?projectname=' . rawurlencode($crs->fields('projectname')) . '">' . $crs->fields('projectname') . '</a>';
	echo '</li>';
	$crs->MoveNext();
}
?>
			</ul>
		</td>
	</tr>
</table>
<?php
}
?>
<?php } ?>
<?php } ?>
		</td>
		<!-- left column (end) -->
		<!-- right column (begin) -->
		<td valign="top" class="ewContentColumn">
<?php
	$getArray = array();
	foreach( $_GET as $key => $value ) {
		if( $key != 'view' ) {
			array_push($getArray,$key . '=' . rawurlencode($value));
		}
	}
	$get = implode('&',$getArray);
	$url = 'archiveslist.php';
	if( $get != '' ) {
		$classicUrl = $url . '?view=classic&' . $get;
		$normalUrl = $url . '?view=normal&' . $get;
	}
	else {
		$classicUrl = $url . '?view=classic';
		$normalUrl = $url . '?view=normal';
	}
?>
<p class="ptitle"><b>中和集成监控系统</b><span><a href="<?php echo $classicUrl ?>">传统视图</a><a href="<?php echo $normalUrl ?>">默认视图</a></span></p>
<?php
if (@$_SESSION[EW_SESSION_MESSAGE] <> "") {
?>
<p><span class="ewmsg"><?php echo $_SESSION[EW_SESSION_MESSAGE] ?></span></p>
<?php
	$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message
}
?>
<?php  if ($nTotalRecs > 0) { ?>
<form method="post" name="farchiveslist" id="farchiveslist" action="archiveslist.php">
<script type="text/javascript">
	function deleteSelected() {
		if( confirm('确实要删除选中项目吗？') ) {
			document.getElementById('action').value = 'delete';
			document.getElementById('farchiveslist').submit();
		}
		document.getElementById('action').value = '';
		return false;
	}
	function archiveSelected() {
		if( confirm('确实要归档选中项目吗？') ) {
			document.getElementById('action').value = 'archive';
			document.getElementById('farchiveslist').submit();
		}
		document.getElementById('action').value = '';
		return false;
	}
	function checkAll(){
		var cbx = document.forms["farchiveslist"].elements["ids[]"];
		var chk = document.forms["farchiveslist"].elements["chkAll"];
		for(var i = 0;i < cbx.length;i ++){
				cbx[i].checked = chk.checked;
		}
	}	
</script>
	<?php
	if( isset($view) && $view == 'classic' ) {
		$keywords = trim($archives->getBasicSearchKeyword());
		if( $archives->getBasicSearchType() == 'OR' || $archives->getBasicSearchType() == 'AND' ) {
			$keywords = explode(' ',$keywords);
		}
	?>
		<table id="ewlistmain" class="ewTable">
		<tr>
<td><input type="hidden" name="action" id="action" value=""/></td>
</tr>	
		<?php
		if (defined("EW_EXPORT_ALL") && $archives->Export <> "") {
			$nStopRec = $nTotalRecs;
		} else {
			$nStopRec = $nStartRec + $nDisplayRecs - 1; // Set the last record to display
		}
		$nRecCount = $nStartRec - 1;
		if (!$rs->EOF) {
			$rs->MoveFirst();
			if (!$archives->SelectLimit) $rs->Move($nStartRec - 1); // Move to first record directly
		}
		
		$RowCnt = 0;
		while (!$rs->EOF && $nRecCount < $nStopRec) {
			$nRecCount++;
			if (intval($nRecCount) >= intval($nStartRec)) {
				$RowCnt++;
			LoadRowValues($rs); // Load row values
			$archives->RowType = EW_ROWTYPE_VIEW; // Render view
			RenderRow();
			$url_info = parse_url($archives->url->ViewValue);
			
		?>
		
			<!-- Table body -->
			<tr>
				<td>
					<div><input type="checkbox" name="ids[]" value="<?php echo $archives->id->ViewValue ?>"/><a href="<?php echo $archives->url->ViewValue ?>" target="_blank"><?php echo $archives->title->ViewValue ?></a><?php echo countword( $archives->content->ViewValue,$keywords) ?></div>
				</td>
			</tr>
			<tr>
				<td>
					<div style="color:#000">  <?php echo highlight(substr($archives->content->ViewValue,0,800),$keywords) ?>   </div>
				</td>
			</tr>
			<tr>
				<td>
					<span style="color:green"><?php echo (isset($url_info['host'])?$url_info['host']:'') . (isset($url_info['path'])?$url_info['path']:'') . '&nbsp;&nbsp;' . $archives->datetime->ViewValue ?></span>
					 - <a href="<?php echo $viewpath . '/' . (isset($url_info['host'])?$url_info['host']:'') . '/' . md5(strtolower($archives->url->ViewValue)) ?>.html" target="_blank">快照</a>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<br>
				</td>
			</tr>
		<?php
			}
			$rs->MoveNext();
		}
		?>
		</table>
	<?php
	}
	else {
	?>
	<?php
		$keywords = trim($archives->getBasicSearchKeyword());
		if( $archives->getBasicSearchType() == 'OR' || $archives->getBasicSearchType() == 'AND' ) {
			$keywords = explode(' ',$keywords);
		}
	?>	
		<table id="ewlistmain" class="ewTable">
			<?php
			$OptionCnt = 0;
			if ($Security->IsLoggedIn()) {
				$OptionCnt++; // view
			}
			if ($Security->IsLoggedIn()) {
				$OptionCnt++; // edit
			}
			if ($Security->IsLoggedIn()) {
				$OptionCnt++; // copy
			}
			if ($Security->IsLoggedIn()) {
				$OptionCnt++; // delete
			}
			?>
			<!-- Table header -->
			<tr class="ewTableHeader">
<?php if ($Security->CanDelete()||$Security->CanArchive()) { ?>
<td width="20"><input type="hidden" name="action" id="action" value=""/></td>
<?php } ?>
				<td valign="top">
		<?php if ($archives->Export <> "") { ?>
		id
		<?php } else { ?>
			编号
		<?php } ?>
				</td>
				<td valign="top">
		<?php if ($archives->Export <> "") { ?>
		来源
		<?php } else { ?>
			站点名称&nbsp;(*)
		<?php } ?>
				</td>
				<td valign="top">
		<?php if ($archives->Export <> "") { ?>
		时间
		<?php } else { ?>
			时间
		<?php } ?>
				</td>
				<td valign="top">
		<?php if ($archives->Export <> "" ) { ?>
		标题
		<?php } else { ?>
			网页标题&nbsp;(*)
		<?php } ?>
				</td>
		<?php if ($archives->Export == "") { ?>
		<?php
			if ($Security->CanView()) {
		?>
		<td nowrap>&nbsp;</td>
		<?php } ?>
		<?php if ($Security->CanEdit()) { ?>

		<?php } ?>
		<?php } ?>
			</tr>
		<?php
		if (defined("EW_EXPORT_ALL") && $archives->Export <> "") {
			$nStopRec = $nTotalRecs;
		} else {
			$nStopRec = $nStartRec + $nDisplayRecs - 1; // Set the last record to display
		}
		$nRecCount = $nStartRec - 1;
		if (!$rs->EOF) {
			$rs->MoveFirst();
			if (!$archives->SelectLimit) $rs->Move($nStartRec - 1); // Move to first record directly
		}
		$RowCnt = 0;
		if( $archives->getBasicSearchKeyword() == '' ) {
			$keyaction = '';
		}
		else {
			$keyaction = EW_TABLE_BASIC_SEARCH . '=' . rawurlencode($archives->getBasicSearchKeyword()) . '&' . EW_TABLE_BASIC_SEARCH_TYPE . '=' . $archives->getBasicSearchType();
		}
		while (!$rs->EOF && $nRecCount < $nStopRec) {
			$nRecCount++;
			if (intval($nRecCount) >= intval($nStartRec)) {
				$RowCnt++;

			// Init row class and style
			$archives->CssClass = "ewTableRow";
			$archives->CssStyle = "";

			// Init row event
			$archives->RowClientEvents = "onmouseover='ew_MouseOver(this);' onmouseout='ew_MouseOut(this);' onclick='ew_Click(this);'";

			// Display alternate color for rows
			if ($RowCnt % 2 == 0) {
				$archives->CssClass = "ewTableAltRow";
			}
			LoadRowValues($rs); // Load row values
			$archives->RowType = EW_ROWTYPE_VIEW; // Render view
			RenderRow();
		?>
			<!-- Table body -->
			<tr<?php echo $archives->DisplayAttributes() ?>>
<?php if ($Security->CanDelete()||$Security->CanArchive()) { ?>
<td nowrap><input type="checkbox" name="ids[]" value="<?php echo $archives->id->ViewValue ?>"/></td>
<?php } ?>
				<!-- id -->
				<td<?php echo $archives->id->CellAttributes() ?>>
					<div<?php echo $archives->id->ViewAttributes() ?>><?php echo $archives->id->ViewValue ?></div>
				</td>
				<!-- url -->
				<td<?php echo $archives->projectname->CellAttributes() ?>>
					<div<?php echo $archives->projectname->ViewAttributes() ?> class="url"><a href="<?php echo $archives->url->ViewValue ?>" target="_blank"><?php echo $archives->projectname->ViewValue ?></a></div>
				</td>
				<!-- datetime -->
				<td<?php echo $archives->datetime->CellAttributes() ?>>
					<div<?php echo $archives->datetime->ViewAttributes() ?>><?php echo $archives->datetime->ViewValue ?></div>
				</td>
				<!-- title -->
				<td<?php echo $archives->title->CellAttributes() ?>>
					<div<?php echo $archives->title->ViewAttributes() ?>><?php echo $archives->title->ViewValue ?><?php echo countword( $archives->content->ViewValue,$keywords) ?></div>
				</td>
				<?php if ($Security->CanView()) { ?>
				<td nowrap><span class="phpmaker">
				<a href="<?php echo $archives->ViewUrl($keyaction) ?>">快照</a>
				</span></td>
				<?php } ?>
				<?php if ($Security->CanEdit()) { ?>

				<?php } ?>
			</tr>
		<?php
			}
			$rs->MoveNext();
		}
		?>
		</table>
	<?php
	}
	?>
<?php if ($archives->Export == "") { ?>
<table>
	<tr><td><span class="phpmaker">
<?php
	echo '<input type="checkbox" onclick="checkAll()" name="chkAll" />全选';
?>	
<?php if ($Security->CanDelete()) { ?>
<?php
	echo '<input type="button" value="删除选中" onclick="deleteSelected()" />';
?>		
<?php } ?>
	</span></td></tr>
</table>
<?php } ?>
</form>
<?php
}
?>
<?php

// Close recordset and connection
if ($rs) $rs->Close();
?>
<?php if ($archives->Export == "") { ?>
<form action="archiveslist.php" name="ewpagerform" id="ewpagerform">
<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td nowrap>
<?php if (!isset($Pager)) $Pager = new cPrevNextPager($nStartRec, $nDisplayRecs, $nTotalRecs) ?>
<?php if ($Pager->RecordCount > 0) { ?>
	<table border="0" cellspacing="0" cellpadding="0"><tr><td><span class="phpmaker">第</span></td>
<!--first page button-->
	<?php if ($Pager->FirstButton->Enabled) { ?>
	<td><a href="archiveslist.php?start=<?php echo $Pager->FirstButton->Start ?>"><img src="images/first.gif" alt="First" width="16" height="16" border="0"></a></td>
	<?php } else { ?>
	<td><img src="images/firstdisab.gif" alt="First" width="16" height="16" border="0"></td>
	<?php } ?>
<!--previous page button-->
	<?php if ($Pager->PrevButton->Enabled) { ?>
	<td><a href="archiveslist.php?start=<?php echo $Pager->PrevButton->Start ?>"><img src="images/prev.gif" alt="Previous" width="16" height="16" border="0"></a></td>
	<?php } else { ?>
	<td><img src="images/prevdisab.gif" alt="Previous" width="16" height="16" border="0"></td>
	<?php } ?>
<!--current page number-->
	<td><input type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" id="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $Pager->CurrentPage ?>" size="4"></td>
<!--next page button-->
	<?php if ($Pager->NextButton->Enabled) { ?>
	<td><a href="archiveslist.php?start=<?php echo $Pager->NextButton->Start ?>"><img src="images/next.gif" alt="Next" width="16" height="16" border="0"></a></td>	
	<?php } else { ?>
	<td><img src="images/nextdisab.gif" alt="Next" width="16" height="16" border="0"></td>
	<?php } ?>
<!--last page button-->
	<?php if ($Pager->LastButton->Enabled) { ?>
	<td><a href="archiveslist.php?start=<?php echo $Pager->LastButton->Start ?>"><img src="images/last.gif" alt="Last" width="16" height="16" border="0"></a></td>	
	<?php } else { ?>
	<td><img src="images/lastdisab.gif" alt="Last" width="16" height="16" border="0"></td>
	<?php } ?>
	<td><span class="phpmaker">页 在 <?php echo $Pager->PageCount ?> 中</span></td>
	</tr></table>
	<span class="phpmaker">第 <?php echo $Pager->FromIndex ?> 条 到 第 <?php echo $Pager->ToIndex ?> 条记录,总共 <?php echo $Pager->RecordCount ?> 条记录</span>
<?php } else { ?>
	<?php if ($sSrchWhere == "0=101") { ?>
	<span class="phpmaker">请输入搜索标准</span>
	<?php } else { ?>
	<span class="phpmaker">没有发现记录</span>
	<?php } ?>
<?php } ?>
		</td>
	</tr>
</table>
</form>
<?php } ?>

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

// Return Basic Search sql
function BasicSearchSQL($Keyword) {
	$sKeyword = ew_AdjustSql($Keyword);
	$sql = "";
	$sql .= "`url` LIKE '%" . $sKeyword . "%' OR ";
	$sql .= "`title` LIKE '%" . $sKeyword . "%' OR ";
	$sql .= "`content` LIKE '%" . $sKeyword . "%' OR ";
	if (substr($sql, -4) == " OR ") $sql = substr($sql, 0, strlen($sql)-4);
	return $sql;
}
function parseTime($starttime,$endtime) {
	$timestamp = 0;
	$startSql = $endSql = '';
	if( $starttime != '' ) {
		$startSql = 'UNIX_TIMESTAMP(`datetime`)>=UNIX_TIMESTAMP("' . trim($starttime) . '")';
	}
	if( $endtime != '' ) {
		$endSql = 'UNIX_TIMESTAMP(`datetime`)<=UNIX_TIMESTAMP("' . trim($endtime) . '")';
	}
	$sql = $startSql;
	if( $endSql != null ) {
		if( $sql != '' ) $sql .= ' AND ';
		$sql = '(' . $sql . $endSql . ')';
	}
	return $sql;
}
function parseCategory($searchscope) {
	$sql = '';
	if( count($searchscope) > 0 ) {
		$scope = implode('","',$searchscope);
		$sql = '(`projectname` in ("' . $scope . '"))';
	}
	return $sql;
}
function parseProjectName($projectname) {
	$sql = '';
	if( isset($projectname) && !empty($projectname) ) {
		$sql = '(`projectname`="' . $projectname . '")';
	}
	return $sql;
}
// Return Basic Search Where based on search keyword and type
function BasicSearchWhere() {
	global $Security, $archives;
	$sSearchStr = "";
	$sSearchKeyword = ew_StripSlashes(@$_GET[EW_TABLE_BASIC_SEARCH]);
	$sSearchType = @$_GET[EW_TABLE_BASIC_SEARCH_TYPE];
	$sSearchStartTime = @$_GET[EW_TABLE_BASIC_SEARCH_START_TIME];
	$sSearchEndTime = @$_GET[EW_TABLE_BASIC_SEARCH_END_TIME];
	$sSearchProjectName = @$_GET[EW_TABLE_BASIC_SEARCH_PROJECTNAME];
	if ($sSearchKeyword <> "") {
		$sSearch = trim($sSearchKeyword);
		if ($sSearchType <> "") {
			while (strpos($sSearch, "  ") !== FALSE)
				$sSearch = str_replace("  ", " ", $sSearch);
			$arKeyword = explode(" ", trim($sSearch));
			foreach ($arKeyword as $sKeyword) {
				if ($sSearchStr <> "") $sSearchStr .= " " . $sSearchType . " ";
				$sSearchStr .= "(" . BasicSearchSQL($sKeyword) . ")";
			}
		} else {
			$sSearchStr = BasicSearchSQL($sSearch);
		}
		$sSearchStrTime = parseTime($sSearchStartTime,$sSearchEndTime);
		if( $sSearchStrTime <> "" ) {
			if( $sSearchStr <> "" ) {
				$sSearchStr = '(' . $sSearchStr . ') AND ';
			}
			$sSearchStr .= $sSearchStrTime;
		}
		$archives->setBasicSearchKeyword($sSearchKeyword);
		$archives->setBasicSearchType($sSearchType);
		$archives->setBasicSearchStartTime($sSearchStartTime);
		$archives->setBasicSearchEndTime($sSearchEndTime);
	}
	$sSearchStrProjectName = parseProjectName($sSearchProjectName);
	if( $sSearchStrProjectName <> "" ) {
		if( $sSearchStr <> "" ) {
			$sSearchStr = '(' . $sSearchStr . ') AND ';
		}
		$sSearchStr .= $sSearchStrProjectName;
	}
	$archives->setBasicSearchProjectName($sSearchProjectName);
	return $sSearchStr;
}

// Clear all search parameters
function ResetSearchParms() {

	// Clear search where
	global $archives;
	$sSrchWhere = "";
	$archives->setSearchWhere($sSrchWhere);

	// Clear basic search parameters
	ResetBasicSearchParms();
}

// Clear all basic search parameters
function ResetBasicSearchParms() {

	// Clear basic search parameters
	global $archives;
	$archives->setBasicSearchKeyword("");
	$archives->setBasicSearchType("");
}

// Restore all search parameters
function RestoreSearchParms() {
	global $sSrchWhere, $archives;
	$sSrchWhere = $archives->getSearchWhere();
}

// Set up Sort parameters based on Sort Links clicked
function SetUpSortOrder() {
	global $archives;

	// Check for an Order parameter
	if (@$_GET["order"] <> "") {
		$archives->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
		$archives->CurrentOrderType = @$_GET["ordertype"];

		// Field id
		$archives->UpdateSort($archives->id);

		// Field url
		$archives->UpdateSort($archives->url);
		
		// Field projectname
		$archives->UpdateSort($archives->projectname);

		// Field datetime
		$archives->UpdateSort($archives->datetime);

		// Field title
		$archives->UpdateSort($archives->title);

		$archives->setStartRecordNumber(1); // Reset start position
	}
	$sOrderBy = $archives->getSessionOrderBy(); // Get order by from Session
	if ($sOrderBy == "") {
		if ($archives->SqlOrderBy() <> "") {
			$sOrderBy = $archives->SqlOrderBy();
			$archives->setSessionOrderBy($sOrderBy);
		}
	}
}

// Reset command based on querystring parameter cmd=
// - RESET: reset search parameters
// - RESETALL: reset search & master/detail parameters
// - RESETSORT: reset sort parameters
function ResetCmd() {
	global $sDbMasterFilter, $sDbDetailFilter, $nStartRec, $sOrderBy;
	global $archives;

	// Get reset cmd
	if (@$_GET["cmd"] <> "") {
		$sCmd = $_GET["cmd"];

		// Reset search criteria
		if (strtolower($sCmd) == "reset" || strtolower($sCmd) == "resetall") {
			ResetSearchParms();
		}

		// Reset Sort Criteria
		if (strtolower($sCmd) == "resetsort") {
			$sOrderBy = "";
			$archives->setSessionOrderBy($sOrderBy);
			$archives->id->setSort("");
			$archives->url->setSort("");
			$archives->projectname->setSort("");
			$archives->datetime->setSort("");
			$archives->title->setSort("");
		}

		// Reset start position
		$nStartRec = 1;
		$archives->setStartRecordNumber($nStartRec);
	}
}
?>
<?php

// Set up Starting Record parameters based on Pager Navigation
function SetUpStartRec() {
	global $nDisplayRecs, $nStartRec, $nTotalRecs, $nPageNo, $archives;
	if ($nDisplayRecs == 0) return;

	// Check for a START parameter
	if (@$_GET[EW_TABLE_START_REC] <> "") {
		$nStartRec = $_GET[EW_TABLE_START_REC];
		$archives->setStartRecordNumber($nStartRec);
	} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
		$nPageNo = $_GET[EW_TABLE_PAGE_NO];
		if (is_numeric($nPageNo)) {
			$nStartRec = ($nPageNo-1)*$nDisplayRecs+1;
			if ($nStartRec <= 0) {
				$nStartRec = 1;
			} elseif ($nStartRec >= intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1) {
				$nStartRec = intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1;
			}
			$archives->setStartRecordNumber($nStartRec);
		} else {
			$nStartRec = $archives->getStartRecordNumber();
		}
	} else {
		$nStartRec = $archives->getStartRecordNumber();
	}

	// Check if correct start record counter
	if (!is_numeric($nStartRec) || $nStartRec == "") { // Avoid invalid start record counter
		$nStartRec = 1; // Reset start record counter
		$archives->setStartRecordNumber($nStartRec);
	} elseif (intval($nStartRec) > intval($nTotalRecs)) { // Avoid starting record > total records
		$nStartRec = intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1; // Point to last page first record
		$archives->setStartRecordNumber($nStartRec);
	} elseif (($nStartRec-1) % $nDisplayRecs <> 0) {
		$nStartRec = intval(($nStartRec-1)/$nDisplayRecs)*$nDisplayRecs+1; // Point to page boundary
		$archives->setStartRecordNumber($nStartRec);
	}
}
?>
<?php

// Load recordset
function LoadRecordset($offset = -1, $rowcnt = -1) {
	global $conn, $archives;

	// Call Recordset Selecting event
	$archives->Recordset_Selecting($archives->CurrentFilter);

	// Load list page sql
	$sSql = $archives->SelectSQL();
	if ($offset > -1 && $rowcnt > -1) $sSql .= " LIMIT $offset, $rowcnt";

	// Load recordset
	$conn->raiseErrorFn = 'ew_ErrorFn';	
	$rs = $conn->Execute($sSql);
	$conn->raiseErrorFn = '';

	// Call Recordset Selected event
	$archives->Recordset_Selected($rs);
	return $rs;
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
	$archives->datetime->setDbValue($rs->fields('datetime'));
	$archives->title->setDbValue($rs->fields('title'));
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

	// datetime
	$archives->datetime->CellCssStyle = "";
	$archives->datetime->CellCssClass = "";

	// title
	$archives->title->CellCssStyle = "";
	$archives->title->CellCssClass = "";

	// content
	$archives->content->CellCssStyle = "";
	$archives->content->CellCssClass = "";
	if ($archives->RowType == EW_ROWTYPE_VIEW) { // View row

		// id
		$archives->id->ViewValue = $archives->id->CurrentValue;
		$archives->id->CssStyle = "";
		$archives->id->CssClass = "";
		$archives->id->ViewCustomAttributes = "";

		// url
		$archives->url->ViewValue = $archives->url->CurrentValue;
		$archives->url->CssStyle = "";
		$archives->url->CssClass = "";
		$archives->url->ViewCustomAttributes = "";
		
		// projectname
		$archives->projectname->ViewValue = $archives->projectname->CurrentValue;
		$archives->projectname->CssStyle = "";
		$archives->projectname->CssClass = "";
		$archives->projectname->ViewCustomAttributes = "";

		// datetime
		$archives->datetime->ViewValue = $archives->datetime->CurrentValue;
		$archives->datetime->ViewValue = ew_FormatDateTime($archives->datetime->ViewValue, 9);
		$archives->datetime->CssStyle = "";
		$archives->datetime->CssClass = "";
		$archives->datetime->ViewCustomAttributes = "";

		// title
		$archives->title->ViewValue = $archives->title->CurrentValue;
		$archives->title->CssStyle = "";
		$archives->title->CssClass = "";
		$archives->title->ViewCustomAttributes = "";
		
		// content
		$archives->content->ViewValue = $archives->content->CurrentValue;
		$archives->content->CssStyle = "";
		$archives->content->CssClass = "";
		$archives->content->ViewCustomAttributes = "";

		// id
		$archives->id->HrefValue = "";

		// url
		$archives->url->HrefValue = "";

		// datetime
		$archives->datetime->HrefValue = "";

		// title
		$archives->title->HrefValue = "";

		// title
		$archives->projectname->HrefValue = "";
		
		// content
		$archives->content->HrefValue = "";
	} elseif ($archives->RowType == EW_ROWTYPE_ADD) { // Add row
	} elseif ($archives->RowType == EW_ROWTYPE_EDIT) { // Edit row
	} elseif ($archives->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$archives->Row_Rendered();
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
function LoadCategoryRecordset($offset = -1, $rowcnt = -1) {
	global $conn, $categories;

	// Call Recordset Selecting event
	$categories->Recordset_Selecting($categories->CurrentFilter);

	// Load list page sql
	$sSql = $categories->SelectSQL();
	if ($offset > -1 && $rowcnt > -1) $sSql .= " LIMIT $offset, $rowcnt";

	// Load recordset
	$conn->raiseErrorFn = 'ew_ErrorFn';
	$rs = $conn->Execute($sSql);
	$conn->raiseErrorFn = '';

	// Call Recordset Selected event
	$categories->Recordset_Selected($rs);
	return $rs;
}
function LoadCategoryRowValues(&$rs) {
	global $categories;
	$categories->id->setDbValue($rs->fields('id'));
	$categories->name->setDbValue($rs->fields('name'));
	$categories->keywords->setDbValue($rs->fields('keywords'));
}
function RenderCategoryRow() {
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

		// id
		$categories->id->ViewValue = $categories->id->CurrentValue;
		$categories->id->CssStyle = "";
		$categories->id->CssClass = "";
		$categories->id->ViewCustomAttributes = "";

		// name
		$categories->name->ViewValue = $categories->name->CurrentValue;
		$categories->name->CssStyle = "";
		$categories->name->CssClass = "";
		$categories->name->ViewCustomAttributes = "";
		
		// keywords
		$categories->keywords->ViewValue = $categories->keywords->CurrentValue;
		$categories->keywords->CssStyle = "";
		$categories->keywords->CssClass = "";
		$categories->keywords->ViewCustomAttributes = "";

		// id
		$categories->id->HrefValue = "";

		// name
		$categories->name->HrefValue = "";
	} elseif ($categories->RowType == EW_ROWTYPE_ADD) { // Add row
	} elseif ($categories->RowType == EW_ROWTYPE_EDIT) { // Edit row
	} elseif ($categories->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$categories->Row_Rendered();
}
function highlight($str,$key) {
	if( is_array($key) ){
		foreach($key as $k ) {
			$str = highlight($str,trim($k));
		}
		return $str;
	}
	else if ( is_string($key) ) {
		if( $key == '' ) return $str;
		//return(str_ireplace($key,'<span style="color:#f00;font-weight:bold;text-decoration:underline">'.$key.'</span>',$str));
		return(preg_replace("/($key)/isU","<span style=\"color:#f00;font-weight:bold;text-decoration:underline\">$1</span>",$str));
	}
	return $str;
}
function countword($str,$key) {
	$count = count_word($str,$key);
	if( $count == 0 ){
		return "";
	}
	else  {
		return "<span style=\"color:#ff0000;font-weight:bold;text-decoration:underline\">($count)</span>";
	}
	return "";
}
function count_word($str,$key) {
	$count = 0;
	if( is_array($key) ){
		foreach($key as $k ) {
			$count = $count + substr_count($str, $k);
		}
		return $count;
	}
	else if ( is_string($key) ) {
		if( $key == '' ) return 0;
		//return(str_ireplace($key,'<span style="color:#f00;font-weight:bold;text-decoration:underline">'.$key.'</span>',$str));
		return(substr_count($str, $key));
	}
	return $count;
}
?>

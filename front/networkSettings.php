<!-- ---------------------------------------------------------------------------
#  Pi.Alert
#  Open Source Network Guard / WIFI & LAN intrusion detector
#
#  network.php - Front module. network relationship
#-------------------------------------------------------------------------------
#  leiweibau 2023                                          GNU GPLv3
#--------------------------------------------------------------------------- -->

<?php
session_start();

// Turn off php errors
error_reporting(0);

if ($_SESSION["login"] != 1) {
	header('Location: /pialert/index.php');
	exit;
}

require 'php/templates/header.php';
require 'php/server/db.php';

$DBFILE = '../db/pialert.db';
OpenDB();
// #####################################
// ## update db dependencies
// #####################################
$sql = 'CREATE TABLE IF NOT EXISTS "network_infrastructure" (
	"device_id"	INTEGER,
	"net_device_name"	TEXT NOT NULL,
	"net_device_typ"	TEXT NOT NULL,
  "net_device_port"  INTEGER,
  "net_downstream_devices" TEXT,
	PRIMARY KEY("device_id" AUTOINCREMENT)
)';
$result = $db->query($sql);

$sql = 'ALTER TABLE "Devices" ADD "dev_Infrastructure" INTEGER';
$result = $db->query($sql);
$sql = 'ALTER TABLE "Devices" ADD "dev_Infrastructure_port" INTEGER';
$result = $db->query($sql);
$sql = 'ALTER TABLE "network_infrastructure" ADD "net_downstream_devices" TEXT';
$result = $db->query($sql);

$sql = 'CREATE TABLE IF NOT EXISTS "network_dumb_dev" (
  "dev_Name"  TEXT,
  "dev_MAC" TEXT,
  "dev_Infrastructure"  INTEGER,
  "dev_Infrastructure_port" INTEGER,
  "dev_PresentLastScan" TEXT,
  "dev_LastIP"  TEXT,
  "id"  INTEGER,
  PRIMARY KEY("id" AUTOINCREMENT)
)';
$result = $db->query($sql);

// Add New Network Devices
// #####################################
if ($_REQUEST['Networkinsert'] == "yes") {
	if (isset($_REQUEST['NetworkDeviceName']) && isset($_REQUEST['NetworkDeviceTyp'])) {
		$sql = 'INSERT INTO "network_infrastructure" ("net_device_name", "net_device_typ", "net_device_port") VALUES("' . $_REQUEST['NetworkDeviceName'] . '", "' . $_REQUEST['NetworkDeviceTyp'] . '", "' . $_REQUEST['NetworkDevicePort'] . '")';
		$result = $db->query($sql);
	}
}
// Edit  Network Devices
// #####################################
if ($_REQUEST['Networkedit'] == "yes") {
	if (($_REQUEST['NewNetworkDeviceName'] != "") && isset($_REQUEST['NewNetworkDeviceTyp'])) {
		$sql = 'UPDATE "network_infrastructure" SET "net_device_name" = "' . $_REQUEST['NewNetworkDeviceName'] . '", "net_device_typ" = "' . $_REQUEST['NewNetworkDeviceTyp'] . '", "net_device_port" = "' . $_REQUEST['NewNetworkDevicePort'] . '", "net_downstream_devices" = "' . $_REQUEST['NetworkDeviceDownlink'] . '" WHERE "device_id"="' . $_REQUEST['NetworkDeviceID'] . '"';
		$result = $db->query($sql);
	}

	if (($_REQUEST['NewNetworkDeviceName'] == "") && isset($_REQUEST['NewNetworkDeviceTyp'])) {
		$sql = 'UPDATE "network_infrastructure" SET "net_device_typ" = "' . $_REQUEST['NewNetworkDeviceTyp'] . '", "net_device_port" = "' . $_REQUEST['NewNetworkDevicePort'] . '", "net_downstream_devices" = "' . $_REQUEST['NetworkDeviceDownlink'] . '" WHERE "device_id"="' . $_REQUEST['NetworkDeviceID'] . '"';
		$result = $db->query($sql);
	}

}
// remove Network Devices
// #####################################
if ($_REQUEST['Networkdelete'] == "yes") {
	if (isset($_REQUEST['NetworkDeviceID'])) {
		$sql = 'DELETE FROM "network_infrastructure" WHERE "device_id"="' . $_REQUEST['NetworkDeviceID'] . '"';
		$result = $db->query($sql);
	}
}

// Add New unmanaged Device
// #####################################
if ($_REQUEST['NetworkUnmanagedDevinsert'] == "yes") {
	if (isset($_REQUEST['NetworkUnmanagedDevName']) && isset($_REQUEST['NetworkUnmanagedDevConnect'])) {
		$ip = 'Unmanaged';
		$dumbvar = 'dumb';
		$sql = 'INSERT INTO "network_dumb_dev" ("dev_Name", "dev_MAC", "dev_Infrastructure", "dev_Infrastructure_port", "dev_PresentLastScan", "dev_LastIP") VALUES("' . $_REQUEST['NetworkUnmanagedDevName'] . '", "' . $dumbvar . '", "' . $_REQUEST['NetworkUnmanagedDevConnect'] . '", "' . $_REQUEST['NetworkUnmanagedDevPort'] . '", "' . $dumbvar . '", "' . $ip . '")';
		$result = $db->query($sql);
	}
}
// Edit  unmanaged Device
// #####################################
if ($_REQUEST['NetworkUnmanagedDevedit'] == "yes") {
	if (($_REQUEST['NewNetworkUnmanagedDevName'] != "") && isset($_REQUEST['NewNetworkUnmanagedDevConnect']) && isset($_REQUEST['NetworkUnmanagedDevID'])) {
		$sql = 'UPDATE "network_dumb_dev" SET "dev_Name" = "' . $_REQUEST['NewNetworkUnmanagedDevName'] . '", "dev_Infrastructure" = "' . $_REQUEST['NewNetworkUnmanagedDevConnect'] . '", "dev_Infrastructure_port" = "' . $_REQUEST['NewNetworkUnmanagedDevPort'] . '" WHERE "id"="' . $_REQUEST['NetworkUnmanagedDevID'] . '"';
		$result = $db->query($sql);
	}

	if (($_REQUEST['NewNetworkUnmanagedDevName'] == "") && isset($_REQUEST['NewNetworkUnmanagedDevConnect']) && isset($_REQUEST['NetworkUnmanagedDevID'])) {
		$sql = 'UPDATE "network_dumb_dev" SET "dev_Infrastructure" = "' . $_REQUEST['NewNetworkUnmanagedDevConnect'] . '", "dev_Infrastructure_port" = "' . $_REQUEST['NewNetworkUnmanagedDevPort'] . '" WHERE "id"="' . $_REQUEST['NetworkUnmanagedDevID'] . '"';
		$result = $db->query($sql);
	}
}
// remove unmanaged Device
// #####################################
if ($_REQUEST['NetworkUnmanagedDevdelete'] == "yes") {
	if (isset($_REQUEST['NetworkUnmanagedDevID'])) {
		$sql = 'DELETE FROM "network_dumb_dev" WHERE "id"="' . $_REQUEST['NetworkUnmanagedDevID'] . '"';
		$result = $db->query($sql);
	}
}

?>

<!-- Page ------------------------------------------------------------------ -->
<div class="content-wrapper">

<!-- Content header--------------------------------------------------------- -->
    <section class="content-header">
    <?php require 'php/templates/notification.php';?>
      <h1 id="pageTitle">
         <?php echo $pia_lang['NetworkSettings_Title']; ?>
      </h1>
    </section>

    <!-- Main content ---------------------------------------------------------- -->
    <section class="content">

    <!-- Manage Devices ---------------------------------------------------------- -->
		<div class="box "> <!-- collapsed-box -->
        <div class="box-header">
          <h3 class="box-title"><?php echo $pia_lang['Network_ManageDevices']; ?></h3>
        </div>
        <!-- /.box-header -->

        <div class="box-body" style="">
          <p><?php echo $pia_lang['Network_ManageDevices_Intro']; ?></p>
          <div class="row">
            <!-- Add Device ---------------------------------------------------------- -->
            <div class="col-md-4">
            <h4 class="box-title"><?php echo $pia_lang['Network_ManageAdd']; ?></h4>
            <form role="form" method="post" action="./networkSettings.php">
              <div class="form-group has-success">
                  <label for="NetworkDeviceName"><?php echo $pia_lang['Network_ManageAdd_Name']; ?>:</label>
                  <div class="input-group">
                      <input class="form-control" id="txtNetworkNodeMac" name="NetworkDeviceName" type="text" placeholder="<?php echo $pia_lang['Network_ManageAdd_Name_text']; ?>">
                          <div class="input-group-btn">
                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id="buttonNetworkNodeMac">
                                <span class="fa fa-caret-down"></span>
                            </button>
                            <ul id="dropdownNetworkNodeMac" class="dropdown-menu dropdown-menu-right">
                              <li class="divider"></li>
<?php
network_infrastructurelist();
?>
                            </ul>
                          </div>
                  </div>
              </div>
              <!-- /.form-group -->
              <div class="form-group has-success">
               <label><?php echo $pia_lang['Network_ManageAdd_Type']; ?>:</label>
                  <select class="form-control" name="NetworkDeviceTyp">
                    <option value=""><?php echo $pia_lang['Network_ManageAdd_Type_text']; ?></option>
                    <option value="0_Internet">Internet</option>
                    <option value="1_Router">Router</option>
                    <option value="2_Switch">Switch</option>
                    <option value="3_WLAN">WLAN</option>
                    <option value="4_Powerline">Powerline</option>
                  </select>
              </div>
              <div class="form-group has-success">
                <label for="NetworkDevicePort"><?php echo $pia_lang['Network_ManageAdd_Port']; ?>:</label>
                <input type="text" class="form-control" id="NetworkDevicePort" name="NetworkDevicePort" placeholder="<?php echo $pia_lang['Network_ManageAdd_Port_text']; ?>">
              </div>
              <div class="form-group">
              <button type="submit" class="btn btn-success" name="Networkinsert" value="yes"><?php echo $pia_lang['Network_ManageAdd_Submit']; ?></button>
          	  </div>
          </form>
              <!-- /.form-group -->
            </div>
            <!-- /.col -->
            <!-- Edit Device ---------------------------------------------------------- -->
            <div class="col-md-4">
              <h4 class="box-title"><?php echo $pia_lang['Network_ManageEdit']; ?></h4>
              <form role="form" method="post" action="./networkSettings.php">
              <div class="form-group has-warning">
              	<label><?php echo $pia_lang['Network_ManageEdit_ID']; ?>:</label>
                  <select class="form-control" name="NetworkDeviceID" onchange="get_networkdev_values(event)">
                    <option value=""><?php echo $pia_lang['Network_ManageEdit_ID_text']; ?></option>
<?php
$sql = 'SELECT "device_id", "net_device_name", "net_device_typ", "net_device_port", "net_downstream_devices" FROM "network_infrastructure" ORDER BY "net_device_typ" ASC';
$result = $db->query($sql); //->fetchArray(SQLITE3_ASSOC);
$netdev_all_ids = array();
while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
	if (!isset($res['device_id'])) {
		continue;
	}
	$temp_name = "netdev_id_" . $res['device_id'];
	echo '<option value="' . $res['device_id'] . '">' . $res['net_device_name'] . ' / ' . substr($res['net_device_typ'], 2) . '</option>';

	$$temp_name = array();
	array_push($netdev_all_ids, $temp_name);
	$$temp_name[0] = $res['device_id'];
	$$temp_name[1] = $res['net_device_name'];
	$$temp_name[2] = $res['net_device_typ'];
	$$temp_name[3] = $res['net_downstream_devices'];
	$$temp_name[4] = $res['net_device_port'];
}
?>
                  </select>
              </div>
<!-- Autofill "Edit" Input fields ---------------------------------------------------------- -->
<script>
function get_networkdev_values(event) {
    var selectElement = event.target;
    var value = 'netdev_id_' + selectElement.value;

<?php
foreach ($netdev_all_ids as $key => $value) {
	echo '    const ' . $value . ' = ["' . $$value[0] . '", "' . $$value[1] . '", "' . $$value[2] . '" , "' . $$value[3] . '", "' . $$value[4] . '"];';
	echo "\n";
}

echo '    var netdev_arrays = {';
echo "\n";
foreach ($netdev_all_ids as $key => $value) {
	echo '        "' . $value . '":' . $value . ',';
	echo "\n";
}
echo '    };';
?>

    var netdev_name = netdev_arrays[value][1];
    $('#NewNetworkDeviceName').val(netdev_name);
    var netdev_type = netdev_arrays[value][2];
    $('#NewNetworkDeviceTyp').val(netdev_type);
    var port_config = netdev_arrays[value][3];
    $('#txtNetworkDeviceDownlinkMac').val(port_config);
    var port_count = netdev_arrays[value][4];
    $('#NewNetworkDevicePort').val(port_count);
};
</script>
              <div class="form-group has-warning">
                <label for="NetworkDeviceName"><?php echo $pia_lang['Network_ManageEdit_Name']; ?>:</label>
                <input type="text" class="form-control" id="NewNetworkDeviceName" name="NewNetworkDeviceName" placeholder="<?php echo $pia_lang['Network_ManageEdit_Name_text']; ?>">
              </div>
              <div class="form-group has-warning">
               <label><?php echo $pia_lang['Network_ManageEdit_Type']; ?>:</label>
                  <select class="form-control" name="NewNetworkDeviceTyp" id="NewNetworkDeviceTyp">
                    <option value=""><?php echo $pia_lang['Network_ManageEdit_Type_text']; ?></option>
                    <option value="0_Internet">Internet</option>
                    <option value="1_Router">Router</option>
                    <option value="2_Switch">Switch</option>
                    <option value="3_WLAN">WLAN</option>
                    <option value="4_Powerline">Powerline</option>
                  </select>
              </div>
              <div class="form-group has-warning">
                <label for="NetworkDevicePort"><?php echo $pia_lang['Network_ManageEdit_Port']; ?>:</label>
                <input type="text" class="form-control" id="NewNetworkDevicePort" name="NewNetworkDevicePort" placeholder="<?php echo $pia_lang['Network_ManageEdit_Port_text']; ?>">
              </div>
              <div class="form-group has-warning">
                  <label for="NetworkDeviceDownlink"><?php echo $pia_lang['Network_ManageEdit_Downlink']; ?>:</label>
                  <div class="input-group">
                      <input class="form-control" id="txtNetworkDeviceDownlinkMac" name="NetworkDeviceDownlink" type="text" placeholder="<?php echo $pia_lang['Network_ManageEdit_Downlink_text']; ?>">
                          <div class="input-group-btn">
                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id="buttonNetworkDeviceDownlinkMac">
                                <span class="fa fa-caret-down"></span>
                            </button>
                            <ul id="dropdownNetworkDeviceDownlinkMac" class="dropdown-menu dropdown-menu-right">
                              <li class="divider"></li>
<?php
//network_infrastructurelist();
network_device_downlink_mac();
?>
                            </ul>
                          </div>
                  </div>
              </div>
              <div class="form-group">
                <button type="submit" class="btn btn-warning" name="Networkedit" value="yes"><?php echo $pia_lang['Network_ManageEdit_Submit']; ?></button>
              </div>
         	 </form>
              <!-- /.form-group -->
            </div>
            <!-- /.col -->
            <!-- Del Device ---------------------------------------------------------- -->
           <div class="col-md-4">
            <h4 class="box-title"><?php echo $pia_lang['Network_ManageDel']; ?></h4>
              <form role="form" method="post" action="./networkSettings.php">
              <div class="form-group has-error">
                <label><?php echo $pia_lang['Network_ManageDel_Name']; ?>:</label>
                  <select class="form-control" name="NetworkDeviceID">
                    <option value=""><?php echo $pia_lang['Network_ManageDel_Name_text']; ?></option>
<?php
$sql = 'SELECT "device_id", "net_device_name", "net_device_typ" FROM "network_infrastructure" ORDER BY "net_device_typ" ASC';
$result = $db->query($sql); //->fetchArray(SQLITE3_ASSOC);
while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
	if (!isset($res['device_id'])) {
		continue;
	}

	echo '<option value="' . $res['device_id'] . '">' . $res['net_device_name'] . ' / ' . substr($res['net_device_typ'], 2) . '</option>';
}
?>
                  </select>
              </div>
              <!-- /.form-group -->
              <div class="form-group">
                <button type="submit" class="btn btn-danger" name="Networkdelete" value="yes"><?php echo $pia_lang['Network_ManageDel_Submit']; ?></button>
              </div>
           </form>
              <!-- /.form-group -->
            </div>
          </div>
          <!-- /.row -->
        </div>
        <!-- /.box-body -->
      </div>

    <div class="box ">
        <div class="box-header">
          <h3 class="box-title"><?php echo $pia_lang['Network_Unmanaged_Devices']; ?></h3>
        </div>
        <!-- /.box-header -->

        <div class="box-body" style="">
          <p><?php echo $pia_lang['Network_Unmanaged_Devices_Intro']; ?></p>
          <div class="row">
            <!-- Add Device ---------------------------------------------------------- -->
            <div class="col-md-4">
            <h4 class="box-title"><?php echo $pia_lang['Network_ManageAdd']; ?></h4>
            <form role="form" method="post" action="./networkSettings.php">
              <!-- /.form-group -->
              <div class="form-group has-success">
                <label for="NetworkUnmanagedDevName"><?php echo $pia_lang['Network_ManageAdd_Name']; ?>:</label>
                <input type="text" class="form-control" id="NetworkUnmanagedDevName" name="NetworkUnmanagedDevName" placeholder="<?php echo $pia_lang['Network_ManageAdd_Name_text']; ?>">
              </div>

              <div class="form-group has-success">
                <label><?php echo $pia_lang['Network_Unmanaged_Devices_Connected']; ?>:</label>
                  <select class="form-control" name="NetworkUnmanagedDevConnect">
                    <option value=""><?php echo $pia_lang['Network_Unmanaged_Devices_Connected_text']; ?></option>
<?php
$sql = 'SELECT "device_id", "net_device_name", "net_device_typ", "net_device_port", "net_downstream_devices" FROM "network_infrastructure" ORDER BY "net_device_typ" ASC';
$result = $db->query($sql); //->fetchArray(SQLITE3_ASSOC);
$netdev_all_ids = array();
while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
	if (!isset($res['device_id'])) {
		continue;
	}
	echo '<option value="' . $res['device_id'] . '">' . $res['net_device_name'] . ' / ' . substr($res['net_device_typ'], 2) . '</option>';
}
?>
                  </select>
              </div>
              <div class="form-group has-success">
                <label for="NetworkUnmanagedDevPort"><?php echo $pia_lang['Network_Unmanaged_Devices_Port']; ?>:</label>
                <input type="text" class="form-control" id="NetworkUnmanagedDevPort" name="NetworkUnmanagedDevPort" placeholder="<?php echo $pia_lang['Network_Unmanaged_Devices_Port_text']; ?>">
              </div>
              <div class="form-group">
              <button type="submit" class="btn btn-success" name="NetworkUnmanagedDevinsert" value="yes"><?php echo $pia_lang['Network_ManageAdd_Submit']; ?></button>
              </div>
          </form>
              <!-- /.form-group -->
            </div>
            <!-- /.col -->
            <!-- Edit Device ---------------------------------------------------------- -->
            <div class="col-md-4">
              <h4 class="box-title"><?php echo $pia_lang['Network_ManageEdit']; ?></h4>
              <form role="form" method="post" action="./networkSettings.php">
              <div class="form-group has-warning">
                <label><?php echo $pia_lang['Network_ManageEdit_ID']; ?>:</label>
                  <select class="form-control" name="NetworkUnmanagedDevID">
                    <option value=""><?php echo $pia_lang['Network_ManageEdit_ID_text']; ?></option>
<?php
$sql = 'SELECT * FROM "network_dumb_dev" ORDER BY "dev_Name" ASC';
$result = $db->query($sql); //->fetchArray(SQLITE3_ASSOC);
$netdev_all_ids = array();
while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
	if (!isset($res['id'])) {
		continue;
	}
	echo '<option value="' . $res['id'] . '">' . $res['dev_Name'] . '</option>';
}
?>
                  </select>
              </div>
              <div class="form-group has-warning">
                <label for="NewNetworkUnmanagedDevName"><?php echo $pia_lang['Network_ManageEdit_Name']; ?>:</label>
                <input type="text" class="form-control" id="NewNetworkUnmanagedDevName" name="NewNetworkUnmanagedDevName" placeholder="<?php echo $pia_lang['Network_ManageEdit_Name_text']; ?>">
              </div>

              <div class="form-group has-warning">
                <label><?php echo $pia_lang['Network_Unmanaged_Devices_Connected']; ?>:</label>
                  <select class="form-control" name="NewNetworkUnmanagedDevConnect">
                    <option value=""><?php echo $pia_lang['Network_Unmanaged_Devices_Connected_text']; ?></option>
<?php
$sql = 'SELECT "device_id", "net_device_name", "net_device_typ", "net_device_port", "net_downstream_devices" FROM "network_infrastructure" ORDER BY "net_device_typ" ASC';
$result = $db->query($sql); //->fetchArray(SQLITE3_ASSOC);
$netdev_all_ids = array();
while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
	if (!isset($res['device_id'])) {
		continue;
	}
	echo '<option value="' . $res['device_id'] . '">' . $res['net_device_name'] . ' / ' . substr($res['net_device_typ'], 2) . '</option>';
}
?>
                  </select>
              </div>
              <div class="form-group has-warning">
                <label for="NetworkDevicePort"><?php echo $pia_lang['Network_Unmanaged_Devices_Port']; ?>:</label>
                <input type="text" class="form-control" id="NewNetworkUnmanagedDevPort" name="NewNetworkUnmanagedDevPort" placeholder="<?php echo $pia_lang['Network_Unmanaged_Devices_Port_text']; ?>">
              </div>

              <div class="form-group">
                <button type="submit" class="btn btn-warning" name="NetworkUnmanagedDevedit" value="yes"><?php echo $pia_lang['Network_ManageEdit_Submit']; ?></button>
              </div>
           </form>
              <!-- /.form-group -->
            </div>
            <!-- /.col -->
            <!-- Del Device ---------------------------------------------------------- -->
           <div class="col-md-4">
            <h4 class="box-title"><?php echo $pia_lang['Network_ManageDel']; ?></h4>
              <form role="form" method="post" action="./networkSettings.php">
              <div class="form-group has-error">
                <label><?php echo $pia_lang['Network_ManageDel_Name']; ?>:</label>
                  <select class="form-control" name="NetworkUnmanagedDevID">
                    <option value=""><?php echo $pia_lang['Network_ManageDel_Name_text']; ?></option>
<?php
$sql = 'SELECT "id", "dev_Name" FROM "network_dumb_dev" ORDER BY "dev_Name" ASC';
$result = $db->query($sql); //->fetchArray(SQLITE3_ASSOC);
while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
	if (!isset($res['id'])) {
		continue;
	}

	echo '<option value="' . $res['id'] . '">' . $res['dev_Name'] . '</option>';
}
?>
                  </select>
              </div>
              <!-- /.form-group -->
              <div class="form-group">
                <button type="submit" class="btn btn-danger" name="NetworkUnmanagedDevdelete" value="yes"><?php echo $pia_lang['Network_ManageDel_Submit']; ?></button>
              </div>
           </form>
              <!-- /.form-group -->
            </div>
          </div>
          <!-- /.row -->
        </div>
        <!-- /.box-body -->
      </div>

<script>
function setTextValue (textElement, textValue) {
  $('#'+textElement).val (textValue);
}
</script>

<?php
// #####################################
// ## Start Function Setup
// #####################################
function network_infrastructurelist() {
	global $db;
	$func_sql = 'SELECT * FROM "Devices" WHERE "dev_DeviceType" = "Router" OR "dev_DeviceType"  = "Switch" OR "dev_DeviceType"  = "AP" OR "dev_DeviceType"  = "Access Point" OR "dev_MAC"  = "Internet"';
	$func_result = $db->query($func_sql); //->fetchArray(SQLITE3_ASSOC);
	while ($func_res = $func_result->fetchArray(SQLITE3_ASSOC)) {
		echo '<li><a href="javascript:void(0)" onclick="setTextValue(\'txtNetworkNodeMac\',\'' . $func_res['dev_Name'] . '\')">' . $func_res['dev_Name'] . '/' . $func_res['dev_DeviceType'] . '</a></li>';
	}
}

function network_device_downlink_mac() {
	global $db;
	$func_sql = 'SELECT * FROM "Devices" WHERE "dev_DeviceType" = "Router" OR "dev_DeviceType"  = "Switch" OR "dev_DeviceType"  = "AP" OR "dev_DeviceType"  = "Access Point" OR "dev_MAC"  = "Internet"';
	$func_result = $db->query($func_sql); //->fetchArray(SQLITE3_ASSOC);
	while ($func_res = $func_result->fetchArray(SQLITE3_ASSOC)) {
		echo '<li><a href="javascript:void(0)" onclick="setTextValue(\'txtNetworkDeviceDownlinkMac\',\'' . $func_res['dev_MAC'] . ',\')">' . $func_res['dev_Name'] . '</a></li>';
	}
}

// #####################################
// ## End Function Setup
// #####################################

?>

  <div style="width: 100%; height: 20px;"></div>
</section>

    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<!-- ----------------------------------------------------------------------- -->
<?php
require 'php/templates/footer.php';
?>
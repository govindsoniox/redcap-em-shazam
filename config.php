<?php
namespace Stanford\Shazam;

// SHOW TABLE OF CONFIGURED SHAZAM OPTIONS
/** @var Shazam $module */

$module->loadConfig();
$user = $module->getUser();

/**
 * Handle calls from the configuration page
 */
if ($_SERVER['REQUEST_METHOD']=='POST') {
	$module->emDebug($_POST, "DEBUG", "INCOMING POST");

	// Parse required fields
	$field_name = !empty($_POST['field_name'])  ? htmlspecialchars( $_POST['field_name'], ENT_QUOTES) : "";
	$action     = !empty($_POST['action'])      ? htmlspecialchars( $_POST['action'], ENT_QUOTES)     : "";

	// Get some instrument information as well:
    global $Proj;
    $instrument         = !empty($field_name) ? $Proj->metadata[$field_name]['form_name'] : "";
    $instrument_fields  = !empty($instrument) ? $Proj->forms[$instrument]['fields']       : "";

	if ($action == "create") {
        // Create a default entry for the new field and save it.  Then render the edit page.
        $module->addDefaultField($field_name);
        $action = "edit";
    }

    // Get the current config as a variable
    $config = $module->config;

    switch ($action) {
        case "edit":
			// Verify edit is valid and then render the 'edit' page.
			// Plugin::log($module->config, "DEBUG", "this->config");
			// Render the editor
			require_once APP_PATH_DOCROOT . 'ProjectGeneral/header.php';

            $b = new \Browser();
            $cmdKey = ( $b->getPlatform() == "Apple" ? "&#8984;" : "Ctrl" );

            ?>
            <h4>
                Shazam Editor:
                <span class="badge badge-info">Instrument: <?php echo $instrument?></span>
                <span class="badge badge-info">Field: <?php echo $field_name?></span>
                <button class="btn btn-success btn-sm float-right" data-toggle="modal" data-target="#shazam-example">Show Example/Instructions</button>
            </h4>
            <hr>
            <div class="modal fade" id="shazam-example" tabindex="-1" role="dialog" aria-labelledby="reportModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-wide">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="shazam-example-label">Shazam Example</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Use the tabs below to create and edit your Shazam HTML block.  This block of HTML will be inserted into the instrument where
                                <?php echo $field_name ?> would normally be displayed.  As you are editing, you can save your progress with
                                <span class="label label-primary"><?php echo $cmdKey?>-S</span>.  It may be best to open up your <?php echo $instrument ?> form and refresh
                                after each save to see how you are progressing.</p>
                            <p>
                                Most commonly, you will create a HTML table and insert REDCap fields into this table as illustrated below:
                            </p>

                            <textarea id="example-data" style="display:none;">
<table class='fy_summary'>
    <tr>
        <th></th>
        <th>2012</th>
        <th>2013</th>
    </tr>
    <tr>
        <th>Plain Text</th>
        <td><div class='shazam'>fed_grants_fy12</div></td>
        <td><div class='shazam'>fed_grants_fy13</div></td>
    </tr>
    <tr>
        <!-- This will map the LABEL to the field nf_grants field -->
        <th class='shazam'>nf_grants:label</th>
        <td class='shazam'>nf_grants_fy12</td>
        <td class='shazam'>nf_grants_fy13</td>
    </tr>
    <tr>
        <th>Research Agreements/Contracts</th>
        <!-- this input will include the history and data resolution widgets -->
        <td class='shazam shazam-icons'>rsch_contract_fy12</td>
        <td class='shazam'>rsch_contract_fy13</td>
    </tr>

    <!-- This will make this entire TR only visible when the field 'clinical_trials' is visible -->
    <tr data-shazam-mirror-visibility="clinical_trials">
        <th>Clinical Trials</th>
        <td class='shazam'>ct_fy12</td>
        <td class='shazam'>ct_fy13</td>
    </tr>
</table>
                            </textarea>
                            <div id="shazam-example-code" style="border: 1px solid #ccc; max-width: 95%;">
                            </div>
                            <p>
                                And the result of something like this might look similar (but not exactly) like:
                                <img style="max-width: 90%;" src="<?php echo $module->getUrl("assets/example_table.png"); ?>"/>
                            </p>
                            <ul>
                                <li>
                                    Notice how each element with a <code>class='shazam'</code> contains the name of a field as the text of the element.  This will move
                                    that redcap field into the element with the class.  If you want to move a fields label instead, use <code>field_name:label</code> inside of the shazam tag.
                                </li>
                                <li>
                                    Also notice how the last row contains the attribute <code>data-shazam-mirror-visibility="field_name"</code>.  This is a way to make an element in your html mimic
                                    the branching-logic visiblity of a redcap element.  In this case, there is a field called 'clinical_trials'.  If it is visible, so will
                                    the row in the table called Clinical Trials.
                                </li>
                                <li>
                                    Notice how you can add <code>class='shazam shazam-icons'</code> and it will also move the history and data resolution workflow widgets
                                </li>
                                <li>
                                    Lastly, notice how you can also MAP the label from another redcap question.  Look at the row header for "Non-Federal Grants" isn't spelled out,
                                    but rather comes from the label of another question.  This can reduce the complexity of your Shazam HTML by relying more on the data dictionary.
                                </li>
                            </ul>
                            <p>
                                Don't forget you can also add custom CSS to your page and super-users can add custom javascript to override the appearance and behaviors on a page.
                            </p>
                        </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-info btn-sm" data-dismiss="modal">
                                    <i class="fas fa-times"></i> Close
                                </button>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="shazam-editor" data-field-name="<?php echo $field_name ?>">

                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#panel_editor_html">HTML</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#panel_editor_css">CSS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#panel_editor_js">JS</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="panel_editor_html" >
                        <div class="editor2" id='editor_html' data-mode="html"></div>
                    </div>
                    <div class="tab-pane fade" id="panel_editor_css">
                        <div class="editor2" id='editor_css' data-mode="css"></div>
                    </div>
                    <div class="tab-pane fade" id="panel_editor_js">
                        <div class="editor2" id='editor_js' data-mode="javascript"></div>
                    </div>
                </div>
            </div>
            <div class="shazam-edit-buttons input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="Comment">Save Comment:</span>
                </div>
                <input type="text" class="form-control" placeholder="(optional)" id="save_comments"/>
                <button class="ml-2 btn btn-sm btn-info" name="save">SAVE (<?php echo $cmdKey; ?>-S)</button>
                <button class="ml-2 btn btn-sm btn-info" name="save_and_close">SAVE AND CLOSE</button>
                <button class="ml-2 btn btn-sm btn-success" name="beautify">BEAUTIFY</button>
                <button class="ml-2 btn btn-sm btn-danger" name="cancel">CANCEL</button>
            </div>

            <script src="<?php echo $module->getUrl('js/ace/ace.js'); ?>"></script>
            <script src="<?php echo $module->getUrl('js/config.js'); ?>"></script>
            <script src="<?php echo $module->getUrl('js/ace/ext-language_tools.js'); ?>"></script>
            <script src="<?php echo $module->getUrl('js/ace/ext-html_beautify.js'); ?>"></script>

            <style>
                .shazam-editor { border-bottom: 1px solid #ddd; margin-bottom: 10px;}

                @media (min-width: 801px) {
                    .modal-dialog-wide {
                        width: 800px;
                        margin: 30px auto;
                    }
                }

                .modal-dialog { max-width: inherit; }

                li.nav-item {
                    font-weight: bold;
                    background-color: #efefef;
                }

                ul.nav-tabs { margin-left: 48px }
            </style>

            <?php
    			require_once APP_PATH_DOCROOT . 'ProjectGeneral/footer.php';
			?>
			<script>
                Shazam.su          = <?php echo ($user->isSuperUser() ? 1 : 0); ?>;
                Shazam.currentUser = <?php echo json_encode($user->getUsername()); ?>;
                Shazam.config      = <?php echo json_encode($module->config[$field_name]); ?>;
                Shazam.fields      = <?php echo json_encode(array_keys($instrument_fields)); ?>;
                Shazam.js_users    = <?php echo json_encode($module->getJavascriptUsers()); ?>;
                Shazam.prepareEditors();
            </script>
            <?php
			exit();
        case "save":
			// SAVE A CONFIGURATION
			// THIS IS AN AJAX METHOD
			$params_raw  = $_POST['params'];
            $module->emDebug("Params", $params_raw);

            // Add a database transit to get around psalm
            $module->setProjectSetting('params_raw', $params_raw);
            $params = $module->getProjectSetting('params_raw');

            $comments   = !empty($_POST['comments']) ? "[$field_name] " . htmlspecialchars($_POST['comments'], ENT_QUOTES) : "-";
            $exceptions = $module->getJavascriptUsers();
            if (empty($exceptions)) $exceptions = [];

            // If not a superuser or user granted access, then you can't change the javascript...  Also prevent someone from trying to inject a change into the post
            if (!$user->isSuperUser() && !in_array($user->getUsername(), $exceptions)) {
                // Is there an existing js
                if (!empty($config[$field_name]['javascript'])) {
                    $module->emDebug("js is not empty - keeping original value since not a superuser");
                    $params['javascript'] = $config[$field_name]['javascript'];
                } else {
                    $module->emDebug("js IS empty");
                    $params['javascript'] = '';
                }
            } else {
                $module->emDebug("Super User is Saving!");
            }

            $update = array(
                $field_name => $params
            );

            // Add or update config
			$new_config = empty($config) ? $update : array_merge($config, $update);
            //$module::log($new_config, "DEBUG", "new_config");

			// Save and backup the Config
			$return = $module->saveConfig($new_config, $comments);

			header('Content-Type: application/json');
			print json_encode($return);
			exit();

        case "delete":
			unset($config[$field_name]);
			$module->saveConfig($config, "Deleted $field_name");
			break;

        case "activate":
			$config[$field_name]['status'] = 1;
			$module->saveConfig($config, "Activated $field_name");
			break;

        case "deactivate":
			$config[$field_name]['status'] = 0;
			$module->saveConfig($config, "Deactivated $field_name");
			break;

        case "restore":
            // The timestamp was passed in through the fieldname attribute
            $ts = $field_name;
            $module->emDebug("Restore", $ts);
            $module->restoreVersion($ts);
            break;

        case "grant":
            $username = $field_name; //isset($_POST['username']) ? filter_var($_POST['username'], FILTER_SANITIZE_STRING) : "";
            $module->emDebug("Granting JS Permissions to {$username}");
            $module->addJavascriptUser($username);
            break;

        case "remove":
            $username = $field_name; //$username = isset($_POST['username']) ? filter_var($_POST['username'], FILTER_SANITIZE_STRING) : "";
            $module->emDebug("Removing JS Permissions for {$username}");
            $module->removeJavascriptUser($username);
            break;

        default:
			print "Unknown action";
	}
}

# Render Table Page
require_once APP_PATH_DOCROOT . 'ProjectGeneral/header.php';
?>
<style>
    #shazam td { vertical-align: middle; }
    .shazam-descriptive { font-style: italic; font-size: smaller; margin: 0 20px; white-space: nowrap;}
    <?php
        $jsUsers = $module->getJavascriptUsers();
        if (empty($jsUsers)) echo ".js-users {display:none;}";
    ?>
    .table th {font-weight: bold;}
</style>

<h3>Shazam Fields</h3>

<p>
    This is a table of descriptive fields in your project that have been configured with Shazam's powers.  Click on the
    add button below to get started.  More instructions are available when you are actually editing the Shazam configuration.
</p>

<div class="shazam-table">
	<?php echo $module->getShazamTable(); ?>
    <div class="btn-group">
        <button type="button" class="btn btn-sm btn-primaryrc dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span>
            Add Shazam Field <span class="caret"></span>
        </button>
        <div class="dropdown-menu add-shazam">
            <div class="dropdown-header">
                Select an unused descriptive field below:
            </div>
            <div class="dropdown-divider"></div>
            <?php echo $module->getAddShazamOptions() ?>
        </div>
    </div>
    <div class="btn-group">
        <button type="button" class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span>
            Recover Previously Saved Version <span class="caret"></span>
        </button>
        <div class="dropdown-menu previous-shazam">
            <div class="dropdown-header">
                Select from the following previously saved versions of your Shazam config:
            </div>
            <div class="dropdown-divider"></div>
            <?php echo $module->getPreviousShazamOptions() ?>
        </div>
    </div>
    <?php
        if($module->getProjectSetting('enable-add-user-javascript-permissions') && $user->isSuperUser()) {
    ?>
    <div class="btn-group">
        <button type="button"  class="grant-permission btn btn-sm btn-warning"
                aria-haspopup="true" aria-expanded="false" data-toggle="modal" data-target="#addUserModal">Grant Javascript Permissions</button>
    </div>
    <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Select User to Grant Javascript
                        Permissions</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Note that the user has to be added to the project before this step can be completed</p>
                    <select class="custom-select">
                        <?php echo($module->getUserOptions()); ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" id="add-user-js" class="btn btn-primary">Add</button>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-5 js-users">
        <h5>Users with JS editing permissions</h5>
        <hr>
        <table class=" w-50 table table-striped table-bordered table-condensed" cellspacing="0">
            <thead>
                <tr>
                    <td><strong>Username</strong></td>
                    <td><strong>Remove</strong></td>
                </tr>
            </thead>
            <tbody>
                <?php echo($module->renderJSTable()); ?>
            </tbody>
        </table>
    </div>
    <?php
        }
    ?>
</div>

<form id="action-form" name="action" class="hidden" method="POST"></form>

<script src="<?php echo $module->getUrl('js/config.js'); ?>"></script>

<script>
    Shazam.prepareTable();
</script>

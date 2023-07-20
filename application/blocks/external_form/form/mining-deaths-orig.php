<? 

// mysqli_connect('localhost', 'uopla1xdfjtk7', 'zcrcvvhrhnqt');
$mysqli = new mysqli('localhost', 'uopla1xdfjtk7', 'zcrcvvhrhnqt');
mysqli_select_db($mysqli,'dbznnv6t8w40et');

function MySQLNow($Y, $M, $D, $h, $m, $s, $mode) {
    if ($mode == "G") {
    return date("Y") . "-" . date("m") . "-" . date("d") . " " . date("H") . ":" . date("i") . ":" . date("s");
    } elseif ($mode == "C") {
    if (strlen($Y) != 4) {
        $Y = date("Y");
    }

    return $Y . "-" . StringZeroFill($M, A, 2) . "-" . StringZeroFill($D, A, 2) . " " . StringZeroFill($h, A, 2) . ":" . StringZeroFill($m, A, 2) . ":" . StringZeroFill($s, A, 2);
    }
}

function DropDownCurrentDate($MySQL_NOW, $r, $arr) {
    if (strlen($MySQL_NOW)) {
    $parts = explode(' ', $MySQL_NOW);
    $YMD = explode('-', $parts["0"]);
    }

    $Y = DropDownYears($YMD["0"], $r, $arr);
    $M = DropDownMonths($YMD["1"], $arr);
    $D = DropDownDays($YMD["2"], $arr);
    return $M . $D . $Y;
}

function DropDownYears($s, $r, $arr) {
    if (!$r) {
    $o[date('Y')] = date('Y');
    } else {
    for ($i = date('Y'); $i < date('Y') + $r; $i++) {
        $o[$i] = date('Y', mktime('1', '1', '1', date('m'), date('d'), $i));
    }
    }

    if (!strlen($s)) {
    $s = date('Y');
    }

    $arr[name] = "YEAR_" . $arr[name];
    return HTMLFormSelect($o, $s, $arr);
}

function DropDownMonths($s, $arr) {
    for ($i = 1; $i < 13; $i++) {
    $o[date('m', mktime('1', '1', '1', $i, date('d'), date('Y')))] = date('F', mktime('1', '1', '1', $i, date('d'), date('Y')));
    }

    if (!strlen($s)) {
    $s = date(m);
    }

    $arr[name] = "MONTH_" . $arr[name];
    return HTMLFormSelect($o, $s, $arr);
}

function DropDownDays($s, $arr) {
    for ($i = 1; $i <= 31; $i++) {
    $o[date('d', mktime('1', '1', '1', 12, $i, date('Y')))] = date('jS', mktime('1', '1', '1', 12, $i, date('Y')));
    }

    if (!strlen($s)) {
    $s = date(d);
    }

    $arr[name] = "DAY_" . $arr[name];
    return HTMLFormSelect($o, $s, $arr);
}

###################################################
## User Input Sterilization Functions #############
###################################################

function CleanUserInput($i, $type, $swp) {
    if ($type === "A") { // alphanumeric with spaces
    $i = preg_replace('/[^\w\s]/', '', $i);
    } elseif ($type === "N") { // numbers only with spaces
    $i = preg_replace('/[^\d\s]/', '', $i);
    } elseif ($type === "L") { // letters only with spaces
    $i = preg_replace('/[^A-Za-z\s]/', '', $i);
    }

    if ($swp === "E") { // excessive whitespace
    $i = preg_replace('/[\s]{2,}/', ' ', $i);
    } elseif ($swp === "A") { // all whitespace
    $i = preg_replace('/[\s]/', '', $i);
    }

    return trim($i);
}

###################################################
## Generic Page Generation Functions ##############
###################################################

function FormatPhoneNumber($input) {
    $output = ZeroPrepend(CleanUserInput($input, 'N', 'A'), 10);
    return "(" . substr($output, 0, 3) . ") " . substr($output, 3, 3) . "-" . substr($output, 6, 4);
}

function ZeroPrepend($input, $length) {
    $cur_length = strlen($input);

    for ($i = $cur_length; $i < $length; $i++) {
    $input = "0" . $input;
    }

    return $input;
}

function TableDropdown($mysqli, $q, $f1, $f2, $s, $arr) {
    
    $r = MysqlFetchAssoc($mysqli, $q);
    $cnt = 0;
    while (list($k, $v) = each($r)) {
    $o[$r[$cnt][$f1]] = $r[$cnt][$f2];
    $cnt++;
    } return HTMLFormSelect($o, $s, $arr);
}

function YesNoFormSelect($ao = array(), $s, $arr) {
    $o = array(
    '0' => 'Please Select',
    'Y' => 'Yes',
    'N' => 'No'
    );

    $o = array_merge($o, $ao);

    return HTMLFormSelect($o, $s, $arr);
}

function ConvertDelimitedString($delimiter, $string) {
    $array = explode($delimiter, $string);

    while (list($k, $v) = each($array)) {
    $output[$v] = $v;
    }

    return $output;
}

function number_range_dropdown($start, $end, $s, $arr) {

    for ($i = $start; $i <= $end; $i++) {
    $o[$i] = $i;
    }

    return HTMLFormSelect($o, $s, $arr);
}

###################################################
## HTML Form Generation Functions #################
###################################################

function HTMLChooseInput($o, $s, $v, $arr, $type) {
    if ($type == 0) {
    return $v;
    } elseif ($type == 1) {
    return HTMLFormGenericInput($arr);
    } elseif ($type == 2) {
    return HTMLFormTextArea($v, $arr);
    } elseif ($type == 3) {
    return HTMLFormSelect($o, $s, $arr);
    }
}

function HTMLFormTextArea($v, $arr) {
    return HTMLPutIn('textarea', $v, $arr);
}

function HTMLFormGenericInput($arr) {
    return "<input" . HTMLArrAssembly($arr) . ">";
}

function HTMLFormSelect($o, $s, $arr = array()) {
    while (list($key, $val) = each($o)) {
    if (is_array($val)) {
        while (list($key2, $val2) = each($val)) {
        if ($key2 == $s) {
            $args['selected'] = "SELECTED";
        }
        $args['value'] = stripslashes($key2);
        $form_options .= HTMLPutIn('option', $val2, $args);
        unset($args);
        }
    } else {
        if ($key == $s) {
        $args['selected'] = "SELECTED";
        }
        $args['value'] = $key;
        $form_options .= HTMLPutIn('option', $val, $args);
        unset($args);
    }
    } return HTMLPutIn('select', $form_options, $arr);
}

function HTMLFormSelectFromQuery($mysqli, $q, $f1, $f2, $s, $arr = array(), $initial = array()) {
    
    $o = array();

    if (sizeof($initial)) {
    $o = array_merge($initial, $o);
    }

    foreach (MysqlFetchAssoc($mysqli, $q) as $row) {
    $o[$row[$f1]] = $row[$f2];
    }

    return HTMLFormSelect($o, $s, $arr);
}

function HTMLFormCheckboxes($o, $s, $arr) {
    while (list($k, $v) = each($o)) {

    if (in_array($k, $s)) {
        $checked = " checked";
    } else {
        $checked = "";
    }

    $boxes .= "<li><input type=\"checkbox\" value=\"" . $k . "\"" . $checked . " " . HTMLArrAssembly($arr) . " />" . stripslashes($v) . "</li>";
    }

    return "<ul id=\"" . $arr['id'] . "\" class=\"" . $arr['class'] . "\">" . $boxes . "</ul>";
}

function HTMLFormRadioButtons($o, $s, $arr) {
    while (list($k, $v) = each($o)) {
    if ($k == $s) {
        $checked = " checked";
    } else {
        $checked = "";
    }

    $radio .= "<li><input type=\"radio\" value=\"" . $k . "\"" . $checked . " " . HTMLArrAssembly($arr) . " />" . $v . "</li>";
    }

    return "<ul " . HTMLArrAssembly($arr) . ">" . $radio . "</ul>";
}

function HTMLPutIn($Tag, $Data, $arr = array()) {
    return "<" . $Tag . HTMLArrAssembly($arr) . ">" . $Data . "</" . $Tag . ">";
}

function HTMLArrAssembly($arr = array()) {
    while (@list($k, $v) = @each($arr)) {
    $data[] = $k . "=\"" . $v . "\"";
    }

    if ($Data = @implode(" ", $data)) {
    return " " . $Data;
    }
}

function HTMLGenericFormTable($FormTable) {
    for ($i = 0; $i < sizeof($FormTable); $i++) {
    if (strlen($FormTable[$i][input][type])) {
        $C1Arr = array(align => right, valign => top);
        $C1 = HTMLPutIn(td, $FormTable[$i][text], $C1Arr);
        $C2 = HTMLPutIn(td, HTMLChooseInput($FormTable[$i][input][o], $FormTable[$i][input][s], $FormTable[$i][input][v], $FormTable[$i][input][arr], $FormTable[$i][input][type]), $NA);
        $Data .= HTMLPutIn(tr, $C1 . $C2, $NA);
    }
    }

    $SB = array(
    type => submit,
    name => submit,
    value => $FormTable['submit']
    );

    $SBArr = array(
    colspan => 2,
    align => right
    );

    $Data .= HTMLPutIn(tr, HTMLPutIn(td, HTMLFormGenericInput($SB), $SBArr), $NA);
    $Data = HTMLPutIn(table, $Data, $FormTable['options']);
    return HTMLPutIn(form, $Data, $FormTable['form']);
}

###################################################
## MySQL Functions ################################
###################################################

function MysqlAddRow($t, $arr) {
    while (list($k, $v) = each($arr)) {
    $fn[] = $k;
    $fv[] = "'" . $v . "'";
    }

    $fn = implode(",", $fn);
    $fv = implode(",", $fv);
    $q = ("insert into $t ($fn) values ($fv)");
    mysqli_query($mysqli,$q) or die(mysqli_error());
    return mysql_insert_id();
}

function MysqlUpdateRow($t, $p, $i, $arr) {
    while (list($k, $v) = each($arr)) {
    if ($k != $p && $k != "submit") {
        $tmpsql[] = $k . " = '" . $v . "'";
    }
    }

    $values = implode(", ", $tmpsql);

    $q = ("update $t set $values where $p = '$i'");
    mysqli_query($mysqli,$q) or die(mysqli_error());
}

function MysqlDeleteRow($t, $p, $i) {
    $q = ("delete from $t where $p = '$i'");
    mysqli_query($mysqli,$q) or die(mysqli_error());
}

function MysqlFetchAssoc($mysqli, $q) {
    
    $cnt = 0;
    $r = mysqli_query($mysqli,$q) or die(mysqli_error());
    while ($row = mysqli_fetch_assoc($r)) {
    while (list($k, $v) = each($row)) {
        $v = ucfirst($v);
        $data[$cnt][$k] = $v;
    } $cnt++;
    }

    if (sizeof($data)) {
    return $data;
    } else {
    return 0;
    }
}

function DatabaseTableExists($mysqli, $db, $table, $primary, $create) {

    $query = MysqlFetchAssoc($mysqli, "show tables");

    $TableExists = 0;

    for ($i = 0; $i < sizeof($query); $i++) {
    if (in_array($table, $query[$i])) {
        $TableExists = 1;
    }
    }

    if (!$TableExists) {
    if ($create == "Y") {
        $q = ("CREATE TABLE $db.$table ( $primary INT NOT NULL AUTO_INCREMENT PRIMARY KEY ) ENGINE = MYISAM");
        mysqli_query($mysqli,$q);
    } else {
        return 0;
    }
    } else {
    return 1;
    }
}

function DatabaseFieldExists($mysqli, $db, $table, $field, $create) {

    $query = MysqlFetchAssoc($mysqli, "SHOW COLUMNS FROM $table");

    $FieldExists = 0;

    for ($i = 0; $i < sizeof($query); $i++) {
    if ($query[$i][Field] == $field) {
        $FieldExists = 1;
    }
    }

    if (!$FieldExists) {
    if ($create == "Y") {
        $q = ("ALTER TABLE $table ADD $field TINYTEXT NOT NULL");
        mysqli_query($mysqli,$q) or die(mysqli_error());
    } else {
        return 0;
    }
    } else {
    return 1;
    }
}

function pre_filename() {
    return substr(md5(time() . rand()), 8, 8) . date('-Y-m-d-H-i-s');
}


$months = array(
  "0"=>"Any",
  "Jan"=>"Jan",
  "Feb"=>"Feb",
  "Mar"=>"Mar",
  "Apr"=>"Apr",
  "May"=>"May",
  "Jun"=>"Jun",
  "Jul"=>"Jul",
  "Aug"=>"Aug",
  "Sep"=>"Sep",
  "Oct"=>"Oct",
  "Nov"=>"Nov",
  "Dec"=>"Dec"
);
  
?>
<style type="text/css">
<!--
#search_form table td {
  padding-bottom:10px;
}
-->
</style>


<fieldset style="padding:20px;margin-top:40px;">
    <legend>Search Form</legend>
    <form id="search_form">
  <table style="width: 100%; color:black;" border="0" cellspacing="0" cellpadding="4">
            <tr>
                <td>Year: </td>
                <td><?php echo HTMLFormSelectFromQuery($mysqli,"select distinct Year from mine_accidents order by Year", "Year", "Year", NULL, array("name" => "Year"), array('0' => "Any")); ?></td>
                <td>Month:</td>
                <td><?php echo HTMLFormSelect($months, NULL, array("name" => "Month")); ?></td>
            <td>Day And Month:</td>
                <td><?php echo HTMLFormSelectFromQuery($mysqli,"select distinct Day_And_Month from mine_accidents order by Day_And_Month", "Day_And_Month", "Day_And_Month", NULL, array("name" => "Day_And_Month"), array('0' => "Any")); ?></td>
            </tr>

            <tr>
                <td>Name Keyword</td>
                <td><input type="text" autocomplete="off" name="Name" id="Name" size="30" style="width:150px;" /></td>
                <td>Occupation: </td>
                <td id="Occupation_Select"><?php echo HTMLFormSelectFromQuery($mysqli,"select distinct Occupation from mine_accidents order by Occupation", "Occupation", "Occupation", NULL, array("name" => "Occupation"), array('0' => "Any")); ?></td>
                <td>Mine:</td>
                <td id="Mine_Select"><?php echo HTMLFormSelectFromQuery($mysqli,"select distinct Mine from mine_accidents order by Mine", "Mine", "Mine", NULL, array("name" => "Mine"), array('0' => "Any")); ?></td>
            </tr>

            <tr>
                <td colspan="4"> </td>
            </tr>

            <tr>
                <td colspan="4" style="text-align:center;"><input style="font-size:16px;padding:3px 10px 3px 10px;" type="submit" name="search" value="Search the Database!" /></td>
            </tr>
  </table>
    </form>
</fieldset>  

<div id="search_result" style="padding:30px 0px 50px 0px;width: 100%; overflow: hidden; clear: both; position: relative"></div>

<script lang="javascript">
    $(function() {
  $('#search_form').live('submit', function() {
      $.post('/online-resources/mine-deaths-and-accidents/search-results', $('#search_form').serialize(), function(data) {
    $('#search_result').html(data);
      });

      return false;
  });
    });
</script>
<?
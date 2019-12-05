<?php
/**
 * weno rx search.
 *
 * @package   OpenEMR
 * @link      https://www.open-emr.org
 * @author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2016-2017 Sherwin Gaddis <sherwingaddis@gmail.com>
 * @copyright Copyright (c) 2018 Brady Miller <brady.g.miller@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */


require_once('../../interface/globals.php');

use OpenEMR\Common\Csrf\CsrfUtils;

if (!CsrfUtils::verifyCsrfToken($_GET["csrf_token_form"])) {
    CsrfUtils::csrfNotVerified();
}

if (isset($_GET['term'])) {
    $return_arr = array();
    $term    = filter_input(INPUT_GET, "term");

    //$sql = "SELECT full_name, rxn_dose_form, route, strength FROM erx_weno_drugs WHERE full_name LIKE ? ";
    $sql = "SELECT * FROM list_options WHERE list_id = 'recommendations' AND title LIKE ? AND activity = 1";
    $val = array($term.'%');
    $res = sqlStatement($sql, $val);
    while ($row = sqlFetchArray($res)) {
        if ($row['title'] == '') {
            //$arr[$row['option_id']] = ' ';
            $arr[] = ' ';
        } else {
            //$arr[$row['option_id']] = xl_list_label($row['title']);
            $arr[] = xl_list_label($row['title']);
        }
    }

    /* Toss back results as json encoded array. */
    echo json_encode($arr);
}

<?php
/**
 * Patient Portal
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Jerry Padgett <sjpadgett@gmail.com>
 * @copyright Copyright (c) 2016-2019 Jerry Padgett <sjpadgett@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

$ignoreAuth = true;
require_once("../../../interface/globals.php");

$data = (array)(json_decode(file_get_contents("php://input")));
$pid = $data['pid'];
$user = $data['user'];
$signer = $data['signer'];
$type = $data['type'];
$output = urldecode($data['output']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($type == 'admin-signature') {
        $signer = $user;
    }

    $image_data = $output;

    $sig_hash = sha1($output);
    $created = time();
    $ip = $_SERVER['REMOTE_ADDR'];
    $status = 'filed';
    $lastmod = date('Y-m-d H:i:s');
    $r = sqlStatement("SELECT COUNT( DISTINCT TYPE ) x FROM onsite_signatures where pid = ? and user = ? ", array($pid, $user));
    $c = sqlFetchArray($r);
    $isit = $c['x'] * 1;
    if ($isit) {
        $qstr = "UPDATE onsite_signatures SET pid=?,lastmod=?,status=?, user=?, signature=?, sig_hash=?, ip=?,sig_image=? WHERE pid=? && user=?";
        $rcnt = sqlStatement($qstr, array($pid, $lastmod, $status, $user, $svgsig, $sig_hash, $ip, $image_data, $pid, $user));
    } else {
        $qstr = "INSERT INTO onsite_signatures (pid,lastmod,status,type,user,signator, signature, sig_hash, ip, created, sig_image) VALUES (?,?,?,?,?,?,?,?,?,?,?) ";
        sqlStatement($qstr, array($pid, $lastmod, $status, $type, $user, $signer, $svgsig, $sig_hash, $ip, $created, $image_data));
    }

    echo json_encode('Done');
}

<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');

require_once '../../includes/functions.php';
chkSession();

if (
  $user_id_2 == 1 ||
  $user_level_2 == 'superadmin' ||
  $user_level_2 == 'subadmin' ||
  $user_level_2 == 'administrator' ||
  $user_level_2 == 'reseller' ||
  $user_level_2 == 'subreseller'
) {
  // ok
} else {
  echo '<script>alert("Sorry! You dont have Permission to Access this Page!...");</script>';
  $db->RedirectToURL($db->base_url());
  exit;
}

if (isset($_POST['submitted'])) {

  if (
    (!isset($_POST['qty']) && !isset($_POST['voucher_secret']) && !isset($_POST['voucher_code'])) ||
    empty($_POST['qty']) || empty($_POST['voucher_secret']) || empty($_POST['voucher_code'])
  ) {
    $errors = 'Sorry! the transaction is inavalid!..';
    $response = 0;
  } else {

    $values = array();

    $get_secret = $db->encryptor('decrypt', $_POST['voucher_secret']); // username
    $get_secret = $db->encryptor('decrypt', $get_secret);

    $get_code = $db->encryptor('decrypt', $_POST['voucher_code']); // user_id
    $get_code = $db->encryptor('decrypt', $get_code);

    $qty = $db->Sanitize($_POST['qty']);
    $qtyInt = (int)$qty;

    // ===== FIX: regla de duración por créditos =====
    // 1 => 7 días
    // 2 => 15 días
    // 3 => 30 días
    // 6 => 60 días
    // 9 => 90 días
    // ... cada 3 créditos = +30 días
    if ($qtyInt < 1) {
      $db->HandleError('Invalid Duration!');
      $response = 2;
      echo json_encode(['response' => $response, 'message' => $db->GetErrorMessage()]);
      exit;
    }

    if ($qtyInt === 1) {
      $d_time = 7 * 86400;
    } elseif ($qtyInt === 2) {
      $d_time = 15 * 86400;
    } else {
      if ($qtyInt % 3 !== 0) {
        $db->HandleError('Invalid Duration!');
        $response = 2;
        echo json_encode(['response' => $response, 'message' => $db->GetErrorMessage()]);
        exit;
      }
      $months = (int)($qtyInt / 3);      // 3->1 mes, 6->2, 9->3...
      $d_time = $months * 30 * 86400;    // 30 días por mes
    }
    // ===== FIN FIX =====

    $category = $db->encryptor('decrypt', $_POST['category']); // premium/vip/private/role
    $sscode = rand(0, 65535);
    $ss_id = $sscode;

    if ($user_id_2 == 1 || $user_level_2 == 'superadmin') {
      $chk_qry = "user_id!=1 AND user_id='" . $db->SanitizeForSQL($get_code) . "' AND user_name='" . $db->SanitizeForSQL($get_secret) . "' LIMIT 1";
    } elseif ($user_level_2 == 'subadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller') {
      $chk_qry = "user_id!=1 AND user_id='" . $db->SanitizeForSQL($get_code) . "' AND user_name='" . $db->SanitizeForSQL($get_secret) . "' AND upline='" . $user_id_2 . "'";
    } else {
      $chk_qry = "user_id!=1 AND user_id='" . $db->SanitizeForSQL($get_code) . "' AND user_name='" . $db->SanitizeForSQL($get_secret) . "' AND upline='" . $user_id_2 . "'";
    }

    $qry = $db->sql_query("SELECT user_id, user_name, credits, is_groupname FROM users WHERE " . $chk_qry) or die();
    $row = $db->sql_fetchrow($qry);

    $qry2 = $db->sql_query("SELECT user_id, user_name, credits FROM users WHERE user_id=" . (int)$user_id_2) or die();
    $row2 = $db->sql_fetchrow($qry2);

    $uid = $row['user_id'];                 // client
    $client_uname = $row['user_name'];
    $is_group = $row['is_groupname'];
    $uname = $row2['user_name'];            // reseller

    // duración "id" solo para nombre/logs, NO para tocar $d_time
    $duration = '';
    $typez = 0;

    if ($category == 'premium') {
      $duration = 36;
      $typez = 1;
      $status = 'Premium';
    } elseif ($category == 'vip') {
      $duration = 34;
      $status = 'VIP';
    } elseif ($category == 'private') {
      $duration = 33;
      $status = 'Private';
    } elseif ($category == 'role') {
      $duration = 36;
      $typez = 2;
      $status = 'Role';
    } else {
      echo '<script> alert("Invalid Transaction"); location.assign("' . $db->base_url() . '404")</script>';
      $response = 0;
      exit;
    }

    // leer nombre (opcional), pero NO sobrescribir $d_time
    $d_qry = $db->sql_query("SELECT * FROM duration WHERE id = '" . $db->SanitizeForSQL($duration) . "'");
    $d_row = $db->sql_fetchrow($d_qry);
    $d_name = $d_row['duration_name'];

    // Validación mínima
    if ($d_time <= 0) {
      $db->HandleError('Invalid Duration!');
      $response = 2;
      echo json_encode(['response' => $response, 'message' => $db->GetErrorMessage()]);
      exit;
    }

    $code1 = ran_code();
    $code2 = ran_code();
    $code3 = ran_code();
    $gen = $code2 . '-' . $code1 . '-' . $code3;

    $result = $db->sql_query("SELECT code_name FROM vouchers WHERE code_name='" . $gen . "'");
    $chk = $db->sql_fetchrow($result);

    $duration_chk = $db->sql_query("SELECT role_duration, duration, vip_duration, private_duration FROM users WHERE user_id='" . $db->SanitizeForSQL($uid) . "'");
    $rows = $db->sql_fetchrow($duration_chk);
    $dura = $rows['role_duration'];
    $dura1 = $rows['duration'];
    $dura2 = $rows['vip_duration'];
    $dura3 = $rows['private_duration'];

    if ($chk != 1) {

      if (
        ($credits_2 == 0 && $user_id_2 != 1 && $user_level_2 != 'superadmin') ||
        ($credits_2 < $qtyInt && $user_id_2 != 1 && $user_level_2 != 'superadmin')
      ) {
        $db->HandleError("Sorry! You don't have enough Credits!");
        $response = 2;
      //} elseif ($typez == 1 && $dura1 > 432000) {
      //  $db->HandleError("Sorry! You can only extend accounts with below 5 days remaining duration!");
      //  $response = 2;
      } elseif ($qtyInt > 0) {

        // ================= SUPERADMIN / ADMIN =================
        if ($user_id_2 == 1 || $user_level_2 == 'superadmin') {

          $insert = "INSERT INTO vouchers
            (code_name,user_id,client_name,reseller_id,reseller_name,is_qty,is_used,duration,gen_date,date_used,category)
            VALUES
            ('" . $db->SanitizeForSQL($gen) . "',
            '" . $db->SanitizeForSQL($uid) . "',
            '" . $db->SanitizeForSQL($client_uname) . "',
            '" . $db->SanitizeForSQL($user_id_2) . "',
            '" . $db->SanitizeForSQL($uname) . "',
            '" . $qtyInt . "',
            1,
            '" . $db->SanitizeForSQL($d_time) . "',
            '" . date('Y-m-d H:i:s') . "',
            '" . date('Y-m-d H:i:s') . "',
            '" . $db->SanitizeForSQL($category) . "')";

          if ($db->sql_query($insert)) {

            $db->sql_query("INSERT INTO voucher_logs
              (code_name,user_id,client_name,reseller_id,reseller_name,is_qty,is_used,date_used,is_date,category)
              VALUES
              ('" . $db->SanitizeForSQL($gen) . "',
              '" . $db->SanitizeForSQL($uid) . "',
              '" . $db->SanitizeForSQL($client_uname) . "',
              '" . $db->SanitizeForSQL($user_id_2) . "',
              '" . $db->SanitizeForSQL($uname) . "',
              '" . $qtyInt . "',
              1,
              '" . date('Y-m-d H:i:s') . "',
              '" . date('Y-m-d') . "',
              '" . $db->SanitizeForSQL($category) . "')");

            if ($category == 'premium') {
              if ($is_group == 'free') {
                $db->sql_query("UPDATE users SET is_groupname='normal', duration=duration+'" . $d_time . "'
                  WHERE user_id='" . $db->SanitizeForSQL($uid) . "'");
              } else {
                $db->sql_query("UPDATE users SET duration=duration+'" . $d_time . "'
                  WHERE user_id='" . $db->SanitizeForSQL($uid) . "'");
              }
            } elseif ($category == 'vip') {
              $ss_id = rand(0, 65535);
              if ($is_group == 'free') {
                $db->sql_query("UPDATE users SET is_groupname='normal', ss_id='" . $ss_id . "', is_vip=1, vip_duration=vip_duration+'" . $d_time . "'
                  WHERE user_id='" . $db->SanitizeForSQL($uid) . "'");
              } else {
                $db->sql_query("UPDATE users SET ss_id='" . $ss_id . "', is_vip=1, vip_duration=vip_duration+'" . $d_time . "'
                  WHERE user_id='" . $db->SanitizeForSQL($uid) . "'");
              }
            } elseif ($category == 'private') {
              $ss_id = rand(0, 65535);
              if ($is_group == 'free') {
                $db->sql_query("UPDATE users SET is_groupname='normal', ss_id='" . $ss_id . "', is_private=1, private_duration=private_duration+'" . $d_time . "'
                  WHERE user_id='" . $db->SanitizeForSQL($uid) . "'");
              } else {
                $db->sql_query("UPDATE users SET ss_id='" . $ss_id . "', is_private=1, private_duration=private_duration+'" . $d_time . "'
                  WHERE user_id='" . $db->SanitizeForSQL($uid) . "'");
              }
            } elseif ($category == 'role') {
              $ss_id = rand(0, 65535);
              $db->sql_query("UPDATE users SET ss_id='" . $ss_id . "', role_duration=role_duration+'" . $d_time . "'
                WHERE user_id='" . $db->SanitizeForSQL($uid) . "'");
            }

            $db->HandleSuccess('Duration added successfully to ' . $client_uname . '');
            $response = 1;
          } else {
            $db->HandleError('Adding duration failed!');
            $response = 2;
          }

        // ================= RESELLER / SUBADMIN / ADMIN =================
        } else if ($user_level_2 == 'administrator' || $user_level_2 == 'subadmin' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller') {

          $insert = "INSERT INTO vouchers
            (code_name,user_id,client_name,reseller_id,reseller_name,is_qty,is_used,duration,gen_date,date_used,category)
            VALUES
            ('" . $db->SanitizeForSQL($gen) . "',
            '" . $db->SanitizeForSQL($uid) . "',
            '" . $db->SanitizeForSQL($client_uname) . "',
            '" . $db->SanitizeForSQL($user_id_2) . "',
            '" . $db->SanitizeForSQL($uname) . "',
            '" . $qtyInt . "',
            1,
            '" . $db->SanitizeForSQL($d_time) . "',
            '" . date('Y-m-d H:i:s') . "',
            '" . date('Y-m-d H:i:s') . "',
            '" . $db->SanitizeForSQL($category) . "')";

          if ($db->sql_query($insert)) {

            $db->sql_query("INSERT INTO voucher_logs
              (code_name,user_id,client_name,reseller_id,reseller_name,is_qty,is_used,date_used,is_date,category)
              VALUES
              ('" . $db->SanitizeForSQL($gen) . "',
              '" . $db->SanitizeForSQL($uid) . "',
              '" . $db->SanitizeForSQL($client_uname) . "',
              '" . $db->SanitizeForSQL($user_id_2) . "',
              '" . $db->SanitizeForSQL($uname) . "',
              '" . $qtyInt . "',
              1,
              '" . date('Y-m-d H:i:s') . "',
              '" . date('Y-m-d') . "',
              '" . $db->SanitizeForSQL($category) . "')");

            if ($category == 'premium') {
              if ($is_group == 'free') {
                $db->sql_query("UPDATE users SET is_groupname='normal', duration=duration+'" . $d_time . "'
                  WHERE user_id='" . $db->SanitizeForSQL($uid) . "'");
              } else {
                $db->sql_query("UPDATE users SET duration=duration+'" . $d_time . "'
                  WHERE user_id='" . $db->SanitizeForSQL($uid) . "'");
              }
              // descuento de créditos
              $db->sql_query("UPDATE users SET credits=credits-'" . $qtyInt . "'
                WHERE user_id='" . $db->SanitizeForSQL($user_id_2) . "'");

            } elseif ($category == 'vip') {
              if ($is_group == 'free') {
                $db->sql_query("UPDATE users SET is_groupname='normal', is_vip=1, vip_duration=vip_duration+'" . $d_time . "'
                  WHERE user_id='" . $db->SanitizeForSQL($uid) . "'");
              } else {
                $db->sql_query("UPDATE users SET is_vip=1, vip_duration=vip_duration+'" . $d_time . "'
                  WHERE user_id='" . $db->SanitizeForSQL($uid) . "'");
              }
              $db->sql_query("UPDATE users SET credits=credits-'" . $qtyInt . "'
                WHERE user_id='" . $db->SanitizeForSQL($user_id_2) . "'");

            } elseif ($category == 'private') {
              if ($is_group == 'free') {
                $db->sql_query("UPDATE users SET is_groupname='normal', is_private=1, private_duration=private_duration+'" . $d_time . "'
                  WHERE user_id='" . $db->SanitizeForSQL($uid) . "'");
              } else {
                $db->sql_query("UPDATE users SET is_private=1, private_duration=private_duration+'" . $d_time . "'
                  WHERE user_id='" . $db->SanitizeForSQL($uid) . "'");
              }
              $db->sql_query("UPDATE users SET credits=credits-'" . $qtyInt . "'
                WHERE user_id='" . $db->SanitizeForSQL($user_id_2) . "'");

            } elseif ($category == 'role') {
              $ss_id = rand(0, 65535);
              $db->sql_query("UPDATE users SET ss_id='" . $ss_id . "', role_duration=role_duration+'" . $d_time . "'
                WHERE user_id='" . $db->SanitizeForSQL($uid) . "'");
              // descuento de créditos también para role (si quieres)
              $db->sql_query("UPDATE users SET credits=credits-'" . $qtyInt . "'
                WHERE user_id='" . $db->SanitizeForSQL($user_id_2) . "'");
            }

            $db->HandleSuccess('Duration added successfully to ' . $client_uname . '');
            $response = 1;

          } else {
            $db->HandleError('Adding duration failed!');
            $response = 2;
          }

        } else {
          $db->HandleError("Sorry Invalid Authorization!");
          $response = 0;
        }

      } else {
        $db->HandleError('Invalid Quantity!');
        $response = 2;
      }

    } else {
      $db->HandleError('Invalid Generate!');
      $response = 2;
    }

    if ($response == 1) {
      $values['response'] = $response;
      $values['message'] = $db->GetSuccessMessage();
    } elseif ($response == 2) {
      $values['response'] = $response;
      $values['message'] = $db->GetErrorMessage();
    } else {
      $values['response'] = $response;
    }

    echo json_encode($values);
  }

} else {

  if (empty($_POST['qty'])) {
    $db->RedirectToURL($db->base_url());
    exit;
  }

  if (empty($_POST['duration_secret'])) {
    $db->RedirectToURL($db->base_url());
    exit;
  }

  if (empty($_POST['duration_code'])) {
    $db->RedirectToURL($db->base_url());
    exit;
  }

  if (
    $user_id_2 == 1 ||
    $user_level_2 == 'superadmin' ||
    $user_level_2 == 'subadmin' ||
    $user_level_2 == 'administrator' ||
    $user_level_2 == 'reseller' ||
    $user_level_2 == 'subreseller'
  ) {
    // ok
  } else {
    $db->RedirectToURL($db->base_url());
    exit;
  }
}
?>
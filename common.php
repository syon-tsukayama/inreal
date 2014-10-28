<?php
/**
 * 共通機能
 */

// 設定ファイル読み込み
require_once('./config.php');

/**
* データベース接続処理
*/
function connect_database()
{
    global $_dsn;
    global $_db_user;
    global $_db_password;

    $conn = null;

    // データベース接続失敗時に「例外」が発生するので、
    // 「例外」発生時にエラーメッセージを表示する
    try
    {
        // データベース接続実施
        $conn = new PDO($_dsn, $_db_user, $_db_password);
    }
    catch(PDOException $e)
    {
        throw $e;
    }

    return $conn;
}

/**
 * ファイルパスデータの件数確認
 *
 * @param object
 * @param string
 * @param string
 *
 * @return mixed
 */
function count_data_file_path($conn, $dir_name, $file_name)
{
    $return_value = false;

    if(is_object($conn))
    {
        $dir_name = trim(rtrim(trim($dir_name), DS));
        $file_name = trim(rtrim(trim($file_name), DS));

        if(!empty($dir_name) && !empty($file_name))
        {
            $query =<<<EOS
SELECT COUNT(*) FROM `motion_histories`
WHERE `dir_name` = :dir_name AND `file_name` = :file_name;
EOS
;
            $stmt = $conn->prepare($query);

            $stmt->bindValue(':dir_name', $dir_name, PDO::PARAM_STR);
            $stmt->bindValue(':file_name', $file_name, PDO::PARAM_STR);

            if($stmt->execute())
            {
                $row = $stmt->fetch();

                if(isset($row['COUNT(*)']) && is_numeric($row['COUNT(*)']))
                {
                    $return_value = $row['COUNT(*)'];
                }
            }
        }
    }

    return $return_value;
}


/**
 * ファイルパスからid取得
 */
function get_id_file_path($conn, $dir_name, $file_name)
{
    $return_value = false;

    if(is_object($conn))
    {
        $dir_name = trim(rtrim(trim($dir_name), DS));
        $file_name = trim(rtrim(trim($file_name), DS));

        if(!empty($dir_name) && !empty($file_name))
        {
            $query =<<<EOS
SELECT `id` FROM `motion_histories`
WHERE `dir_name` = :dir_name AND `file_name` = :file_name;
EOS
;
            $stmt = $conn->prepare($query);

            $stmt->bindValue(':dir_name', $dir_name, PDO::PARAM_STR);
            $stmt->bindValue(':file_name', $file_name, PDO::PARAM_STR);

            if($stmt->execute())
            {
                $row = $stmt->fetch();

                if(isset($row['id']) && is_numeric($row['id']))
                {
                    $return_value = $row['id'];
                }
            }
        }
    }

    return $return_value;
}



function get_motion_history($conn, $id)
{
    $return_data = array();

    if(is_object($conn))
    {
        if(!empty($id) && is_numeric($id))
        {
            $query =<<<EOS
SELECT * FROM `motion_histories` WHERE `id` = :id;
EOS
;
            $stmt = $conn->prepare($query);

            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            if($stmt->execute())
            {
                $return_data = $stmt->fetch();
            }
        }
    }

    return $return_data;
}

/**
 * データ新規登録処理
 */
function insert_motion_history($conn, $motion_history, &$error_info)
{
    $return_value = false;

    if(is_object($conn))
    {
        $query =<<<EOS
INSERT INTO `motion_histories` (
    `dir_name`,
    `file_name`,
    `subject`,
    `grade_id`,
    `person_name`,
    `pose_id`,
    `record_date`,
    `exposure_time`,
    `speak_remark`,
    `part_name_1`,
    `motion_level_1`,
    `part_name_2`,
    `motion_level_2`,
    `part_name_3`,
    `motion_level_3`,
    `part_name_4`,
    `motion_level_4`,
    `motion_remark`,
    `motion_type_id`,
    `created`,
    `modified`
    )
VALUES (
    :dir_name,
    :file_name,
    :subject,
    :grade_id,
    :person_name,
    :pose_id,
    :record_date,
    :exposure_time,
    :speak_remark,
    :part_name_1,
    :motion_level_1,
    :part_name_2,
    :motion_level_2,
    :part_name_3,
    :motion_level_3,
    :part_name_4,
    :motion_level_4,
    :motion_remark,
    :motion_type_id,
    :created,
    :modified
    );
EOS
;
        $stmt = $conn->prepare($query);

        $stmt->bindValue(':dir_name',       $motion_history['dir_name'],       PDO::PARAM_STR);
        $stmt->bindValue(':file_name',      $motion_history['file_name'],      PDO::PARAM_STR);
        $stmt->bindValue(':subject',        $motion_history['subject'],        PDO::PARAM_STR);
        $stmt->bindValue(':grade_id',       $motion_history['grade_id'],       PDO::PARAM_INT);
        $stmt->bindValue(':person_name',    $motion_history['person_name'],    PDO::PARAM_STR);
        $stmt->bindValue(':pose_id',        $motion_history['pose_id'],        PDO::PARAM_INT);
        $stmt->bindValue(':record_date',    $motion_history['record_date'],    PDO::PARAM_STR);
        $stmt->bindValue(':exposure_time',  $motion_history['exposure_time'],  PDO::PARAM_INT);
        $stmt->bindValue(':speak_remark',   $motion_history['speak_remark'],   PDO::PARAM_STR);
        $stmt->bindValue(':part_name_1',    $motion_history['part_name_1'],    PDO::PARAM_STR);
        $stmt->bindValue(':motion_level_1', $motion_history['motion_level_1'], PDO::PARAM_INT);
        $stmt->bindValue(':part_name_2',    $motion_history['part_name_2'],    PDO::PARAM_STR);
        $stmt->bindValue(':motion_level_2', $motion_history['motion_level_2'], PDO::PARAM_INT);
        $stmt->bindValue(':part_name_3',    $motion_history['part_name_3'],    PDO::PARAM_STR);
        $stmt->bindValue(':motion_level_3', $motion_history['motion_level_3'], PDO::PARAM_INT);
        $stmt->bindValue(':part_name_4',    $motion_history['part_name_4'],    PDO::PARAM_STR);
        $stmt->bindValue(':motion_level_4', $motion_history['motion_level_4'], PDO::PARAM_INT);
        $stmt->bindValue(':motion_remark',  $motion_history['motion_remark'],  PDO::PARAM_STR);
        $stmt->bindValue(':motion_type_id', $motion_history['motion_type_id'], PDO::PARAM_INT);

        $datetime = date('Y-m-d H:i:s');
        $stmt->bindValue(':created',  $datetime,  PDO::PARAM_STR);
        $stmt->bindValue(':modified', $datetime,  PDO::PARAM_STR);

        if($stmt->execute())
        {
            $return_value = true;

            $error_info = array();
        }
        else
        {
            $error_info = $stmt->errorInfo();
        }
    }

    return $return_value;
}

/**
 * データ更新処理
 */
function update_motion_history($conn, $motion_history, &$error_info)
{
    $return_value = false;

    if(is_object($conn) && !empty($motion_history['id']) && is_numeric($motion_history['id']))
    {
        $query =<<<EOS
UPDATE `motion_histories` SET
    `subject` = :subject,
    `grade_id` = :grade_id,
    `person_name` = :person_name,
    `pose_id` = :pose_id,
    `record_date` = :record_date,
    `exposure_time` = :exposure_time,
    `speak_remark` = :speak_remark,
    `part_name_1` = :part_name_1,
    `motion_level_1` = :motion_level_1,
    `part_name_2` = :part_name_2,
    `motion_level_2` = :motion_level_2,
    `part_name_3` = :part_name_3,
    `motion_level_3` = :motion_level_3,
    `part_name_4` = :part_name_4,
    `motion_level_4` = :motion_level_4,
    `motion_remark` = :motion_remark,
    `motion_type_id` = :motion_type_id,
    `modified` = :modified
WHERE `id` = :id;
EOS
;
        $stmt = $conn->prepare($query);

        $stmt->bindValue(':subject',        $motion_history['subject'],        PDO::PARAM_STR);
        $stmt->bindValue(':grade_id',       $motion_history['grade_id'],       PDO::PARAM_INT);
        $stmt->bindValue(':person_name',    $motion_history['person_name'],    PDO::PARAM_STR);
        $stmt->bindValue(':pose_id',        $motion_history['pose_id'],        PDO::PARAM_INT);
        $stmt->bindValue(':record_date',    $motion_history['record_date'],    PDO::PARAM_STR);
        $stmt->bindValue(':exposure_time',  $motion_history['exposure_time'],  PDO::PARAM_INT);
        $stmt->bindValue(':speak_remark',   $motion_history['speak_remark'],   PDO::PARAM_STR);
        $stmt->bindValue(':part_name_1',    $motion_history['part_name_1'],    PDO::PARAM_STR);
        $stmt->bindValue(':motion_level_1', $motion_history['motion_level_1'], PDO::PARAM_INT);
        $stmt->bindValue(':part_name_2',    $motion_history['part_name_2'],    PDO::PARAM_STR);
        $stmt->bindValue(':motion_level_2', $motion_history['motion_level_2'], PDO::PARAM_INT);
        $stmt->bindValue(':part_name_3',    $motion_history['part_name_3'],    PDO::PARAM_STR);
        $stmt->bindValue(':motion_level_3', $motion_history['motion_level_3'], PDO::PARAM_INT);
        $stmt->bindValue(':part_name_4',    $motion_history['part_name_4'],    PDO::PARAM_STR);
        $stmt->bindValue(':motion_level_4', $motion_history['motion_level_4'], PDO::PARAM_INT);
        $stmt->bindValue(':motion_remark',  $motion_history['motion_remark'],  PDO::PARAM_STR);
        $stmt->bindValue(':motion_type_id', $motion_history['motion_type_id'], PDO::PARAM_INT);

        $datetime = date('Y-m-d H:i:s');
        $stmt->bindValue(':modified', $datetime,  PDO::PARAM_STR);

        $stmt->bindValue(':id', $motion_history['id'], PDO::PARAM_INT);

        if($stmt->execute())
        {
            $return_value = true;

            $error_info = array();
        }
        else
        {
            $error_info = $stmt->errorInfo();
        }
    }

    return $return_value;
}


/**
 * ディレクトリ内のファイル名・ディレクトリ名取得
 *
 * @param string
 * @return array
 */
function get_dir_lists()
{

}


/**
 * ディレクトリ内の画像ファイル名取得
 *
 * @param string
 *
 * @return array
 */
function get_img_lists($img_dir_url)
{
    $img_file_name_array = array();

    $img_dir_url = rtrim($img_dir_url, DS);

    // ディレクトリ内のファイル名取得
    $file_name_array = scandir($img_dir_url);

    if(is_array($file_name_array))
    {
        foreach($file_name_array as $file_name)
        {
            if(exif_imagetype($img_dir_url.DS.$file_name))
            {
                $img_file_name_array[] = $file_name;
            }
        }
    }

    return $img_file_name_array;
}


/**
 * アラートメッセージ表示処理
 *
 * @param string
 *
 * @return string
 */
function create_html_error_alert($message)
{
    $return_value = '';

    if(!empty($message))
    {
        $return_value =<<<EOS
<div class="alert alert-danger" role="alert">
    <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">Close</span>
    </button>
{$message}
</div>
EOS
;
    }

    return $return_value;
}

/**
 * アラートメッセージ表示処理
 *
 * @param string
 *
 * @return string
 */
function create_html_success_alert($message)
{
    $return_value = '';

    if(!empty($message))
    {
        $return_value =<<<EOS
<div class="alert alert-success" role="alert">
    <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">Close</span>
    </button>
{$message}
</div>
EOS
;
    }

    return $return_value;
}


/**
 * 配列からセレクトボックスの選択肢タグ作成
 *
 * @param array
 *
 * @return string
 */
function create_html_select_options($options, $selected_value = null)
{
    $return_value = '';

    foreach($options as $key => $display_value)
    {
        if(!is_null($selected_value) && $selected_value == $key)
        {
            $selected = ' selected';
        }
        else
        {
            $selected = '';
        }

        $return_value .=<<<EOS
<option value="{$key}"{$selected}>{$display_value}</option>
EOS
;
    }

    return $return_value;
}


/**
 * input.phpへのhref生成
 *
 * @param string
 * @param array
 *
 * @return string
 */
function create_html_href($file_path, $get_params = array())
{
    if(is_array($get_params))
    {
        $get_params_string = '';
        foreach($get_params as $key => $value)
        {
            if(!empty($get_params_string))
            {
                $get_params_string .= '&';
            }

            $get_params_string .= urlencode(trim($key)).'='.urlencode(trim($value));
        }
    }

    $href_string = $file_path.'?'.$get_params_string;

    return $href_string;
}


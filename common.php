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


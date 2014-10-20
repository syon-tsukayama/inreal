<?php
/**
 * 共通機能
 */

// 設定ファイル読み込み
require_once('./config.php');

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


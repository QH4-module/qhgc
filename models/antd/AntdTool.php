<?php
/**
 * File Name: AntdTool.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/19 4:43 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\qhgc\models\antd;


use qttx\helper\StringHelper;

class AntdTool
{
    public static function getRoutePath($ui_root,$ui_file,$type)
    {
        if (isset($ui_file['path'])) {
            return $ui_file['path'];
        }
        $ui_root = trim($ui_root, '/');
        return "/{$ui_root}/{$type}";
    }

    public static function getTitle($col)
    {
        if ($col['title']) {
            return $col['title'];
        }else{
            return lcfirst(StringHelper::underline2capital($col['COLUMN_NAME']));
        }
    }
}
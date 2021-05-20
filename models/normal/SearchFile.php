<?php
/**
 * File Name: SearchFile.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/18 5:55 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\qhgc\models\normal;


use qh4module\qhgc\external\ExtQhgc;
use qttx\basic\Loader;
use qttx\helper\StringHelper;
use qttx\web\ServiceModel;

/**
 * Class SearchFile
 * @package qh4module\qhgc\models\normal
 * @property ExtQhgc $external
 */
class SearchFile extends ServiceModel
{
    /**
     * @var string 接收参数
     */
    public $text;


    public function rules()
    {
        return [
            [['text'], 'string']
        ];
    }

    public function run()
    {
        $namespace = str_replace('/', '\\', $this->text);
        $namespace = trim($namespace, '\\');

        // 根据命名空间计算写入目录
        $class = trim(str_replace('\\', '/', $namespace), '/');
        if (empty($class)) {
            return [];
        }
        $root = Loader::getAlias('@' . $class, false);

        if (!$root) {
            return [];
        }

        $files = @scandir($root);
        if(!$files) return [];

        $ret = [];
        foreach ($files as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            if (is_dir(StringHelper::combPath($root, $item))) {
                $name = $item;
            }else if (is_file(StringHelper::combPath($root, $item))) {
                $name = stristr($item, '.', true);
            }else{
                continue;
            }

            $ret[] = [
                'label' => $namespace . '\\' . $name,
                'value' => $namespace . '\\' . $name
            ];
        }

        return $ret;
    }
}
<?php
/**
 * File Name: GenerateTool.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/4/2 9:42 上午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\qhgc\models;


use QTTX;
use qttx\basic\Loader;
use qttx\helper\FileHelper;
use qttx\web\Model;

class GenerateTool
{
    /**
     * 获取数据表前缀
     * @return mixed|string
     */
    public static function getTablePrefix()
    {
        $master = null;
        $config = QTTX::getConfig('components.db.config.masters', null);
        if ($config && is_array($config)) {
            $master = current($config);
        } else {
            $config = QTTX::getConfig('db.masters');
            if ($config && is_array($config)) {
                $master = current($config);
            }
        }

        if (is_array($master) && isset($master['table_prefix'])) {
            return $master['table_prefix'];
        } else {
            return '';
        }
    }

    /**
     * 获取几种格式的表名
     * @param $tablename
     * @return array
     */
    public static function getTableName($tablename)
    {
        $ary['table_name'] = $tablename;
        $prefix = self::getTablePrefix();
        if ($prefix) {
            $ary['table_base_name'] = str_replace($prefix, '', $tablename);
        } else {
            $ary['table_base_name'] = $tablename;
        }
        $ary['table_mask_name'] = '{{%' . $ary['table_base_name'] . '}}';

        /*
         {
            table_name: tbl_user
            table_base_name: user
            table_mask_name: {{%user}}
         }
         */
        return $ary;
    }

    public static function columnType()
    {
        $int = ['TINYINT', 'SMALLINT', 'MEDIUMINT', 'INT', 'INTEGER', 'BIGINT'];
        $double = ['FLOAT', 'DOUBLE', 'DECIMAL'];

        return array($int, $double);
    }


    public static function getColumns($tablename, $external)
    {
        $dbname = $external->databaseName();

        return QTTX::$app->db
            ->select('*')
            ->from('information_schema.COLUMNS')
            ->whereArray([
                'TABLE_SCHEMA' => $dbname,
                'TABLE_NAME' => $tablename
            ])
            ->orderByASC(['ORDINAL_POSITION'])
            ->query();

        /*
         array(21) {
    ["TABLE_CATALOG"]=>
    string(3) "def"
    ["TABLE_SCHEMA"]=>
    string(5) "frame"
    ["TABLE_NAME"]=>
    string(16) "tbl_bk_privilege"
    ["COLUMN_NAME"]=>
    string(2) "id"
    ["ORDINAL_POSITION"]=>
    int(1)
    ["COLUMN_DEFAULT"]=>
    NULL
    ["IS_NULLABLE"]=>
    string(2) "NO"
    ["DATA_TYPE"]=>
    string(7) "varchar"
    ["CHARACTER_MAXIMUM_LENGTH"]=>
    int(64)
    ["CHARACTER_OCTET_LENGTH"]=>
    int(256)
    ["NUMERIC_PRECISION"]=>
    NULL
    ["NUMERIC_SCALE"]=>
    NULL
    ["DATETIME_PRECISION"]=>
    NULL
    ["CHARACTER_SET_NAME"]=>
    string(7) "utf8mb4"
    ["COLLATION_NAME"]=>
    string(18) "utf8mb4_general_ci"
    ["COLUMN_TYPE"]=>
    string(11) "varchar(64)"
    ["COLUMN_KEY"]=>
    string(3) "PRI"
    ["EXTRA"]=>
    string(0) ""
    ["PRIVILEGES"]=>
    string(31) "select,insert,update,references"
    ["COLUMN_COMMENT"]=>
    string(0) ""
    ["GENERATION_EXPRESSION"]=>
    string(0) ""
  }
         */
    }


    public static function loadTemplate($template, $ary)
    {
        $search = [];
        $replace = [];

        foreach ($ary as $k => $v) {
            $search[] = '{{' . $k . '}}';
            $replace[] = $v;
        }

        return str_replace($search, $replace, $template);
    }


    public static function mkdirDirByNamespace($namespace, &$path)
    {
        // 根据命名空间计算写入目录
        $class = trim(str_replace('\\', '/', $namespace), '/');
        if (empty($class)) {
            return '命名空间无效';
        }
        $root = Loader::getAlias('@' . $class, false);

        if (!$root) {
            return '命名空间无效';
        }

        if (!FileHelper::mkdir($root, false)) {
            return '新建目录失败';
        }

        $path = $root;

        return true;
    }


    public static function getPrimaryKey($columns)
    {
        foreach ($columns as $item) {
            if ($item['COLUMN_KEY'] == 'PRI') {
                return $item['COLUMN_NAME'];
            }
        }

        return $columns[0]['COLUMN_NAME'];
    }


    public static function formatNamespace($namespace)
    {
        $namespace = str_replace('/', '\\', $namespace);
        $namespace = trim($namespace, '\\');
        return $namespace;
    }

    /**
     * 从Model中解析所有字段的校验方式
     * @param $model_class string model类名
     * @return array [字段1=>规则,字段2=>规则,...]
     */
    public static function getRuleByModel($model_class)
    {
        $validate_type = [];
        /**
         * @var $model_class Model
         */
        $model_class = QTTX::createObject($model_class);
        $rules = $model_class->rules();
        foreach ($rules as $row) {
            if (is_string($row[0])) {
                $validate_type[$row[0]] = $row[1];
            } else {
                foreach ($row[0] as $fd) {
                    $validate_type[$fd] = $row[1];
                }
            }
        }
        return $validate_type;
    }

    /**
     * 获取字段的注释类型
     * @param $rules array 所有字段的校验规则,[getRuleByModel()] 方法返回值
     * @param $field string 字段名称
     * @return string
     */
    public static function getFieldDocType($rules, $field)
    {
        if (!isset($rules[$field])) return 'mixed ';

        $type = $rules[$field];
        if (in_array($type, ['integer', 'int'])) {
            $s1 = 'int ';
        } else if ($type == 'number') {
            $s1 = 'double ';
        } else if ($type == 'boolean') {
            $s1 = 'bool ';
        } else if (in_array($type, ['string', 'match', 'url', 'idcard', 'account', 'mobile'])) {
            $s1 = 'string ';
        } else {
            $s1 = 'mixed ';
        }

        return $s1;
    }

    public static function addImport($import,$k1,$k2,$v)
    {
        if (isset($import[$k1][$k2])) {
            $value = $import[$k1][$k2];
            if ($value != $v) {
                if (is_string($value)) {
                    $import[$k1][$k2] = [$value];
                    $import[$k1][$k2][] = $v;
                }else{
                    $import[$k1][$k2][] = $v;
                }
            }
        }else{
            $import[$k1][$k2] = $v;
        }
        return $import;
    }

    public static function formatImport($import)
    {
        $str = '';

        $temp = "import{{default}}{{normal}} from '{{from}}';";

        foreach ($import as $from => $item) {
            if ($str) $str .= PHP_EOL;
            $default = '';
            $normal = '';
            if (isset($item['default']) && $item['default']) {
                $default = ' ' . $item['default'];
            }
            if (isset($item['normal']) && $item['normal']) {
                if ($default) {
                    $default .= ',';
                }
                if (is_string($item['normal'])) {
                    $item['normal'] = [$item['normal']];
                }
                $normal = implode(', ', $item['normal']);
            }
            if ($normal) {
                $normal = ' { ' . $normal . ' }';
            }
            $str .= GenerateTool::loadTemplate($temp, [
                'default' => $default,
                'normal' => $normal,
                'from' => $from
            ]);
        }

        return $str;
    }


    /**
     * 将引用的类格式化
     * @param $use array 要引用类的全路径一维数组
     * @return string
     */
    public static function formatUseClass($use)
    {
        $temp = 'use %s;';
        $use_str = '';
        foreach ($use as $item) {
            if($use_str) $use_str .= PHP_EOL;
            $use_str .= sprintf($temp, $item);
        }
        return $use_str;
    }

    public static function fullClass2Single($class)
    {
        $ary = explode('\\', $class);
        return $ary[sizeof($ary) - 1];
    }
}
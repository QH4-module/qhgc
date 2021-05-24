<?php
/**
 * File Name: GenerateCurdFile.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/4/2 4:29 下午
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
use qh4module\qhgc\models\antd\GenerateApiFile;
use qh4module\qhgc\models\antd\GenerateCreateFile;
use qh4module\qhgc\models\antd\GenerateDetailFile;
use qh4module\qhgc\models\antd\GenerateEditFormFile;
use qh4module\qhgc\models\antd\GenerateLayoutFile;
use qh4module\qhgc\models\antd\GenerateListFile;
use qh4module\qhgc\models\antd\GenerateRoute;
use qh4module\qhgc\models\antd\GenerateUpdateFile;
use qh4module\qhgc\models\GenerateTool;
use qh4module\qhgc\models\server\GenerateControllerFile;
use qh4module\qhgc\models\server\GenerateCreateModelFile;
use qh4module\qhgc\models\server\GenerateDeleteModelFile;
use qh4module\qhgc\models\server\GenerateDetailModelFile;
use qh4module\qhgc\models\server\GenerateIndexModelFile;
use qh4module\qhgc\models\server\GenerateModuleFile;
use qh4module\qhgc\models\server\GenerateUpdateModelFile;
use qttx\basic\Loader;
use qttx\helper\FileHelper;
use qttx\helper\StringHelper;
use qttx\web\ServiceModel;


/**
 * Class GenerateCurdFile
 * @package qh4module\qhgc\models\normal
 * @property ExtQhgc $external
 */
class GenerateCurdFile extends ServiceModel
{
    public $active_class;
    public $model_class;
    public $namespace;
    public $module;
    public $controller;
    public $fun_index;
    public $fun_create;
    public $fun_update;
    public $fun_detail;
    public $fun_delete;

    public $ui_dictionary;
    public $ui_list_api;
    public $ui_create_api;
    public $ui_update_api;
    public $ui_detail_api;
    public $ui_delete_api;

    public $ui_list_file;
    public $ui_create_file;
    public $ui_update_file;
    public $ui_detail_file;

    public $columns;

    public function run()
    {
        // 校验并格式化输入的类
        if (!LoadFile::checkARandModel($this)) return false;

        $path = '';
        // 处理 namespace
        $this->namespace = GenerateTool::formatNamespace($this->namespace);
        // 通过namespace 新建目录
        $ret = GenerateTool::mkdirDirByNamespace($this->namespace, $path);
        if ($ret !== true) {
            $this->addError('namespace', $ret);
            return false;
        }

        $service_paths = $this->mkdirService($path);
        $this->generateService($service_paths);

        return $this->generateReact();
    }

    protected function mkdirService($root)
    {
        $ctrl_path = StringHelper::combPath($root, 'controllers');
        // model 的目录应该在 [models/控制器名称]
        $model_name = StringHelper::capital2underline(str_replace('Controller', '', $this->controller));
        $model_namespace = $this->namespace . '\\models\\' . $model_name;
        $model_path = StringHelper::combPath($root, 'models', $model_name);

        if (!FileHelper::mkdir($ctrl_path, false) || !FileHelper::mkdir($model_path, false)) {
            $this->addError('namespace', '新建目录失败');
            return false;
        }

        return array(
            'namespace' => $this->namespace,
            'model_namespace' => $model_namespace,
            'root' => $root,
            'ctrl' => $ctrl_path,
            'model' => $model_path
        );
    }

    protected function generateReact()
    {
        $primary = call_user_func([$this->active_class, 'primaryKey']);
        $build_dir = StringHelper::combPath(Loader::getAlias('@runtime'), 'qhgc_build');
        // 格式化输入的目录
        $this->ui_dictionary = trim($this->ui_dictionary, '/');
        $ary_ui_dir = explode('/', $this->ui_dictionary);
        $ui_root_dir = $build_dir;
        $ui_route = '';
        foreach ($ary_ui_dir as $dr) {
            $ui_root_dir = StringHelper::combPath($ui_root_dir, $dr);
            if ($ui_route) $ui_route .= '/';
            $ui_route .= strtolower($dr);
        }

        $com_dir = StringHelper::combPath($ui_root_dir, 'components');
        $srv_dir = StringHelper::combPath($ui_root_dir, 'service');

        if (!FileHelper::mkdir($ui_root_dir, false)
            || !FileHelper::mkdir($com_dir, false)
            || !FileHelper::mkdir($srv_dir, false)
        ) {
            $this->addError('controller', '新建UI目录失败');
            return false;
        }

        $model_index = new GenerateListFile();
        $model_index->dir = $ui_root_dir;
        $model_index->columns = $this->columns;
        $model_index->active_record_classname = $this->active_class;
        $model_index->run();

        $model_api = new GenerateApiFile();
        $model_api->dir = $srv_dir;
        $model_api->ui_dictionary = $this->ui_dictionary;
        $model_api->ui_list_api = $this->ui_list_api;
        $model_api->ui_create_api = $this->ui_create_api;
        $model_api->ui_update_api = $this->ui_update_api;
        $model_api->ui_detail_api = $this->ui_detail_api;
        $model_api->ui_delete_api = $this->ui_delete_api;
        $model_api->ui_create_file = $this->ui_create_file;
        $model_api->ui_update_file = $this->ui_update_file;
        $model_api->ui_list_file = $this->ui_list_file;
        $model_api->ui_detail_file = $this->ui_detail_file;
        $model_api->run();

        $model_create = new GenerateCreateFile();
        $model_create->dir = $ui_root_dir;
        $model_create->run();

        $model_update = new GenerateUpdateFile();
        $model_update->dir = $ui_root_dir;
        $model_update->active_record_classname = $this->active_class;
        $model_update->run();

        $model_edit_form = new GenerateEditFormFile();
        $model_edit_form->dir = $com_dir;
        $model_edit_form->columns = $this->columns;
        $model_edit_form->run();

        $model_dtl = new GenerateDetailFile();
        $model_dtl->dir = $ui_root_dir;
        $model_dtl->columns = $this->columns;
        $model_dtl->active_record_classname = $this->active_class;
        $model_dtl->external = $this->external;
        $model_dtl->run();

        $model_dtl_blk = new GenerateLayoutFile();
        $model_dtl_blk->dir = $ui_root_dir;
        $model_dtl_blk->run();

        $zip_file = StringHelper::combPath($build_dir, $this->controller . '.zip');
        $res = FileHelper::zipDir($ui_root_dir, $zip_file);

        $model_route = new GenerateRoute();
        $model_route->ui_dictionary = $this->ui_dictionary;
        $model_route->ui_create_file = $this->ui_create_file;
        $model_route->ui_update_file = $this->ui_update_file;
        $model_route->ui_list_file = $this->ui_list_file;
        $model_route->ui_detail_file = $this->ui_detail_file;
        $routes = $model_route->run();

        if ($res === true) {
            return [
                'zip' => 1,
                'file' => $this->controller . '.zip',
                'routes' => $routes,
            ];
        } else {
            return [
                'zip' => 0,
                'routes' => $routes,
            ];
        }
    }


    protected function generateService($paths)
    {
        // 获取表名
        $table_name = call_user_func([$this->active_class, 'originalTableName']);

        $model_ctrl = new GenerateControllerFile();
        $model_ctrl->dir = $paths['ctrl'];
        $model_ctrl->controller_name = $this->controller;
        $model_ctrl->namespace = $paths['namespace'] . '\\controllers';
        $model_ctrl->model_namespace = $paths['model_namespace'];
        $model_ctrl->active_record_classname = $this->active_class;
        $model_ctrl->model_classname = $this->model_class;
        $model_ctrl->fun_index = $this->fun_index;
        $model_ctrl->fun_create = $this->fun_create;
        $model_ctrl->fun_update = $this->fun_update;
        $model_ctrl->fun_delete = $this->fun_delete;
        $model_ctrl->fun_detail = $this->fun_detail;
        $model_ctrl->run();

        $model_index = new GenerateIndexModelFile();
        $model_index->dir = $paths['model'];
        $model_index->columns = $this->columns;
        $model_index->namespace = $paths['model_namespace'];
        $model_index->active_record_classname = $this->active_class;
        $model_index->model_classname = $this->model_class;
        $model_index->table_name = $table_name;
        $model_index->external = $this->external;
        $model_index->run();

        $model_create = new GenerateCreateModelFile();
        $model_create->dir = $paths['model'];
        $model_create->columns = $this->columns;
        $model_create->namespace = $paths['model_namespace'];
        $model_create->active_record_classname = $this->active_class;
        $model_create->model_classname = $this->model_class;
        $model_create->table_name = $table_name;
        $model_create->external = $this->external;
        $model_create->run();

        $model_update = new GenerateUpdateModelFile();
        $model_update->dir = $paths['model'];
        $model_update->columns = $this->columns;
        $model_update->namespace = $paths['model_namespace'];
        $model_update->active_record_classname = $this->active_class;
        $model_update->model_classname = $this->model_class;
        $model_update->table_name = $table_name;
        $model_update->external = $this->external;
        $model_update->run();

        $model_del = new GenerateDeleteModelFile();
        $model_del->dir = $paths['model'];
        $model_del->columns = $this->columns;
        $model_del->namespace = $paths['model_namespace'];
        $model_del->active_record_classname = $this->active_class;
        $model_del->model_classname = $this->model_class;
        $model_del->table_name = $table_name;
        $model_del->external = $this->external;
        $model_del->run();

        $model_dtl = new GenerateDetailModelFile();
        $model_dtl->dir = $paths['model'];
        $model_dtl->columns = $this->columns;
        $model_dtl->namespace = $paths['model_namespace'];
        $model_dtl->active_record_classname = $this->active_class;
        $model_dtl->model_classname = $this->model_class;
        $model_dtl->table_name = $table_name;
        $model_dtl->external = $this->external;
        $model_dtl->run();

        $ary_namespace = explode('\\', $paths['namespace']);
        if (sizeof($ary_namespace) > 2 && $ary_namespace[1] == 'modules') {
            // 需要生成Module文件
            $model_module = new GenerateModuleFile();
            $model_module->dir = $paths['root'];
            $model_module->namespace = $paths['namespace'];
            $model_module->run();
        }

    }
}
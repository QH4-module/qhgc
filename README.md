QH4框架扩展模块-代码生成模块

该模块需要配合前端框架使用

### 功能

* 该模块可以帮助你快速生成针对某个数据表的 ActiveRecord 和 Model 类,以及用户带有筛选排序的增删改查相关类

* 模块还可以生成针对 antd 的前端框架文件,包括列表/新增/更新/详情页

* 模块现在无法生成vue版本的前端文件

### 关于 SorterValidator
校验排序规则的过滤器,所有通过模块自动生成的 `查` 文件都要这个过滤器

在config.php文件中加入
```php
// 在配置文件中有这一条,默认被注释掉了
'validator' => array(
    'sorter'=>'\qh4module\qhgc\SorterValidator',
),
```


### api 列表
```php
/**
 * 获取所有表名
 * @return array|false
 */
public function actionTableList()
```

```php
/**
 * 获取表的字段信息
 * @return array|false
 */
public function actionColumnList()
```

```php
/**
 * 生成 activerecord 文件
 * @return array|false
 */
public function actionGenerateActiveRecord()
```

```php
/**
 * 生成model文件
 * @return array|false
 */
public function actionGenerateModel()
```

```php
/**
 * 根据命名空间遍历目录下的文件
 * @return array|false
 */
public function actionSearchFile()
```

```php
/**
 * curd页面通过 ar和model类加载
 * @return array|false
 */
public function actionLoadFile()
```

```php
/**
 * 生成curd文件
 * @return array|false
 */
public function actionGenerateCurd()
```

```php
/**
 * 下载生成压缩文件
 * @return false
 */
public function actionDownloadZip()
```
<?php

/**
 * 数据库结构导出.
 * User: orginly
 * Date: 2021/3/11
 * Time: 12:20
 */

declare(strict_types = 1);

namespace app\logic;

class DatabaseExport
{

    private $_host;
    private $_username;
    private $_password;
    private $_dbname;
    private $_port;
    private $_charset;

    /**
     * 数据库连接配置 为空则获取配置文件中的信息
     * @param string $host     [数据库地址]
     * @param string $username [用户名]
     * @param string $password [密码]
     * @param string $dbname   [数据库名称]
     * @param int $port        [端口号]
     */
    public function __construct(string $host = '', string $username = '', string $password = '', string $dbname = '', int $port = 0){
        $config          = config('database.connections.mysql');
        $this->_host     = $host ? : $config['hostname'];
        $this->_username = $username ? : $config['username'];
        $this->_password = $password ? : $config['password'];
        $this->_dbname   = $dbname ? : $config['database'];
        $this->_port     = $port ? : (string)$config['hostport'];
        $this->_charset  = $config['charset'];
    }

    /**
     * Notes:导出为md格式
     * @param string $path [路径名称]
     * @return array
     */
    public function saveMd($path = './数据库表结构说明文档.md'): array{
        try {
            // 使用Pdo连接数据库
            $dsn  = "mysql:host={$this->_host};dbname={$this->_dbname};port={$this->_port};charset=utf8";
            $conn = new \PDO($dsn, $this->_username, $this->_password);
            // 显示表信息
            $result = $conn->query("show tables");
            if ($result->rowCount() > 0) {
                // 主标题
                $mark = '# '.$this->_dbname.'数据库'.PHP_EOL;
                // 每次取出一条数据
                while ($row = $result->fetch()) {
                    $num        = 1;                                  // 序号
                    $table_name = $row["Tables_in_{$this->_dbname}"]; // 表名称
                    // 获取当前表所有字段
                    $obj = $conn->query("show full columns from {$table_name}");
                    // 获取表描述信息
                    $comment = $conn->query("SELECT TABLE_COMMENT FROM INFORMATION_SCHEMA.TABLES WHERE table_name = '{$table_name}'")->fetch();
                    // 二级标题
                    $mark .= '### '.$table_name.' '.$comment['TABLE_COMMENT'].PHP_EOL;
                    $mark .= '> 描述信息：'.$comment['TABLE_COMMENT'].PHP_EOL.PHP_EOL;
                    // 表格标题
                    $mark .= '|  序号  |  字段名称  |  数据类型  |  允许空  |  缺省值  |  描述  |'.PHP_EOL;
                    $mark .= '| ------ | ------ | ------ | ------ | ------ | ------ |'.PHP_EOL;
                    // 每次取出一条字段信息
                    while ($data = $obj->fetch()) {
                        // 表格内容
                        $mark .= '| '.$num++.'| '.$data['Field'].' | '.$data['Type'].' | '.$data['Null'].
                            ' | '.$data['Default'].' | '.$data['Comment'].' | '.PHP_EOL;
                    }
                    // 获取表创建Sql语句
                    $sql  = $conn->query("show create table {$table_name}")->fetch();
                    $mark .= '```sql'.PHP_EOL;
                    $mark .= isset($sql["Create View"]) ? $sql["Create View"] : $sql['Create Table'].PHP_EOL;
                    $mark .= '```'.PHP_EOL;
                }
                // 写入文件
                $res = file_put_contents($path, $mark);
                if (!$res) {
                    return return_fail(-1, '导出失败');
                }
                return return_fail(-1, '导出成功');
            }
        } catch (\Exception $ex) {
            setErrLog('DatabaseExport > saveMd > ', $ex);
            return return_fail(-1, $ex->getMessage());
        }
    }


    /**
     * Notes:设置字符集
     * @param string $charset
     * @return $this
     */
    public function setCharset(string $charset): DatabaseExport{
        $this->_charset = $charset;
        return $this;
    }


}
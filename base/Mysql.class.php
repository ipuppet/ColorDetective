<?php
/**
 * 数据库查询封装
 */

define('__DBHOST__', 'localhost');
define('__DBUSERNAME__', 'username');
define('__DBPWD__', 'password');
define('__DBNAME__', 'dbname');
/*
 * 数据库结构
 * CREATE TABLE `gamedata` (
  `id` varchar(11) DEFAULT NULL,
  `ColorDetective` varchar(255) DEFAULT '{"scoreList":{"2":{"score":0,"time":0},"3":{"score":0,"time":0},"1":{"score":0,"time":0}}}'
) ENGINE=MyISAM DEFAULT CHARSET=utf8
 */

class Mysql
{
    /**
     * 连接数据库函数
     **/
    public function connectDB()
    {
        $con = mysqli_connect(__DBHOST__, __DBUSERNAME__, __DBPWD__, __DBNAME__);
        if (!$con) {
            $err = 'Could not connect: ' . mysqli_error($con);
            return $err;
        }
        mysqli_set_charset($con, "utf8");
        return $con;
    }

    public function sql($sql)
    {
        $con = $this->connectDB();
        $retval = mysqli_query($con, $sql);
        $result = array();
        while ($row = mysqli_fetch_assoc($retval)) {
            $result[] = $row;
        }
        mysqli_close($con);
        return $result;
    }

    /**
     * 返回数据
     **/
    public function returnData($con, $sql)
    {
        if (mysqli_query($con, $sql)) {
            mysqli_close($con);
            return true;
        } else {
            echo "<script>console.log('error:" . mysqli_error($con) . "');</script>";
            mysqli_close($con);
            return false;
        }
    }

    /**
     * 对where进行处理
     **/
    public function replaceWhere($con, $where, $connect = '=')
    {
        if (is_array($where)) {//判断限定条件是否为数组
            $len = 0;
            foreach ($where as $key => $value) {
                $where[$key] = mysqli_real_escape_string($con, $value);
                $len++;//数组长度
            }
            if ($len == 1) {//数组长度为1则返回*** $connect '***'否则返回*** $connect '***' and *** $connect '***'
                $where = array_keys($where)[0] . $connect . '\'' . array_values($where)[0] . '\'';
            } else {
                $sets = array();
                foreach ($where as $key => $value) {
                    $kstr = '`' . $key . '`';
                    $vstr = '\'' . $value . '\'';
                    array_push($sets, $kstr . $connect . $vstr);
                }
                $where = implode(' and ', $sets);
            }
            return $where;
        } else {
            return false;
        }
    }

    /**
     * select * from db1 where uid=1;
     * 等价于
     * $select = new mysql();
     * $select->select('*',db1','uid','1');
     */
    public function select($data, $table, $where, $orderby = '', $offset = 0, $limit = 1000)
    {
        $con = $this->connectDB();
        $table = mysqli_real_escape_string($con, $table);
        $where = $this->replaceWhere($con, $where);
        if (is_array($data)) {//判断传入数据是否为数组
            $data = implode(',', $data);
            $data = mysqli_real_escape_string($con, $data);
        } else {
            $data = mysqli_real_escape_string($con, $data);
        }
        if (empty($where)) {//判断是否有限定条件
            $sql = "SELECT {$data} FROM {$table} {$orderby} LIMIT {$offset},{$limit}";
            $retval = mysqli_query($con, $sql);
            $result = array();
            while ($row = mysqli_fetch_assoc($retval)) {
                $result[] = $row;
            }
        } else {
            $sql = "SELECT {$data} FROM {$table} WHERE {$where} {$orderby} LIMIT {$offset},{$limit}";
            $retval = mysqli_query($con, $sql);
            $result = array();
            while ($row = mysqli_fetch_assoc($retval)) {
                $result[] = $row;
            }
        }
        if (count($result) == 1) {
            $result = $result[0];
            $result['onlyOneRow'] = true;
        }
        mysqli_close($con);
        return $result;
    }

    /**
     * 插入数据
     * @param $table 数据表
     * @param $data 数据数组
     * @return true or false
     */
    public function insert($table, $data)
    {
        $con = $this->connectDB();
        $table = mysqli_real_escape_string($con, $table);
        foreach ($data as $key => $value) {
            $data[$key] = mysqli_real_escape_string($con, $value);
        }
        $keys = '`' . implode('`,`', array_keys($data)) . '`';
        $values = "'" . implode("','", array_values($data)) . "'";
        $sql = "INSERT INTO `{$table}` ( {$keys} ) VALUES ( {$values} )";
        return $this->returnData($con, $sql);
    }

    /**
     * 更新数据
     * @param $table 数据表
     * @param $data 数据数组
     * @param $where 过滤条件
     * @return true or false
     */
    public function update($table, $data, $where = null)
    {
        $con = $this->connectDB();
        $table = mysqli_real_escape_string($con, $table);
        $where = $this->replaceWhere($con, $where);
        foreach ($data as $key => $value) {
            $data[$key] = mysqli_real_escape_string($con, $value);
        }
        $sets = array();
        foreach ($data as $key => $value) {
            $kstr = '`' . $key . '`';
            $vstr = '\'' . $value . '\'';
            array_push($sets, $kstr . '=' . $vstr);
        }
        $kav = implode(',', $sets);
        $sql = "UPDATE {$table} SET {$kav} WHERE {$where}";
        return $this->returnData($con, $sql);
    }
}
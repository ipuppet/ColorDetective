<?php

class model
{
    var $value;
    static $mysql;

    public function __construct()
    {
        include 'Mysql.class.php';
        self::$mysql = new Mysql;
        $this->value['rules']='
        1、当点击>>Start Now!<<按钮时，视为同意该规则并开始游戏<br>
        2、点击与屏幕上方长条色块相同颜色正方形色块，若点击正确计一分，错误则游戏结束。
        3、游戏过程中可能会出现相同色块。<br>
        4、分数相同者，按照闯关总时间进行排名<br>';
    }

    //处理用户答案
    public function submit($userAns)
    {
        $ret = false;
        $score = (int)$userAns['score'];
        $time = (int)$userAns['time'];
        $level = $userAns['level'];
        switch ($level) {
            case '3000':
                $level = 1;
                break;
            case '6000':
                $level = 2;
                break;
            case '10000':
                $level = 3;
                break;
            default:
                $level = null;
        }
        $Db = self::$mysql->select('ColorDetective as score, id', 'GameData', array('id' => $_SESSION['user']['id']));
        $name = self::$mysql->select('name', 'user', array('id' => $Db['id']));
        $Db['score'] = json_decode($Db['score'], true);
        $Db_score = (int)$Db['score']['scoreList'][$level]['score'];
        $Db_time = (int)$Db['score']['scoreList'][$level]['time'];
        if ($score > $Db_score) {
            $Db['score']['scoreList'][$level]['score'] = $score;
            $Db['score']['scoreList'][$level]['time'] = $time;
            $str = json_encode($Db['score']);
            $ret = self::$mysql->update('GameData', array('ColorDetective' => $str), array('id' => $_SESSION['user']['id']));
        } elseif ($score == $Db_score) {
            if ($time < $Db_time) {
                $Db['score']['scoreList'][$level]['time'] = $time;
                $str = json_encode($Db['score']);
                $ret = self::$mysql->update('GameData', array('ColorDetective' => $str), array('id' => $_SESSION['user']['id']));
            }
        }
        if ($ret) {
            return array(
                'str' => $Db['score'],
                'flag' => 'new',
                'score' => $score,
                'time' => $time,
                'name' => $name['name']
            );
        } else {
            return array(
                'flag' => 'old',
                'score' => $Db_score,
                'time' => $Db_time,
                'name' => $name['name']
            );
        }
    }

    //获取排行榜前10
    public function getTop10($level)
    {
        function quick_sort($a)
        {
            // 判断是否需要运行，因下面已拿出一个中间值，这里<=1
            if (count($a) <= 1) {
                return $a;
            }
            $middle = $a[0]; // 中间值
            $left = array(); // 接收小于中间值
            $right = array();// 接收大于中间值
            // 循环比较
            for ($i = 1; $i < count($a); $i++) {
                if ($middle['score'] < $a[$i]['score']) {
                    // 大于中间值
                    $right[] = $a[$i];
                } else {
                    // 小于中间值
                    $left[] = $a[$i];
                }
            }
            // 递归排序划分好的2边
            $left = quick_sort($left);
            $right = quick_sort($right);
            // 合并排序后的数据，别忘了合并中间值
            return array_merge($right, array($middle), $left);
        }

        function fin_sort($a)
        {
            $arr = quick_sort($a);
            for ($i = 0; $i < count($arr); $i++) {
                for ($j = 0; $j < count($arr); $j++) {
                    if ($arr[$i]['score'] == $arr[$j]['score']) {
                        if ($arr[$i]['time'] < $arr[$j]['time']) {
                            $t = $arr[$i]['time'];
                            $arr[$i]['time'] = $arr[$j]['time'];
                            $arr[$j]['time'] = $t;
                        }
                    }
                }
            }
            return $arr;
        }

        switch ($level) {
            case '3000':
                $level = 1;
                break;
            case '6000':
                $level = 2;
                break;
            case '10000':
                $level = 3;
                break;
            default:
                $level = null;
        }
        $list = self::$mysql->sql('SELECT a.name,a.id,b.ColorDetective as score FROM user a,GameData b WHERE a.id=b.id');
        $rank = array();
        foreach ($list as $key => $value) {
            $arr = json_decode($value['score'], true);
            $rank[] = array(
                'id' => $list[$key]['id'],
                'name' => $list[$key]['name'],
                'score' => $arr['scoreList'][$level]['score'],
                'time' => $arr['scoreList'][$level]['time'],
            );
        }
        $ret = fin_sort($rank);
        $ret['level'] = $level;
        foreach ($ret as $key => $value) {
            if ($value['name'] == $_SESSION['user']['name']) {
                $ret['userInfo'] = array(
                    'rank' => $key + 1,
                    'name' => $value['name'],
                    'score' => $value['score'],
                    'time' => $value['time']
                );
            }
        }
        return $ret;
    }

    public function login($info)
    {
        $arr = $info;
        foreach ($info as $key => $value) {
            $arr[$key] = quotemeta($value);
        }
        $ret = self::$mysql->select('*', 'user', array('id' => $arr['id']));
        if (@$ret['onlyOneRow'] && !empty($ret)) {
            if ($arr['pwd'] == $ret['pwd']) {
                $_SESSION['user'] = $ret;
                return '0';//登陆成功
            } else {
                return '1';//密码错误
            }
        } else {
            return '2';//无该用户
        }
    }

    public function registered($info)
    {
        $arr = $info;
        foreach ($info as $key => $value) {
            $arr[$key] = quotemeta($value);
        }
        if (strlen($arr['id']) == 11 && is_numeric($arr['id'])) {//判断是否为11位且为数字
            $res = self::$mysql->select('*', 'user', array('id' => $arr['id']));
            if (empty($res)) {
                if (!empty($arr['name'])) {
                    if (!empty($arr['pwd'])) {
                        $data = array(
                            'id' => $arr['id'],
                            'name' => $arr['name'],
                            'pwd' => $arr['pwd']
                        );
                        $ret = self::$mysql->insert('user', $data);
                        if ($ret) {
                            return '0';//注册成功
                        } else {
                            return '2';//插入数据库失败
                        }
                    } else {
                        return '4';
                    }
                } else {
                    return '5';
                }
            } else {
                return '1';//已被注册
            }
        } else {
            return '3';//学号位数不是11位
        }
    }
}
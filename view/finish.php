<?php
$userAns['time'] = $_POST['time'];
$userAns['score'] = $_POST['score'];
$userAns['level'] = $_POST['level'];
$data = $model->submit($userAns);
if ($data['flag'] == 'old') {
    $new = false;
} elseif ($data['flag'] == 'new') {
    $new = true;
}
$list = $model->getTop10($userAns['level']);
$level = $list['level'];
$userInfo = $list['userInfo'];
unset($list['level']);
unset($list['userInfo']);
switch ($level) {
    case 3:
        $level = '入门';
        break;
    case 2:
        $level = '普通';
        break;
    case 1:
        $level = '困难';
        break;
    default:
        $level = null;
}
?>
<div class="list">
    <div class="new">
        <?php
        if (@$new) {
            echo '新纪录！';
        }
        ?>
    </div>
    <div class="score">
        <span><?= $data['name'] ?></span><br>
        <span>您的最终得分：<?= $userAns['score'] ?>&nbsp;分</span><br>
        <span>所用时间：<?= (float)$userAns['time'] / 1000 ?>&nbsp;s</span><br>
    </div>
    <div class="history">
        <span>历史最高：<?= $data['score'] ?>&nbsp;分</span><br>
        <span>所用时间：<?= (float)$data['time'] / 1000 ?>&nbsp;s</span>
    </div>
</div>
<div class="list">
    <table style="width: 100%">
        <tr>
            <?= $level ?>难度Top10玩家
        </tr>
        <tr>
            <td>排名</td>
            <td>用户</td>
            <td>得分</td>
            <td>用时</td>
        </tr>
        <?php
        for ($i = 0; $i < 10; $i++) { ?>
            <tr class="top10Box">
                <td><?= $i + 1 ?></td>
                <td><?= @$list[$i]['name'] ?></td>
                <td><?= @$list[$i]['score'] ?></td>
                <td><?= @(float)$list[$i]['time'] / 1000 ?>&nbsp;s</td>
            </tr>
        <?php } ?>
        <tr style="border-top: 1px solid #adadad">
            <td><?= $userInfo['rank'] ?></td>
            <td id="userName"><?= $userInfo['name'] ?></td>
            <td><?= $userInfo['score'] ?></td>
            <td><?= $userInfo['time'] / 1000 ?>&nbsp;s</td>
        </tr>
    </table>
</div>
<div class="reTry">
    <span class="glyphicon glyphicon-repeat" onclick="window.history.back(-1)"></span>
</div>
<script>
    function rgb() {//rgb颜色随机
        let arr = new Array(2);
        let r = Math.floor(Math.random() * 256);
        let g = Math.floor(Math.random() * 256);
        let b = Math.floor(Math.random() * 256);
        let rgb = r + ',' + g + ',' + b;
        let reR = (255 - r) < 150 && (255 - r) > 100 ? 255 : (255 - r);
        let reG = (255 - g) < 150 && (255 - g) > 100 ? 255 : (255 - g);
        let reB = (255 - b) < 150 && (255 - b) > 100 ? 255 : (255 - b);
        let reRgb = reR + ',' + reG + ',' + reB;
        arr['rgb'] = rgb;
        arr['reRgb'] = reRgb;
        return arr;
    }

    function changeBg(time = null) {
        let color = rgb();
        $('.reTry').css('color', 'rgb(' + color['reRgb'] + ')');
        $('body').css({
            'background': 'rgb(' + color['rgb'] + ')',
            'transition': '500ms'
        });
        if (time !== null) {
            setTimeout(function () {
                changeBg(time)
            }, time)
        }
    }

    $('.list').addClass('animated swing').click(function () {
        changeBg();
        var e = $(this);
        e.removeClass('swing').addClass('swing');
        setTimeout(function () {
            e.removeClass('swing')
        }, 1000)
    });
    $(function () {
        changeBg(3000);
        var name = $('#userName').text();
        $('.top10Box').each(function () {
            $(this).find('td').each(function () {
                if ($(this).text() === name) {
                    $(this).parent().css('color', '#ff6040')
                }
            })
        })
    })
</script>
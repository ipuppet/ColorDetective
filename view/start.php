<div id="dowebok">
    <div class="section Home_">
        <div class="userInfo">
            <span>
                <?php if (isset($_SESSION['user'])) {
                    echo $_SESSION['user']['name']; ?>
                    <a onclick="logout()">退出登录</a>
                    <form action="" method="post" target="iframe" id="logoutForm">
                        <input type="hidden" name="action" value="logout">
                    </form>
                <?php } else {
                    echo '请登陆';
                } ?>
            </span>
        </div>
        <div class="ruleBox animated bounceIn">
            <span style="font-size: 26px;font-weight: bold;">规则</span><br>
            <?= $model->value['rules'] ?>
            <h3>难度选择</h3>
            <div class="form-group">
                <label>
                    <input type="radio" name="level_radio" value="10000" checked>入门
                </label>
                <label>
                    <input type="radio" name="level_radio" value="6000">普通
                </label>
                <label>
                    <input type="radio" name="level_radio" value="3000">困难
                </label>
            </div>
        </div>
        <div class="startBtn">
            <?php if (isset($_SESSION['user'])) { ?>
                <button class="btn btn-danger" onclick="start()">>>Start Now!<<</button>
            <?php } else { ?>
                <button class="btn btn-danger" onclick="loginBox(1)">请登陆</button>
            <?php } ?>
        </div>
    </div>
    <!--游戏开始！！-->
    <div class="section contentBox">
        <div class="progress">
            <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar"
                 aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 0;">
                100%
            </div>
        </div>
        <form action="?v=finish" id="submit" method="post">
            <input id="useTime" type="hidden" name="time">
            <input id="score" type="hidden" name="score">
            <input id="level" type="hidden" name="level">

            <div id="slide" class="slide animated">
                <div class="question"></div>
                <div class="scoreBox">当前得分：0</div>
                <div class="optBtn"></div>
            </div>

        </form>
    </div>
</div>
<div class="loginBox animated">
    <div class="login">
        <form action="" method="post" target="iframe">
            <input type="hidden" name="action" value="login">
            <div class="form-group">
                <label for="id">学号</label>
                <input class="form-control" type="text" name="id" placeholder="请输入学号">
            </div>
            <div class="form-group">
                <label for="pwd">密码</label>
                <input class="form-control" type="password" name="pwd" placeholder="请输入密码">
            </div>
            <a onclick="changeBox(1)">>>点我注册<<</a><br>
            <button class="btn btn-primary" style="float: right">登陆</button>
        </form>
        <button class="btn btn-success" onclick="loginBox(0)">戳此关闭这个窗口</button>
    </div>
    <div class="registered">
        <a onclick="changeBox(0)">< 返回</a>
        <form action="" method="post" target="iframe">
            <input type="hidden" name="action" value="registered">
            <div class="form-group">
                <label for="id">学号</label>
                <input class="form-control" type="text" name="id" placeholder="请输入学号">
            </div>
            <div class="form-group">
                <label for="name">昵称</label>
                <input class="form-control" type="text" name="name" placeholder="请输入昵称">
            </div>
            <div class="form-group">
                <label for="pwd">密码</label>
                <input class="form-control" type="password" name="pwd" placeholder="请输入密码">
            </div>
            <button class="btn btn-primary">注册</button>
        </form>
    </div>
</div>
<!--用来控制登陆注册的js-->
<script>
    function loginBox(flag) {
        if (flag) {
            $('.loginBox').removeClass('rollOut').addClass('rollIn').show()
        } else {
            $('.loginBox').removeClass('rollIn').addClass('rollOut')
        }
    }

    function logout() {
        $('#logoutForm').submit();
    }

    function changeBox(flag) {
        if (flag === 1) {
            $('.login').addClass('animated bounceOutUp').removeClass('bounceInDown');
            $('.registered').addClass('animated bounceInUp').removeClass('bounceOutDown').show();
        } else {
            $('.login').addClass('animated bounceInDown').removeClass('bounceOutUp');
            $('.registered').addClass('animated bounceOutDown').removeClass('bounceInUp');
        }
    }
</script>
<iframe name="iframe" style="display: none"></iframe>
<script>
    var time;
    var animationTime = 1000;//换页以及进度条重置动画效果时间
    var clickAnimateTime = 800;//点击选项的动画时间
    var e = $(".progress-bar");//进度条元素

    var len = 100;//进度条长度
    var timeFlag;//每1%对应的时间，控制进度条的变化速度
    var nowPage = 1;
    var autoChangeFlag = true;//用来终止countdown
    var clickProtect = true;//点击保护，防止用户在动画未加载完点击
    var startTime;
    var endTime;
    var level = 1;
    var score = 0;

    function start() {
        time = $('input[name="level_radio"]:checked').val();
        timeFlag = time / len;
        $('.startBtn').addClass('animated heartBeat');
        $('.Home_').addClass('animated bounceOutUp');
        creatColor(1, 3);
        setTimeout("run()", animationTime);
    }

    function run() {
        $.fn.fullpage.moveSectionDown();
        $('#slide1').addClass('bounceInUp');
        startTime = new Date().getTime();
        e.text('100%').css({
            'transition': animationTime + 'ms',
            'width': '100%',
        });
        setTimeout("e.css({'transition': timeFlag + 'ms', 'transition-timing-function': 'linear'});autoChangeFlag = true;clickProtect = false;countdown();", animationTime);
    }

    function clickOptions(ele) {
        checkLevel();
        if (!clickProtect) {
            clickProtect = true;
            $(ele).removeClass('bounceIn').addClass('rubberBand').css('animation-duration', clickAnimateTime + 'ms');
            //判断是否得分
            let titCol = $('.question').css('background-color');
            let clickCol = $(ele).css('background-color');
            var t;
            //换下一页
            autoChangeFlag = false;
            t = setTimeout("changePage()", clickAnimateTime);
            if (titCol === clickCol){
                score++;
                $('.scoreBox').text('当前得分：' + score);
            }
            else{
                finish();
                clearTimeout(t);
            }
        } else return false;
    }

    function checkLevel() {
        if (score % 5 === 0) {
            timeFlag = timeFlag / 5 * 4;
            animationTime = animationTime / 5 * 4;
            clickAnimateTime = clickAnimateTime / 5 * 4;
            level++
        }
    }

    function countdown() {
        if (len > 0) {
            len--;
            e.text(len + '%').css('width', len + '%');
            if (len >= 0 && len <= 30) {
                e.addClass("progress-bar-danger");
            } else if (len >= 30 && len <= 60) {
                e.removeClass("progress-bar-danger");
                e.addClass("progress-bar-warning");
            } else if (len >= 60 && len <= 100) {
                e.removeClass("progress-bar-warning");
                e.addClass("progress-bar-success");
            }
            if (autoChangeFlag) setTimeout("countdown()", timeFlag);
        } else {
            finish()
        }
    }

    function changePage() {
        creatColor(level, level >= 3 ? level : 3);
        //切换下一页
        $.fn.fullpage.moveSlideRight();
        e.removeClass("progress-bar-danger progress-bar-warning");
        $('#slide' + (nowPage + 1)).addClass('bounceInRight');
        $('#slide' + nowPage).removeClass('bounceInRight');
        clickProtect = true;
        nowPage++;
        len = 100;
        e.text('100%').css({
            'transition': animationTime + 'ms',
            'width': '100%',
        });
        setTimeout("e.css({'transition': timeFlag + 'ms', 'transition-timing-function': 'linear'});autoChangeFlag = true;clickProtect = false;countdown();", animationTime);

    }

    function finish() {
        showMsg('Game Over!', 1000);
        setTimeout("$('.optBtn').addClass('animated hinge')",1000);
        endTime = new Date().getTime();
        $('#level').val(time);
        $('#useTime').val(endTime - startTime);
        $('#score').val(score);
        setTimeout("$('#submit').submit()",2000)
    }

    //生成颜色
    function rgb(only = 0) {//rgb颜色随机
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
        return only ? rgb : arr;
    }

    function creatColor(row, column) {
        /*生成新的颜色快*/
        var color = new Array(row * column);
        var tem = '';
        var optionsWidth = document.body.clientWidth / column;
        for (var i = 0; i < row * column; i++) {
            color[i] = rgb(1);
            tem = tem + '<div class="col-xs-4 animated bounceIn" onclick="clickOptions(this)" style="background: rgb(' + color[i] + ');height: ' + optionsWidth + 'px;width: ' + optionsWidth + 'px"></div>'
        }
        let index = Math.floor((Math.random() * color.length));
        $('.question').css('background', 'rgb(' + color[index] + ')');
        $('.optBtn').html(tem);
    }

    //初始化fullpage
    $(function () {
        optionsWidth = $('#seeWidth').width();
        $('#dowebok').fullpage({
            loopHorizontal: false,
            slidesNavigation: false,
            controlArrows: false,
            scrollingSpeed: 0,
            verticalCentered: false,
        });
        $.fn.fullpage.setKeyboardScrolling(false);
        $.fn.fullpage.setAllowScrolling(false, 'all');
    });
</script>
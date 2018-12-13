<?php
require '_lp.php';
$APIKEY = null; // 添加调用保护 

if( strlen( $APIKEY ) > 0 && t(v('key')) != $APIKEY )
{
    return send_error("错误的Key");
    exit;
}

// 标准返回值

$ret = [];
$ret['code'] = 0;
$ret['js'] = ''; // 页面 js
$ret['cmd'] = ''; // eletron 命令

$cmd = t(v('cmd')); // TimeTodo 上的命令，包含冒号
$type = t(v('type'));

if( $type == 'preview' )
{
    switch( $cmd )
    {
        case "@demo":
            $ret['preview'] = "[Markdown](http://ftqq.com)";
            break;
    }
    return $ret;
    
}

// 根据命令来修改返回值相关数据
switch( $cmd )
{
    // 直接返回JS命令
    case ':who':
        $ret['js'] = "alert('It\' me 🤠 ')";
        break;

    // 分析 TODO （会 POST 到 $_REQUEST['todos'] 里边 ）
    // 并返回数据
    case ':howmany':
        $todos = json_decode( v('todos') , true );
        $ret['js'] = "alert('你总共有" . count( $todos ) . "个TODO')"; 
        break; 
    
    // 对 TODO 数据进行操作
    // 数据对象为 this.props.store
    // 方法包括：
    // 添加 todo_add( text )
    // 完成 todo_check( id ) // id 可以分析 $_POST['todos'] 取到
    // 未完成 todo_uncheck( id ) 
    // 开始计时 todo_play( id ) 
    // 清除已经完成的TODO todo_clean()
    case ':add':
        $ret['js'] = "this.props.store.todo_add('remote one')"; 
        break;

    // JS 直接操作剪贴板
    case ':rand':
        $ret['js'] = 'window.require("electron").clipboard.writeText("'.uniqid().'");alert("随机密码已复制到剪贴板")';  
        break; 
    
    // 设置面板背景    
    case ':bg':
        $ret['js'] = 'document.querySelector("html").style.backgroundImage = "url(\'https://ws1.sinaimg.cn/large/40dfde6fly1fxy3his1hsj20jq0rsk0q.jpg\')";';  
        break;
    
    // 提示音
    case ':beep':
        $ret['js'] = 'window.require("electron").shell.beep()'; 
        break;

    // 取消顶层浮动
    case ':nofloat':
        $ret['cmd'] = 'win.setAlwaysOnTop(false, "normal", 0);'; 
        break; 
    
    // 开启顶层浮动
    case ':float':
        $ret['cmd'] = 'win.setAlwaysOnTop(true, "floating", 1);'; 
        break;     
    
    // 运行 electron 命令，支持所有的命令
    case ':quit':
        $ret['cmd'] = "app.quit()"; 
        break; 
    
    default:
        // 正则演示 find sort 搜索 PHP 手册
        if( preg_match("/:find\s(.+?)$/i" , $cmd , $out ) )
        {
            $ret['js'] = 'window.require("electron").shell.openExternal("http://www.php.net/manual-lookup.php?pattern=' . $out[1] . '");';
        }
        else
        {
            $ret['js'] = 'alert("输入的命令是' . $cmd . '")';
        }
        

}

// $ret['cmd'] = 'app.quit()';

return send_result( $ret );



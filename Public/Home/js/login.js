$(
/* 切换到当前动作的锚点 */
function(){
    var action = window.location.hash;
    if(action){
    pattern = 'a[href="' + action + '"]';
       $(pattern).click();
    }
}
);
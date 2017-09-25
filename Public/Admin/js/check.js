/*
 * 函数：验证数据是否为空
 * 参数：val
 */
function isEmpty( val ) {
    if( val == '' ) {
        return true;
    } else { 
        return false;
    }
}

/*
 * 函数：验证数据是否为手机号
 * 规则：只验证第一位是1，长度为11的数字
 * 参数：val 
 */
function isPhone( val ) {
    var reg = /^1\d{10}$/;
    return reg.test(val);
}

/*
 * 函数：验证数据是否为电话
 * 规则：区号+号码，区号以0开头，3位或4位
 * 参数：val 
 * 说明：不含400电话
 */
function isTel( val ) {
    var reg = /^0\d{2,3}-?\d{7,8}$/;
    return reg.test(val);
}

/*
 * 函数：验证数据是否为邮箱
 * 规则：邮箱地址分成“第一部分@第二部分，由字母、数字、下划线、短线“-”、
 * 点号“.”组成，为一个域名，域名由字母、数字、短线“-”、域名后缀组成
 * 参数：val 
 */
function isEmail( val ) {
    var reg = /^(\w-*\.*)+@(\w-?)+(\.\w{2,})+$/;
    return reg.test(val);
}

/*
 * 函数：验证数据是否为身份证
 * 规则：身份证号码为18位，18位前17位为数字，最后一位是校验位，可能为数字或字符X 
 * 参数：val 
 */
function isPid( val ) {
    var reg = /(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
    return reg.test(val);
}

/*
 * 函数：验证数据是否为身份证
 * 规则：身份证号码为18位，18位前17位为数字，最后一位是校验位，可能为数字或字符X,出生日期验证
 * 参数：val 
 */
function isPid2( val ){
    if( !isPid( val ) ) {
        return false;
    }
    var sBirthday= val.substr(6,4)+"-"+Number(val.substr(10,2))+"-"+Number(val.substr(12,2));
    var d =new Date(sBirthday.replace(/-/g,"/"));
    if(sBirthday!=(d.getFullYear()+"-"+ (d.getMonth()+1) + "-" + d.getDate())){
        return false
    }
    var myDate = new Date();
    var nowDate = (myDate.getFullYear()+"-"+ (myDate.getMonth()+1) + "-" + myDate.getDate())
    if( nowDate < sBirthday ) {
        return false;
    }
    return true;
}

/*
 * 函数：验证数据为整数
 * 规则：正整数，0，负整数
 * 参数：val
 */
function isInteger( val ) {

    if( isEmpty(val) ) {
        return false;
    }

    if( val%1 === 0 ) {
        return true;
    } else {
        return false;
    }
}

/*
 * 函数：验证数据是数字
 * 规则：
 * 参数：val
 */
function isNumber( val ) {
    var reg = /^(-)?\d+(\.\d+)?$/;
    if (reg.exec(val) == null || val == "") {
        return false
    } else {
        return true
    }
}

/*
 * 函数：验证数据是汉字
 * 规则：
 * 参数：val
 */
function isChinese( val ) {
    var reg = /^[\u2E80-\u9FFF]+$/;
    return reg.test( val );
}

/*
 * 函数：验证数据是字母
 * 规则：
 * 参数：val
 */
function isABC( val ) {
    var reg =  /^[A-Za-z]+$/;
    return reg.test( val );
}

/*
 * 函数：验证数据是日期
 * 规则：不验证时分秒
 * 参数：val
 */
function isDate( val ) {
    var result = val.match(/^(\d{1,4})(-|\/)(\d{1,2})\2(\d{1,2})$/);

    if (result == null){
        return false;
    }
        
    var d = new Date(result[1], result[3] - 1, result[4]);
    if (d.getFullYear() == result[1] && (d.getMonth() + 1) == result[3] && d.getDate() == result[4]) {
        return true;
    } else {
        return false;
    }
}

/*
 * 函数：验证数据长度
 * 规则：默认为6-16长度
 * 参数：val ,minlen=最小长度，maxlen=最大长度
 */
function cLength( val,minlen,maxlen ) {
    if( minlen > maxlen ) {
        return false;
    }

    if( val.length < minlen || val.length > maxlen ) {
        return false;
    }

    return true;
}

/*
 * 函数：验证数据为正整数，最小值，最大值
 * 规则：默认为6-16长度
 * 参数：val ,min=最小值，max=最大值
 */
function cInteger( val,min,max ) {
    //判断最大最小设置是否合法
    if( min > max ) {
        return false;
    }

    if( min <= 0 || max <= 0 ) {
        return false;
    }

    //是否为整数
    if( !isInteger( val ) ) {
        return false
    }

    if( val <= 0 ) {
        return false;
    }

    if( val > max ) {
        return false;
    }

    return true;
}

/*
 * 函数：验证数据是数字,最小值，最大值，小数点后几位
 * 规则：
 * 参数：val,min=最小值，max=最大值，xlength=小数点后几位
 */
function cNumber( val,min,max,xlength) {
    //判断最大最小设置是否合法
    if( min > max ) {
        return false;
    }

    if( !isNumber(val) ) {
        return false;
    }

    if( val < min || val > max ) {
        return false;
    }

    var arrVal= new Array();
    arrVal = val.split(".");

    if( arrVal[1] == null ) {
        return true;
    }

    if( arrVal[1].length > 2 ) {
        return false;
    }
    
    return true;
}

/*
 * 函数：验证数据字母，数字，字母数字组
 * 规则：
 * 参数：val ,minlen=最小长度，maxlen=最大长度
 */
function cABCNumber( val,minlen,maxlen ) {
    if( minlen > maxlen ) {
        return false;
    }

    if( val.length < minlen || val.length > maxlen ) {
        return false
    }

    var reg = /^[0-9a-zA-Z]*$/;

    return reg.test( val );
}

/*
 * 函数：只能输入数字
 * 规则：小数点不能输入
 * 参数：id ,maxlen = 最大长度
 */
function onkeyupNumber( id, maxlen  ){
    var val = $('#'+id).val();
    if( val == null || !isInteger(maxlen) ) {
        return '';
    }
    val = val.replace(/[^0-9]/g,'');
    val = val.substr(0,maxlen);
    $('#'+id).val(val);
}

/*
 * 函数：只能输入数字和小数字
 * 规则：
 * 参数：id ,maxlen = 最大长度,xlength=小数点后几位
 */
function onkeyupNumberFloat( id, maxlen, xlength ){
    var val = $('#'+id).val();
    if( val == null || !isInteger(maxlen) || !isInteger(xlength) ) {
        return '';
    }
    val = val.replace(/[^0-9.]/g,'');

    var arrVal= new Array();
    arrVal = val.split(".");

    if( arrVal[1] != null ) {
        arrVal[1] = arrVal[1].substr(0,xlength);
        val = arrVal[0] + '.' + arrVal[1];
    }    

    val = val.substr(0,maxlen);
    $('#'+id).val(val);
}

/*
 * 函数：只能输入字母
 * 规则：小数点不能输入
 * 参数：id ,maxlen = 最大长度
 */
function onkeyupABC( id, maxlen ){
    var val = $('#'+id).val();
    if( val == null || !isInteger(maxlen) ) {
        return '';
    }
    val = val.replace(/[^A-Za-z]/g,'');
    val = val.substr(0,maxlen);
    $('#'+id).val(val);
}

/*
 * 函数：只能输入数字和字母
 * 规则：小数点不能输入
 * 参数：id ,maxlen = 最大长度
 */
function onkeyupNumberABC( id, maxlen ){
    var val = $('#'+id).val();
    if( val == null || !isInteger(maxlen) ) {
        return '';
    }
    val = val.replace(/[^0-9A-Za-z]/g,'');
    val = val.substr(0,maxlen);
    $('#'+id).val(val);
}

/*
 * 函数：只能输入汉字
 * 规则：小数点不能输入
 * 参数：id ,maxlen = 最大长度
 */
function onkeyupChinese( id, maxlen ) {
    var val = $('#'+id).val();
    if( val == null || !isInteger(maxlen) ) {
        return '';
    }
    val = val.replace(/[^\u4E00-\u9FA5]/g,'');
    val = val.substr(0,maxlen);
    $('#'+id).val(val);
}














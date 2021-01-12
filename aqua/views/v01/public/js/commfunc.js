function setDate(gb, st , ed) {
    var today = new Date();
    var tmpDt = new Date();

    if (gb == "today") {
        var strDateS = formatDt(today);
        var strDateE = formatDt(today);
    }
    if (gb == "yesterday") {
        var tmpVal = tmpDt.setDate(tmpDt.getDate() - 1);
        tmpVal = new Date(tmpVal);
        var strDateS = formatDt(tmpVal);
        var strDateE = formatDt(tmpVal);
    }
    if (gb == "newly1week") {
        var tmpVal = tmpDt.setDate(tmpDt.getDate() - 7);
        tmpVal = new Date(tmpVal);
        var strDateS = formatDt(tmpVal);
        var strDateE = formatDt(today);
    }
    if (gb == "newly1month") {
        var tmpVal = tmpDt.setMonth(tmpDt.getMonth() - 1);
        tmpVal = new Date(tmpVal);
        var strDateS = formatDt(tmpVal);
        var strDateE = formatDt(today);
    }
    if (gb == "newly3month") {
        var tmpVal = tmpDt.setMonth(tmpDt.getMonth() - 3);
        tmpVal = new Date(tmpVal);
        var strDateS = formatDt(tmpVal);
        var strDateE = formatDt(today);
    }
    if (gb == "newly6month") {
        var tmpVal = tmpDt.setMonth(tmpDt.getMonth() - 6);
        tmpVal = new Date(tmpVal);
        var strDateS = formatDt(tmpVal);
        var strDateE = formatDt(today);
    }
    if (gb == "thismonth") {
        var tmpVal = tmpDt.setDate(0);
        tmpVal = new Date(tmpVal);
        tmpVal = new Date(tmpVal.setDate(tmpVal.getDate() + 1));
        var strDateS = formatDt(tmpVal);

        var tmpVal = new Date(today.setMonth(today.getMonth() + 1));
        tmpVal = new Date(tmpVal.setDate(0));
        var strDateE = formatDt(tmpVal);
    }
    if (gb == "lastmonth") {
        var tmpVal = tmpDt.setDate(0);
        tmpVal = new Date(tmpVal);
        var strDateE = formatDt(tmpVal);

        var tmpVal = new Date(tmpDt.setDate(0));
        tmpVal = new Date(tmpVal.setDate(tmpVal.getDate() + 1));
        var strDateS = formatDt(tmpVal);
    }

    $("#"+st).val(strDateS);
    $("#"+ed).val(strDateE);

    return false;
}

function formatDt(dt) {
    var year = dt.getFullYear();
    var mon = (dt.getMonth() + 1) > 9 ? '' + (dt.getMonth() + 1) : '0' + (dt.getMonth() + 1);
    var day = dt.getDate() > 9 ? '' + dt.getDate() : '0' + dt.getDate();
    var val = year + '-' + mon + '-' + day;
    return val;
}


/**
 * chkForm(form)
 *
 * 입력박스의 null 유무 체크와 패턴 체크
 *
 * @Usage	<form onSubmit="return chkForm(this)">
 */

function chkForm(form)
{
    if (typeof(mini_obj)!="undefined" || document.getElementById('_mini_oHTML')) mini_editor_submit();

    for (i=0;i<form.elements.length;i++){
        currEl = form.elements[i];
        if (currEl.disabled) continue;
        if (currEl.getAttribute("required")!=null || currEl.getAttribute("fld_esssential")!=null){
            if (currEl.type=="checkbox" || currEl.type=="radio"){
                if (!chkSelect(form,currEl,currEl.getAttribute("msgR"))) return false;
            } else {
                if (!chkText(currEl,currEl.value,currEl.getAttribute("msgR"))) return false;
            }
        }
        if (currEl.getAttribute("option")!=null && currEl.value.length>0){
            if (!chkPatten(currEl,currEl.getAttribute("option"),currEl.getAttribute("msgO"))) return false;
        }
        if (currEl.getAttribute("minlength")!=null){
            if (!chkLength(currEl,currEl.getAttribute("minlength"))) return false;
        }
        if (currEl.getAttribute("maxlen")!=null){
            if(!chkMaxLength(currEl,currEl.getAttribute("maxlen"))) return false;
        }
    }
    if (form.password2){
        if (form.password.value!=form.password2.value){
            alert("비밀번호가 일치하지 않습니다");
            form.password.value = "";
            form.password2.value = "";
            return false;
        }
    }

    if (form['resno[]'] && !chkResno(form)) return false;
    if (form.chkSpamKey) form.chkSpamKey.value = 1;
    if (document.getElementById('avoidDbl')) document.getElementById('avoidDbl').innerHTML = "--- 데이타 입력중입니다 ---";
    return true;
}

function chkMaxLength(field,len){
    if (chkByte(field.value) > len){
        if (!field.getAttribute("label")) field.setAttribute("label", field.name);
        alert("["+field.getAttribute("label") + "]은 "+ len +"Byte 이하 여야 합니다.");
        return false;
    }
    return true;
}

function chkLength(field,len)
{
    text = field.value;
    if (text.trim().length<len){
        alert(len + "자 이상 입력하셔야 합니다");
        field.focus();
        return false;
    }
    return true;
}

function chkText(field,text,msg)
{
    text = text.replace("　", "");
    text = text.replace(/\s*/, "");
    if (text==""){
        var caption = field.parentNode.parentNode.firstChild.innerText;
        if (!field.getAttribute("label")) field.setAttribute("label",(caption)?caption:field.name);
        if (!msg) msg = "[" + field.getAttribute("label") + "] 필수입력사항";
        alert(msg);
        if (field.tagName!="SELECT") field.value = "";
        if (field.type!="hidden") field.focus();
        return false;
    }
    return true;
}

function chkSelect(form,field,msg)
{
    var ret = false;
    fieldname = eval("form.elements['"+field.name+"']");
    if (fieldname.length){
        for (j=0;j<fieldname.length;j++) if (fieldname[j].checked) ret = true;
    } else {
        if (fieldname.checked) ret = true;
    }
    if (!ret){
        if (!field.getAttribute("label")) field.setAttribute("label", field.name);
        if (!msg) msg = "[" + field.getAttribute("label") + "] 필수선택사항";
        alert(msg);
        field.focus();
        return false;
    }
    return true;
}

function chkPatten(field,patten,msg)
{
    var regNum			= /^[0-9]+$/;
    var regEmail		= /^[^"'@]+@[._a-zA-Z0-9-]+\.[a-zA-Z]+$/;
    var regUrl			= /^(http\:\/\/)*[.a-zA-Z0-9-]+\.[a-zA-Z]+$/;
    var regAlpha		= /^[a-zA-Z]+$/;
    var regHangul		= /[\uAC00-\uD7A3]/;
    var regHangulEng	= /[\uAC00-\uD7A3a-zA-Z]/;
    var regHangulOnly	= /^[\uAC00-\uD7A3]*$/;
    var regId			= /^[a-zA-Z0-9]{1}[^"']{3,15}$/;
    var regPass			= /^[a-zA-Z0-9_-]{4,12}$/;
    var regPNum			= /^[0-9]*(,[0-9]+)*$/;

    patten = eval(patten);
    if (!patten.test(field.value)){
        if (!field.getAttribute("label")) field.setAttribute("label", field.name);
        if (!msg) msg = "[" + field.getAttribute("label") + "] 입력형식오류";
        alert(msg);
        field.focus();
        return false;
    }
    return true;
}

function formOnly(form){
    var i,idx = 0;
    var rForm = document.getElementsByTagName("form");
    for (i=0;i<rForm.length;i++) if (rForm[i].name==form.name) idx++;
    return (idx==1) ? form : form[0];
}

function chkResno(form)
{
    var resno = form['resno[]'][0].value + form['resno[]'][1].value;

    fmt = /^\d{6}[1234]\d{6}$/;
    if (!fmt.test(resno)) {
        alert('잘못된 주민등록번호입니다.'); return false;
    }

    birthYear = (resno.charAt(6) <= '2') ? '19' : '20';
    birthYear += resno.substr(0, 2);
    birthMonth = resno.substr(2, 2) - 1;
    birthDate = resno.substr(4, 2);
    birth = new Date(birthYear, birthMonth, birthDate);

    if ( birth.getYear()%100 != resno.substr(0, 2) || birth.getMonth() != birthMonth || birth.getDate() != birthDate) {
        alert('잘못된 주민등록번호입니다.');
        return false;
    }

    buf = new Array(13);
    for (i = 0; i < 13; i++) buf[i] = parseInt(resno.charAt(i));

    multipliers = [2,3,4,5,6,7,8,9,2,3,4,5];
    for (i = 0, sum = 0; i < 12; i++) sum += (buf[i] *= multipliers[i]);

    if ((11 - (sum % 11)) % 10 != buf[12]) {
        alert('잘못된 주민등록번호입니다.');
        return false;
    }
    return true;
}


//################   input text 글자 byte 표기 #############
//1. input 속성에  id="itDateShow" maxByte="100"  지정
//2. 글자수 표시 id값은  글자수 세는 폼값 id에Cnt 붙이기  id="itTitleCnt"
//3. 실행 => $("#itTitle).bind("keyup",viewTextNum);
//################3########################3#############
function viewTextNum(event)
{
    var $this	= $(this);
    var $currentLength   =    $this.val().length;
    var formNm			 =    $this.attr('id');
    var $maxmumLength    =    $this.attr('maxbyte');

    $("#"+formNm+"Cnt").html($currentLength);

    if($currentLength > $maxmumLength){
        $this.val($this.val().substr(0,$maxmumLength));
        $("#"+formNm+"Cnt").html($maxmumLength);
    }
}


//###################################################################
function check_all(f)
{
    var chk = document.getElementsByName("chk[]");

    for (i=0; i<chk.length; i++)
        chk[i].checked = f.chkall.checked;
}

function btn_check(f, act)
{
    if (act == "update") // 선택수정
    {
        f.action = list_update_php;
        str = "수정";
    }
    else if (act == "delete") // 선택삭제
    {
        f.action = list_delete_php;
        str = "삭제";
    }
    else
        return;

    var chk = document.getElementsByName("chk[]");
    var bchk = false;

    for (i=0; i<chk.length; i++)
    {
        if (chk[i].checked)
            bchk = true;
    }

    if (!bchk)
    {
        alert(str + "할 자료를 하나 이상 선택하세요.");
        return;
    }

    if (act == "delete")
    {
        if (!confirm("선택한 자료를 정말 삭제 하시겠습니까?"))
            return;
    }

    f.submit();
}

function is_checked(elements_name)
{
    var checked = false;
    var chk = document.getElementsByName(elements_name);
    for (var i=0; i<chk.length; i++) {
        if (chk[i].checked) {
            checked = true;
        }
    }
    return checked;
}

function delete_confirm(el)
{
    if(confirm("한번 삭제한 자료는 복구할 방법이 없습니다.\n\n정말 삭제하시겠습니까?")) {
        var token = get_ajax_token();
        var href = el.href.replace(/&token=.+$/g, "");
        if(!token) {
            alert("토큰 정보가 올바르지 않습니다.");
            return false;
        }
        el.href = href+"&token="+token;
        return true;
    } else {
        return false;
    }
}

function delete_confirm2(msg)
{
    if(confirm(msg))
        return true;
    else
        return false;
}

function get_ajax_token()
{
    var token = "";

    $.ajax({
        type: "POST",
        url: g5_admin_url+"/ajax.token.php",
        cache: false,
        async: false,
        dataType: "json",
        success: function(data) {
            if(data.error) {
                alert(data.error);
                if(data.url)
                    document.location.href = data.url;

                return false;
            }

            token = data.token;
        }
    });

    return token;
}

$(function() {
    $(document).on("click", "form input:submit", function() {
        var f = this.form;
        var token = get_ajax_token();

        if(!token) {
            alert("토큰 정보가 올바르지 않습니다.");
            return false;
        }

        var $f = $(f);

        if(typeof f.token === "undefined")
            $f.prepend('<input type="hidden" name="token" value="">');

        $f.find("input[name=token]").val(token);

        return true;
    });
});


//  file upload
function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $("#thumb").attr("src", e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
    }
}
//  image upload delete
function deleteURL() {
    var src = $("#thumb").data("src");
    $("#thumb").attr("src", src);
    $("#uploadFile").val("");
    $("#thumb_tmp").val("");
}


var tableToExcel = (function() {
  var uri = 'data:application/vnd.ms-excel;base64,'
    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="content-type" content="text/plain; charset=UTF-8"/></head><body><table>{table}</table></body></html>'
    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
  return function(table, name) {
    if (!table.nodeType) table = document.getElementById(table)
    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
    window.location.href = uri + base64(format(template, ctx))
  }
})()
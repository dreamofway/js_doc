//	공백 제거
String.prototype.trim = function(){
	return this.replace(/(^\s*)|(\s*$)/gi, "");
}
//	숫자 체크
String.prototype.isNum = function() {
	return (/^[0-9]+$/).test(this) ? true : false;
}
//	영어 체크
String.prototype.isEng = function() {
	return (/^[a-zA-Z]+$/).test(this) ? true : false;
}
//	영어숫자 체크
String.prototype.isEngNum = function() {
	return (/^[0-9a-zA-Z]+$/).test(this) ? true : false;
}
//	숫자영어 체크
String.prototype.isNumEng = function() {
	return this.isEngNum();
}
//	한글 체크
String.prototype.isKor = function() {
	return (/^[가-힣]+$/).test(this) ? true : false;
}
//	아이디 체크 (첫글자 영문)
String.prototype.isUserid = function() {
	return (/^[a-zA-z]{1}[0-9a-zA-Z]+$/).test(this) ? true : false;
}
//	이메일 체크
String.prototype.isEmail = function() {
	return (/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i).test(this.trim());
}
//	도메인 체크
String.prototype.isDomain = function() {
	return (/^((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i).test(this.trim());
}
//	일반전화 체크
String.prototype.isPhone = function() {
	var arg = arguments[0] ? arguments[0] : "";
	return eval("(/(02|0[3-9]{1}[0-9]{1,2})" + arg + "[1-9]{1}[0-9]{2,3}" + arg + "[0-9]{4}$/).test(this)");
}
//	휴대전화 체크
String.prototype.isMobile = function() {
	var arg = arguments[0] ? arguments[0] : "";
	return eval("(/01[016789]" + arg + "[1-9]{1}[0-9]{2,3}" + arg + "[0-9]{4}$/).test(this)");
}
//	날짜형식 체크
String.prototype.isDate = function() {
	return (/([1-2][0-9]{3})-([0][1-9]|[1][012])-([0-2][0-9]|[3][01])/).test(this) ? true : false;
}

//	콤마변환
function formatComma(str) {
	return str.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
//	날짜입력
$(document).ready(function() {
	$('.input-date').datepicker({
		calendarWeeks: false,
		todayHighlight: true,
		autoclose: true,
		toggleActive: true,
		format: "yyyy-mm-dd",
		language: "kr"
	});
});
<?php
/**
 * ---------------------------------------------------
 * AQUA Framework 기본 설정
 * ---------------------------------------------------
 * 필수 옵션 설명
 * ---------------------------------------------------
 * 
 * 변수 : $_aqua_config
 * 변수 유형 : array
 * $_aqua_config['url_rewrite'] : RewriteEngine 을 사용할 경우 true 로 설정합니다.
 * $_aqua_config['view'] : url_rewrite = true 일 때 사용되는 view 필수 옵션 view 파일 내에서 변수 처럼 사용 가능
 * $_aqua_config['view']['ver'] : 개발시 /aqua/views/ 하위에 생성해야하는 사용자 html 파일 구분용도의 명칭 (실제 만든 폴더명과 일치시키기만 하면 됨)
 * $_aqua_config['view']['use_layout'] : true / false - html 파일을 하나의 파일에서 유동적으로 구조를 변경할 수 있는 구조를 사용할 것인가에 대한 옵션 
 * $_aqua_config['view']['use_cdn'] : true / false - CDN 서버를 사용하는지에 대한 옵션 이 값에 때라 class.aqua 에서 view 를 생성 할 때 $img_path 변수 경로가 달라짐니다. 
 * $_aqua_config['view']['img_path'] : 공통 이미지 경로 use_cdn = true - 외부 url / use_cdn = false - 내부 /aqua/views/[ver]/ 하위의 경로를 적어줍니다 ex) '/public/img'
 * $_aqua_config['view']['favicon_path'] : 파비콘 이미지 경로 use_cdn = true - 외부 url / use_cdn = false - 내부 /aqua/views/[ver]/ 하위의 경로를 적어줍니다 ex) '/public/img/favicon.ico'
 * $_aqua_config['view']['meta_title'] : mata tag 의 값
 * $_aqua_config['view']['meta_description'] : mata tag 의 값
 * $_aqua_config['view']['meta_keywords'] : mata tag 의 값
 * $_aqua_config['view']['ogp_title'] : sns 공유시 표시될 제목
 * $_aqua_config['view']['ogp_stitle_name'] : sns 공유시 표시될 부제목
 * $_aqua_config['view']['ogp_image'] : sns 공유시 노출될 대표 이미지 경로
 * $_aqua_config['view']['ogp_url'] : sns 공유시 접근 url
 * $_aqua_config['db'][{DB 구분 키값}] : 접속 db 정보 설정 {DB 구분 키값} 은 DB 접속시 인자값으로 전달 해야합니다.
 * $_aqua_config['db'][{DB 구분 키값}]['host'] 
 * $_aqua_config['db'][{DB 구분 키값}]['user']
 * $_aqua_config['db'][{DB 구분 키값}]['dbname']
 * $_aqua_config['db'][{DB 구분 키값}]['dbpasswd']
 * 
 * ---------------------------------------------------
*/
define('DEFAULT_DB', 'doc'); # 사용 DB Key
define('CODE_SERVICE', 'SV'); # 서비스 코드 : SV001
define('CODE_SERVICE_ITEM', 'SI'); # 서비스 항목 코드 : SI001
define('CODE_SERVICE_FUNCTION', 'SF'); # 서비스 항목 기능 코드 : SF001
define('CODE_FOOD_LARGE', 'FL'); # 식품유형 대분류 : FL001
define('CODE_FOOD_MIDDLE', 'FM'); # 식품유형 중분류 코드 : FM001
define('CODE_FOOD_TYPE', 'FT'); # 식품유형 유형 코드 : FT001
define('UPLOAD_PATH', '/upload'); # 파일 업로드 위치
define('QR_CODE_PATH', UPLOAD_PATH. '/company/qrcode'); # qrcode 업로드 위치
define('UPLOAD_COMPANY_CONTRACT_PATH', UPLOAD_PATH.'/company/contract'); # 계약서 파일 업로드 위치
define('UPLOAD_COMPANY_SECURITY_PATH', UPLOAD_PATH.'/company/security'); # 정보보안 파일 업로드 위치

$_aqua_config = [];

$_aqua_config['url_rewrite'] = true;
$_aqua_config['view']['ver'] = '01'; # view 버전
$_aqua_config['view']['use_layout'] = true; # layout 사용여부 true : layout 구조에 contents 파일 inc / false : contents 파일 inc
$_aqua_config['view']['top_path'] = '/layout/top.php';
$_aqua_config['view']['left_menu_path'] = '/layout/menu.php';
$_aqua_config['view']['footer_path'] = '/layout/footer.php';
$_aqua_config['view']['use_cdn'] = false; # cdn 서버 사용여부 aqua.view() 에서 이미지 path 경로를 설정한다.
$_aqua_config['view']['img_path'] = '/public/images';
$_aqua_config['view']['favicon_path'] = '/public/images/favicon.ico';
$_aqua_config['view']['meta_title'] = '스마트공장 관리시스템';
$_aqua_config['view']['meta_description'] = '문서처리';
$_aqua_config['view']['meta_keywords'] = '문서처리';
$_aqua_config['view']['ogp_title'] = '문서처리';
$_aqua_config['view']['ogp_stitle_name'] = '문서처리';
$_aqua_config['view']['ogp_description'] = $_aqua_config['view']['meta_description'];
$_aqua_config['view']['ogp_image'] = '';
$_aqua_config['view']['ogp_url'] = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$_aqua_config['db']['doc']['host'] = '';
$_aqua_config['db']['doc']['user'] = '';
$_aqua_config['db']['doc']['dbname'] = '';
$_aqua_config['db']['doc']['dbpasswd'] = '';


$_aqua_config['file']['use_db_status'] = true;
$_aqua_config['file']['use_db'] = 'doc';
$_aqua_config['file']['use_table'] = 't_files';
// $_aqua_config['file']['make_dir'] = false;



?>
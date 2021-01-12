/**
 * ---------------------------------------------------
 * form 입력 유효성 체크 v1.0.0
 * ---------------------------------------------------
 * History
 * ---------------------------------------------------
 * [v1.0.0] 2020.02.28 - 이정훈
 *  - 초기 개발
 * ---------------------------------------------------
 * 설명
 * ---------------------------------------------------
 * data-valid attr 사용
 * data-valid="( blank,check(/체크 최소수), kr(/최소수(-최대수)) )"
 * blank : 공백확인만 진행, checkbox 또는 radio 체크 true/false 만 확인
 * check : 체크박스인 경우만 진행, 체크 true/false 확인 및 "check/3" 과 같이 입력시 최소 3개 이상 선택 유도
 * 정규식 체크 
 *      kr : 한글만
 *      en : 영문만
 *      num : 숫자만
 *      pw : 비밀번호 형식
 *      kr|en : 한글 또는 영문만 허용 
 *      en|num : 영문 또는 숫자만 허용
 *      str : 한글 또는 영문 또는 숫자 또는 특수문자 포함 허용
 *      email : 이메일 형식  
 * alert_type : add - 해당 tag 다음에 텍스트 영역 append 하여 보여줌 / alert - alert 창 띄움
 * 정규식
    regx_kr : 한글 완성형
    regx_notkr : 한글 완성형이 아닌 경우
    regx_en : 영문 대소문자
    regx_moten : 영문 대소문자가 아닌 경우
    regx_num : 숫자
    regx_notnum : 숫자가 아닌경우
    regx_chr : 영문 대소문자 숫자가 아닌 경우
    regx_kokr : 한글 완성형 또는 한글 자음모음 
    regx_ko : 한글 자음모음
    regx_kren : 영문 대소문자 한글 완성형에 공백 포함
    regx_notkren : 영문 대소문자 한글 완성형에 공백 포함이 아닌 경우
    email : 이메일 형식

 */
var viewFormValid = {
    form_id : ''
    , alert_type : 'add'
    , alert_color : '#cc6600'
    , pw_type : '||'
    , errors : []
    , regx_kr : /[가-힣]+/
    , regx_notkr : /[^가-힣]+/
    , regx_en : /[a-zA-Z]+/
    , regx_moten : /[a-zA-Z]+/
    , regx_num : /[0-9]+/
    , regx_notnum : /[^0-9]+/
    , regx_chr : /[^a-zA-Z0-9]+/
    , regx_kokr : /[가-힣ㄱ-ㅎ]+/
    , regx_ko : /[ㄱ-ㅎ]+/
    , regx_kren : /[a-zA-Z가-힣\s]+/
    , regx_notkren : /[^a-zA-Z가-힣\s]+/
    , email : /^[0-9a-zA-Z]([-_\.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-_\.]?[0-9a-zA-Z])*\.[a-zA-Z]{2,3}$/i
    , checkKR : function( arg_elenment ) {
        /**
         * 한글만 허용
         */
        var get_valid = $( arg_elenment ).data('valid').split('/');
        var get_valid_len = [];
        var check_val = $( arg_elenment ).val();
        var check_result = true;
        var check_len_result = true;
        var alert_msg = '한글로 __len__ 입력해주세요.';

        if( get_valid.length > 1 ) {
            get_valid_len = get_valid[1].split('-');
        }

        if( check_val == '' ) {

            check_result = false;

        } else {

            check_result = !( viewFormValid.regx_notkr.test( check_val ) );

        }

        len_check_result = viewFormValid.checkLength( check_val, alert_msg, get_valid_len ); 
        check_len_result = len_check_result['result'];
        alert_msg = len_check_result['return_msg'];
        

        if( ( check_result && check_len_result ) ) {
            
            return true;
            
        } else {

            viewFormValid.errors.push({
                id : $( arg_elenment ).attr('name')
            });

            return viewFormValid.alert( arg_elenment, alert_msg );

        }

    }
    , checkEN : function( arg_elenment ) {
        /**
         * 영문만 허용
         */

        var get_valid = $( arg_elenment ).data('valid').split('/');
        var get_valid_len = [];
        var check_val = $( arg_elenment ).val();
        var check_result = true;
        var check_len_result = true;
        var alert_msg = '영문으로 __len__ 입력해주세요.';

        if( get_valid.length > 1 ) {
            get_valid_len = get_valid[1].split('-');
        }

        if( check_val == '' ) {

            check_result = false;

        } else {

            check_result = !( viewFormValid.regx_moten.test( check_val ) );

        }

        len_check_result = viewFormValid.checkLength( check_val, alert_msg, get_valid_len ); 
        check_len_result = len_check_result['result'];
        alert_msg = len_check_result['return_msg'];
        

        if( ( check_result && check_len_result ) ) {
            
            return true;
            
        } else {

            viewFormValid.errors.push({
                id : $( arg_elenment ).attr('name')
            });

            return viewFormValid.alert( arg_elenment, alert_msg );

        }

    }
    , checkNum : function( arg_elenment ) {
        /**
         * 숫자만 허용
         */

        var get_valid = $( arg_elenment ).data('valid').split('/');
        var get_valid_len = [];
        var check_val = $( arg_elenment ).val();
        var check_result = true;
        var check_len_result = true;
        var alert_msg = '숫자로 __len__ 입력해주세요.';

        if( get_valid.length > 1 ) {
            get_valid_len = get_valid[1].split('-');
        }

        if( check_val == '' ) {

            check_result = false;

        } else {

            check_result = !( viewFormValid.regx_notnum.test( check_val ) );

        }

        len_check_result = viewFormValid.checkLength( check_val, alert_msg, get_valid_len ); 
        check_len_result = len_check_result['result'];
        alert_msg = len_check_result['return_msg'];
        

        if( ( check_result && check_len_result ) ) {
            
            return true;
            
        } else {

            viewFormValid.errors.push({
                id : $( arg_elenment ).attr('name')
            });

            return viewFormValid.alert( arg_elenment, alert_msg );

        }

    }
    , checkEmail : function( arg_elenment ) {
        /**
         * 이메일 형식 체크
         */        
        var check_val = $( arg_elenment ).val();        
        var alert_msg = '이메일 형식으로 입력해주세요.';

        if( viewFormValid.email.test( check_val ) ) {
            
            return true;
            
        } else {

            viewFormValid.errors.push({
                id : $( arg_elenment ).attr('name')
            });

            return viewFormValid.alert( arg_elenment, alert_msg );

        }
    }
    , checkString : function( arg_elenment ){
        /**
         * 영문/숫자/특수문자 허용
         */
        var get_valid = $( arg_elenment ).data('valid').split('/');
        var get_valid_len = [];
        var check_val = $( arg_elenment ).val();
        var check_result = true;
        var check_len_result = true;
        var alert_msg = '한글/영문/숫자/특수문자  형식으로 __len__ 입력해주세요.';

        if( get_valid.length > 1 ) {
            get_valid_len = get_valid[1].split('-');
        }

        if( check_val == '' ) {

            check_result = false;

        } else {

            check_result = ( ( viewFormValid.regx_en.test( check_val ) || viewFormValid.regx_num.test( check_val ) || viewFormValid.regx_chr.test( check_val ) ) && ( !( viewFormValid.regx_ko.test( check_val ) ) ) );

        }

        len_check_result = viewFormValid.checkLength( check_val, alert_msg, get_valid_len ); 
        check_len_result = len_check_result['result'];
        alert_msg = len_check_result['return_msg'];
        

        if( ( check_result && check_len_result ) ) {
            
            return true;
            
        } else {

            viewFormValid.errors.push({
                id : $( arg_elenment ).attr('name')
            });

            return viewFormValid.alert( arg_elenment, alert_msg );

        }

    }
    , checkEnNum : function( arg_elenment ){
        /**
         * 영문/숫자만 허용
         */
        var get_valid = $( arg_elenment ).data('valid').split('/');
        var get_valid_len = [];
        var check_val = $( arg_elenment ).val();
        var check_result = true;
        var check_len_result = true;
        var alert_msg = '영문 또는 숫자 형식으로 __len__ 입력해주세요.';

        if( get_valid.length > 1 ) {
            get_valid_len = get_valid[1].split('-');
        }

        if( check_val == '' ) {

            check_result = false;

        } else {

            check_result = !( viewFormValid.regx_chr.test( check_val ) );

        }

        len_check_result = viewFormValid.checkLength( check_val, alert_msg, get_valid_len ); 
        check_len_result = len_check_result['result'];
        alert_msg = len_check_result['return_msg'];
        

        if( ( check_result && check_len_result ) ) {
            
            return true;
            
        } else {

            viewFormValid.errors.push({
                id : $( arg_elenment ).attr('name')
            });

            return viewFormValid.alert( arg_elenment, alert_msg );

        }


    }
    , checkPw : function( arg_elenment ) {

        var get_valid = $( arg_elenment ).data('valid').split('/');
        var get_valid_len = [];
        var check_val = $( arg_elenment ).val();
        var check_result = true;
        var check_len_result = true;
        var alert_msg = '';
        var pw_confirm = '';
        
        if( get_valid.length > 1 ) {
            get_valid_len = get_valid[1].split('-');
        }


        if( viewFormValid.pw_type == '||' ) {
            // 영문, 숫자, 특수문자 중 하나만 일치해도 OK            
            check_result = ( viewFormValid.regx_en.test( check_val ) || viewFormValid.regx_num.test( check_val ) || viewFormValid.regx_chr.test( check_val ) );
            alert_msg = '영문 또는 숫자 형식으로 __len__ 입력해주세요.';
        } else {
            // & 영문,숫자,특수문자 모두 포함
            check_result = ( viewFormValid.regx_en.test( check_val ) && viewFormValid.regx_num.test( check_val ) && viewFormValid.regx_chr.test( check_val ) );
            alert_msg = '영문/숫자/특수문자 조합 형식으로 __len__ 입력해주세요.';
        }

        len_check_result = viewFormValid.checkLength( check_val, alert_msg, get_valid_len ); 
        check_len_result = len_check_result['result'];
        alert_msg = len_check_result['return_msg'];

        if( !( check_result && check_len_result ) ) {
            
            viewFormValid.errors.push({
                id : $( arg_elenment ).attr('name')
            });

            return viewFormValid.alert( arg_elenment, alert_msg );
        }

        // 패스워드 타입이 더 있는지 확인                
        if( $( '#'+ viewFormValid.form_id ).find('input[type="password"]').length > 1 ) {
            
            // 비밀번호 확인 input 확인
            $.each( $( '#'+ viewFormValid.form_id ).find('input[type="password"]'), function(){
                if( $(this).attr('name') !== $( arg_elenment ).attr('name') ){                            
                    pw_confirm = this;
                    return false;
                }                        
            }); 

            if( $( pw_confirm ).val() !== check_val ) {

                viewFormValid.errors.push({
                    id : $( arg_elenment ).attr('name')
                });

                return viewFormValid.alert( pw_confirm, '비밀번호 입력 값과 재입력 값이 일치하지 않습니다.' );

            }

        }

    }
    , checkKrEn : function( arg_elenment ) {
        /**
         * 한글/영문만 허용
         */

        var get_valid = $( arg_elenment ).data('valid').split('/');
        var get_valid_len = [];
        var check_val = $( arg_elenment ).val();
        var check_result = true;
        var check_len_result = true;
        var alert_msg = '한글/영문 형식으로 입력해주세요.';

        if( get_valid.length > 1 ) {
            get_valid_len = get_valid[1].split('-');
        }

        if( check_val == '' ) {

            check_result = false;

        } else {

            check_result = ( ( viewFormValid.regx_kren.test( check_val ) )  && ( !( viewFormValid.regx_ko.test( check_val ) )  && ( !( viewFormValid.regx_notkren.test( check_val ) ) ) ) );

        }

        

        if( ( check_result ) ) {
            
            return true;
            
        } else {

            viewFormValid.errors.push({
                id : $( arg_elenment ).attr('name')
            });

            return viewFormValid.alert( arg_elenment, alert_msg );

        }

    }
    ,regexHandler : function( arg_elenment ) {

        
        var regx_ogj = {
            'kr' : this.checkKR
            , 'en' : this.checkEN
            , 'num' : this.checkNum            
            , 'email' : this.checkEmail
            , 'pw' : this.checkPw
            , 'en|num' : this.checkEnNum
            , 'str' : this.checkString
            , 'kr|en' : this.checkKrEn
        };
        
        
        
        if( regx_ogj[ $( arg_elenment ).data('valid').split('/')[0] ] ) {

            console.log( '체크유형 : ' + get_valid[0] );
            
            return regx_ogj[ $( arg_elenment ).data('valid').split('/')[0] ]( arg_elenment );
            

        } else {

            viewFormValid.errors.push({
                id : $( arg_elenment ).attr('name')
            });
            
            console.error('Err - viewFormValid.regexHandler() : 정의되지 않은 valid 형식입니다. [ ' + get_valid[0] + ' ]' );
            return true;

        }
        
        return true;
        
    }
    , checkLength : function( arg_val, arg_msg, arg_len ) {

        /**
         * 문자열 길이 체크
         */
        var result = [];

        if( arg_len.length > 0 ) {
            
            if( arg_len.length > 1 ) {
            
                if( ( arg_val.length < arg_len[0] ) || ( arg_val.length > arg_len[1] ) ) {
                    result['result'] = false;
                } else {
                    result['result'] = true
                }
    
                result['return_msg'] = arg_msg.replace('__len__', arg_len[0]+'자 이상 '+ arg_len[1] + '자 이하로' );
    
            } else {
    
                if( arg_len[0] > 0 ){
                    if( arg_val.length < arg_len[0] ) {
                        result['result'] = false;
                    } else {
                        result['result'] = true
                    }
    
                    result['return_msg'] = arg_msg.replace('__len__', arg_len[0]+'자 이상으로');
    
                } 
    
            }

        } else {
            result['result'] = true
            result['return_msg'] = arg_msg.replace('__len__', '');
        }
         

        return result;

    }
    ,alert : function( arg_element, arg_msg ) {      
        /**
         *  알림처리
         */
        if( this.alert_type == 'add' ) {
            $( arg_element ).parent().append( '<div class="__alert_msg" ><br><br><span style="color:'+ this.alert_color +'" >' + arg_msg + '</span></div>' );
            return true;
        } else {
            alert( arg_msg );
            $( arg_element ).focus();
            return false;
        }
        
    }
    ,run : function( arg_form_id ) {
        /**
         * form 객체의 하위 입력 태그를 찾아 vaild 값 확인 및 처리
         */
        this.form_id = arg_form_id;
        this.errors = [];
        var alert_str = '';
        var work_guide_text = {
            text : '입력'
            ,password : '입력'
            ,checkbox : '선택'
            ,radio : '선택'
            ,undefined : '선택'
        };

        if( arg_form_id == '' ) {
            console.error( '확인 할 form id를 전달해야합니다.' ); return;
        }

        if( $('#' + arg_form_id ).length == 0  ) {
            console.error( 'form id 와 일치한 태그가 없습니다. id를 다시 확인해주세요.' ); return;
        }
        
        $('.__alert_msg').remove();

        $.each( $('#' + arg_form_id ).find('input, select, textarea'), function(idx, item){
            
            alert_str = '항목을 ' + work_guide_text[ $(item).attr('type') ] + ' 해주세요.';

            if( $(item).data('valid') === undefined ) {
                // 다음 객체로 이동
                return true;
            }

            get_valid = $(item).data('valid').split('/');

            switch( get_valid[0] ) {
               
                case 'blank' : {
                    // 공백체크
                    
                    // console.log( $(item).attr('type') + ' : id=' + $(item).attr('id') + ' : name=' + $(item).attr('name') + ' : value =' + $(item).val() );
                    
                    switch( $(item).attr('type') ) {

                        case 'radio' : {

                            if( $('input:radio[name="'+ $(item).attr('name') +'"]').is(":checked") === false ) {

                                viewFormValid.errors.push({
                                    id : $(item).attr('name')
                                });

                                return viewFormValid.alert( item, alert_str );

                            }

                            break;
                        }
                        case 'checkbox' : {
                        
                            if( $('input:checkbox[name="'+ $(item).attr('name') +'"]').is(":checked") === false ) {

                                viewFormValid.errors.push({
                                    id : $(item).attr('name')
                                });
                                
                                return viewFormValid.alert( item, alert_str );
                            }

                            break;
                        }
       
                        default : {

                            if( $(item).val() == '' ) {
                                
                                viewFormValid.errors.push({
                                    id : $(item).attr('name')
                                });

                                return viewFormValid.alert( item, alert_str );

                            }

                        }
                    }
                    
                    break;
                }

                case 'check' : {
                    
                    if( $(item).attr('type') !== 'checkbox' ) {
                        
                        console.error('type Err : ' + $(item).attr('name') + '는 checkbox TAG가 아닙니다.' );

                        viewFormValid.errors.push({
                            id : $(item).attr('name')
                        });

                        return true;
                    }

                    if( $('input:checkbox[name="'+ $(item).attr('name') +'"]').is(":checked") === false ) {

                        viewFormValid.errors.push({
                            id : $(item).attr('name')
                        });

                        return viewFormValid.alert( item, alert_str );
                        

                    } else {
                        
                        if( $('input:checkbox[name="'+ $(item).attr('name') +'"]:checked').length < get_valid[1] ) {
                            
                            viewFormValid.errors.push({
                                id : $(item).attr('name')
                            });

                            return viewFormValid.alert( item
                                , '항목을 ' + get_valid[1] + '개 이상 선택해주세요.'
                            );
                            
                        }

                    }

                    break;
                }
                default : {
                    return viewFormValid.regexHandler( item );
                }
                
            }
            

        });

        if( this.errors.length > 0 ) {
            return false;
        } else {
            return true;
        }
    
    }
}
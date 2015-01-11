<?php
namespace WebStream\Delegate;

/**
 * ルーティングルールを記述する
 */
Router::setRule([
    '/' => "test#test1",
    '/top' => "test#test2",
    '/top/:id' => "test#test3",
    '/notfound/controller' => "notfound#test1",
    '/notfound/action' => "test#notfound",
    '/action' => "test#test_action",
    '/action2' => "test#test_action_hoge_fuga",
    '/feed.:format' => "test#test_feed",
    '/snake' => "test_snake#index",
    '/snake2' => "test_snake_hoge_fuga#index",
    '/encoded/:name' => "test#test_encoded",
    '/similar/:name' => "test#test_similar1",
    '/similar/:name/:page' => "test#test_similar2",
    '/snake_ng1' => "test__snake#index",
    '/snake_ng2' => "test_snake_#index",
    '/no_service' => "test_no_service_class#execute",
    '/no_service2' => "test_no_service_method#execute",
    '/no_service_no_model' => "test_no_service_and_model#execute",
    '/test_service1' => "test#service1",
    '/test_service2' => "test#service2",
    '/exist_service_exist_model_exist_model_method_param' => "test_exist_service_exist_model_exist_model_method#send_param",
    '/exist_service_exist_model_exist_model_method_params' => "test_exist_service_exist_model_exist_model_method#send_params",
    '/test_template/index1' => "test_template#index1",
    '/test_template/index2' => "test_template#index2",
    '/test_template/index3' => "test_template#index3",
    '/test_template/index4' => "test_template#index4",
    '/test_template/index5' => "test_template#index5",
    '/test_template/index6' => "test_template#index6",
    '/test_template/index7' => "test_template#index7",
    '/test_template/error1' => "test_template#error1",
    '/test_template/error2' => "test_template#error2",
    '/test_template/error3' => "test_template#error3",
    '/test_template/error4' => "test_template#error4",
    '/test_template/error5' => "test_template#error5",
    '/test_template/error6' => "test_template#error6",
    '/test_template/error7' => "test_template#error7",
    '/test_template/error8' => "test_template#error8",
    '/test_template/model/is_model' => "test_template_with_model#index1",
    '/test_template/model/access_db' => "test_template_with_model#index2",
    '/test_template/helper/is_helper' => "test_template_with_helper#index1",
    '/test_template/helper/access_helper' => "test_template_with_helper#index2",
    '/test_template/model/null_model' => "test_template_without_model#index1",
    '/test_template/helper/null_helper' => "test_template_without_helper#index1",
    '/test_template/javascript1' => "test_template#javascript1",
    '/test_template/javascript2' => "test_template#javascript2",
    '/test_template/javascript3' => "test_template#javascript3",
    '/test_template/javascript4' => "test_template#javascript4",
    '/test_template/javascript5' => "test_template#javascript5",
    '/test_template/javascript6' => "test_template#javascript6",
    '/test_template/javascript7' => "test_template#javascript7",
    '/test_template/javascript8' => "test_template#javascript8",
    '/test_template/html1' => "test_template#html1",
    '/test_template/php1' => "test_template#php1",
    '/csrf' => "test_security#test_csrf",
    '/csrf_get' => "test_security#test_csrf_get",
    '/csrf_post' => "test_security#test_csrf_post",
    '/csrf_post_view' => "test_security#test_csrf_post_view",
    '/test_header/html' => "test_header#test1",
    '/test_header/xml' => "test_header#test2",
    '/test_header/atom' => "test_header#test3",
    '/test_header/rss' => "test_header#test4",
    '/test_header/rdf' => "test_header#test5",
    '/test_header/get1' => "test_header#test6",
    '/test_header/get2' => "test_header#test7",
    '/test_header/post1' => "test_header#test8",
    '/test_header/post2' => "test_header#test9",
    '/test_header/get_or_post' => "test_header#test10",
    '/test_header/post_or_put' => "test_header#test11",
    '/test_header/html_get' => "test_header#test12",
    '/test_header/xml_post' => "test_header#test13",
    '/test_header/dummy_contenttype' => "test_header#test14",
    '/test_header/dummy_allowmethod' => "test_header#test15",
    '/test_header/parent/html' => "test_header#test16",
    '/test_template_cache/index1' => "test_template_cache#index1",
    '/get_validate1' => "test_validator#get_required",
    '/get_validate2' => "test_validator#get_min_length",
    '/get_validate3' => "test_validator#get_max_length",
    '/get_validate4' => "test_validator#get_min",
    '/get_validate5' => "test_validator#get_max",
    '/get_validate6' => "test_validator#get_equal",
    '/get_validate7' => "test_validator#get_length",
    '/get_validate8' => "test_validator#get_range",
    '/get_validate9' => "test_validator#get_regexp",
    '/get_validate10' => "test_validator#get_number",
    '/post_validate1' => "test_validator#post_required",
    '/post_validate2' => "test_validator#post_min_length",
    '/post_validate3' => "test_validator#post_max_length",
    '/post_validate4' => "test_validator#post_min",
    '/post_validate5' => "test_validator#post_max",
    '/post_validate6' => "test_validator#post_equal",
    '/post_validate7' => "test_validator#post_length",
    '/post_validate8' => "test_validator#post_range",
    '/post_validate9' => "test_validator#post_regexp",
    '/post_validate10' => "test_validator#post_number",
    '/put_validate1' => "test_validator#put_required",
    '/put_validate2' => "test_validator#put_min_length",
    '/put_validate3' => "test_validator#put_max_length",
    '/put_validate4' => "test_validator#put_min",
    '/put_validate5' => "test_validator#put_max",
    '/put_validate6' => "test_validator#put_equal",
    '/put_validate7' => "test_validator#put_length",
    '/put_validate8' => "test_validator#put_range",
    '/put_validate9' => "test_validator#put_regexp",
    '/put_validate10' => "test_validator#put_number",
    '/exception_handler1' => "test_exception_handler#index1",
    '/exception_handler2' => "test_exception_handler#index2",
    '/exception_handler3' => "test_exception_handler#index3",
    '/exception_handler4' => "test_exception_handler#index4",
    '/exception_handler5' => "test_exception_handler#index5",
    '/exception_handler6' => "test_exception_handler#index6",
    '/exception_handler7' => "test_exception_handler#error1",
    '/exception_handler8' => "test_exception_handler#error2",
    '/exception_handler9' => "test_exception_handler#error3",
    '/exception_handler10' => "test_exception_handler#error4",
    '/exception_handler11' => "test_exception_handler#error5",
    '/multiple_exception_handler1' => "test_multiple_exception_handler#index1",
    '/multiple_exception_handler2' => "test_sub_multiple_exception_handler#index1",
    '/multiple_exception_handler3' => "test_multiple_exception_handler#index2",
    '/multiple_exception_handler4' => "test_multiple_exception_handler#index3",
    '/multiple_exception_handler5' => "test_multiple_exception_handler#index4",
    '/multiple_exception_handler6' => "test_multiple_exception_handler#index5",
    '/double_exception_handler1' => "test_double_exception_handler#index1",
    '/double_exception_handler2' => "test_double_exception_handler#index2",
    '/session_limit' => "test_session#set_session_limit_expire",
    '/session_no_limit' => "test_session#set_session_no_limit_expire",
    '/session_index' => "test_session#index1",
    '/before_after_filter' => "test_filter#index",
    '/before_after_multiple_filter' => "test_multiple_filter#index",
    '/before_after_override_filter' => "test_override_filter#index",
    '/initialize_filter_error' => "test_initialize_filter#index",
    '/invalid_filter_error' => "test_invalid_filter#index",
    '/before_filter_except_enable' => "test_except_filter#index",
    '/before_filter_except_disable' => "test_except_filter#index2",
    '/after_filter_except_enable' => "test_except_filter#index3",
    '/after_filter_except_disable' => "test_except_filter#index4",
    '/before_filter_only_enable' => "test_only_filter#index",
    '/before_filter_only_disable' => "test_only_filter#index2",
    '/after_filter_only_enable' => "test_only_filter#index3",
    '/after_filter_only_disable' => "test_only_filter#index4",
    '/before_filter_multiple_except_enable' => "test_multiple_except_filter#index",
    '/before_filter_multiple_except_enable2' => "test_multiple_except_filter#index2",
    '/after_filter_multiple_except_enable' => "test_multiple_except_filter#index3",
    '/after_filter_multiple_except_enable2' => "test_multiple_except_filter#index4",
    '/before_filter_multiple_only_enable' => "test_multiple_only_filter#index",
    '/before_filter_multiple_only_enable2' => "test_multiple_only_filter#index2",
    '/after_filter_multiple_only_enable' => "test_multiple_only_filter#index3",
    '/after_filter_multiple_only_enable2' => "test_multiple_only_filter#index4",
    '/filter_except_only' => "test_except_only_filter#index",
    '/skip_filter1' => "test_skip_filter#index1",
    '/skip_filter2' => "test_skip_filter#index2",
    '/test_helper1' => "test_helper#help1",
    '/test_helper2' => "test_helper#help2",
    '/test_helper3' => "test_helper#help3",
    '/test_helper4' => "test_helper#help4",
    '/test_helper5' => "test_helper#help5",
    '/test_helper6' => "test_helper#help6",
    '/test_model1' => "test_mysql#model1",
    '/test_model2' => "test_mysql#model2",
    '/test_model3' => "test_mysql#model3",
    '/test_model4' => "test_sqlite#model1",
    '/test_model5' => "test_sqlite#model2",
    '/test_model6' => "test_mysql#model5",
    '/test_model7' => "test_mysql#model4",
    '/test_model8' => "test_database_error1#model1",
    '/test_model9' => "test_database_error2#model1",
    '/test_model10' => "test_mysql#model6",
    '/test_model11' => "test_database_error3#model1",
    '/test_model12' => "test_postgres#model1",
    '/test_model13' => "test_postgres#model2",
    '/test_model14' => "test_postgres#model3",
    '/test_model15' => "test_postgres#model4",
    '/test_model16' => "test_postgres#model5",
    '/test_model17' => "test_postgres#model6",
    '/test_model18' => "test_sqlite#model3",
    '/test_model19' => "test_mysql#model7",
    '/test_model20' => "test_postgres#model7",
    '/test_model21' => "test_sqlite#model4",
    '/test_model22' => "test_multiple_database#model1",
    '/test_model23' => "test_mysql_yml#model1",
    '/test_model24' => "test_mysql_yaml#model1",
    '/test_model25' => "test_mysql#model8",
    '/test_model26' => "test_postgres#model8",
    '/test_model27' => "test_sqlite#model5",
    '/test_transaction1' => "test_transaction#transaction_in_controller",
    '/test_transaction2' => "test_transaction#transaction_rollback_in_controller",
    '/test_transaction3' => "test_transaction#transaction_in_model",
    '/test_transaction_clear' => "test_transaction#clear",
    '/test_model_prepare' => "test_mysql#prepare",
    '/test_model_clear' => "test_mysql#clear",
    '/test_model_prepare2' => "test_postgres#prepare",
    '/test_model_clear2' => "test_postgres#clear",
    '/test_model_prepare3' => "test_sqlite#prepare",
    '/test_model_clear3' => "test_sqlite#clear",
    '/test_model_prepare4' => "test_mysql_yml#prepare",
    '/test_model_clear4' => "test_mysql_yml#clear",
    '/test_template/xml' => "test_template#xml",
    '/test_json1' => "test_json#json1",
    '/test_json2' => "test_json#json2",
    '/test_jsonp1' => "test_json#jsonp1",
    '/test_jsonp2' => "test_json#jsonp2",
    '/test_iterator1' => "test_iterator#count",
    '/test_iterator2' => "test_iterator#seek",
    '/test_iterator3' => "test_iterator#seek_failure",
    '/test_iterator4' => "test_iterator#key_value",
    '/test_iterator5' => "test_iterator#array_access_get",
    '/test_iterator6' => "test_iterator#array_access_set",
    '/test_custom_dir1' => "test_custom_dir#from_controller",
    '/test_custom_dir2' => "test_custom_dir#from_service",
    '/test_custom_dir3' => "test_custom_dir#from_model",
    '/test_custom_dir4' => "test_custom_dir#from_view",
    '/test_custom_dir5' => "test_custom_dir#from_helper"
]);

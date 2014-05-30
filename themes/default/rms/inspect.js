
var __wtw_lucky_ref_id = 1256357;
var __wtw_lucky_setup_key = {};
__wtw_lucky_setup_key.id = '10981';
__wtw_lucky_setup_key.key = 'k8k7eB6RUqZuvejdjgQdRaQpRYxYeyAG';

    (function() {
        var wa = document.createElement('script'); wa.type = 'text/javascript'; wa.async = true;
        wa.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://ca406015') + '.luckyorange.com/w.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(wa, s);
      })();
    
    
    
 

// JavaScript Document
// embed the watcher swf
var __wtw_g_started = false;
var __wtw_dom_ready_loaded = false;

var WTW_Watcher = {
    
    extra_msg: '',
    offset_bottom: 0,
    disable_ctrl_shift : false,
    third_party_opt_out : '',
    pre_chat_question1 : 'Enter Your Name',
    pre_chat_question2 : 'Enter Your Email',
    pre_chat_question3 : '',
    
    
    can_record_sess : false,
    
    
    pre_chat_question1_slashed : 'Enter Your Name',
    pre_chat_question2_slashed : 'Enter Your Email',
    pre_chat_question3_slashed : '',
    
    text : { 'text_btn_yes' : 'Yes, please.',
             'text_btn_no' : 'No thanks.',
             'text_btn_stop' : 'Stop asking me.',
             'text_btn_lbl' : 'chat',
             'text_btn_yes' : 'Yes, please.',
    },
        
    use_full: 0,
    white_labeled: false,
    never_show_chat_buttons: window.__wtw_lucky_no_chat_box || false,
    remove_powered_by: false,
    privacy_disable_keystrokes : false,
     privacy_disable_mouse_clicks : false,
     privacy_disable_mouse_movements : false,
     privacy_disable_scrolls : false,
     
     encoded_ref: 'http%3A%2F%2Fwww.audiorms.pl%2Fcar-audio%2Fakumlatory-i-kondensatory%2F',
     show_offline_form: false,
     scan_html_diffs: false,
     detect_hash_changes: false,
     
     my_ip: '78.28.59.157',
     track_all_errors: false,
     spy_request_queue: [],
     
      // prechat
      
     pre_chat_ask_question: false,
         
     ga_push_poll_events: 0,
     ga_push_chat_events: 0,
     ga_event_cat: 'lo_analytics',
     
     click_to_chat_title_length: 20,
     on_poll_exit_just_hide: false,
     offline_message_length: 25,
     channel_site: '18486dfdf2dd62a38573a7aa2c9d08df',
     channel_me: '103d92609566c81997acd5860cec840f',
     flash_save_is_loaded: false,
     
     kick_idle_after: 60,
     kick_idle_after_timeout_id: 0,
     
     roomID : 6497,
      click_to_chat_title: 'Live Help is Online.',
     offline_msg_title: 'Offline. Leave a Message.',
     do_not_record: window.__wtw_lucky_do_not_record || false,
     
     do_not_track: false,
      is_saving_mouse_coords: true,
     main_record_event_id: 0,
    
         my_uid: '50eb667624203782859157',
     my_uuid: 'vis_50eb667624203782859157_510984',
     
     my_pass_key: 'null',
     chat_btn_alignment: ('undefined' === typeof window.__wtw_lucky_chat_align) ? 'right' : window.__wtw_lucky_chat_align,
     
     colors : { 'chat_subtitle' : '#999999',
                'chat_bg' : '#000000',
                'chat_title' : '#ffffff',
                'chat_subtitle' : '#999999',
                'chat_border' : '#ffffff',
                'chat_show_blinker' : '1',
                'is_chat_bg_light' : '',
                
                'chat_bg_lighter' : '#262626',
                'chat_bg_lighter2' : '#404040',
                'chat_bg_lighter3' : '#808080',
                'chat_bg_text_bg' : '#262626',
                'chat_bg_darker1' : '#000000',
                'chat_bg_darker2' : '#000000'
                                
                },
};
     
    
// English Translations
var _lo_words = {};

_lo_words.connecting = 'Connecting to chat...';
_lo_words.paging = "One moment, we're paging an agent...";
_lo_words.chatting_with = "Chatting with ";
_lo_words.no_response = "Sorry, no one responded. ";
_lo_words.page_again = "Page them again?";
_lo_words.agent_typing = ' is typing...';
_lo_words.agent_typed_text = ' has typed some text';    
_lo_words.agent_idle = ' is idle.';
_lo_words.agent_gone = ' is away from keyboard.';
_lo_words.powered_by = 'Powered By';
_lo_words.comments = 'Comments';

// Dashboard Interface
_lo_words.joined_room = 'has joined the room.';
_lo_words.left_room = 'has exited the room.';

_lo_words.operator_chat_title = 'Operator Chat';
_lo_words.visitor_chat_title = 'Visitor Chat';

_lo_words.type_here = 'Type here and press <enter> to chat with other operators.';

_lo_words.direct_link = 'Direct Link';

_lo_words.visitor_view_line = 'Visitor could view up to this line';
_lo_words.avg_viewable_screen_height = 'The average viewable screen height is this line.';

_lo_words.ended_session = 'The operator ended the session.';

_lo_words.disconnected = 'Lost connection with the server...';

_lo_words.confirm_end_chat = 'Are you sure you want to end the chat session?';

_lo_words.submit_lbl = 'Submit';



    
    
    
        // Load Main JS
    (function() {
        
        var loaded_search = false;
        var loaded_diff = false;
        var lo_loaded = false;
        
        load_search();
        
    
        function load_lo()
        {
            try
            {
                var pre_path = ('https:' == document.location.protocol ? 'https://ssl' : 'http://w1') ;                
                load_script( pre_path + '.luckyorange.com/lo.min.js?nc=1', function(){ 
                    lo_loaded = true;
                });                
            }
            catch(ex)
            {
            }
        }
        
        function check_done()
        {
            // load the final script only if pre-reqs are loaded
            if (loaded_search && loaded_diff)
            {
                load_lo();
            }
        }
        
        function load_search()
        {
            try
            {        
                // search engines
                var pre_path = ('https:' == document.location.protocol ? 'https://ssl' : 'http://w1') ;
                
                load_script( pre_path + '.luckyorange.com/js/engines.min.js', function(){ 
                    loaded_search = true;                    
                    check_done();
                });
                
                // scan diffs
                if (WTW_Watcher.scan_html_diffs)
                {
                    load_script( pre_path + '.luckyorange.com/js/diff_match_patch.min.js', function(){ 
                        loaded_diff = true;
                        check_done();
                    });    
                }
                else
                {                
                    loaded_diff = true;
                    check_done();
                }
                                            
                
            }
            catch(ex)
            {
            }
        }
        
        function load_script(url, callback)
        {
            try
            {
                var head = document.head || document.getElementsByTagName( "head" )[0] || document.documentElement;
                    var script = document.createElement("script");
                              
                    script.async = true;
                    script.charset = 'utf-8';
                    script.type = 'text/javascript';
                    script.src = url;
                    
                    // Attach handlers for all browsers
                    script.onload = script.onreadystatechange = function( _, isAbort ) {
                        
                        if ( isAbort || !script.readyState || /loaded|complete/.test( script.readyState ) ) {

                            // Handle memory leak in IE
                            script.onload = script.onreadystatechange = null;
                        
                            if (typeof(callback) === 'function')
                            {
                                callback();
                            }
                        }
        
                    }
                    
                    head.insertBefore( script, head.firstChild );
            }
            catch(ex)
            {
            }
        }
        
    })();
    
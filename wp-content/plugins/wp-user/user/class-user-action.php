<?php

if (!class_exists('wpuserAjax')) :

    class wpuserAjax
    {

        public function __construct()
        {
            $this->ip = $_SERVER["REMOTE_ADDR"];
            $this->time = time();
            add_action('wp_user_action_register', array($this, 'wp_user_action_register_function'));
            add_action('wp_user_action_login_attempts_admin_notify', array($this, 'wp_user_action_login_attempts_admin_notify_function'));
            add_filter('wp_user_filter_email', array($this, 'wp_user_filter_email_function'), 10, 6);

            add_action('after_setup_theme', array($this, 'remove_admin_bar'));

            add_action('wp_ajax_nopriv_wpuser_login_action', array($this, 'wpuser_login'));
            add_action('wp_ajax_nopriv_wpuser_forgot_action', array($this, 'wpuser_forgot'));
            add_action('wp_ajax_nopriv_wpuser_register_action', array($this, 'wpuser_register'));
            add_action('wp_ajax_nopriv_wpuser_activation', array($this, 'wpuser_activation'));
            add_action('wp_ajax_wpuser_activation', array($this, 'wpuser_activation'));
            add_action('wp_ajax_nopriv_wpuser_user_details', array($this, 'wpuser_user_details'));
            add_action('wp_ajax_wpuser_user_details', array($this, 'wpuser_user_details'));
            add_action('wp_ajax_nopriv_wpuser_send_mail_action', array($this, 'wpuser_send_mail_action'));
            add_action('wp_ajax_wpuser_send_mail_action', array($this, 'wpuser_send_mail_action'));

            add_action('wp_ajax_wpuser_update_profile_action', array($this, 'wpuser_update_profile_action'));

            add_action('wp_ajax_nopriv_wpuser_contact', array($this, 'wpuser_contact'));
            add_action('wp_ajax_wpuser_contact', array($this, 'wpuser_contact'));

            add_action('wp_ajax_wpuser_get_notification', array($this, 'wpuser_get_notification'));
            add_action('wp_ajax_wpuser_read_notification', array($this, 'wpuser_read_notification'));
            add_action('wp_ajax_wpuser_delete_notification', array($this, 'wpuser_delete_notification'));
        }

        function wpuser_login()
        {
            $creds = array();
            $loginLog = array();

            $wp_user_email_name = ((isset($_POST['wp_user_email_name'])) ? $_POST['wp_user_email_name'] : '');
            $wp_user_password = ((isset($_POST['wp_user_password'])) ? $_POST['wp_user_password'] : '');

            $loginLog['ip'] = $this->ip;
            $loginLog['user'] = sanitize_text_field($wp_user_email_name);

            if (isset($wp_user_email_name)) {
                if (filter_var($wp_user_email_name, FILTER_VALIDATE_EMAIL)) {
                    $userInfo = get_user_by_email(sanitize_text_field($wp_user_email_name));
                    if (!empty($userInfo->user_login))
                        $creds['user_login'] = $userInfo->user_login;
                } else {
                    $creds['user_login'] = sanitize_text_field($wp_user_email_name);
                }
            } else {
                $creds['user_login'] = '';
            }

            if (isset($wp_user_password)) {
                $creds['user_password'] = sanitize_text_field($wp_user_password);
            } else {
                $creds['user_password'] = '';
            }

            if (isset($_POST['wp_user_remember'])) {
                //$creds['remember'] = sanitize_text_field($data['wp_user_remember']);
            }

            /* Checks if this IP address is currently blocked */
            $attemp_msg = '';
            $wp_user_login_limit_enable = get_option('wp_user_login_limit_enable');
            if (isset($wp_user_login_limit_enable) && !empty($wp_user_login_limit_enable)) {
                $confirmResponse = self::confirmIPAddress($this->ip, $creds['user_login']);
                if ($confirmResponse['status'] == 1) {
                    $wp_user_login_limit_time = get_option('wp_user_login_limit_time');
                    if (empty($wp_user_login_limit_time)) {
                        $wp_user_login_limit_time = 30;
                    }
                    $wp_user_disable_signup = get_option('wp_user_disable_signup');
                    if (empty($wp_user_disable_signup)) {
                        $wp_user_disable_signup = 0;
                    }
                    $loginLog['message'] = $result['message'] = __('Access denied for', 'wpuser') . " " . $wp_user_login_limit_time . " " . __('minuts', 'wpuser');
                    $loginLog['status'] = "Failed";
                    $result['status'] = 'warning';
                    $result['wp_user_disable_signup'] = $wp_user_disable_signup;
                    print_r(json_encode($result));
                    SELF::loginLog($loginLog);
                    exit;
                }

                $attemp_msg = (!empty($confirmResponse['remaning'])) ? $confirmResponse['remaning'] . __(' attempts remaining.', 'wpuser') : '';
            }


            $user = get_user_by('login', $creds['user_login']);
            //Get stored value
            @$stored_value = get_user_meta($user->ID, 'wp-approve-user', true);
            @$wpuser_activation_key = get_user_meta($user->ID, 'wpuser_activation_key', true);
            if (!empty($user) && ($stored_value == 2 || $stored_value == 5)) {
                $loginLog['message'] = $result['message'] = ($stored_value == 2 && !empty($wpuser_activation_key)) ? __("Access denied : Waiting for approval.
                     Please Activate Your Account. Before you can login, you must active your account with the link sent to your email address", 'wpuser')
                    : __("Access denied : Waiting for admin approval", 'wpuser');
                $result['status'] = 'warning';
                $loginLog['status'] = "Failed";
                print_r(json_encode($result));
                SELF::loginLog($loginLog);
                exit;
            }


            $login_user = @wp_signon($creds, false);
            if (!is_wp_error($login_user)) {
                $args = (isset($_POST) ? $_POST : '');
                do_action_ref_array('wp_user_action_login', array(&$args));
                /* Null login attempts */
                if (isset($wp_user_login_limit_enable) && !empty($wp_user_login_limit_enable)) {
                    self::clearLoginAttempts($this->ip);
                }
                $result['message'] = __('Successfully login!! Refresh Page.', 'wpuser');
                $loginLog['message'] = __('Successfull login', 'wpuser');
                $loginLog['status'] = __('Successfull', 'wpuser');
                $result['status'] = 'success';
                $result['location'] = get_permalink(get_option('wp_user_page'));
                $result['wp_user_disable_signup'] = get_option('wp_user_disable_signup');
                print_r(json_encode($result));
                SELF::loginLog($loginLog);
                exit;
            } elseif (is_wp_error($login_user)) {
                if (isset($wp_user_login_limit_enable) && !empty($wp_user_login_limit_enable)) {
                    SELF::addLoginAttempt($this->ip);
                }
                $args = array($creds['user_login'], $creds['user_password']);
                do_action_ref_array('wp_user_action_login_invalid', array(&$args));
                $user = get_user_by('login', $creds['user_login']);
                //Get stored value
                @$stored_value = get_user_meta($user->ID, 'wp-approve-user', true);
                @$wpuser_activation_key = get_user_meta($user->ID, 'wpuser_activation_key', true);
                if (!empty($user) && ($stored_value == 2 || $stored_value == 5)) {
                    $loginLog['message'] = $result['message'] = ($stored_value == 2 && !empty($wpuser_activation_key)) ? __("Access denied : Waiting for approval.
                     Please Activate Your Account. Before you can login, you must active your account with the link sent to your email address", 'wpuser')
                        : __("Access denied : Waiting for admin approval", 'wpuser');
                } else {
                    $loginLog['message'] = $result['message'] = __('Invalid username or password. ', 'wpuser') . $attemp_msg;
                }
                $result['status'] = 'warning';
                $loginLog['status'] = "Failed";
                $result['wp_user_disable_signup'] = get_option('wp_user_disable_signup') ? 1 : 0;
                print_r(json_encode($result));
                SELF::loginLog($loginLog);
                //error_log( $login_user->get_error_message());
                exit;
            }
            die;
            //   print_r(json_encode($data['data']));
            //return json_encode($data);
        }

        function wpuser_forgot()
        {

            $email = ((isset($_POST['wp_user_email'])) ? $_POST['wp_user_email'] : '');

            if (empty($email)) {
                $error = __('Enter e-mail address', 'wpuser');
            } else if (!is_email($email)) {
                $error = __('Invalid email', 'wpuser');
            } else if (!email_exists($email)) {
                $error = __('There is no user registered with that email address', 'wpuser');
            } else {

                // lets generate our new password
                $random_password = wp_generate_password(12, false);

                // Get user data by field and data, other field are ID, slug, slug and login
                $user = get_user_by('email', $email);

                $update_user = wp_update_user(array(
                        'ID' => $user->ID,
                        'user_pass' => $random_password
                    )
                );
                $args = array($email, $user->ID, $random_password);
                do_action_ref_array('wp_user_action_forgot_password', array(&$args));

                // if  update user return true then lets send user an email containing the new password
                if ($update_user) {
                    $to = $email;
                    $subject = get_option('wp_user_email_user_forgot_subject');
                    $subject = (empty($subject)) ? 'Your new password' : $subject;
                    $message = "";
                    $sender = get_option('name');
                    $site_url = site_url();
                    $user_login = $user->user_login;
                    $email_header_text = get_option('wp_user_email_user_forgot_subject');
                    $email_body_text = apply_filters('wp_user_filter_email', get_option('wp_user_email_user_forgot_content'), $to, $user_login, null, null, $random_password);
                    $email_footer_text = 'You\'re receiving this email because you have register on ' . $site_url;
                    include('template_email/template_email_defualt.php');
                    $headers[] = 'MIME-Version: 1.0' . "\r\n";
                    $headers[] = 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                    $headers[] = "X-Mailer: PHP \r\n";
                    $headers[] = 'From: ' . $sender . ' < ' . $email . '>' . "\r\n";

                    $mail = wp_mail($to, $subject, $message, $headers);
                    if ($mail)
                        $success = __('Check your email address for your new password', 'wpuser');
                } else {
                    $error = __('Oops something went wrong updaing your account', 'wpuser');
                }
            }

            if (!empty($error)) {
                $result['message'] = $error;
                $result['status'] = 'warning';
                print_r(json_encode($result));
                exit;
            }
            if (!empty($success)) {
                $result['message'] = $success;
                $result['status'] = 'success';
                print_r(json_encode($result));
                exit;
            }

            echo "false";
            die();
        }

        function wpuser_register()
        {
            $data = array();
            $result = array();
            $data = $_POST;
            //  var_dump($data);exit;
            //reCaptcha
            $wp_user_security_reCaptcha_enable = get_option('wp_user_security_reCaptcha_enable');
            $wp_user_security_reCaptcha_secretkey = get_option('wp_user_security_reCaptcha_secretkey');

            /*
             if ($wp_user_security_reCaptcha_enable == 1 && !empty($wp_user_security_reCaptcha_secretkey)) {


                //Should be some validations before you proceed

                $captcha = $data['wp_user_Recaptcha']; //Captcha response send by client
                //Build post data to make request with fetch_file_contents
                $postdata = http_build_query(
                        array(
                            'secret' => $wp_user_security_reCaptcha_secretkey, //secret KEy provided by google
                            'response' => $captcha, // wp_user_Recaptcha string sent from client
                            'remoteip' => $_SERVER['REMOTE_ADDR']
                        )
                );

                //Build options for the post request
                $opts = array('http' =>
                    array(
                        'method' => 'POST',
                        'header' => 'Content-type: application/x-www-form-urlencoded',
                        'content' => $postdata
                    )
                );
                //Create a stream this is required to make post request with fetch_file_contents
                $context = stream_context_create($opts);

                // Send request to Googles siteVerify API
                $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify", false, $context);
                $response = json_decode($response, true);

                //var_dump($response);
                if ($response["success"] === false) {//if user verification failed
                    $result['message'] = 'Robots Not allowed (Captcha verification failed)';
                    $result['status'] = 'warning'; //error codes sent buy google's siteVerify API
                    print_r(json_encode($result));
                    exit;
                }
            }
            */
            //end reCaptcha

            $wp_user_email_name = (isset($data['user_login'])) ? $data['user_login'] : ((isset($_POST['user_login'])) ? $_POST['user_login'] : '');

            $wp_user_email = (isset($data['user_email'])) ? $data['user_email'] : ((isset($_POST['user_email'])) ? $_POST['user_email'] : '');

            $wp_user_password = (isset($data['user_pass'])) ? $data['user_pass'] : ((isset($_POST['user_pass'])) ? $_POST['user_pass'] : '');

            $wp_user_re_password = (isset($data['confirm_pass'])) ? $data['confirm_pass'] : ((isset($_POST['confirm_pass'])) ? $_POST['confirm_pass'] : '');

            if (!isset($_POST['wpuser_update_setting'])) {
                $responce = array(
                    'status' => 'warning',
                    'message' => __('Invalid form data. form request came from the somewhere else not current site! Please Refresh Page.', 'wpuser')
                );
                print_r(json_encode($responce));
                die;
            }
            if (!wp_verify_nonce($_POST['wpuser_update_setting'], 'wpuser-update-setting')) {
                $responce = array(
                    'status' => 'warning',
                    'message' => __('Invalid form data. form request came from the somewhere else not current site! Please Refresh Page.', 'wpuser')
                );
                print_r(json_encode($responce));
                die;
            }

            $form_role = 'subscriber';

            if (isset($_POST['wpuser_form_id']) && !empty($_POST['wpuser_form_id'])) {
                $form_role = get_post_meta($_POST['wpuser_form_id'], 'userplus_form_role', true);

                $form_role = (isset($_POST['role']) && !empty($_POST['role'])) ? $_POST['role'] : $form_role;
                $_POST['role'] = $form_role;

                if (isset($_POST['user_login'])) {
                    $user_exists = username_exists($_POST['user_login']);
                    $user_login = $_POST['user_login'];
                } else {
                    $user_exists = '';
                    $user_login = $_POST['user_email'];
                }
                if (empty($user_exists) && isset($_POST['user_email']) && email_exists($_POST['user_email']) == false) {
                    if (!isset($_POST['user_pass']))
                        $password = wp_generate_password($length = 12, $include_standard_special_chars = false);
                    else
                        $password = $_POST['user_pass'];
                }

                if (!isset($_POST['user_email'])) {
                    $_POST['user_email'] = $user_login . '@fakemail.com';
                } else {
                    if (!empty($_POST['user_email'])) {
                        $email = sanitize_text_field($_POST['user_email']);
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $result['message'] = __('Invalid email format', 'wpuser');
                            $result['status'] = 'warning';
                            print_r(json_encode($result));
                            exit;
                        }
                    }
                }

                if (username_exists($_POST['user_login'])) {
                    $result['status'] = 'warning';
                    $result['message'] = __('The username is already taken', 'wpuser');
                }
                if (username_exists($_POST['user_email'])) {
                    $result['status'] = 'warning';
                    $result['message'] = __('The email address already exists', 'wpuser');
                    print_r(json_encode($result));
                    exit;
                }

                $userplus_field_order = get_post_meta($_POST['wpuser_form_id'], 'userplus_field_order', true);
                $form_fields = get_post_meta($_POST['wpuser_form_id'], 'fields', true);;
                if ($userplus_field_order) {
                    $fields_count = count($userplus_field_order);
                    for ($i = 0; $i < $fields_count; $i++) {
                        $key = $userplus_field_order[$i];
                        $array = $form_fields[$key];
                        if (isset ($array['is_required']) && $array['is_required'] == 1 &&
                            (!isset($_POST[$array['meta_key']]) || empty($_POST[$array['meta_key']]))
                        ) {
                            $result['status'] = 'warning';
                            $result['message'] = __($array['label'] . ' field is required', 'wpuser');
                            print_r(json_encode($result));
                            exit;
                        }
                    }
                }

                $wp_user_tern_and_condition = get_option('wp_user_tern_and_condition');
                if (isset($wp_user_tern_and_condition) && $wp_user_tern_and_condition == 1) {
                    $wp_user_term_condition = (isset($data['wp_user_term_condition'])) ? $data['wp_user_term_condition'] : ((isset($_POST['wp_user_term_condition'])) ? $_POST['wp_user_term_condition'] : '');
                    if (!(isset($wp_user_term_condition) && !empty($wp_user_term_condition))) {
                        $result['message'] = __('Please accept terms', 'wpuser');
                        $result['status'] = 'warning';
                        print_r(json_encode($result));
                        exit;
                    }
                }

                global $wpdb;
                $register_user = wp_insert_user(array(
                    'user_login' => $user_login,
                    'user_pass' => $password,
                    'display_name' => sanitize_title($user_login),
                    'user_email' => $_POST['user_email']
                ));
                // unset($_POST['wpuser_form_id']);
            } else {

                $form_role = (isset($_POST['role']) && !empty($_POST['role'])) ? $_POST['role'] : $form_role;
                $_POST['role'] = $form_role;

                if (isset($wp_user_email_name) && !empty($wp_user_email_name)) {
                    $username = sanitize_text_field($wp_user_email_name);
                } else {
                    $result['message'] = __('Username field is required', 'wpuser');
                    $result['status'] = 'warning';
                    print_r(json_encode($result));
                    $username = "";
                    exit;
                }


                if (isset($wp_user_email) && !empty($wp_user_email)) {
                    $email = sanitize_text_field($wp_user_email);
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $result['message'] = __('Invalid email format', 'wpuser');
                        $result['status'] = 'warning';
                        print_r(json_encode($result));
                        exit;
                    }
                } else {
                    $result['message'] = __('Email field is required', 'wpuser');
                    $result['status'] = 'warning';
                    print_r(json_encode($result));
                    exit;
                }

                if (isset($wp_user_password) && !empty($wp_user_password)) {
                    $password = sanitize_text_field($wp_user_password);
                } else {
                    $result['message'] = __('Password field is required', 'wpuser');
                    $result['status'] = 'warning';
                    print_r(json_encode($result));
                    $password = "";
                    exit;
                }

                if (isset($wp_user_re_password) && !empty($wp_user_re_password)) {
                    $wp_user_login_limit_password = (get_option('wp_user_login_limit_password'));
                    $wp_user_login_limit_password_enable = get_option('wp_user_login_limit_password_enable');

                    $re_password = sanitize_text_field($wp_user_re_password);
                    if (($password != $re_password)) {
                        $result['message'] = __('Password is not match', 'wpuser');
                        $result['status'] = 'warning';
                        print_r(json_encode($result));
                        exit;
                    }
                    if (isset($wp_user_login_limit_password_enable) && $wp_user_login_limit_password_enable == 1 && !empty($wp_user_login_limit_password) && !(preg_match($wp_user_login_limit_password, $password))) {
                        $wp_user_login_password_valid_message = get_option('wp_user_login_password_valid_message');
                        $result['message'] = !empty($wp_user_login_password_valid_message) ? $wp_user_login_password_valid_message : _e('Invalid Password', 'wpuser');;
                        $result['status'] = 'warning';
                        print_r(json_encode($result));
                        exit;
                    }
                } else {
                    $result['message'] = __('Retype Password field is required', 'wpuser');
                    $result['status'] = 'warning';
                    print_r(json_encode($result));
                    $re_password = "";
                    exit;
                }
                $wp_user_tern_and_condition = get_option('wp_user_tern_and_condition');
                if (isset($wp_user_tern_and_condition) && $wp_user_tern_and_condition == 1) {
                    $wp_user_term_condition = (isset($data['wp_user_term_condition'])) ? $data['wp_user_term_condition'] : ((isset($_POST['wp_user_term_condition'])) ? $_POST['wp_user_term_condition'] : '');
                    if (!(isset($wp_user_term_condition) && !empty($wp_user_term_condition))) {
                        $result['message'] = __('Please accept terms', 'wpuser');
                        $result['status'] = 'warning';
                        print_r(json_encode($result));
                        exit;
                    }
                }
                $register_user = wp_create_user($username, $password, $email);
            }

            //print_r($data);exit;
		
                $autologin = get_post_meta($_POST['wpuser_form_id'], 'userplus_form_autologin', t);

         
         $wp_user_default_status = get_option('wp_user_default_status');
            if ($register_user && !is_wp_error($register_user)) {
                
                if ($wp_user_default_status == 3) {
                    $result['message'] = __('Please Activate Your Account. Before you can login, 
                    you must active your account with the link sent to your email address', 'wpuser');
                    $wp_user_default_status = 2;
                }
               elseif($wp_user_default_status == 2)
		{
			$result['message'] = __('Your Accout is Pending for Admin approval', 'wpuser');

		}
		 else {
		 
                  $result['message'] = __('Registration completed', 'wpuser');
                  
			
		$user_info = get_userdata($register_user);	

          
	if(($autologin == 1 && $wp_user_default_status == 1) || ( !is_plugin_active( 'wp-user-form-builder/userplus.php') && $wp_user_default_status == 1 ))
        {
	         $this->userplus_auto_login( $user_info->user_login, true );		
         }
                }
                unset($_POST['confirm_pass']);
                unset($_POST['wpuser_update_setting']);
                SELF::wpuser_update_user_profile($register_user, $_POST);
                add_user_meta($register_user, 'wp-approve-user', $wp_user_default_status);
                $result['status'] = 'success';
                $args = (isset($data)) ? $data : (isset($_POST) ? $_POST : '');
                $args['user_id'] = $register_user;
                print_r(json_encode($result));
                do_action_ref_array('wp_user_action_register', array(&$args));
            } elseif (is_wp_error($register_user)) {
                $result['message'] = $register_user->get_error_message();
                $result['status'] = 'warning';
                print_r(json_encode($result));
            }
            exit;

        }

   function userplus_auto_login( $username, $remember=true ) {
        
		ob_start();
		if ( !is_user_logged_in() ) {
		
			$user = get_user_by('login', $username );
			$user_id = $user->ID;

			wp_set_current_user( $user_id, $username );
			wp_set_auth_cookie( $user_id, $remember );
			do_action( 'wp_login', $username,$user );
                      
		} else {
			wp_logout();
			$user = get_user_by('login', $username );
			$user_id = $user->ID;
                             
			wp_set_current_user( $user_id, $username );
			wp_set_auth_cookie( $user_id, $remember );
			do_action( 'wp_login', $username,$user );
		}
		ob_end_clean();
	}

        function wpuser_update_profile_action()
        {
            $result = array();
            $advanced_info = array();
            $wpuser_form_id = '';
            $data = $_POST;
            $register_user = get_current_user_id();
            $wp_user_email = (isset($data['user_email'])) ? $data['user_email'] : ((isset($_POST['user_email'])) ? $_POST['user_email'] : '');
            $wp_user_password = (isset($data['user_pass'])) ? $data['user_pass'] : ((isset($_POST['user_pass'])) ? $_POST['user_pass'] : '');
            $wp_user_re_password = (isset($data['confirm_pass'])) ? $data['confirm_pass'] : ((isset($_POST['confirm_pass'])) ? $_POST['confirm_pass'] : '');

            if (!isset($_POST['wpuser_update_setting'])) {
                $responce = array(
                    'status' => 'warning',
                    'message' => __('Invalid form data. form request came from the somewhere else not current site! Please Refresh Page.', 'wpuser')
                );
               // print_r(json_encode($responce));
              //  die;
            }
            if (!wp_verify_nonce($_POST['wpuser_update_setting'], 'wpuser-update-setting')) {
                $responce = array(
                    'status' => 'warning',
                    'message' => __('Invalid form data. form request came from the somewhere else not current site! Please Refresh Page.', 'wpuser')
                );
              //  print_r(json_encode($responce));
              //  die;
            }

            if (isset($wp_user_re_password) && !empty($wp_user_re_password)) {
                $wp_user_login_limit_password = (get_option('wp_user_login_limit_password'));
                $wp_user_login_limit_password_enable = get_option('wp_user_login_limit_password_enable');
                $re_password = sanitize_text_field($wp_user_re_password);
                // echo $wp_user_login_limit_password;die;
                if (!($wp_user_password == $re_password)) {
                    $result['message'] = __('Password is not match', 'wpuser');
                    $result['status'] = 'warning';
                    print_r(json_encode($result));
                    exit;
                } else if (isset($wp_user_login_limit_password_enable) && $wp_user_login_limit_password_enable == 1 && isset($wp_user_login_limit_password) && !empty($wp_user_login_limit_password) && !(preg_match($wp_user_login_limit_password, $wp_user_password))) {
                    $wp_user_login_password_valid_message = get_option('wp_user_login_password_valid_message');
                    $result['message'] = !empty($wp_user_login_password_valid_message) ? $wp_user_login_password_valid_message : _e('Invalid Password', 'wpuser');;
                    $result['status'] = 'warning';
                    print_r(json_encode($result));
                    exit;
                }
            } else if (isset($wp_user_password) && !empty($wp_user_password)) {
                $result['message'] = __('Retype Password field is required', 'wpuser');
                $result['status'] = 'warning';
                print_r(json_encode($result));
                exit;
            }

            if (isset($wp_user_email) && !empty($wp_user_email)) {
                $email = sanitize_text_field($wp_user_email);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $result['message'] = __('Invalid email format', 'wpuser');
                    $result['status'] = 'warning';
                    print_r(json_encode($result));
                    exit;
                }
            }

            if (isset($_POST['wpuser_form_id']) && !empty($_POST['wpuser_form_id'])) {
                unset($_POST['user_login']);
                $wpuser_form_id = $_POST['wpuser_form_id'];
                //Validation
                $userplus_field_order = get_post_meta($_POST['wpuser_form_id'], 'userplus_field_order', true);
                $form_fields = get_post_meta($_POST['wpuser_form_id'], 'fields', true);;
                if ($userplus_field_order) {
                    $fields_count = count($userplus_field_order);
                    for ($i = 0; $i < $fields_count; $i++) {
                        $key = $userplus_field_order[$i];
                        $array = $form_fields[$key];
                        if (!in_array($array['meta_key'], array('user_login', 'user_pass'))) {
                            if (isset ($array['is_required']) && $array['is_required'] == 1 &&
                                (!isset($_POST[$array['meta_key']]) || empty($_POST[$array['meta_key']]))
                            ) {
                                $result['status'] = 'warning';
                                $result['message'] = __($array['label'] . ' field is required', 'wpuser');
                                print_r(json_encode($result));
                                exit;
                            }
                        }
                    }
                }
                unset($_POST['wpuser_form_id']);
            } else {

                if (isset($wp_user_email) && !empty($wp_user_email)) {
                    $email = sanitize_text_field($wp_user_email);
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $result['message'] = __('Invalid email format', 'wpuser');
                        $result['status'] = 'warning';
                        print_r(json_encode($result));
                        exit;
                    }
                }
                //Validation
                include('view/option.php');
                foreach ($wp_user_options_my_profile_form as $array) {
                    if (!in_array($array['meta_key'], array('user_login'))) {
                        if (isset ($array['is_required']) && $array['is_required'] == 1 &&
                            (!isset($_POST[$array['meta_key']]) || empty($_POST[$array['meta_key']]))
                        ) {
                            $result['status'] = 'warning';
                            $result['message'] = __($array['label'] . ' field is required', 'wpuser');
                            print_r(json_encode($result));
                            exit;
                        }
                    }
                }
            }

            if ($register_user && !is_wp_error($register_user)) {
                $result['message'] = __('Profile updated successfully', 'wpuser');
                unset($_POST['confirm_pass']);
                unset($_POST['wpuser_update_setting']);
                SELF::wpuser_update_user_profile($register_user, $_POST);
                $result['status'] = 'success';

                $user_id = $register_user;
                $attachment_url = esc_url(get_the_author_meta('user_meta_image', $user_id));
                $attachment_id = profileController::get_attachment_image_by_url($attachment_url);
                $image_thumb = wp_get_attachment_image_src($attachment_id, 'thumbnail');

                if (!empty($image_thumb[0])) {
                    $wp_user_profile_img = $image_thumb[0];
                } else if (!empty($attachment_url)) {
                    $wp_user_profile_img = $attachment_url;
                } else {
                    $args = get_avatar_data($user_id);
                    if (!empty($args['url']))
                        $wp_user_profile_img = $args['url'];
                    else
                        $wp_user_profile_img = WPUSER_PLUGIN_URL . 'assets/images/wpuser.png';
                }

                $name = get_the_author_meta('first_name', $user_id) . " " . get_the_author_meta('last_name', $user_id);
                if (empty(str_replace(' ', '', $name))) {
                    $user_info = get_userdata($user_id);
                    $name = $user_info->display_name;
                    if (empty($name)) {
                        $name = $user_info->user_nicename;
                    }
                    if (empty($name)) {
                        $name = $user_info->user_login;
                    }
                }

                if (isset($wpuser_form_id) && !empty($wpuser_form_id)) {
                    //Get Advanced meta data
                    $userplus_field_order = get_post_meta($wpuser_form_id, 'userplus_field_order', true);
                    $form_fields = get_post_meta($wpuser_form_id, 'fields', true);;
                    if ($userplus_field_order) {
                        $fields_count = count($userplus_field_order);
                        for ($i = 0; $i < $fields_count; $i++) {
                            $key = $userplus_field_order[$i];
                            $array = $form_fields[$key];
                            if (!in_array($array['type'], array('image_upload')) &&
                                !in_array($array['meta_key'],
                                    array('user_login', 'user_pass', 'user_url', 'user_pass', 'first_name', 'description', 'user_email', 'last_name'))
                            ) {
                                $advanced_info[$array['meta_key']] = get_the_author_meta($key, $register_user);
                            }
                        }
                    }
                }

                $profile_background_pic = get_user_meta($user_id, 'profile_background_pic', true);

                $meta = get_user_meta($user_id);
                $email = profileController::wpuser_profile_details('user_email', $user_id);
                $user_url = profileController::wpuser_profile_details('user_url', $user_id);

                $user_info = array(
                    "name" => $name,
                    "profile_img" => $wp_user_profile_img,
                    "profile_background_pic" => $profile_background_pic,
                    "first_name" => $meta['first_name'],
                    "last_name" => $meta['last_name'],
                    "description" => $meta['description'],
                    "email" => $email,
                    'user_url' => $user_url,
                    'advanced' => $advanced_info
                );

                $user_info = apply_filters('wpuser_profile_info', $user_info, $user_id);
                $result['user_info'] = $user_info;

                print_r(json_encode($result));
            } else if (is_wp_error($register_user)) {
                $result['message'] = $register_user->get_error_message();
                $result['status'] = 'warning';
                print_r(json_encode($result));
            } else {
                $result['message'] = __('Please Refresh Page', 'wpuser');
                $result['status'] = 'warning';
                print_r(json_encode($result));
            }
            exit;

        }

        function wpuser_user_details()
        {
            $result = array();
            $user_id = ((isset($_POST['id'])) ? $_POST['id'] : '');

            if (!isset($_POST['wpuser_update_setting'])) {
                $responce = array(
                    'status' => 'warning',
                    'message' => __('Invalid form data. form request came from the somewhere else not current site! Please Refresh Page.', 'wpuser')
                );
                print_r(json_encode($responce));
                die;
            }
            if (!wp_verify_nonce($_POST['wpuser_update_setting'], 'wpuser-update-setting')) {
                $responce = array(
                    'status' => 'warning',
                    'message' => __('Invalid form data. form request came from the somewhere else not current site! Please Refresh Page.', 'wpuser')
                );
                print_r(json_encode($responce));
                die;
            }
            global $wpdb;
            $wp_user_member_filter = '';
            $member_post = array();
            $value = json_decode(file_get_contents("php://input"));
            $wp_user_labels = apply_filters('wp_user_member_filter', $wp_user_member_filter, $user_id);
            $attachment_url = esc_url(get_the_author_meta('user_meta_image', $user_id));
            $attachment_id = profileController::get_attachment_image_by_url($attachment_url);
            // retrieve the thumbnail size of our image
            $image_thumb = wp_get_attachment_image_src($attachment_id, 'thumbnail');
            // return the image thumbnail
            if (!empty($image_thumb[0])) {
                $wp_user_profile_img = $image_thumb[0];
            } else if (!empty($attachment_url)) {
                $wp_user_profile_img = $attachment_url;
            } else {
                $args = get_avatar_data($user_id);
                if (!empty($args['url']))
                    $wp_user_profile_img = $args['url'];
                else
                    $wp_user_profile_img = WPUSER_PLUGIN_URL . 'assets/images/wpuser.png';
            }
            $name = get_the_author_meta('first_name', $user_id) . " " . get_the_author_meta('last_name', $user_id);
            if (empty(str_replace(' ', '', $name))) {
                $user_info = get_userdata($user_id);
                $name = $user_info->display_name;
                if (empty($name)) {
                    $name = $user_info->user_nicename;
                }
                if (empty($name)) {
                    $name = $user_info->user_login;
                }
            }
            $authors_posts = get_posts(array('author' => $user_id, 'post_status' => 'publish'));
            // Get all user meta data for $user_id
            $meta = get_user_meta($user_id);
            $header_block_info = array(
                array(
                    "name" => 'Blogs',
                    "type" => 'block',
                    "id" => 'profile_block',
                    "url" => get_author_posts_url($user_id),
                    "user_id" => $user_id,
                    'icon' => 'fa fa-th-large',
                    "count" => count($authors_posts),
                )
            );

            $user_url = profileController::wpuser_profile_details('user_url', $user_id);

            $user_info = array(
                "First name" => $meta['first_name'],
                "Last name" => $meta['last_name'],
                "Description" => $meta['description'],
                'Website' => $user_url,
            );
            $user_info = apply_filters('wpuser_profile_info', $user_info, $user_id);
            //$result['user_info'] = $user_info;
            $user_header_follow_button ='';
            $user_header_follow_button = apply_filters('wp_user_member_filter_header_button', $user_header_follow_button,$user_id);
            $user_badge ='';
            $user_badge = apply_filters('wp_user_member_filter_badge', $user_badge,$user_id);

            $header_block_info = apply_filters('wp_user_member_filter_header_block', $header_block_info, $user_id);
            $user_info = apply_filters('wp_user_member_info', $user_info, $user_id);
            $profile_background_pic = get_user_meta($user_id, 'profile_background_pic', true);
            $data = array(
                'status' => 1,
                "id" => $user_id,
                "name" => $name,
                'labels' => $wp_user_labels,
                "wp_user_profile_img" => $wp_user_profile_img,
                "wp_user_background_img" => $profile_background_pic,
                'header_block_info' => $header_block_info,
                'user_info' => $user_info,
                'user_header_follow_button'=>$user_header_follow_button,
                'user_badge'=>$user_badge
            );
            print_r(json_encode($data));
            die();

        }

        function wpuser_contact()
        {
            global $wpdb;
            $error = array();
            global $current_user, $wp_roles;
            $current_user = wp_get_current_user();
            $WP_USER_INPUT = (isset($_POST)) ? $_POST : '';
            if (!empty($WP_USER_INPUT['wp_user_email_subject']) && !empty($WP_USER_INPUT['wp_user_email_content'])) {

                $wp_user_email_name = get_option('wp_user_email_name');
                $wp_user_email_id = get_option('wp_user_email_id');
                $sender = !empty($wp_user_email_name) ? $wp_user_email_name : get_option('blogname');
                $email = !empty($wp_user_email_id) ? $wp_user_email_id : get_option('admin_email');
                $subject = get_option('wp_user_email_admin_register_subject');
                $site_url = site_url();
                $headers[] = 'MIME-Version: 1.0' . "\r\n";
                $headers[] = 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers[] = "X-Mailer: PHP \r\n";
                $headers[] = 'From: ' . $sender . ' < ' . $current_user->user_email . '>' . "\r\n";
                $message = $WP_USER_INPUT['wp_user_email_content'];

                $message .= '<br>Username: ' . $current_user->user_login . '<br />';
                $message .= 'User email: ' . $current_user->user_email . '<br />';

                if (!(wp_mail($email, $WP_USER_INPUT['wp_user_email_subject'], $message, $headers)))
                    $error[] = "Error : Mail Not send";

            } else {
                $error[] = "All field are required";
            }

            if (count($error) == 0) {
                $result['message'] = ' Mail send to admin';
                $result['status'] = 'success';
            } else {
                $result['message'] = implode(',', $error);
                $result['status'] = 'warning';
            }

            print_r(json_encode($result));
            die();
        }


        function wpuser_send_mail_action()
        {
            $result = array();
            $user_id = ((isset($_POST['id'])) ? $_POST['id'] : '');

            if (!isset($_POST['wpuser_update_setting'])) {
                $responce = array(
                    $result['status'] = 'warning',
                    'message' => __('Invalid form data. form request came from the somewhere else not current site! Please Refresh Page.', 'wpuser')
                );
                print_r(json_encode($responce));
                die;
            }
            if (!wp_verify_nonce($_POST['wpuser_update_setting'], 'wpuser-update-setting')) {
                $responce = array(
                    $result['status'] = 'warning',
                    'message' => __('Invalid form data. form request came from the somewhere else not current site! Please Refresh Page.', 'wpuser')
                );
                print_r(json_encode($responce));
                die;
            }

            if (!isset($_POST['id']) || empty($_POST['id'])) {
                $result['message'] = __('Invalid receiver', 'wpuser');
                $result['status'] = 'warning';
                print_r(json_encode($result));
                exit;
            }

            if (!isset($_POST['message']) || empty($_POST['message'])) {
                $result['message'] = __('Please enter message', 'wpuser');
                $result['status'] = 'warning';
                print_r(json_encode($result));
                exit;
            }
            $user_info = get_userdata($_POST['id']);

            $to = $user_info->user_email;
            $subject = (isset($_POST['subject']) && !empty($_POST['subject'])) ? $_POST['subject'] : "New Message From " . get_bloginfo('name');
            $body = $_POST['message'];
            $headers[] = 'MIME-Version: 1.0' . "\r\n";
            $headers[] = 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $headers[] = "X-Mailer: PHP \r\n";
            $headers [] = 'Content-Type: text/html; charset=UTF-8';
            if (isset($_POST['from']) && !empty($_POST['from'])) {
                $headers[] = 'From: ' . get_bloginfo('name') . ' < ' . $_POST['from'] . '>' . "\r\n";
            }

            if (!wp_mail($to, $subject, $body, $headers)) {
                $responce = array(
                    'status' => 'warning',
                    'message' => __('Error : Mail send', 'wpuser')
                );

            } else {
                $responce = array(
                    'status' => 'success',
                    'message' => __('Mail send successfully', 'wpuser')
                );
            }

            print_r(json_encode($responce));
            die();

        }

        function wpuser_activation()
        {
            $wp_user_email_name = ((isset($_REQUEST['email'])) ? sanitize_text_field($_REQUEST['email']) : '');
            $key = ((isset($_REQUEST['key'])) ? $_REQUEST['key'] : '');
            if (!empty($wp_user_email_name)) {
                if (filter_var($wp_user_email_name, FILTER_VALIDATE_EMAIL)) {
                    $userInfo = get_user_by_email(sanitize_text_field($wp_user_email_name));
                    if (!empty($userInfo->ID)) {
                        $wpuser_activation_key = get_user_meta($userInfo->ID, 'wpuser_activation_key', true);
                        if ($wpuser_activation_key == $key) {
                            delete_user_meta($userInfo->ID, 'wpuser_activation_key');
                            update_user_meta($userInfo->ID, 'wp-approve-user', 1);
                            _e('Your account has been aprroved. ');
                            echo '<a href="' . get_permalink(get_option('wp_user_page')) . '">';
                            _e('Click here to login.', 'wpuser');
                            echo '</a>';
                            die;
                        }
                    }
                }
            }
            die('Invalid URL');
        }

        public static function wpuser_register_form_validation()
        {

        }


        public static function wpuser_update_user_profile($user_id, $form_data)
        {

            foreach ($form_data as $key => $form_value) {

                if (isset($key) && in_array($key, array('user_url', 'display_name', 'role', 'user_login', 'user_pass', 'user_email'))) {
                    wp_update_user(array('ID' => $user_id, $key => $form_value));
                } else {
                    update_user_meta($user_id, $key, $form_value);
                }
            }
        }


        public static function confirmIPAddress($value, $user_login = null)
        {
            global $wpdb;
            $wp_user_login_limit_time = get_option('wp_user_login_limit_time');
            if (empty($wp_user_login_limit_time)) {
                $wp_user_login_limit_time = 30;
            }

            $wwp_user_login_limit = get_option('wp_user_login_limit');
            if (empty($wwp_user_login_limit)) {
                $wwp_user_login_limit = 5;
            }
            $accessTime = date('Y-m-d h:i:m');

            $q = "SELECT Attempts, (CASE when lastlogin is not NULL and DATE_ADD(LastLogin, INTERVAL " . $wp_user_login_limit_time .
                " MINUTE)>'" . $accessTime . "' then 1 else 0 end) as Denied FROM {$wpdb->prefix}WPUser_LoginAttempts WHERE ip = '$value'";
            //echo $q;die;
            $data = $wpdb->get_results($q);
            //Verify that at least one login attempt is in database
            if (!$data) {
                return array(
                    'status' => 0,
                    'remaning' => 0
                );
            }
            if ($data[0]->Attempts >= $wwp_user_login_limit) {
                $args = array($value, $accessTime, $user_login);
                do_action_ref_array('wp_user_action_login_attempts_admin_notify', array(&$args));
                if ($data[0]->Denied == 1) {
                    return array(
                        'status' => 1,
                        'remaning' => ($wwp_user_login_limit - $data[0]->Attempts)
                    );
                } else {
                    self::clearLoginAttempts($value);
                    return array(
                        'status' => 0,
                        'remaning' => ($wwp_user_login_limit - $data[0]->Attempts)
                    );
                }
            }
            return array(
                'status' => 0,
                'remaning' => ($wwp_user_login_limit - $data[0]->Attempts)
            );
        }

        public static function addLoginAttempt($value)
        {
            //Increase number of Attempts. Set last login attempt if required.
            global $wpdb;
            $q = "SELECT * FROM {$wpdb->prefix}WPUser_LoginAttempts WHERE ip = '$value'";
            $data = $wpdb->get_results($q);

            if ($data) {
                $Attempts = $data[0]->Attempts + 1;

                if ($Attempts == 3) {
                    $values['Attempts'] = $Attempts;
                    $values['lastlogin'] = date('Y-m-d h:i:m');
                    $wpdb->update($wpdb->prefix . 'WPUser_LoginAttempts', $values, array('IP' => $value));
                } else {
                    $values['Attempts'] = $Attempts;

                    $wpdb->update($wpdb->prefix . 'WPUser_LoginAttempts', $values, array('IP' => $value));
                }
            } else {
                $values['Attempts'] = 1;
                $values['IP'] = $value;
                $values['lastlogin'] = date('Y-m-d h:i:m');
                $result = $wpdb->insert($wpdb->prefix . 'WPUser_LoginAttempts', $values);
            }
        }

        static function clearLoginAttempts($value)
        {
            global $wpdb;
            $values['Attempts'] = 0;
            return $wpdb->update($wpdb->prefix . 'WPUser_LoginAttempts', $values, array('IP' => $value));
        }

        public static function loginLog($value)
        {
            global $wpdb;
            $value['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            $wpdb->insert($wpdb->prefix . 'wpuser_login_log', $value);
        }

        function wp_user_action_register_function(&$args)
        {
            //error_log("WP USER :Inside wp_user_action_register action");
            $to = $args['user_email'];
            $message = "";
            $wp_user_email_name = get_option('wp_user_email_name');
            $wp_user_email_id = get_option('wp_user_email_id');
            $sender = !empty($wp_user_email_name) ? $wp_user_email_name : get_option('blogname');
            $email = !empty($wp_user_email_id) ? $wp_user_email_id : get_option('admin_email');
            $subject = get_option('wp_user_email_admin_register_subject');
            $subject = (empty($subject)) ? 'New User Registration' : $subject;
            $site_url = site_url();
            $headers[] = 'MIME-Version: 1.0' . "\r\n";
            $headers[] = 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $headers[] = "X-Mailer: PHP \r\n";
            $headers[] = 'From: ' . $sender . ' < ' . $email . '>' . "\r\n";

            if (get_option('wp_user_email_admin_register_enable')) {
                //error_log("WP USER :Inside wp_user_email_admin_register_enable");
                $email_header_text = get_option('wp_user_email_admin_register_subject');
                $email_body_text = apply_filters('wp_user_filter_email', get_option('wp_user_email_admin_register_content'), $to, $args['user_email'], null, null, null);
                $email_footer_text = 'You\'re receiving this email because you have Enable notifiacion for new user register on ' . $site_url;
                include('template_email/template_email_defualt.php');
                $mail = wp_mail($email, $subject, $message, $headers);
                //error_log("WP USER :New user registration: Mail send to Admin");
            }

            if (get_option('wp_user_email_user_register_enable')) {
                //error_log("WP USER :Inside wp_user_email_user_register_enable");
                $email_header_text = get_option('wp_user_email_user_register_subject');
                $email_body_text = apply_filters('wp_user_filter_email', get_option('wp_user_email_user_register_content'), $to, $args['user_email'], null, null, null);
                $email_footer_text = 'You\'re receiving this email because you have register on ' . $site_url;
                include('template_email/template_email_defualt.php');
                $mail = wp_mail($to, $subject, $message, $headers);
               // error_log("WP USER :New user registration: Mail send to $to");
            }

            $wp_user_default_status = get_option('wp_user_default_status');
            if ($wp_user_default_status == 3) {
                $email_header_text = get_option('wp_user_email_user_register_subject');
                $random_key = wp_generate_password(12, false);
                $email_body_text = __('Click on following link to activate your account', 'wpuser');
                $email_body_text .= admin_url('admin-ajax.php') . '?action=wpuser_activation&key=' . $random_key . '&email=' . $to;
               // error_log($email_body_text);
                $email_footer_text = 'You\'re receiving this email because you have register on ' . $site_url;
                include('template_email/template_email_defualt.php');
              //  error_log($args['user_id']);
                update_user_meta($args['user_id'], 'wpuser_activation_key', $random_key);
              //  error_log('wp_user_default_status');
                //error_log($message);
                $subject = __('Confirm your ' . get_option('blogname') . ' account', 'wpuser');
               // error_log($subject);
                if(!wp_mail($to, $subject, $message, $headers)){
                  //  error_log('wp_user_default_status mail not send');
                }
            }


        }

        function wpuser_get_notification(){
            if (!isset($_POST['wpuser_update_setting'])) {
                $responce = array(
                    $result['status'] = 'warning',
                    'message' => __('Invalid form data. form request came from the somewhere else not current site! Please Refresh Page.', 'wpuser')
                );
                print_r(json_encode($responce));
                die;
            }
            if (!wp_verify_nonce($_POST['wpuser_update_setting'], 'wpuser-update-setting')) {
                $responce = array(
                    $result['status'] = 'warning',
                    'message' => __('Invalid form data. form request came from the somewhere else not current site! Please Refresh Page.', 'wpuser')
                );
                print_r(json_encode($responce));
                die;
            }
            $mynotifications=profileController::getNotification(get_current_user_id());
            $responce = array(
                'status' => 'success',
                'notifications' => $mynotifications
            );
                print_r(json_encode($responce));
                die();
        }

        function wpuser_read_notification(){
            global $wpdb;
            if (!isset($_POST['wpuser_update_setting'])) {
                $responce = array(
                    $result['status'] = 'warning',
                    'message' => __('Invalid form data. form request came from the somewhere else not current site! Please Refresh Page.', 'wpuser')
                );
                print_r(json_encode($responce));
                die;
            }
            if (!wp_verify_nonce($_POST['wpuser_update_setting'], 'wpuser-update-setting')) {
                $responce = array(
                    $result['status'] = 'warning',
                    'message' => __('Invalid form data. form request came from the somewhere else not current site! Please Refresh Page.', 'wpuser')
                );
                print_r(json_encode($responce));
                die;
            }

            if (!isset($_POST['id']) || empty($_POST['id'])) {
                $result['message'] = __('Invalid receiver', 'wpuser');
                $result['status'] = 'warning';
                print_r(json_encode($result));
                exit;
            }

            if($wpdb->update(
                $wpdb->prefix.'wpuser_notification',
                array(
                    'is_unread' => 0
                ),
                array( 'ID' => $_POST['id'])
            )){
                $result['status'] = 'success';
                print_r(json_encode($result));
                exit;
            }
        }

        function wpuser_delete_notification(){
            global $wpdb;
            if (!isset($_POST['wpuser_update_setting'])) {
                $responce = array(
                    $result['status'] = 'warning',
                    'message' => __('Invalid form data. form request came from the somewhere else not current site! Please Refresh Page.', 'wpuser')
                );
                print_r(json_encode($responce));
                die;
            }
            if (!wp_verify_nonce($_POST['wpuser_update_setting'], 'wpuser-update-setting')) {
                $responce = array(
                    $result['status'] = 'warning',
                    'message' => __('Invalid form data. form request came from the somewhere else not current site! Please Refresh Page.', 'wpuser')
                );
                print_r(json_encode($responce));
                die;
            }

            if (!isset($_POST['id'])) {
                $result['message'] = __('Invalid receiver', 'wpuser');
                $result['status'] = 'warning';
                print_r(json_encode($result));
                exit;
            }

            if($_POST['id']==0){
               $delete=array( 'recipient_id' => get_current_user_id() );
            }else{
                $delete= array( 'ID' => $_POST['id'] );
            }

            if($wpdb->delete( $wpdb->prefix.'wpuser_notification', $delete)){
                $result['status'] = 'success';
                print_r(json_encode($result));
                exit;
            }


        }

        function wp_user_action_login_attempts_admin_notify_function(&$args)
        {
            if (get_option('wp_user_login_limit_admin_notify')) {
                //error_log("WP USER :Inside wp_user_action_login_attempts_admin_notify_function action");
                $subject = 'Login Attempts';
                $message = "";
                $wp_user_email_name = get_option('wp_user_email_name');
                $wp_user_email_id = get_option('wp_user_email_id');
                $sender = !empty($wp_user_email_name) ? $wp_user_email_name : get_option('blogname');
                $email = !empty($wp_user_email_id) ? $wp_user_email_id : get_option('admin_email');
                $site_url = site_url();
                $headers[] = 'MIME-Version: 1.0' . "\r\n";
                $headers[] = 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers[] = "X-Mailer: PHP \r\n";
                $headers[] = 'From: ' . $sender . ' < ' . $email . '>' . "\r\n";
                $ip = $args[0];
                $accesTime = $args[1];
                $accesUserName = $args[2];
                $bodyText = '<p>A failed login attempt has occurred on ' . $accesTime . '.
                            Someone from the ' . $ip . ' IP address  used the ' . $accesUserName . ' username  to attempt to login on ' . $site_url . '</p>
                 <p>If you did not attempt to access your site, please contact your Information Technology Security Team immediately.</p>
                 <p>
                 Server Date & Time: ' . $accesTime . ' <br>
                 From IP Address: ' . $ip . '
                 </p>';
                $email_header_text = 'SECURITY ALERT: Failed Login Attempt on ' . $site_url;
                $email_body_text = apply_filters('wp_user_filter_email', $bodyText, null, $accesUserName, null, null, null);
                $email_footer_text = ' You\'re receiving this email because you have enable setting (WP User) "Notify on lockout (Email to admin after)" on ' . $site_url;
                include('template_email/template_email_defualt.php');
                $mail = wp_mail($email, $subject, $message, $headers);
                //error_log("WP USER :Login Attempts $ip");
            }
        }

        function wp_user_filter_email_function($value, $userEmail = null, $userName = null, $userFirstName = null, $userLastName = null, $newPassword = null)
        {
            $wp_user_email_name = get_option('wp_user_email_name');
            $wp_user_email_id = get_option('wp_user_email_id');
            $replace = array(
                '{WPUSER_ADMIN_EMAIL}' => !empty($wp_user_email_id) ? $wp_user_email_id : get_option('admin_email'),
                '{WPUSER_BLOGNAME}' => get_option('blogname'),
                '{WPUSER_LOGIN_URL}' => get_permalink(get_option('wp_user_page')),
                '{WPUSER_BLOG_ADMIN}' => !empty($wp_user_email_name) ? $wp_user_email_name : get_option('blogname'),
                '{WPUSER_BLOG_URL}' => get_option('siteurl'),
                '{WPUSER_USERNAME}' => $userName,
                '{WPUSER_FIRST_NAME}' => $userFirstName,
                '{WPUSER_LAST_NAME}' => $userLastName,
                '{WPUSER_NAME}' => $userName,
                '{WPUSER_EMAIL}' => $userEmail,
                '{WPUSER_NEW_PASSWORD}' => $newPassword
            );
            $value = str_replace(array_keys($replace), $replace, $value);
            return $value;
        }

        static function remove_admin_bar()
        {
            $wp_user_disable_admin_bar = get_option('wp_user_disable_admin_bar');
            if (!empty($wp_user_disable_admin_bar) && $wp_user_disable_admin_bar == 1) {
                if (!current_user_can('administrator') && !is_admin()) {
                    show_admin_bar(false);
                }
            }
        }


    }
endif;

$obj = new wpuserAjax();

=== WP User ===
Contributors: walke.prashant
Donate link: http://wpuserplus.com/pricing/
Tags: login, register, user, profile, social, gravatar, ajax, restrict content, popup, avatar
Requires at least: 3.3.3
Tested up to: 4.9.2
Stable tag: 4.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Create elegant Login, Register, and Forgot Your Password form on Page, widget or Popups on your website in just minutes with AJAX.

== Description ==

* WP User plugin helps you to create front end login and registration form.
* User logins or registrations and would like to avoid the normal wordpress login pages, this plugin adds the capability of placing a login, Registration, forgot password with smooth effects in AJAX.


<a target="_blank" href="http://wpuserplus.com/pricing/">http://wpuserplus.com</a>

= Features =
<ul>
	<li><strong>Login : Login with Username or Email Id</strong></li>
	<li><strong>Registration</strong></li>
	<li><strong>Forgot Password</strong></li>
	<li>Profile : View/Edit Profile</li>
	<li><strong>security</strong>
<ul>
	<li><strong>Limit Login</strong> Attempts</li>
	<li>Mechanism for slow down brute force attack</li>
	<li>Notify on lockout (Email to admin after cross limit the number of login attempts)</li>
	<li>Password Regular Expression (Form Validation &amp; Security )</li>
    <li>Google <strong>reCAPTCHA</strong></li>
    <li>Approve/Deny User</li>
    <li>Auto / Email Approval user</li>
    <li>View Login Log</li>
    <li>Restrict an entire post or page</li>
    <li>Restrict section of content within a post/page</li>
    <li>Logged in or selected role users only access content</li>
</ul>
</li>
	<li><strong>Email Notification</strong>
<ul>
	<li>New Registration</li>
	<li>Email to admin after cross limit the number of login attempts</li>
	<li>Custom email subject, content</li>
</ul>
</li>
<li><strong>Front-end profile</strong>
<ul>
	<li>View/Edit user information user front end dashboard</li>
	<li><strong>User Avatar</strong> : for users to upload images or enter url to their profile</li>
	<li>Change the Default Gravatar</li>
	<li>Send mail to admin via Contact Us form</li>
	<li>Get Notification new comment on user post, woocommerce order status changed to refund/complete, new follow</li>
	<li>Whenever a user publish posts, all the followers will receive a notification</li>
</ul>
<li><strong>Member Directory</strong>
<ul>
	<li>Member Pagination,Search.</li>
	<li>View Member Profile.</li>
	<li>Send Mail to Member</li>
</ul>
</li>
</li>
	<li>Auto <strong>Generate page</strong> for Login,Register</li>
	<li>Enable / <strong>Disable Admin Bar</strong></li>
	<li>Templates : 4 login,register front end <strong>templates</strong></li>
	<li><strong>Customizable CSS</strong></li>
	<li> Admin : Export Users CSV</li>
<li>AJAX based verification for username and email accounts</li>
<li>Add smooth ajax login/registration effects</li>
<li>Login redirection</li>
<li>Login/registration/forgot password <strong>popup model</strong> :  You can create one popup that contains all 3 with a great interface for switching between them</li>
<li>Light weight plugin</li>
<li>Customize skin color,buttons, link, box, form background etc.</li>
<li>login,register , forgot password form using shortcode, widget, popup</li>
<li><strong>Responsive</strong></li>
<li>MultiSite</li>
<li>Multi language</li>
</ul>

= Demo =
<a target="_blank" href="http://wpuserplus.com/demo/">http://wpuserplus.com/demo/</a>

= Screenshots =
<a target="_blank" href="http://wpuserplus.com/blog/doc/screenshots/">http://wpuserplus.com/blog/doc/screenshots/</a>

= Documentation =
<a target="_blank" href="http://wpuserplus.com/documentation/">http://wpuserplus.com/documentation/</a>

= Get Pro Feature =
<a target="_blank" href="http://wpuserplus.com/#optimizer_front_blocks-3">http://wpuserplus.com</a>

= Pro Feature =
* Ultimate Registration form
* Custom form fields
* Create required fields
* Social Login/Register i.e Facebook,Google,Twitter
* Add / Edit / Delete / Duplicate Multiple Address
* Get user current location(address) using Geolocation
* Set defualt WooCommerce billing/shipping address from address list
* Select WooCommerce billing/shipping address from address book on checkout page
* Subscription newslatter on new user Registration with MailChimp, Aweber and Campaign Monitor
* Show the percentage of user profile completion
* On click Improve button it will show highlighted fields for improve profile strength.
* Set custom weight for field
* Profile progress on member profile
* WoCommerce integration
* Support Multiple address and set billing and shipping address
* Badges and Achievements - Automatically or manually assign badges to users based on different criteria’s like
* Specific user roles
* Based on activity score i.e Number of posts, comments, followers etc.
* Admin can manually assign badge
* Follow / Unfollow Feature lets users follow other users.
* Whenever a user posts, all the followers will receive a notification regarding the update.
* Keeps your user community more interactive and engaging.
* Premium Support
* New features added regularly!


== Installation ==
* Download the plugin file, unzip and place it in your wp-content/plugins/ folder. You can alternatively upload it via the WordPress plugin backend.
* Activate the plugin through the 'Plugins' menu in WordPress.
* WP User menu will appear in Dashboard->WP User.
* <b>shortcode</b><br>
<b> [wp_user] </b> shortcode for display login, registration, forgot password form.<br>
You Can use following attribute for custom form<br>
<b>[wp_user id='1234' width='360px' popup='1' active='register' role='subscriber' login_redirect='".get_site_url()."']</b><br>
<b> id </b> : If Multiple Form Add-on activated then create form and set id='form_id'.
You can use diffrent registration form for diffrent page.<br>
Ex. [wp_user id='1234']<br>

<b> width </b> : set custom width to login, registration, forgot password form.<br>
[wp_user width='360px']<br>

<b> popup </b>:  set  popup='1' shortcode for popuup model login, registration, forgot password form.<br>
Ex. [wp_user popup='1']<br>

<b> active </b>: For activate default form. By Defualt login.<br>
[wp_user active='register' popup='1'] shortcode for popuup model login, registration, forgot password form. default active registration form<br>
[wp_user active='register'] for display default active registration form.(sign up page)<br>
[wp_user active='forgot'] shortcode for display login, registration, forgot password form. default active forgot form<br>

<b> role </b>: Set role for new register user via register form. You can set diffrent role for diffrent form. By Defualt subscriber role<br>
Ex. [wp_user role='subscriber']<br>

<b> login_redirect </b>: Custom login redirection url for each login form.<br>
Ex. [wp_user login_redirect='www.yoursite.com/redirectPageUlr'] for redirect user after login to custom link. Replace 'www.yoursite.com/redirectPageUlr' Url with redirect page Url.

<br>
<b> [wp_user_member] </b> shortcode for display member list/directory<br>
You can use following attributes for filter/show member list <br>
<b>[wp_user_member role_in='subscriber' role_not_in='author' include='1,2,5,7' exclude='55,44,78,87' approve='1' size='small']</b><br>
<b>role_in </b> : If you want to show only selected member role in list then set this attribute by comma seprated<br>
Ex. [wp_user_member role_in='subscriber,author']<br>

<b>role_not_in </b> : If you want exclude to show some user roles in member list then set this attribute by comma seprated<br>
Ex. [wp_user_member role_not_in='subscriber,author']<br>

<b>include </b> : If you want only show selected user ids then set this attribute by comma seprated<br>
Ex. [wp_user_member include='1,2,5,7' ]<br>

<b>exclude </b> : If you don't want show selected user ids then set this attribute by comma seprated<br>
Ex. [wp_user_member exclude='55,44,78,87' ]<br>

<b>approve </b> : If you want show only approve user then set approve='1'<br>
Ex. [wp_user_member approve='1' ]<br>

<b>size </b> : If you want change default display member list template to small one then set size='small'<br>
Ex. [wp_user_member size='small' ]<br>

<b> [wp_user_restrict] your restricted content goes here [/wp_user_restrict]</b>
shortcode for Restrict Content to registered users only. logged in users only access content<br>
To restrict just a section of content within a post or page, you may use above shortcodes<br>
You can also set user role for access content.<br>
You can use role attribute for only access content to selected user role:<br>
Ex. [wp_user_restrict role='author,editor'] your restricted content goes here [/wp_user_restrict]<br>
Ex. [wp_user_restrict role='author'] your restricted content goes here [/wp_user_restrict]<br>
Ex. [wp_user_restrict role='logged_in'] your restricted content goes here [/wp_user_restrict] : logged in users only access content<br>
To restrict an entire post or page, simply select the user role you’d like to restrict the post or page to from the drop down menu added just below the post/page editor.

* Refer to the for More Information.
* http://wpuserplus.com/documentation

== Screenshots ==


1. screenshot-1.png
2. screenshot-2.png
3. screenshot-3.jpeg
4. screenshot-4.jpeg
5. screenshot-5.jpeg
6. screenshot-6.jpeg
7. screenshot-7.png
8. screenshot-8.png
9. screenshot-9.jpg


== Changelog ==

= 4.3 =
* 13-02-2018
* Added feature-option to add new custom tabs
* Added Setting - Set user profile menu tab vertically / horizontal
* http://wpuserplus.com/blog/release-new-version-4-3-wp-user

= 4.2.3 =
* 09-02-2018
* Added auto login after register
* fixed bug

= 4.2.2 =
* 05-02-2018
* Added Layout - customize skin color,buttons, link, box, form background etc
* http://wpuserplus.com/blog/release-new-version-4-2-2-wp-user/

= 4.2.1 =
* 06-01-2018
* Load minified css/js
* Imporve performance


= 4.2 =
* 28-12-2017
* Added language support
* Added support for addon backend
* Updated submenu hook position
* Chages wpseed name and icons


= 4.1 =
* 28-11-2017
* Added feature : Get Notification new comment on user post, woocommerce order status changed to refund/complete, new follow

= 4.0 =
* 22-11-2017
* Changes Complete UI
* AJAX implementation
* Added feature : Admin Approve
* Added feature : Email Approve
* Deny / Allow user lo login
* Set Default New User Approval status
* Restrict content
* Member Directory Changes
* Set Diffrent role for diffrent form (shortcode)
* Changes in widget
* Code Optimization
* Integration with WooCommerce
* More Secure and fast


= 2.9.3 =
* Change the Default Gravatar

= 2.9.2 =
* User Avatar : for users to upload images or enter url to their profile

= 2.9.1 =
* Subscription feature in edit profile - WP Subscription
* http://www.wpseeds.com/documentation/docs/wp-subscription/integration/wp-user-wordpress-plugin/

= 2.9 =
* View/Edit user information user front end dashboard
* View/Edit billing, shipping Address on user dashboard (WooCommerce)
* Support System in user profile
* Send mail to admin via Contact Us form
* http://www.wpseeds.com/blog/release-new-version-2-9-wp-user/

= 2.8 =
* login,register, forgot password form using widget
* [wp_user active='register'] for display default active registration form.(sign up page)
* [wp_user active='forgot'] default active forgot form.
* added hook for login,register, forgot password 


= 2.7.2 =
* Added : Language Translation for Hungarian

= 2.7.1 =
* Added : Language Translation for Detch, German

= 2.7 =
* Added google reCAPTCHA to registration form
* http://www.wpseeds.com/blog/release-new-version-2-7-wp-user/

= 2.6 =
* Added : Login Redirect - redirect user after login to custom link.
* Added : Language Translation for English, French
* Changed : Make Terms and Conditions readonly on front end
* Added : Forgot password email template (admin setting).

= 2.5.1 =
* Make Terms and Conditions readonly on front end

= 2.5 =
* Resolved WP User css(bootstrap) conflict with wordpress plugin css and theme css.
* Sign up form should reset after submitting sign form
* change in register template mail.

= 2.4 = 
* User getting confused that what is user name becuase in the sign form there is no username field - so name is changed as username 
* Login with Username or Email Id
* fixed form icon issue

= 2.3 =
* Change user register email template

= 2.2 = 
* Fixed css load issue 

= 2.1 = 
* Fixed missing file issue

= 2.0 = 
* Rebuilt Plugin in AngularJS
* security
* Limit Login Attempts
* Mechanism for slow down brute force attack
* Notify on lockout (Email to admin after cross limit the number of login attempts)
* Password Regular Expression (Form Validation & Security )
* Email Notification
* New Registration
* Custom email subject,content
* Auto Generate page for Login,Register
* Popup model for login,register, forgot password

= 1.1 = 
* Added google reCAPTCHA to registration form

= 1.0.0 = 
* Plugin Created

== Frequently Asked Questions ==

* Q-How to disable WordPress admin bar for all users except admin?
  <br>Go to Dashboard->WP User
  <br>1) Click on Disable Admin Bar check box 
  <br>2) Save setting

* Q-How to disable signup form?
  <br>Go to Dashboard->WP User
  <br>1) Click on uncheck Enable Signup Form check box.
  <br>2) Save setting
  
* do you offer custom pricing?
<br> Yes, we do. We can offer you a custom plan to meet your requirements. Please let us know all your requirements and we will contact you asap.

* Q.want more feature?
 <br>If you want more feature then
 <br>Drop Mail :walke.prashant28@gmail.com
  
== Upgrade Notice ==
* Added feature-option to add new custom tabs

== Official Site ==
* For More Information
* http://wpuserplus.com
* Or Advanced feature drop mail:walke.prashant28@gmail.com

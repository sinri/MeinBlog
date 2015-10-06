<?php
/**
MeinBlog Language Resource File
**/

/**
* 
*/
class MeinBlogLanguagePackage
{
	/*
	NAME_RULE
	---------
	PAGE_STRING-ID
	*/
	private $lang=array(
		'en'=>array(
			// INDEX
			'INDEX_HEADER_FILES'=>'FILES',
			'INDEX_BUTTON_SEARCH'=>'Search',
			'INDEX_BUTTON_EDIT'=>'Edit',
			'INDEX_HEADER_USERS'=>'FILES',
			'INDEX_BUTTON_NEW_BLOG'=>'Write New Blog',
			// SESSION
			'INDEX_HEADER_LOGIN'=>'LOGIN',
			'INDEX_BUTTON_LOGIN'=>'Login',
			'INDEX_LABEL_USERNAME'=>'Username',
			'INDEX_LABEL_PASSWORD'=>'Password',
			'INDEX_LOGIN_FAILED'=>'Login Failed!',
			'INDEX_BUTTON_LOGOUT'=>'Logout',
			'INDEX_BUTTON_REGISTER'=>'Register',
			'INDEX_BUTTON_USERS'=>'Users',
			'INDEX_BUTTON_REGISTER_CODES'=>'Register Codes',
			'INDEX_HEADER_USER'=>'USER',
			'WELCOME_USER'=>'Welcome, %s!',
			'LABEL_USER_GROUP'=>'User Group',
			'INDEX_BUTTON_MY_PROFILE'=>'My Profile',
			// CONST OF USER GROUPS
			'ADMIN'=>'ADMIN',
			'USER'=>'USER',
			'GUEST'=>'GUEST',
			'OUTSIDER'=>'OUTSIDER',
			// COMMON
			'API_FIELDS_UNCOMPLETED'=>'Please fill the required fields.',
			'API_FAILED_SAVING'=>'Failed to save modification.',
			'API_DONE_SAVING'=>'Saved modification.',
			'MSG_HEADER_TITLE'=>'Message:',
			'MSG_NOT_LOGINED_DIRECTING'=>'You have not logined into MeinBlog. Now directing to login...',
			// FILES
			'FILES_NO_FILES'=>'No Files Exist.',
			'ABSTRACT_NOT_FOUND'=>'No abstract found.',
			'PAGES_TOTAL_N'=>'%d Pages',
			// CATEGORY
			'CATEGORY_HEADER_CATEGORIES'=>'Categories',
			'CATEGORY_HEADER_MODIFY'=>'Modify Category',
		),
	);

	public function getStringInLanguage($string_id,$language=''){
		if(empty($language)){
			if(isset($_SESSION['language'])){
				$language=$_SESSION['language'];
			}else{
				$language='en';
			}
		}
		if(!isset($this->lang[$language])){
			return $string_id;
		}
		if(!isset($this->lang[$language][$string_id])){
			return $string_id;
		}
		return $this->lang[$language][$string_id];
	}
}
?>
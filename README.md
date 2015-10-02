# MeinBlog
A Simple Blog System in PHP

# Status
Version 1.0

# Install

Make server and database ready. Apache + MySQL has been tested with. PHP version should be above 5.0.

## Database

Prepare the SQL initialization file `MeinBlogDatabaseInit.sql` in `model` folder.

Check if the default database name `MeinBlog` had been used. If used or you want to change it, you may need to edit the top set paramter `@dbname` to the target.

	-- If you need to change database name, just modify the @dbname.
	SET @dbname='MeinBlog';

Then run the sql script in MySQL shell.

## Server

Fork the project, or download a copy, to the server and place into certain path. This project could be deployed into a sub directory as sub-project, or if possible as an independent project.

Edit `MeinBlogConfig.php`. Just replace the parameters with correct ones.
	
	// Database: for PDO
	'db_host'=>'127.0.0.1',
	'db_port'=>'3306',
	'db_charset'=>'utf8',
	'db_scheme'=>'MeinBlog',
	'db_username'=>'root',
	'db_password'=>'123456',
	// Depolyment: level as DEV, TEST, PROD, etc.
	'deploy_level'=>'DEV',

If vhost setting is needed, just do it.

OK now should it be. Open your browser, and check your MeinBlog.

# Memo

Current version only supports English interface.

The front end code is still somewhat ugly. Need time and power to improve.

Everything provided under MIT licence. Free for using and free for changing, it would be graceful to let me know your improvement.

# Homepage 

The homepage domain is [http://meinblog.ga/](http://meinblog.ga/) after release.
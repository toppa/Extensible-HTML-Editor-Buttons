# ElectNext installation guide

The following steps are for an installation of ElectNext on Ubuntu 11.10, and assumes a fresh environment, so it covers installing Rails, etc as well. Environment setup steps will vary on other platforms. ElectNext requires Postgres, and requires a reverse proxy server running https, that points to a standard http server. This guide uses Nginx with Phusion Passenger, but other web servers can be used.

## Setup Ruby, Rails, and the ElectNext codebase

1. The ElectNext codebase is on GitHub in a private repository, so you will first need to get access to it. You will also need a dumpfile of the database before proceeding.

1. Install git if needed

	`sudo apt-get install git`

1. From within the desired parent directory, clone the project

		git clone https://github.com/ElectNext/electnext.git

1. Install required libraries for Ruby if needed

		sudo apt-get install build-essential openssl libreadline6 libreadline6-dev curl
		git-core zlib1g zlib1g-dev libssl-dev libyaml-dev libsqlite3-0 libsqlite3-dev
		sqlite3 libxml2-dev libxslt-dev autoconf libc6-dev ncurses-dev automake libtool
		bison subversion

1. Install Ruby with Rails if needed (this will get the latest version of Ruby, but ElectNext uses Ruby 1.9.2: you can either specify the version here, or install 1.9.2 specifically for ElectNext in the following step). Note: do NOT install from the Ubuntu repository, which is still on Rails 2.

		curl -L https://get.rvm.io | bash -s stable --rails
		source /your_home_path/.rvm/scripts/rvm

1. Change directory into the electnext folder. You will be asked to install ruby 1.9.2 if it is not already installed.
		
		rvm install ruby-1.9.2-p320

1. Add the path to the rvm bin directory to your .bashrc file (or your equivalent)

		PATH=$PATH:$HOME/.rvm/bin 

1. Install the dev libs for the postgres server (required for installation of the "pg" gem listed in the electnext Gemfile)

		sudo apt-get install postgresql-server-dev-all

1. From inside the electnext folder, install gems. 

		gem install bundler
		source /your_home_path/.rvm/scripts/rvm
		bundle install --binstubs

1. Copy the example config files and edit them
	* Copy config/database.yml.example to config/database.yml and edit the development environment settings. Change the username to "electnext" and enter a password for it (you will use this password when creating the database account)
	* Copy config/oauth.yml.example to config/oauth.yml. Unless you are specifically testing social media integration you can leave this unchanged.

1. Update email notification settings (you don't want your dev environment generating real notification emails to the ElectNext staff)
	* Copy config/development\_env.rb.sample to config/development\_env.rb and change the email address and password
	* Edit config/environments/development.rb and update the settings on lines 33-45, and 62-63
	* Edit app/mailers/user_mailer.rb and update the email address on line 44

## Setup Postgres and import the ElectNext database dump file

1. Install postgres, the pgadmin GUI, and required libraries if needed

		sudo apt-get install postgresql pgadmin3 postgresql-contrib

1. Set the postgres superuser password if needed

		sudo -u postgres psql postgres
		# you are now at the postgres prompt
		\password postgres
		# enter a password, then add the admin pack (needed for the pgadmin GUI)
		CREATE EXTENSION adminpack;
		# ctrl+D to exit postgres

1. Connect the local database to the pgadmin GUI if needed. Start pgadmin3, click "File -> Add Server" and enter:

		Name: localhost
		Server: localhost
		Port: 5432
		Username: postgres
		Password: your_password

1. In the pgadmin GUI, create a new login role named "electnext", with the privilege to create database objects (importing the dump file requires this role to be created first)

1. In the pgadmin GUI, create an "electnext_dev" database, owned by the electnext role

1. Change directory to where you have the dumpfile, and restore it. This can take up to 15 minutes to run, depending on the size of the dumpfile and the speed of your system

		sudo -u postgres pg_restore -d electnext_dev name_of_your_dump_file.dump

1. The dump file does not include the "requests" table, due to its size (all requests are logged to this table). You can create it manually with the following SQL.

		CREATE TABLE requests
		(
		   id serial NOT NULL, 
		   user_id integer, 
		   "session" character varying(255), 
		   role_name character varying(255), 
		   remote_host character varying(255), 
		   remote_ip character varying(255), 
		   method character varying(255), 
		   path character varying(255), 
		   referrer character varying(255), 
		   controller character varying(255), 
		   "action" character varying(255), 
		   "exception" character varying(255), 
		   created_at timestamp without time zone, 
		   updated_at timestamp without time zone, 
		   redirect character varying(255), 
		   user_agent character varying(255), 
		   duration double precision, 
		   beta_login character varying(255), 
		   ab_tokens text, 
		   widget_id integer, 
		   ref character varying(255),
		   CONSTRAINT requests_pkey PRIMARY KEY (id)
		) 
		WITH (
		  OIDS = FALSE
		)
		;

		ALTER TABLE requests OWNER TO electnext;

		CREATE INDEX index_requests_on_created_at
		  ON requests
		  USING btree
		  (created_at);

		CREATE INDEX index_requests_on_session
		  ON requests
		  USING btree
		  (session);

		CREATE INDEX index_requests_on_user_id
		  ON requests
		  USING btree
		  (user_id);

		CREATE INDEX index_requests_on_widget_id
		  ON requests
		  USING btree
		  (widget_id);


## Setup the web servers (Phusion Passenger with Nginx)

1. Add electnext.dev as a local domain name to your /etc/hosts file

		127.0.0.1	electnext.dev

1. Create a "tmp" directory at the top level of the electnext project (it already has the required "public" directory and the config.ru file needed to run it as a Rack-based application)

1. Install Phusion Passenger with Nginx. [Full documentation is here](http://www.modrails.com/documentation/Users%20guide%20Nginx.html), but these are the necessary steps:

		# install required library for building nginx
		sudo apt-get install libcurl4-openssl-dev
		gem install passenger
		passenger-install-nginx-module
		# then follow the instructions as prompted for the desired nginx installation path, etc

1. Create a self-signed certificate for the https server by changing to the nginx conf directory, and accepting the default values when prompted by the following command (defaults are fine since this is a local dev site)

		sudo openssl req -new -nodes -keyout server.key -out server.csr
		sudo openssl x509 -req -days 365 -in server.csr -signkey server.key -out server.crt

1. Open the nginx.conf file, uncomment the HTTPS server block, and edit as follows

		# HTTPS server
		#
		server {
			listen       443;
			server_name  electnext.dev;
			ssl                  on;
			ssl_certificate      server.crt;
			ssl_certificate_key  server.key;
			ssl_session_timeout  5m;
			ssl_protocols  SSLv2 SSLv3 TLSv1;
			ssl_ciphers  HIGH:!aNULL:!MD5;
			ssl_prefer_server_ciphers   on;
			location / {
				proxy_set_header Host $host;
				proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
				proxy_set_header X-Forwarded-Proto https;
				proxy_redirect off;
				proxy_pass http://127.0.0.1;
			}
		}

1. Also in the nginx.conf file, the Passenger installation should have automatically added the passenger\_root and passenger\_ruby paths. Check that they are correct. Then edit the server block as follows

		server {
			listen       80;
			server_name  electnext.dev;
			root /home/toppa/Projects/electnext/public;
			passenger_enabled on;
			rails_env development;
		}

1. Test nginx, and run it:

		sudo nginx -t
		# if no configuration errors, start it up
		sudo nginx

1. If desired, change directory to the top of the electnext project, and start the delayed_job script, which is used for sending emails:
		
		script/delayed_job start

1. In your web browser you should now be able to navigate successfully to https://electnext.dev !
